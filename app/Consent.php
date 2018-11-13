<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Consent management.
 *
 * Stores and manage user consent by topics, @see \KBox\Consents for the list of consent topics
 *
 * @property int $id the autoincrement identifier of the stored consent
 * @property int $consent_topic the topic the user gave consent to, @see \KBox\Consents
 * @property string $consent the label corresponding to the $consent_topic
 * @property \KBox\User $user the User that gave the consent
 * @property \Carbon\Carbon $created_at when the consent was stored
 * @property \Carbon\Carbon $updated_at when the consent was last updated
 */
class Consent extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'consent_topic'];

    /**
     * Instruct the activity logger to log changes to $fillable attributes
     */
    protected static $logFillable = true;

    /**
     * Let's log a subset of the eloquent events.
     *
     * In this case a consent is given when model is created and consent
     * is withdrawn when consent is deleted
     */
    protected static $recordEvents = ['created', 'deleted'];

    /**
     * Use this activity logger name
     */
    protected static $logName = 'consent';

    /**
     * Shortcut for creating a consent entry that express the user agreement to a consent topic
     *
     * @param \KBox\User $user
     * @param int $consent
     * @return \KBox\Consent
     */
    public static function agree(User $user, $consent)
    {
        if (! Consents::isValidEnumValue($consent)) {
            throw new InvalidArgumentException("Invalid consent topic specified. Given $consent");
        }

        return static::create([
            'user_id' => $user->getKey(),
            'consent_topic' => $consent
        ]);
    }

    /**
     * Disagree to the specific consent
     * Shortcut for finding a consent and delete it
     *
     * @param \KBox\User $user
     * @param int $consent
     * @return bool
     */
    public static function withdraw(User $user, $consent)
    {
        if (! Consents::isValidEnumValue($consent)) {
            throw new InvalidArgumentException("Invalid consent topic specified. Given $consent");
        }

        $stored_consent = static::where([
            'user_id' => $user->getKey(),
            'consent_topic' => $consent
        ])->first();

        if (! $stored_consent) {
            return true;
        }

        return $stored_consent->delete();
    }

    /**
     * Shortcut for checking if the user gave a specific consent
     *
     * @param \KBox\User $user
     * @param int $consent
     * @return bool
     */
    public static function isGiven(User $user, $consent)
    {
        if (! Consents::isValidEnumValue($consent)) {
            throw new InvalidArgumentException("Invalid consent topic specified. Given $consent");
        }

        return static::where('user_id', $user->getKey())->where('consent_topic', $consent)->exists();
    }
}
