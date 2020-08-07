<?php

namespace KBox\Http\Controllers;

use KBox\DocumentDescriptor;
use KBox\Documents\Services\DocumentsService;
use KBox\Group;
use KBox\Http\Requests\ShareDialogRequest;

class ListUsersWithAccess extends Controller
{
    
    /**
     * [$adapter description]
     * @var KBox\Documents\Services\DocumentsService
     */
    private $service = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DocumentsService $adapterService)
    {
        $this->middleware('auth');

        $this->service = $adapterService;
    }

    public function index(ShareDialogRequest $request)
    {
        $me = $request->user();

        $groups_input = $request->input('collections', []);
        $documents_input = $request->input('documents', []);

        $groups_req = is_array($groups_input) ? $groups_input : array_filter(explode(',', $request->input('collections', '')));

        $documents_req = is_array($documents_input) ? $documents_input : array_filter(explode(',', $request->input('documents', '')));

        $documents = DocumentDescriptor::withTrashed()->whereIn('id', $documents_req)->get();

        $groups = Group::whereIn('id', $groups_req)->get();

        $all_in = $documents->merge($groups);
        
        $first = $all_in->first();

        $elements_count = $all_in->count();
        $is_multiple_selection = $elements_count > 1;

        // details for public/private
        $existing_shares = null;

        if (! is_null($first) && $first instanceof DocumentDescriptor && ! $is_multiple_selection) {

            // grab the existing share made by the user
            // let's do it for $first only first

            $existing_shares = $first->shares()->sharedByMe($me)->where('sharedwith_type', \KBox\User::class)->get();

            // is the document in a project? the current user has access to the project? if yes we can also remove the members of that project(s)
            $users_from_projects = $this->service->getUsersWithAccess($first, $me);

            $existing_shares = $existing_shares->merge($users_from_projects);
        } elseif (! is_null($first) && $first instanceof Group && ! $is_multiple_selection) {
            // grab the existing share made by the user
            // let's do it for $first only first

            $existing_shares = $first->shares()->sharedByMe($me)->where('sharedwith_type', \KBox\User::class)->get();
        }

        return view('share.partials.access-list', [
            'existing_shares' => $existing_shares,
        ]);
    }
}
