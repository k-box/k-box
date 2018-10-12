<?php

namespace KBox\Http\Controllers;

use KBox\DocumentDescriptor;
use Illuminate\Http\Request;

class OembedController extends Controller
{
    /**
     * Return the oEmbed (https://oembed.com/) response for the specified request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $format = $request->input('format', 'json');
        $url = $request->input('url', '');
        $maxwidth = $request->input('maxwidth', 960);
        $maxheight = $request->input('maxheight', 540);

        // if format is not json, abort
        abort_if($format !== 'json', 501);

        // Get the document id from the given URL
        $base_url = route('documents.preview', '').'/';

        // if the url don't start with this application URL, or
        // contains query parameters => abort
        abort_unless(starts_with($url, $base_url), 404);
        abort_if(str_contains($url, '?') || str_contains($url, '#'), 404);
        
        $id = e(str_replace($base_url, '', $url));

        $document = DocumentDescriptor::whereUuid($id)->first();

        // if document not found simply return
        abort_if(is_null($document), 404);

        $width = e(min([480, $maxwidth]));
        $height = e(min([360, $maxheight]));

        // the URL that will be used in the resulting iframe to show the embed
        $embed_url = route('documents.embed', $document->uuid);

        $data = [
            "version" => "1.0",
            "type" => "rich",
            "provider_name" => config('app.name'),
            "provider_url" => config('app.url'),
            "width" => $width,
            "height" => $height,
            "title" => e($document->title), // optional
            "html" => "<iframe width=\"$width\" height=\"$height\" src=\"$embed_url\" class=\"kbox_embed_iframe\" frameborder=\"0\" allowfullscreen></iframe>",
        ];

        return response()->json($data, 200);
    }
}
