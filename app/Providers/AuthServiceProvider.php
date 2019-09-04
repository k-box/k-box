<?php

namespace KBox\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use KBox\DocumentDescriptor;
use KBox\File;
use KBox\Policies\DocumentDescriptorPolicy;
use KBox\Policies\FilePolicy;

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

        Gate::define('upload-via-tus', function ($user, $upload_request) {
            // $upload_request instanceof \OneOffTech\TusUpload\Http\Requests\CreateUploadRequest
            // ...

            \Log::info('Gate: Tus upload request', ['user' => $user->id, 'upload_request' => $upload_request->all()]);

            return true;
        });
    }
}
