<?php

namespace KBox\Http\Controllers\Administration;

use KBox\Http\Controllers\Controller;
use KBox\Option;
use Config;
use KBox\Http\Requests\MailSettingsRequest;
use Illuminate\Support\Facades\Mail;
use KBox\Mail\TestingMail;
use Illuminate\Support\Facades\DB;

/**
 * Controller
 */
class MailAdministrationController extends Controller
{

  /**
   * Create a new controller instance.
   *
   * @return void
   */
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
    }

    public function getIndex()
    {
        $mail_config = config('mail');

        $sections = Option::section('mail')->get(['key', 'value']);

        $flat = $sections->toArray();

        return view('administration.mail', [
        'pagetitle' => trans('administration.menu.mail'),
        'config' => $mail_config,
        'is_server_configurable' => $mail_config['driver'] !== 'log'
        ]);
    }

    public function postStore(MailSettingsRequest $request)
    {
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

        $mail_config = config('mail');
        $is_log_driver = $mail_config['driver'] === 'log';

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
        try {
            $res = Mail::to(config('mail.from.address'))->sendNow(new TestingMail());

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
