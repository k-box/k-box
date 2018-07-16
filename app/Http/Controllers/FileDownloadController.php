<?php

namespace KBox\Http\Controllers;

use Exception;
use KBox\File;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use KBox\Exceptions\ForbiddenException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Enable to directly download a file.
 *
 * This is similar to KlinkApiController@show with action === download,
 * but it directly reference the file given its UUID.
 * The only possible action is file being downloaded.
 *
 */
class FileDownloadController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \KBox\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $uuid)
    {
        $file = File::whereUuid($uuid)->first();

        $token = $request->input('t', null);
        
        if (! $file) {
            throw tap(new ModelNotFoundException(), function ($modelNotFound) use ($uuid) {
                $modelNotFound->setModel('File', $uuid);
            });
        }

        if (! $this->verifyToken($token, $file)) {
            throw new ForbiddenException();
        }

        \Log::info("requesting download for $uuid, with {$request->method()}", ['file' => $file]);

        $ascii_name = Str::ascii($file->name); // ascii name is required for the response as it is mandatory for the Symfony binary response

        $headers = [
            'Content-Type' => $file->mime_type
        ];

        if (strtolower($request->method()) === 'head') {
            return response()->head($headers);
        }

        return response()->download($file->absolute_path, $ascii_name, $headers);
    }

    /**
     * Verify that a temporary download token is valid
     *
     * @param string $token
     * @return bool
     */
    private function verifyToken($token, File $file)
    {
        if (! $token) {
            return false;
        }

        try {
            $components = explode('#', Crypt::decryptString($token));

            if (count($components) !== 4) {
                return false;
            }

            return $file->uuid === $components[0] &&
                   $file->hash === $components[1] &&
                   Carbon::now()->between(
                    Carbon::createFromTimestamp($components[2]),
                    Carbon::createFromTimestamp($components[3]));
        } catch (DecryptException $ex) {
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }
}
