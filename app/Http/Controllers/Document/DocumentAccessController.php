<?php

namespace KBox\Http\Controllers\Document;

use Log;
use KBox\File;
use Illuminate\Support\Str;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use KBox\Exceptions\ForbiddenException;
use KBox\Documents\Services\DocumentsService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class DocumentAccessController extends Controller
{

    /**
     * @var KBox\Documents\Services\DocumentsService
     */
    private $documentsService = null;

    public function __construct(DocumentsService $documentsService)
    {
        $this->documentsService = $documentsService;
    }

    protected function getDocument($request, $uuid, $versionUuid)
    {
        $doc = DocumentDescriptor::withTrashed()->whereUuid($uuid)->with('file')->first();

        $file_version = null; // will contain the file instance to use in case $version is not null, null means the latest

        if (is_null($doc) || is_null(optional($doc)->file)) {
            throw new ModelNotFoundException();
        }

        $versions = $doc->file->versions();

        if (! is_null($versionUuid) && $doc->file->uuid !== $versionUuid && $versions->contains('uuid', $versionUuid)) {
            $file_version = $versions->where('uuid', $versionUuid)->first();
        }

        // if version is requested, the file must be a version of the same document descriptor is referenced in $id

        if (is_null($doc) || (! is_null($doc) && ! $doc->isMine())) {
            throw new ModelNotFoundException();
        }
        
        $user = $request->user();

        if (! ($doc->isPublished() || $doc->hasPendingPublications() || $doc->hasPublicLink()) && is_null($user)) {
            Log::warning('KlinkApiController, requested a document that is not public and user is not authenticated', ['url' => $request->url()]);

            throw new AuthenticationException();
        }
        if ($doc->trashed()) {
            throw new ModelNotFoundException();
        }

        $collections = $doc->groups;
        $is_in_collection = false;

        if (! is_null($collections) && ! $collections->isEmpty() && ! is_null($user)) {
            $serv = $this->documentsService;

            $filtered = $collections->filter(function ($c) use ($serv, $user) {
                return $serv->isCollectionAccessible($user, $c);
            });
            
            $is_in_collection = ! $filtered->isEmpty();
        }

        $is_shared = $doc->hasPublicLink() ? true : (! is_null($user) ? $doc->shares()->sharedWithMe($user)->count() > 0 : false);

        $owner = ! is_null($user) && ! is_null($doc->owner) ? $doc->owner->id === $user->id || $user->isContentManager() : (is_null($doc->owner) ? true : false);

        if (! ($is_in_collection || $is_shared || $doc->isPublic() || $owner || $doc->hasPendingPublications())) {
            throw new ForbiddenException('not shared, not in collection, not public or private of the user');
        }

        return [$doc, $file_version];
    }

    protected function downloadDocument(Request $request, DocumentDescriptor $doc, File $version = null)
    {
        /* File */ $file = $version ?? $doc->file;

        $embed = $request->input('embed', false);

        $ascii_name = Str::ascii($file->name); // ascii name is required for the response as it is mandatory for the Symfony binary response

        $headers = [
            'Content-Type' => $file->mime_type,
            'Etag' => $file->hash,
        ];

        if (strtolower($request->method()) === 'head') {
            return response()->head(array_merge($headers, [
                'Content-Length' => $file->size
            ]));
        }

        if ($embed) {
            return response()->file($file->absolute_path, $headers);
        }

        return response()->download($file->absolute_path, $ascii_name, $headers);
    }
}
