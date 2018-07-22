<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * KBox\Institution
 *
 * @property int $id
 * @property string $klink_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $type
 * @property string $address_street
 * @property string $address_country
 * @property string $address_locality
 * @property string $address_zip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $thumbnail_uri
 * @property string $url
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution fromKlinkID($klink_id)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereAddressCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereAddressLocality($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereAddressStreet($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereAddressZip($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereKlinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereThumbnailUri($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\Institution whereUrl($value)
 * @mixin \Eloquent
 */
class Institution extends Model
{
    /*
    id: bigIncrements
    klink_id: string
    name: string
    email: string
    type: string
    url: string
    thumbnail_uri: string
    phone: string
    address_street: string
    address_country: string
    address_locality: string
    address_zip: string
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'institutions';

    protected $fillable = ['klink_id', 'name', 'email','phone','thumbnail_uri','type','url','address_street','address_country','address_locality','address_zip'];

    public function scopeFromKlinkID($query, $klink_id)
    {
        return $query->where('klink_id', $klink_id);
    }

    /**
     * Get the institution that correspond to the given Klink Institution Identifier
     * @param  string $id The K-Link Institution Identifier
     * @return Institution|null     The Institution or null if cannot be found
     */
    public static function findByKlinkID($id)
    {
        return self::fromKlinkID($id)->first();
    }

    /**
     * Get the configured institution for this DMS
     *
     * @return Institution|null     The Institution or null if cannot be found
     */
    public static function current()
    {
        return self::findByKlinkID(config('dms.institutionID'));
    }

    /**
     * [toKlinkInstitutionDetails description]
     * @return KlinkInstitutionDetails [description]
     */
    public function toKlinkInstitutionDetails()
    {
        $instance = \KlinkInstitutionDetails::create($this->klink_id, $this->name, $this->type);
        
        $instance->setUrl($this->url);
        
        $instance->setThumbnail($this->thumbnail_uri);
        $instance->setMail($this->email);
        $instance->setPhoneNumber($this->phone);
        $instance->addressStreet = $this->address_street;
        $instance->addressCountry = $this->address_country;
        $instance->addressLocality = $this->address_locality;
        $instance->addressZip = $this->address_zip;
        
        return $instance;
    }

    /**
     * Check if two institutions are equal.
     *
     * @param object $instance The instance to check against. $instance must be of type \KlinkInstitutionDetails or Institution. Any other types automatically cause the equality check to return false.
     */
    public function equal($instance)
    {
        if (is_a($instance, '\KlinkInstitutionDetails')) {
            return $this->klink_id == $instance->id &&
                $this->name == $instance->name &&
                $this->email == $instance->email &&
                $this->phone == $instance->phone &&
                $this->thumbnail_uri == $instance->thumbnailURI &&
                $this->type == $instance->type &&
                $this->url == $instance->url &&
                $this->address_street == $instance->addressStreet &&
                $this->address_country == $instance->addressCountry &&
                $this->address_locality == $instance->addressLocality &&
                $this->address_zip == $instance->addressZip;
        } elseif (is_a($instance, \KBox\Institution::class)) {
            return $this->klink_id == $instance->klink_id &&
                $this->name == $instance->name &&
                $this->email == $instance->email &&
                $this->phone == $instance->phone &&
                $this->thumbnail_uri == $instance->thumbnail_uri &&
                $this->type == $instance->type &&
                $this->url == $instance->url &&
                $this->address_street == $instance->address_street &&
                $this->address_country == $instance->address_country &&
                $this->address_locality == $instance->address_locality &&
                $this->address_zip == $instance->address_zip;
        }
        
        return false;
    }

    public static function fromKlinkInstitutionDetails(\KlinkInstitutionDetails $instance)
    {
        $exists = self::findByKlinkID($instance->id);
        
        if (is_null($exists)) {
            // not exists
            $cached = self::create([
                'klink_id' => $instance->id,
                'name' => $instance->name,
                'email' => $instance->email,
                'phone' => $instance->phone,
                'thumbnail_uri' => $instance->thumbnailURI,
                'type' => $instance->type,
                'url' => $instance->url,
                'address_street' => $instance->addressStreet,
                'address_country' => $instance->addressCountry,
                'address_locality' => $instance->addressLocality,
                'address_zip' => $instance->addressZip,
            ]);
            
            return $cached;
        } elseif (! is_null($exists) && ! $exists->equal($instance)) {
            // exists and needs an update
            
            $exists->klink_id = $instance->id;
            $exists->name = $instance->name;
            $exists->email = $instance->email;
            $exists->phone = $instance->phone;
            $exists->thumbnail_uri = $instance->thumbnailURI;
            $exists->type = $instance->type;
            $exists->url = $instance->url;
            $exists->address_street = $instance->addressStreet;
            $exists->address_country = $instance->addressCountry;
            $exists->address_locality = $instance->addressLocality;
            $exists->address_zip = $instance->addressZip;
            
            $exists->save();
        }

        // exists and it's fine
        return $exists;
    }
}
