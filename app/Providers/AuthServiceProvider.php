<?php

namespace KBox\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\File;
use KBox\Group;
use KBox\Invite;
use KBox\Policies\DocumentDescriptorPolicy;
use KBox\Policies\FilePolicy;
use KBox\Policies\GroupPolicy;
use KBox\Policies\InvitePolicy;
use KBox\Policies\ProjectPolicy;
use KBox\Policies\UploadPolicy;
use KBox\Project;
use Illuminate\Auth\Access\Response;
use KBox\Policies\PublicationPolicy;
use KBox\Policies\PublicLinkPolicy;
use KBox\Policies\SharedPolicy;
use KBox\Policies\StarredPolicy;
use KBox\Publication;
use KBox\PublicLink;
use KBox\Shared;
use KBox\Starred;
use KBox\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        DocumentDescriptor::class => DocumentDescriptorPolicy::class,
        File::class => FilePolicy::class,
        Invite::class => InvitePolicy::class,
        Group::class => GroupPolicy::class,
        Project::class => ProjectPolicy::class,
        Starred::class => StarredPolicy::class,
        Shared::class => SharedPolicy::class,
        Publication::class => PublicationPolicy::class,
        PublicLink::class => PublicLinkPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('upload-via-tus', UploadPolicy::class.'@uploadFileViaTus');

        Gate::define('upload-file', UploadPolicy::class.'@uploadFile');

        Gate::define('manage-kbox', function (User $user) {
            return $user->can_capability(Capability::MANAGE_KBOX)
                ? Response::allow()
                : Response::deny('You must be an administrator.');
        });
    }
}
