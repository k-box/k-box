<?php

namespace KBox\Http\Controllers\Administration;

use KBox\Http\Controllers\Controller;
use KBox\Option;
use Exception;
use Illuminate\Support\Arr;
use KBox\Http\Requests\MailSettingsRequest;
use Illuminate\Support\Facades\Mail;
use KBox\Mail\TestingMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * Controller
 */
class MailAdministrationController extends Controller
{
    private $mappings = [
        'mailers.smtp.port' => 'port',
        'mailers.smtp.host' => 'host',
        'mailers.smtp.username' => 'username',
        'mailers.smtp.password' => 'password',
        'from.address' => 'from.address',
        'from.name' => 'from.name',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getIndex()
    {
        Gate::authorize('manage-kbox');

        $mail_config = Arr::dot(config('mail'));

        $config = [];

        foreach ($this->mappings as $configKey => $key) {
            $config[$key] = $mail_config[$configKey];
        }

        return view('administration.mail', [
            'pagetitle' => trans('administration.menu.mail'),
            'config' => $config,
            'is_server_configurable' => $mail_config['default'] !== 'log'
        ]);
    }

    public function postStore(MailSettingsRequest $request)
    {
        Gate::authorize('manage-kbox');

        $server_fields = [
        'pretend' => 'mail.pretend',
        'host' => 'mail.host',
        'port' => 'mail.port',
        'encryption' => 'mail.encryption',
        'smtp_u' => 'mail.username',
        'smtp_p' => 'mail.password',
        ];
    
        $from_fields = [
        'from_address' => 'mail.from.address',
        'from_name' => 'mail.from.name',
        ];

        $is_log_driver = Option::isMailUsingLogDriver();

        $res = DB::transaction(function () use ($request, $from_fields, $server_fields, $is_log_driver) {
            $att = null;
            foreach ($from_fields as $field => $setting) {
                if ($request->has($field)) {
                    $value = $request->get($field);

                    $att = Option::firstOrNew(['key' => $setting]);
                    $att->value = $value;
                    $att->save();
                }
            }

            if (! $is_log_driver) {
                foreach ($server_fields as $field => $setting) {
                    if ($request->has($field)) {
                        $value = $request->get($field);

                        if ($field === 'smtp_p') {
                            $value = base64_encode($value);
                        } elseif ($field === 'pretend') {
                            $value = "0";
                        }

                        $att = Option::firstOrNew(['key' => $setting]);
                        $att->value = $value;
                        $att->save();
                    } elseif ($field=='pretend' && ! $request->has($field)) {
                        $value = "1";

                        $att = Option::firstOrNew(['key' => $setting]);
                        $att->value = $value;
                        $att->save();
                    }
                }
            }

            return true;
        });

        return redirect()->route('administration.mail.index')->with([
            'flash_message' => trans('administration.mail.configuration_saved_msg')
        ]);
    }

    public function getTest()
    {
        Gate::authorize('manage-kbox');
        
        try {
            if (! Option::isMailEnabled()) {
                throw new Exception(trans('validation.required', ['attribute' => ('administration.mail.from_address')]));
            }

            $res = Mail::to(config('mail.from.address'))->send(new TestingMail());

            return redirect()->route('administration.mail.index')->with([
                'flash_message' => trans('administration.mail.test_success_msg', ['from' => config('mail.from.address')])
            ]);
        } catch (\Exception $ex) {
            return redirect()->route('administration.mail.index')->withErrors(
                ['mail_send' => trans('administration.mail.test_failure_msg')]
            );
        }
    }
}
