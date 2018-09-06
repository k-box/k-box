<?php

namespace KBox\Documents\Services;

use Log;
use Exception;
use ErrorException;
use ReflectionClass;
use ReflectionException;
use InvalidArgumentException;
use KBox\Documents\FileHelper;
use KBox\Documents\DocumentType;
use KBox\Documents\TypeIdentifier;
use KBox\Documents\TypeIdentification;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\Debug\Exception\FatalErrorException;

/**
 * Service to interact with physical file for
 * - mime type/document type recognition
 * - hashing
 * - get extension from type
 */
class FileService
{

    /**
     * Store the registered type identifiers in a map that uses the accept mime
     * type/wildcard as first element, followed by the priority
     *
     * accept => priority => class
     *
     * e.g. [ 'accept' => "application/json", 'priority' => 10,  'mime' => $mimeType, 'doc' => $documentType, 'extension' => $extension, 'identifier' => JsonTypeIdentifier::class]
     *
     * @var array
     */
    private $identifiers = [];

    /**
     * Extension of the FileHlper $fileExtensionToMimeType conversion map.
     *
     * The key is the file extension, followed by the document type
     * and then the mime type. The document type is optional and can be omitted
     *
     * e.g. 'geotiff' => [DocumentType::GEODATA => 'image/tiff'],
     * See FileHelper::$fileExtensionToMimeType
     * @var array
     */
    private $extensionTypeMap = [];

    public function __construct()
    {
        $this->identifiers = collect([]);
    }

    /**
     * Register a new type identifier
     *
     * @param string $mimeType The mime type that will be recognized
     * @param string $documentType The document type that will correspond to the mime type
     * @param string $extension The usual extension that is associated with the mime type
     * @param string $identifierClass The identifier class to use to recognize the type
     * @return FileService
     */
    public function register(string $mimeType, string $documentType, string $extension, string $identifierClass)
    {
        if (! DocumentType::isValidEnumValue($documentType)) {
            throw new InvalidArgumentException("The specified document type [$documentType] is not valid. See \KBox\Documents\DocumentType for the possible values.");
        }

        if (! $this->isValidIdentifierClass($identifierClass)) {
            throw new InvalidArgumentException("The specified identifier class [$identifierClass] do not exists or extend \KBox\Documents\TypeIdentifier.");
        }

        $defaultIdentifierProperties = (new ReflectionClass($identifierClass))->getDefaultProperties();

        $accept = $defaultIdentifierProperties['accept'] ?? '*';
        $priority = $defaultIdentifierProperties['priority'] ?? 0;

        $identifierDetails = [
            'accept' => $accept,
            'priority' => $priority,
            'mime' => $mimeType,
            'doc' => $documentType,
            'extension' => $extension,
            'identifier' => $identifierClass
        ];

        if ($this->identifiers->contains($identifierDetails)) {
            return $this;
        }

        $this->identifiers->push($identifierDetails);
        
        // mix extensions, document type and mime type in the $extensionTypeMap
        data_set($this->extensionTypeMap, "$mimeType.$documentType", $extension, false);

        return $this;
    }

    /**
     * Return the list of registered type identifiers.
     *
     * Each entry in the list is an array in the form
     * [
     * 'mime' => $mimeType,
     * 'doc' => $documentType,
     * 'extension' => $extension,
     * 'identifier' => $identifierClass
     * ]
     *
     * @return array
     */
    public function identifiers()
    {
        return $this->identifiers->toArray();
    }

    private function isValidIdentifierClass($identifierClass)
    {
        try {
            return (new ReflectionClass($identifierClass))->isSubclassOf(TypeIdentifier::class);
        } catch (ReflectionException $ex) {
            return false;
        }
    }

    /**
     * Computes the hash of the file content
     *
     * Uses SHA-512 variant of SHA-2 (Secure hash Algorithm)
     *
     * @param string $path The file path
     * @return string
     */
    public function hash(string $path)
    {
        $absolute_path = @is_file($path) ? $path : Storage::path($path);

        return FileHelper::hash($absolute_path);
    }

    /**
     * Retrieve the mime type and document type of a file, given its path on disk
     *
     * @param string $path The path to the file
     * @return array with mime type as first element, and document type as second
     */
    public function recognize($path)
    {
        $absolute_path = @is_file($path) ? $path : Storage::path($path);

        $default = tap(new TypeIdentification(), function ($identification) use ($absolute_path) {
            list($resolvedMime, $resolvedDocument) = FileHelper::type($absolute_path);
            $identification->mimeType = $resolvedMime;
            $identification->documentType = $resolvedDocument;
        });

        $identifiers = $this->getIdentifiersFor($default->mimeType);
        
        $identifications = $this->executeIdentifiers($identifiers, $absolute_path, $default);
        
        if (! $identifications->isEmpty()) {
            $maxPriority = $identifications->max('priority');
            $maxPriorityIdentifications = $identifications->where('priority', $maxPriority);

            if ($maxPriorityIdentifications->count() == 1) {
                return $maxPriorityIdentifications->first()['result']->toArray();
            }

            $groupedByIdentifiedType = $identifications->groupBy(function ($item, $key) {
                return $item['result']->__toString();
            });

            $mostProbableType = $groupedByIdentifiedType->mapWithKeys(function ($value, $key) {
                return [$value->count() => $key];
            })->max();

            return $groupedByIdentifiedType->get($mostProbableType)->first()['result']->toArray();
        }

        return $default->toArray();
    }

    /**
     * Return the file extension that corresponds to the given mime type and document type
     *
     * @param  string $mimeType the mime-type of the file
     * @param  string $documentType the document-type of the file. Default null
     * @return string           the known file extension
     * @throws InvalidArgumentException If the mime type is unkwnown, null or empty
     */
    public function extensionFromType($mimeType, $documentType = null)
    {
        $default = null;

        try {
            $default = FileHelper::getExtensionFromType($mimeType, $documentType);
        } catch (InvalidArgumentException $ex) {
        }

        $extension = data_get($this->extensionTypeMap, "$mimeType.$documentType", null);
        
        if (is_null($default) && is_null($extension)) {
            throw new InvalidArgumentException("Unknown extension for mime type [{$mimeType}].");
        }

        return $extension ?? $default;
    }

    private function getIdentifiersFor($mimeType)
    {
        $matching = $this->identifiers->filter(function ($item) use ($mimeType) {
            $accepted = array_wrap(data_get($item, 'accept'));
            return in_array('*', $accepted) || in_array($mimeType, $accepted);
        });
        return $matching;
    }

    private function executeIdentifiers($identifiers, $path, $default)
    {
        return $identifiers->map(function ($identifier) use ($path, $default) {
            try {
                return [
                    'result' => (new $identifier['identifier']())->identify($path, $default),
                    'priority' => $identifier['priority']
                ];
            } catch (Exception $kex) {
                Log::error('Type identifier execution error', compact('identifier', 'path', 'default', 'kex'));
            } catch (ErrorException $kex) {
                Log::error('Type identifier execution error', compact('identifier', 'path', 'default', 'kex'));
            } catch (FatalErrorException $kex) {
                Log::error('Type identifier execution error', compact('identifier', 'path', 'default', 'kex'));
            } catch (FatalThrowableError $kex) {
                Log::error('Type identifier execution error', compact('identifier', 'path', 'default', 'kex'));
            }

            return null;
        })->filter();
    }
}
