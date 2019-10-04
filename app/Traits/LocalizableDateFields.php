<?php

namespace KBox\Traits;

use Jenssegers\Date\Date as LocalizedDate;
use DateTime;

/**
 * Add support for localizing the created_at, updated_at and deleted_at date fields
 *
 * @uses Jenssegers\Date\Date
 * @deprecated use the Blade @date or @datetime directives or the render() method on the Carbon instance
 */
trait LocalizableDateFields
{
    protected function getLocalizedDateInstance(DateTime $dt)
    {
        return LocalizedDate::instance($dt);
    }

    /**
     * Generate the time difference output string.
     * If the difference in days is major or equal to 2 the normal date string is used
     *
     * @param Jenssegers\Date\Date $dt the date
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return the localized date difference
     */
    protected function generateHumanDiffOutput(LocalizedDate $dt)
    {
        $diff = $dt->diffInDays();
        
        if ($diff < 1) {
            return $dt->diffForHumans();
        }
        
        return null;
    }
    
    /**
     * Get the creation date localized in the current application locale.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized version of the date
     */
    public function getCreatedAt($full = false)
    {
        if (is_null($this->created_at)) {
            return "";
        }

        return $this->created_at->render($full);
    }

    /**
     * Get the update date localized in the current application locale.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized version of the date
     */
    public function getUpdatedAt($full = false)
    {
        if (is_null($this->updated_at)) {
            return "";
        }

        return $this->updated_at->render($full);
    }

    /**
     * Get the deletion date localized in the current application locale.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized version of the date
     */
    public function getDeletedAt($full = false)
    {
        if (is_null($this->deleted_at)) {
            return "";
        }

        return $this->deleted_at->render($full);
    }

    /**
     * Get the localized time difference between the creation date and now.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized time difference
     */
    public function getCreatedAtHumanDiff($full = false)
    {
        if (is_null($this->created_at)) {
            return "";
        }

        return $this->generateHumanDiffOutput($this->created_at->asLocalizableDate()) ?? $this->created_at->render($full);
    }

    /**
     * Get the localized time difference between the update date and now.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized time difference
     */
    public function getUpdatedAtHumanDiff($full = false)
    {
        if (is_null($this->updated_at)) {
            return "";
        }

        return $this->generateHumanDiffOutput($this->updated_at->asLocalizableDate()) ?? $this->updated_at->render($full);
    }

    /**
     * Get the localized time difference between the deletion date and now.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized time difference
     */
    public function getDeletedAtHumanDiff($full = false)
    {
        if (is_null($this->deleted_at)) {
            return "";
        }

        return $this->generateHumanDiffOutput($this->deleted_at->asLocalizableDate()) ?? $this->deleted_at->render($full);
    }
}
