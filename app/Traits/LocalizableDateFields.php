<?php namespace KlinkDMS\Traits;


use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Jenssegers\Date\Date as LocalizedDate;
use DateTime;

/**
 * Add support output a localized version of created_at, updated_at and deleted_at fields
 */
trait LocalizableDateFields
{

    protected function getLocalizedDateInstance(DateTime $dt){
        return LocalizedDate::instance($dt);
    }

    protected function generateHumanDiffOutput(LocalizedDate $dt, $full = false){

        $diff = $dt->diffInDays();
        
        if($diff < 2){
            return $dt->diffForHumans();
        }
        
        return $dt->format( trans( $full ? 'units.date_format_full' : 'units.date_format') );

    }
    
    /**
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     */
    public function getCreatedAt($full = false){

        if(is_null($this->created_at)){
            return "";
        }

        return $this->getLocalizedDateInstance($this->created_at)->format( trans( $full ? 'units.date_format_full' : 'units.date_format') );
    }

    /**
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     */
    public function getUpdatedAt($full = false){

        if(is_null($this->updated_at)){
            return "";
        }

        return $this->getLocalizedDateInstance($this->updated_at)->format( trans( $full ? 'units.date_format_full' : 'units.date_format') );
    }

    /**
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     */
    public function getDeletedAt($full = false){

        if(!is_null($this->deleted_at)){
            return $this->getLocalizedDateInstance($this->deleted_at)->format( trans( $full ? 'units.date_format_full' : 'units.date_format') );
        }

        return "";
    }

    /**
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     */
    public function getCreatedAtHumanDiff($full = false){
        
        if(is_null($this->created_at)){
            return "";
        }

        $dt = $this->getLocalizedDateInstance($this->created_at);

        return $this->generateHumanDiffOutput($dt, $full);
    }

    /**
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     */
    public function getUpdatedAtHumanDiff($full = false){

        if(is_null($this->updated_at)){
            return "";
        }

        $dt = $this->getLocalizedDateInstance($this->updated_at);

        return $this->generateHumanDiffOutput($dt, $full);
    }

    /**
     *
     * @param bool $full if you want to output the full date and time string and not the short version
     */
    public function getDeletedAtHumanDiff($full = false){

        if(is_null($this->deleted_at)){
            return "";
        }

        $dt = $this->getLocalizedDateInstance($this->deleted_at);

        return $this->generateHumanDiffOutput($dt, $full);
    }

}