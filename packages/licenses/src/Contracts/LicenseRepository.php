<?php

namespace OneOffTech\Licenses\Contracts;

/**
 * The contract that define the license listing and finding
 */
interface LicenseRepository
{
    /**
     * Retrieve all the available licenses
     *
     * @return \Illuminate\Support\Collection|\OneOffTech\Licenses\License[] the collection of all registered licenses
     */
    public function all();

    /**
     * Find a license by the given identifier
     *
     * @param  string $id the identifier of the license retrieve
     * @return License|null the license if found, null otherwise
     */
    public function find($id);

    /**
     * Find a license by the given identifier.
     * If the license cannot be found an exception is thrown
     *
     * @param  string $id the identifier of the license retrieve
     * @return License the license
     * @throws \OneOffTech\Licenses\Exceptions\LicenseNotFoundException if the license cannot be found
     */
    public function findOrFail($id);
}
