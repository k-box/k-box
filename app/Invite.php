<?php

namespace KBox;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use KBox\Events\UserInviteAccepted;
use KBox\Events\UserInvited;

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'creator_id', 'email', 'token', 'actionable_id', 'actionable_type', 'accepted_at',
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
        'uuid' => 'uuid',
        'accepted_at' => 'datetime',
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
     * Scope a query to only include waitlist of a user.
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
            ]);

        event(new UserInvited($invite));

        return $invite;
    }
}
