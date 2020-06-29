<?php

namespace KBox;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use KBox\Events\UserInviteAccepted;
use KBox\Events\UserInvited;
use Illuminate\Notifications\Notifiable;
use KBox\Auth\Registration;
use KBox\Notifications\InviteEmail;
use Dyrynda\Database\Casts\EfficientUuid;

/**
 * Invite to register and account.
 *
 * Generate and store user invitations.
 * Invites may be created when
 * - sharing to non-users
 * - adding an non-user as member of project
 * or by direct invitation from a user
 *
 * @property int $id autoincrement identifier of the invite
 * @property \Ramsey\Uuid\Uuid $uuid
 * @property string $email E-Mail of the invitee
 * @property string $token Invite token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Invite extends Model
{
    use GeneratesUuid;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'creator_id', 'email', 'token', 'actionable_id', 'actionable_type', 'accepted_at', 'expire_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'uuid' => EfficientUuid::class,
        'accepted_at' => 'datetime',
        'expire_at' => 'datetime',
        'details' => 'array',
    ];

    /**
     * Get the creator of this invite.
     *
     * @return \KBox\User
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * The model on which perform and action when
     * the invite is accepted
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function actionable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include invites of a user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \KBox\User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMine($query, User $user)
    {
        return $query->where('creator_id', $user->getKey());
    }

    /**
     * Check if the invite was created by a user
     *
     * @param \KBox\User $user
     * @return boolean
     */
    public function wasCreatedBy(User $user)
    {
        return $this->creator_id === $user->getKey();
    }

    /**
     * Retrieve if the invite was accepted
     *
     * An invite can be considered accepted
     * once a user registered using the
     * invitation token
     *
     * @return boolean
     */
    public function wasAccepted()
    {
        return ! (is_null($this->accepted_at) && is_null($this->user_id));
    }
    
    /**
     * Check if the invite expired
     *
     * The expiration is considered at the end of the day
     *
     * @see invite.expiration for configuring the invite lifespan
     * @return boolean
     */
    public function isExpired()
    {
        return $this->expire_at->lessThan(now());
    }

    /**
     * Scope a query to only include expired invites.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \KBox\User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('expire_at', '<', now());
    }

    /**
     * Scope a query to only include valid invites.
     *
     * Valid invites are not accepted and not expired
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \KBox\User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValid($query)
    {
        return $query
            ->where('expire_at', '>', now())
            ->whereNull('accepted_at');
    }

    /**
     * Scope a query to only include invites with a token invites.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $token
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasToken($query, $token)
    {
        return $query->where('token', $token);
    }

    /**
     * Accept the invitation
     *
     * @param  \KBox\User  $user
     * @return \KBox\Invite
     */
    public function accept(User $user)
    {
        $done = $this->forceFill([
            'accepted_at' => now(),
            'user_id' => $user->getKey()
        ])->save();

        $invite = $this->fresh();

        if ($done) {
            event(new UserInviteAccepted($invite));
        }

        return $invite;
    }

    /**
     * Send the invite notification.
     *
     * @return void
     */
    public function sendInviteNotification()
    {
        if (! Registration::isEnabled()) {
            return ;
        }
        if ($this->wasAccepted()) {
            return ;
        }
        if ($this->isExpired()) {
            return ;
        }

        $this->notify(new InviteEmail);

        $this->markNotified();
    }

    /**
     * Apply the notified state
     */
    public function markNotified()
    {
        $this->details = array_merge($this->details ?? [], [
            'notified_at' => now()->toRfc822String(),
        ]);

        if (isset($this->details['errored_at'])) {
            unset($this->details['errored_at']);
        }

        $this->save();

        return $this;
    }
    
    /**
     * Apply the errored state
     */
    public function markErrored()
    {
        $this->details = array_merge($this->details ?? [], [
            'errored_at' => now()->toRfc822String(),
        ]);

        $this->save();

        return $this;
    }

    /**
     * Create a new invite
     *
     * @param string $email
     *
     * @return \KBox\Invite
     */
    public static function generate(User $creator, $email, ?Model $action = null)
    {
        if (! $creator->can('create', Invite::class)) {
            throw new AuthorizationException(trans('invite.create.not-authorized'));
        }

        $invite = Invite::firstOrCreate([
                'creator_id' => $creator->getKey(),
                'email' => $email,
            ], [
                'token' => InviteToken::generate(),
                'actionable_id' => optional($action)->getKey() ?? null,
                'actionable_type' => $action ? get_class($action) : null,
                'expire_at' => now()->endOfDay()->addDays(config('invites.expiration'))
            ]);

        event(new UserInvited($invite));

        return $invite;
    }
}
