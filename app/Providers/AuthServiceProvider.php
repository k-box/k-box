<?php


namespace KBox\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
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
            // $upload_request instanceof \Avvertix\TusUpload\Http\Requests\CreateUploadRequest
            // ...

            \Log::info('Gate: Tus upload request', ['user' => $user->id, 'upload_request' => $upload_request->all()]);

            return true;
        });
    }
}
