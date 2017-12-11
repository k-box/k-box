<?php

namespace KBox\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\Contracts\KlinkAdapter;

class NetworkDocumentsController extends Controller
{
    private $adapter = null;

    public function __construct(KlinkAdapter $adapter)
    {
        // $this->middleware('auth', ['except' => ['showByKlinkId']]);

        // $this->middleware('capabilities', ['except' => ['showByKlinkId']]);

        $this->adapter = $adapter;
    }

    /**
     * Shows the details coming from the Network about the given Data
     */
    public function show(Request $request, $uuid)
    {
        try {
            $data = $this->adapter->getDocument($uuid, KlinkVisibilityType::KLINK_PUBLIC);
            
            if ($request->wantsJson()) {
                return response()->json($data);
            }
            
            return view('panels.network_data', [
                'item' => $data
            ]);
        } catch (\Exception $kex) {
            Log::error('NetworkDocumentsController error', ['error' => $kex, 'uuid' => $uuid]);
            
            return view('panels.error', ['message' => $kex->getMessage()]);
        }
    }
}
