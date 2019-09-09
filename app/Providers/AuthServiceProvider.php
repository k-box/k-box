<?php

namespace KBox\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use KBox\DocumentDescriptor;
use KBox\File;
use KBox\Policies\DocumentDescriptorPolicy;
use KBox\Policies\FilePolicy;
use KBox\Policies\UploadPolicy;

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
    }
}
