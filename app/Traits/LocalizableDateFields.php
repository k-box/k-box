<?php namespace KlinkDMS\Traits;


use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Jenssegers\Date\Date as LocalizedDate;
use DateTime;

/**
 * Add support for localizing the created_at, updated_at and deleted_at date fields
 *
 * @uses Jenssegers\Date\Date
 *
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
    protected function generateHumanDiffOutput(LocalizedDate $dt, $full = false)
    {

        $diff = $dt->diffInDays();
        
        if($diff < 2)
        {
            return $dt->diffForHumans();
        }
        
        return $dt->format( trans( $full ? 'units.date_format_full' : 'units.date_format') );

    }
    
    /**
     * Get the creation date localized in the current application locale.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized version of the date
     */
    public function getCreatedAt($full = false)
    {

        if(is_null($this->created_at))
        {
            return "";
        }

        return $this->getLocalizedDateInstance($this->created_at)->format( trans( $full ? 'units.date_format_full' : 'units.date_format') );
    }

    /**
     * Get the update date localized in the current application locale.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized version of the date
     */
    public function getUpdatedAt($full = false)
    {

        if(is_null($this->updated_at))
        {
            return "";
        }

        return $this->getLocalizedDateInstance($this->updated_at)->format( trans( $full ? 'units.date_format_full' : 'units.date_format') );
    }

    /**
     * Get the deletion date localized in the current application locale.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized version of the date
     */
    public function getDeletedAt($full = false)
    {

        if(!is_null($this->deleted_at))
        {
            return $this->getLocalizedDateInstance($this->deleted_at)->format( trans( $full ? 'units.date_format_full' : 'units.date_format') );
        }

        return "";
    }

    /**
     * Get the localized time difference between the creation date and now.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized time difference
     */
    public function getCreatedAtHumanDiff($full = false)
    {
        
        if(is_null($this->created_at))
        {
            return "";
        }

        $dt = $this->getLocalizedDateInstance($this->created_at);

        return $this->generateHumanDiffOutput($dt, $full);
    }

    /**
     * Get the localized time difference between the update date and now.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized time difference
     */
    public function getUpdatedAtHumanDiff($full = false)
    {

        if(is_null($this->updated_at))
        {
            return "";
        }

        $dt = $this->getLocalizedDateInstance($this->updated_at);

        return $this->generateHumanDiffOutput($dt, $full);
    }

    /**
     * Get the localized time difference between the deletion date and now.
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     * @return string the localized time difference
     */
    public function getDeletedAtHumanDiff($full = false)
    {

        if(is_null($this->deleted_at))
        {
            return "";
        }

        $dt = $this->getLocalizedDateInstance($this->deleted_at);

        return $this->generateHumanDiffOutput($dt, $full);
    }

}