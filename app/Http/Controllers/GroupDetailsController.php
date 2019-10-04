<?php

namespace KBox\Http\Controllers;

use KBox\Group;
use Illuminate\Http\Request;
use KBox\Documents\Services\DocumentsService;
use KBox\Exceptions\ForbiddenException;

class GroupDetailsController extends Controller
{

    /**
     * [$adapter description]
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $service = null;

    public function __construct(DocumentsService $service)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->service = $service;
    }

    /**
     * Display the specified resource.
     *
     * @param  \KBox\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Group $group)
    {
        $user = $request->user();
        
        if (! $this->service->isCollectionAccessible($user, $group)) {
            throw new ForbiddenException(trans('errors.401_title'), 401);
        }

        $share_with = $group->shares()->sharedWithMe($user)->orderBy('created_at', 'ASC')->first();
        $share = $group->shares()->by($user)->orderBy('created_at', 'ASC')->exists();

        return view('groups.detail', [
            'group' => $group,
            'share' => $share_with,
            'has_share' => $share || ! is_null($share_with),
            'is_personal' => $group->is_private,
            'is_project' => ! $group->is_private,
        ]);
    }
}
