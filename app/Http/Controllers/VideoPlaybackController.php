<?php

namespace KlinkDMS\Http\Controllers;

use KlinkDMS\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use KlinkDMS\Exceptions\ForbiddenException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Handle the video streaming.
 * Serve the dash manifest and the range request for a video file, identified by its UUID
 *
 */
class VideoPlaybackController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  string  $uuid The UUID of the reference file
     * @param string $resource the specific resource of the file, e.g. mpd for the Dash manifest
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $uuid, $resource = 'mpd')
    {
        $user = $request->user();

        $file = File::whereUuid($uuid)->first();
        
        if (! $file) {
            throw tap(new ModelNotFoundException(), function ($modelNotFound) use ($uuid) {
                $modelNotFound->setModel('File', $uuid);
            });
        }
        
        if (! $file->document->isAccessibleBy($user)) {
            throw new ForbiddenException('File access is restricted.');
        }

        if (! $file->isVideo()) {
            throw tap(new ModelNotFoundException(), function ($modelNotFound) use ($uuid) {
                $modelNotFound->setModel('File', $uuid);
            });
        }
        
        if ($file->videoResources()->isEmpty()) {
            throw tap(new ModelNotFoundException(), function ($modelNotFound) use ($uuid) {
                $modelNotFound->setModel('File', $uuid);
            });
        }

        $resources = $file->videoResources();
        
        if ($resource === 'mpd') {
            // dash manifest needs to be returned

            if (! $resources->has('dash')) {
                throw tap(new ModelNotFoundException(), function ($modelNotFound) use ($uuid) {
                    $modelNotFound->setModel('File', $uuid);
                });
            }

            $playlist_file = Storage::disk('local')->path($resources->get('dash'));

            return response()->download($playlist_file, $file->uuid.'.mpd', [
                'Content-Type' => 'application/dash+xml'
            ]);
        }

        $resource_path = $resources->get('streams')->first(function ($value, $key) use ($resource) {
            return ends_with($value, $resource);
        });

        if (is_null($resource_path)) {
            throw tap(new ModelNotFoundException(), function ($modelNotFound) use ($uuid) {
                $modelNotFound->setModel('File', $uuid);
            });
        }

        $absolute_path = Storage::disk('local')->path($resource_path);

        if (! is_null($request->header('range'))) {
            $range_header = $request->header('range');

            return $this->rangeResponse($absolute_path, $file->uuid.'.mp4', $request->header('range'), [
                'Content-Type' => 'video/mp4'
            ]);
        }

        // no range request, hence sending the whole file

        return response()->download($absolute_path, $file->uuid.'.mp4', [
            'Content-Type' => 'video/mp4'
        ]);
    }

    private function rangeResponse($path, $filename, $range_header, $headers)
    {
        $size = filesize($path);
        $start = 0;
        $length = $size;
        $status = 200;
    
        $response_headers = array_merge([
            'Content-Length' => $size,
            'Accept-Ranges' => 'bytes'
        ], $headers);

        list($param, $range) = explode('=', $range_header);
            
        if (strtolower(trim($param)) !== 'bytes') {
            \App::abort(400, 'Invalid request');
        }
            
        list($from, $to) = explode('-', $range);
            
        if ($from === '') {
            $end = $size - 1;

            $start = $end - intval($from);
        } elseif ($to === '') {
            $start = intval($from);

            $end = $size - 1;
        } else {
            $start = intval($from);

            $end = intval($to);
        }

        if ($end >= $length) {
            $end = $length - 1;
        }

        $length = $end - $start + 1;

        $status = 206;

        $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $start, $end, $size);

        $headers['Content-Length'] = $length;

        return response()->stream(function () use ($start, $length, $path) {
            $stream = fopen($path, 'rb');
            
            fseek($stream, $start);
        
            if (! $stream || ! feof($stream)) {
                echo fread($stream, $length);
            }
        
            fclose($stream);

            $stream = null;
        }, $status, $headers);
    }
}
