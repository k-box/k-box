<?php

namespace KBox\Http\Controllers\Document;

use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;

class DocumentDownloadController extends DocumentAccessController
{
    public function show(Request $request, $uuid, $versionUuid = null)
    {
        try{
            
            list($document, $file) = $this->getDocument($request, $uuid, $versionUuid);

            return $this->downloadDocument($request, $document, $file);

        }
        catch(AuthenticationException $ex){
            Log::warning('KlinkApiController, requested a document that is not public and user is not authenticated', ['url' => $request->url()]);

            session()->put('url.dms.intended', $request->url());

            return redirect()->to(route('frontpage'));
        }
        catch(ForbiddenException $ex){
            return view('errors.403', ['reason' => 'ForbiddenException: ' . $ex->getMessage()]);
        }
        catch(ModelNotFoundException $ex){
            return view('errors.404', ['reason' => trans('errors.document_not_found')]);
        }
    }
}
