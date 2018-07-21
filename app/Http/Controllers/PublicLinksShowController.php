<?php

namespace KBox\Http\Controllers;

use KBox\PublicLink;
use KBox\Shared;
use KBox\DocumentDescriptor;
use KBox\RoutingHelpers;

class PublicLinksShowController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function show($link)
    {
        // find the share
        // find the document given the share
        // build the KlinkApiController route

        $public_link = null;

        if (is_integer($link)) {
            $public_link = PublicLink::findOrFail($link);
        } else {
            $public_link = PublicLink::where('slug', $link)->first();

            if (is_null($public_link)) {
                $by_token = Shared::token($link)->first();
                $public_link = ! is_null($by_token) ? $by_token->sharedwith : null;
            }
        }
        if (! is_null($public_link) && ! is_null($public_link->share) && ! $public_link->isExpired()) {
            $share = $public_link->share->shareable;

            if ($share instanceof DocumentDescriptor) {
                $url = RoutingHelpers::preview($share);

                return redirect($url);
            }

            \abort(404, 'Collections are not yet supported.');
        }

        \abort(404, trans('errors.document_not_found'));
    }
}
