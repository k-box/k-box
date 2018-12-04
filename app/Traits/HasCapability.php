<?php

namespace KBox\Traits;

use KBox\Capability;
use Illuminate\Support\Facades\DB;

/**
 * Add support for checking capabilities and permission to the User Eloquent Model
 */
trait HasCapability
{

    // User relations ---------------------------------------------------------
    
    private $dirtyCapabilities = false;

    /**
     * Retrive the associated Capabilities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function capabilities()
    {
        return $this->belongsToMany(\KBox\Capability::class, 'capability_user')->withTimestamps();
    }

    // Testing capabilities ---------------------------------------------------
    
    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Capability name or array of Capability names
     * @return boolean return true if the user supports at least one of the specified $permission
     */
    public function can_capability($permission)
    {
        if (empty($permission)) {
            return false;
        }

        $caps = $this->dirtyCapabilities ? $this->fresh()->capabilities->toArray() : $this->capabilities->toArray();

        if (empty($caps)) {
            return false;
        }

        $names = array_pluck($caps, 'key');

        if (empty($names)) {
            return false;
        }

        if (is_array($permission)) {
            $diff = array_diff($permission, $names);
            $perms_count = count($permission);
            $diff_count = count($diff);

            $intersect = array_intersect($permission, $names);

            return ! empty($intersect);
        }

        return in_array($permission, $names);
    }

    /**
     * Verify if all the specified capabilities are met by the user
     *
     * @param string[] $capabilities the array of Capability names to check for
     * @return boolean true if the the $capabilities are met by the user
     */
    public function can_all_capabilities(array $capabilities)
    {
        if (empty($capabilities)) {
            return false;
        }

        $caps = $this->dirtyCapabilities ? $this->fresh()->capabilities->toArray() : $this->capabilities->toArray();

        if (empty($caps)) {
            return false;
        }

        $names = array_pluck($caps, 'key');

        $intersect = array_intersect($capabilities, $names);

        return $intersect == $capabilities;
    }

    /**
     * Check if the user can manage content.
     *
     * Test if the user has all the @see \KBox\Capability::$CONTENT_MANAGER capabilities
     *
     * @return boolean true if is a content manager, false otherwise
     */
    public function isContentManager()
    {
        return $this->can_all_capabilities(Capability::$CONTENT_MANAGER);
    }

    /**
     * Check if the user has the partner role.
     *
     * Test if the user has all the @see \KBox\Capability::$PARTNER capabilities
     *
     * @return boolean true if is a partner, false otherwise
     */
    public function isPartner()
    {
        return $this->can_all_capabilities(Capability::$PARTNER) && ! $this->can_capability(Capability::MANAGE_PEOPLE_GROUPS);
    }

    /**
     * Check if the user has full administrative powers
     *
     * Test if the user has all the @see \KBox\Capability::$ADMIN capabilities
     *
     * @return boolean true if is a DMS administrator, false otherwise
     */
    public function isDMSAdmin()
    {
        return $this->can_all_capabilities(Capability::$ADMIN);
    }

    /**
     * Check if the user can perform DMS Management operations
     *
     * Test if the user has all the @see \KBox\Capability::$DMS_MASTER capabilities
     *
     * @return boolean true if can manage the DMS configuration, false otherwise
     */
    public function isDMSManager()
    {
        return $this->can_all_capabilities(Capability::$DMS_MASTER);
    }
    
    /**
     * Check if the user is a Project Manager
     *
     * Test if the user has all the @see \KBox\Capability::$DMS_MASTER capabilities
     *
     * @return boolean true if the user is a project manager, false otherwise
     */
    public function isProjectManager()
    {
        return $this->can_all_capabilities(Capability::$PROJECT_MANAGER_LIMITED);
    }

    // Adding and removing capabilities ---------------------------------------

    /**
     * Add a capability to the model
     *
     * @param string|\KBox\Capability $cap the capability to add. It can be a string (corresponding to the Capability key) or the Capability instance
     * @return void
     */
    public function addCapability($cap)
    {
        if (is_string($cap)) {
            $cap = Capability::fromKey($cap)->first();
        }

        if (is_object($cap)) {
            $cap = $cap->getKey();
        }
        if (is_array($cap)) {
            $cap = $cap['id'];
        }
        $this->dirtyCapabilities = true;
        
        return $this->capabilities()->attach($cap);
    }

    /**
     * Add multiple capabilities to the user
     *
     * @param array $caps array of capability names, ids or Capability instances
     * @return int the number of capabilities succesfully added
     */
    public function addCapabilities(array $capabilities)
    {
        $cap_instances = array_map(function ($el) {
            if (is_int($el)) {
                return Capability::findOrFail($el);
            } elseif (is_string($el)) {
                return Capability::fromKey($el)->first();
            } elseif ($el instanceof Capability) {
                return $el;
            } else {
                throw new \Exception('Unkwnown capability');
            }
        }, $capabilities);

        $that = $this;

        return DB::transaction(function () use ($that, $cap_instances) {
            $count = 1;
            
            foreach ($cap_instances as $cap) {
                $that->addCapability($cap);
                $count = $count+1;
            }
            
            $that->dirtyCapabilities = true;
            
            return $count;
        });
    }

    /**
     * Remove a capability from the model
     *
     * @param string|\KBox\Capability $cap the capability to remove. It can be a string (corresponding to the Capability key) or the Capability instance
     * @return void
     */
    public function removeCapability($cap)
    {
        if (is_object($cap)) {
            $cap = $cap->getKey();
        }
        if (is_array($cap)) {
            $cap = $cap['id'];
        }
        
        if (is_string($cap)) {
            $cap = Capability::fromKey($cap)->first();
        }
        
        $this->dirtyCapabilities = true;
        
        return $this->capabilities()->detach($cap);
    }
}
