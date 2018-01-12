<?php

namespace OneOffTech\Licenses\Services;

use Illuminate\Support\Collection;
use OneOffTech\Licenses\License;
use OneOffTech\Licenses\Contracts\LicenseRepository;
use OneOffTech\Licenses\Exceptions\LicenseNotFoundException;
use OneOffTech\Licenses\Exceptions\LicensesLoadingException;

/**
 *
 */
class LicenseService implements LicenseRepository
{
    protected $licenses = null;

    private $assets;
    private $licensesPath;
    private $descriptionsPath;
    private $iconsPath;

    /**
     * ...
     *
     * @return void
     */
    public function __construct($config)
    {
        // dump($config);
        $this->licensesPath = $config['assets'].$config['license_collection'];
        $this->assets = $config['assets'];
        $this->descriptionsPath = $config['assets'].'descriptions';
        $this->iconsPath = $config['assets'].'icons';

        $this->loadLicenses();
    }

    /**
     * Load the licenses contained in the configured license file
     *
     * @throws \OneOffTech\Licenses\Exceptions\LicensesLoadingException if the license JSON file cannot be parsed or the file doesn't exist
     */
    private function loadLicenses()
    {
        if (! is_file($this->licensesPath)) {
            throw new LicensesLoadingException("Licenses cannot be loaded. License file does not exists [$this->licensesPath]");
        }

        $decoded = json_decode(file_get_contents($this->licensesPath), JSON_OBJECT_AS_ARRAY);

        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new LicensesLoadingException('Licenses cannot be loaded. '.json_last_error_msg());
        }

        $this->licenses = collect($decoded)->mapWithKeys(function ($item) {
            return [strtolower($item['id']) => (new License($item))->setAssetsPath($this->assets)];
        });
    }

    /**
     * Retrieve all the available licenses
     *
     * @return \Illuminate\Support\Collection|\OneOffTech\Licenses\License[] the collection of all registered licenses
     */
    public function all()
    {
        return collect($this->licenses->values());
    }

    /**
     * Find a license by the given identifier
     *
     * @param  string $id the identifier of the license retrieve
     * @return License|null the license if found, null otherwise
     */
    public function find($id)
    {
        return $this->licenses->get(strtolower($id));
    }

    /**
     * Find a license by the given identifier.
     * If the license cannot be found an exception is thrown
     *
     * @param  string $id the identifier of the license retrieve
     * @return License the license
     * @throws \OneOffTech\Licenses\Exceptions\LicenseNotFound if the license cannot be found
     */
    public function findOrFail($id)
    {
        $found = $this->find($id);

        if (! $found) {
            throw new LicenseNotFoundException();
        }

        return $found;
    }
}
