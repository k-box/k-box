<?php

namespace KBox\Http\Controllers\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use KBox\Http\Controllers\Controller;
use Zip;
use KBox\DocumentDescriptor;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;
use Illuminate\Http\JsonResponse;
use PHPUnit\Util\Json;

use function Matrix\trace;

class BulkDownloadController extends Controller
{
        /*
    |--------------------------------------------------------------------------
    | Bulk Operation on Documents and Groups Controller
    |--------------------------------------------------------------------------
    |
    | handle the operation when something is performed on a multiple selection.
    | To simply JS stuff
    |
    */

    /**
     * [$service description]
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $service = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\KBox\Documents\Services\DocumentsService $adapterService)
    { 
        $this->middleware('auth');

    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        try {
            \Log::info('Bulk  Download', ['params' => $request->all()]);
            
            //$user = $auth->user();
            $docs = $request->input('documents', []);
            $status = [];

            $files_to_zip = [];

                $docs = $request->input('documents', []);

                $documents = DocumentDescriptor::whereIn('id', $docs)->with('file')->get();
                foreach ($documents as $key => $value) {
                    $files_to_zip=array_merge($files_to_zip,[$value->file->absolute_path => $value->file->name]);
                }   

                
                $zipSize = Zip::create('download.zip',$files_to_zip)->predictZipSize()/1000000;
               
                if($zipSize <50 ){

                    $status = [
                        'status' => 'download',
                        'title' => trans('documents.bulk.download_file_size_over.title'),
                        'message' => trans('documents.bulk.download_file_size_over.message')
                    ];

                }elseif ($zipSize > 50 &&  $zipSize < 4000){

                    $status = [
                        'status' => 'ok',
                        'title' => trans('documents.bulk.download_file_size_over.title'),
                        'message' => trans('documents.bulk.download_file_size_over.message')
                    ];

                } else {
                    
                    $status = [
                        'status' => 'ok',
                        'title' => trans('documents.bulk.download_file_size_big.title'),
                        'message' => trans('documents.bulk.download_file_size_big.message')
                    ];
                } 
                return new JsonResponse($status, 200);
              

        } catch (\Exception $kex) {
            \Log::error('Bulk Download Validation to error', ['error' => $kex, 'request' => $request->all()]);

            $status = ['status' => 'error', 'message' =>  trans('documents.bulk.download_error', ['error' => $kex->getMessage()])];

            if ($request->wantsJson()) {
                return new JsonResponse($status, 500);
            }

            return response('error');
        }
    }
    //
    public function buildzip(AuthGuard $auth, Request $request){

        try {
            \Log::info('Bulk  Download', ['params' => $request->all()]);
            
            //$user = $auth->user();
            $docs = $request->input('documents', []);
            $status = [];

            $files_to_zip = [];

                $docs = $request->input('documents', []);

                $documents = DocumentDescriptor::whereIn('id', $docs)->with('file')->get();
                foreach ($documents as $key => $value) {
                    $files_to_zip=array_merge($files_to_zip,[$value->file->absolute_path => $value->file->name]);
                }   
                
                return Zip::create('download.zip',$files_to_zip)->response();  

        } catch (\Exception $kex) {
            \Log::error('Bulk Download to error', ['error' => $kex, 'request' => $request->all()]);

            $status = ['status' => 'error', 'message' =>  trans('documents.bulk.download_error', ['error' => $kex->getMessage()])];

            if ($request->wantsJson()) {
                return new JsonResponse($status, 500);
            }

            return response('error');
        }
    }
}
