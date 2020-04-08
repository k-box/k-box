<?php

namespace KBox\Geo\Http\Controllers;

use Log;
use KBox\User;
use KBox\File;
use KBox\Geo\GeoService;
use Illuminate\Support\Str;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use KBox\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use KBox\Documents\Services\DocumentsService;
use KBox\Http\Controllers\Document\DocumentAccessController;

use Intervention\Image\Facades\Image as ImageFacade;

class WebMapServiceController extends Controller
{
    
    /**
     * 
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $documents = null;

    private $geoservice = null;

    private $formatMapping = [
        'image/png' => 'png',
        'image/png8' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DocumentsService $documentsService, GeoService $geoservice)
    {
    
        $this->middleware('flags:plugins');

        $this->documents = $documentsService;

        $this->geoservice = $geoservice;
    }

    protected function verifyUserHasAccess($user, $file)
    {
        $doc = $file->document;

        if (is_null($doc) || (! is_null($doc) && ! $doc->isMine())) {
            throw new ModelNotFoundException();
        }

        if (! ($doc->isPublished() || $doc->hasPendingPublications() || $doc->hasPublicLink()) && is_null($user)) {
            throw new AuthenticationException();
        }
        if ($doc->trashed()) {
            throw new ModelNotFoundException();
        }

        $collections = $doc->groups;
        $is_in_collection = false;

        if (! is_null($collections) && ! $collections->isEmpty() && ! is_null($user)) {
            $serv = $this->documents;

            $filtered = $collections->filter(function ($c) use ($serv, $user) {
                return $serv->isCollectionAccessible($user, $c);
            });
            
            $is_in_collection = ! $filtered->isEmpty();
        }

        $is_shared = $doc->hasPublicLink() ? true : (! is_null($user) ? $doc->shares()->sharedWithMe($user)->count() > 0 : false);

        $owner = ! is_null($user) && ! is_null($doc->owner) ? $doc->owner->id === $user->id || $user->isPartner() : (is_null($doc->owner) ? true : false);

        if (! ($is_in_collection || $is_shared || $doc->isPublic() || $owner || $doc->hasPendingPublications())) {
            throw new ForbiddenException('not shared, not in collection, not public or private of the user');
        }

        return true;
    }

    /**
     * Proxy the Web Map Service (WMS) request to
     * the configured GeoServer
     *
     * @return Response
     */
    public function show(Guard $auth, Request $request)
    {
        $user = $auth->user();

        $layers = $request->input('layers', '');
        $fingerprint = $this->fingerprint($request);

        $uuid = trim(Str::after($layers, ':'));

        if(!$uuid){
            return $this->createEmptyResponse($request);
        }

        $file = File::whereUuid($uuid)->first();

        if(!$file){
            return $this->createEmptyResponse($request);
        }

        $this->verifyUserHasAccess($user, $file);
        
        $canBeFoundOnGeoserver = !is_null($file->properties->get('geoserver.store', null));
        
        if(!$canBeFoundOnGeoserver){
            return $this->createEmptyResponse($request);
        }


        return Cache::remember($fingerprint, 10, function() use($request) {
            $parameters = $request->only(
                'bbox',
                'format',
                'height',
                'width',
                'id',
                'layers',
                'request',
                'service',
                'srs',
                'styles',
                'transparent',
                'version',
                'query_layers',
                'info_format',
                'exceptions',
                'x',
                'y',
                'i',
                'j'
            );
    
            try{
    
                return $this->geoservice->proxyWmsRequest($parameters);
    
            }catch(Exception $ex)
            {
                Log::error('Unable to proxy GeoServer request', ['params' => $parameters, 'error' => $ex]);
    
                return $this->createEmptyResponse($request);
            }
        });
        
        

    }

    private function fingerprint($request)
    {
        return sha1(implode('|', 
            array_values($request->only(
                'bbox','format','height','width','id','layers',
                'request','service','srs','styles','transparent',
                'version','query_layers','info_format',
                'exceptions','x','y','i','j'
            ))
        ));
    }


    private function createEmptyResponse($request)
    {
        $info_format = $request->input('info_format', null);

        if($info_format === 'application/json'){
            return response()->json([]);
        }

        $format = $request->input('format', 'image/png');
        $width = $request->input('width', 256);
        $height = $request->input('hei$height', 256);

        return $this->respondWithEmptyImage($width, $height, $format);
    }


    private function respondWithEmptyImage($width = 256, $height = 256, $format = 'image/png')
    {
        $img = ImageFacade::canvas($width, $height);

        return $img->response($this->formatMapping[$format] ?? 'png');
    }
}
