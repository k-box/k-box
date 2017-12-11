<?php

namespace KBox\Http\Controllers;

use KBox\Group;
use Klink\DmsDocuments\DocumentsService;

class UploadPageController extends Controller
{
    private $service;

    public function __construct(DocumentsService $service)
    {
        $this->middleware('auth');

        // $this->middleware('capabilities');

        $this->service = $service;
    }

    /**
     * Display the tus internal upload page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = request()->user();
        $request_target = request()->input('c', null);

        $target_data = [
            'target' => trans('upload.target.personal', ['link' => route('documents.index')]),
            'target_collection' => null,
        ];

        if ($request_target) {
            $collection = Group::find($request_target);

            if ($collection !== null && $this->service->isCollectionAccessible($user, $collection)) {
                $target_data['target_collection'] = $collection->id;
                if ($collection->is_private) {
                    $target_data['target'] = trans('upload.target.collection', ['name' =>  $collection->name, 'link' => \DmsRouting::group($collection->id)]);
                } elseif ($collection->project !== null) {
                    $target_data['target'] = trans('upload.target.project', ['name' =>  $collection->project->name, 'link' => \DmsRouting::group($collection->id)]);
                } else {
                    $root_collection = $collection->getAncestorsWhere('parent_id', '=', null)->first();
                    $project = $root_collection ? $root_collection->project : null;

                    if ($project !== null) {
                        $target_data['target'] = trans('upload.target.project_collection', [
                            'name' =>  $collection->name, 'link' => \DmsRouting::group($collection->id),
                            'project_name' =>  $project->name, 'project_link' => \DmsRouting::group($root_collection->id)
                        ]);
                    }
                }
            } else {
                $target_data['target_error'] = trans('upload.target.error');
            }
        }

        return view('upload.index', array_merge([
                'pagetitle' => trans('actions.upload_video'),
                'context' => 'uploads',
            ], $target_data));
    }
}
