<?php namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Http\Requests\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use KlinkDMS\User;
use KlinkDMS\Option;
use Config;
use KlinkDMS\Http\Requests\MailSettingsRequest;

/**
 * Controller
 */
class MailAdministrationController extends Controller {


  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct() {

      $this->middleware('auth');

      $this->middleware('capabilities');

  }

  public function getIndex()
  {

    $mail_config = Config::get('mail');

    $sections = Option::section('mail')->get(array('key', 'value'));

    $flat = $sections->toArray();

    return view('administration.mail', ['pagetitle' => trans('administration.menu.mail'), 'config' => $mail_config]);
  }



  public function postStore(MailSettingsRequest $request){

    $fields = [
      'pretend' => 'mail.pretend',
      'host' => 'mail.host',
      'port' => 'mail.port',
      'encryption' => 'mail.encryption',
      'smtp_u' => 'mail.username',
      'smtp_p' => 'mail.password',
      'from_address' => 'mail.from.address',
      'from_name' => 'mail.from.name',
    ];

    $res = \DB::transaction(function() use($request, $fields){

      $att = null;
      foreach ($fields as $field => $setting) {
      
          if($request->has($field)){

            $value = $request->get($field);

            if($field === 'smtp_p'){
              $value = base64_encode($value);
            }
            else if($field === 'pretend'){
              $value = "0";
            }

            $att = Option::firstOrNew(array('key' => $setting));
            $att->value = $value;
            $att->save();

          }
          else if($field=='pretend' && !$request->has($field)){
            $value = "1";

            $att = Option::firstOrNew(array('key' => $setting));
            $att->value = $value;
            $att->save();
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

      try{

        $res = \Mail::send('emails.test', [], function($message)
        {
            $message->from(\Config::get('mail.from.address'), \Config::get('mail.from.name'));

            $message->to(\Config::get('mail.from.address'), \Config::get('mail.from.name'))->subject('DMS Test Mail');
        });

        if($res){

          return redirect()->route('administration.mail.index')->with([
                'flash_message' => trans('administration.mail.test_success_msg', ['from' => \Config::get('mail.from.address')])
            ]);

        }
        else {

          return redirect()->route('administration.mail.index')->withErrors(
            ['mail_send' => trans('administration.mail.test_failure_msg')]
          );

        }

      }catch(\Exception $ex){

        return redirect()->route('administration.mail.index')->withErrors(
            ['mail_send' => trans('administration.mail.test_failure_msg')]
          );

      }
    
  }

}
