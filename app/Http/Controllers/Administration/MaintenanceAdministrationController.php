<?php

namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\User;
use KlinkDMS\Option;

/**
 * Controller
 */
class MaintenanceAdministrationController extends Controller
{

  /*
  |--------------------------------------------------------------------------
  | User Management Page Controller
  |--------------------------------------------------------------------------
  |
  | This controller respond to ations for the "users management page".
  |
  */

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
        $active = Option::option('dms.queuelistener.active', false);

        $isInError = Option::option('dms.queuelistener.errorState', false);

    
        $log = storage_path('logs/laravel-'.date('Y-m-d').'.log');
    
        $log_entries = null;
        if (is_file($log)) {
            $log_entries = $this->tailCustom($log, 1000);
        }
    
    
        return view('administration.maintenance', [
      'pagetitle' => trans('administration.menu.maintenance'),
      'log_entries' => $log_entries,
      'queue_runner_status' => $active ? trans('administration.maintenance.queue_runner_started') : trans('administration.maintenance.queue_runner_stopped'),
      'queue_runner_status_class' => $active ? 'success' : 'error',
      'queue_runner_status_boolean' => $active,
    ]);
    }
  
  
    public function tailCustom($filepath, $lines = 1, $adaptive = true)
    {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) {
            return false;
        }
        // Sets buffer size
        if (! $adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }
        // Jump to last character
        fseek($f, -1, SEEK_END);
        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }
        
        // Start reading
        $output = '';
        $chunk = '';
        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)).$output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }
        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        // Close file and return
        fclose($f);
        return trim($output);
    }
}
