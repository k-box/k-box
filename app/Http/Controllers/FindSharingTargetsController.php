<?php

namespace KBox\Http\Controllers;

use KBox\User;
use KBox\Group;
use KBox\Capability;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use KBox\Http\Resources\ShareTargetCollection;
use KBox\Shared;

class FindSharingTargetsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List the users that can be select as target of a share.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('create', Shared::class);
        
        $me = $request->user();

        $validatedData = $request->validate([
            'collections' => 'nullable|sometimes|array|exists:groups,id',
            'documents' => 'nullable|sometimes|array|exists:document_descriptors,id',
            's' => 'required|string|min:2',
            'e' => 'sometimes|required|array|exists:users,id',
        ]);

        $search_query = $validatedData['s'];

        $groups_input = $validatedData['collections'] ?? [];
        $documents_input = $validatedData['documents'] ?? [];

        $documents = DocumentDescriptor::whereIn('id', $documents_input)->get();

        $groups = Group::whereIn('id', $groups_input)->get();

        $all_in = $documents->merge($groups);
        
        $first = $all_in->first();

        $is_multiple_selection = ! empty($documents_input) && ! empty($groups_input);

        if ($groups->where('type', Group::TYPE_PROJECT)->isNotEmpty()) {
            // if there is at least a project collection in the selection
            // return an empty list. See
            // https://github.com/k-box/k-box/pull/355#issuecomment-551448888
            // and https://github.com/k-box/k-box/issues/356
            return new ShareTargetCollection(collect([]));
        }

        // users to exclude from the available for share
        // by default the current user is excluded as no one can share
        // with himself/herself
        // additionally all ids passed as part of the `e` parameter are excluded
        $users_to_exclude = collect([$me->id])->merge($validatedData['e'] ?? []);
        
        if ($first && ! $is_multiple_selection) {
            $users_to_exclude = $users_to_exclude->merge($this->getExcludeListFor($first, $me));
        }

        $available_users = User::whereNotIn('id', $users_to_exclude->toArray())
            ->whereHas('capabilities', function ($q) {
                $q->where('key', '=', Capability::RECEIVE_AND_SEE_SHARE);
            })
            ->where(function ($query) use ($search_query) {
                $query->where('email', e($search_query))
                ->orWhere('name', 'like', '%'.$this->normalizeTerms($search_query).'%');
            })
            ->orderBy('id', 'ASC') // since the ids are autoincrement it is equivalent to order for creation date
            ->take(6);

        return new ShareTargetCollection($available_users->get());
    }

    /**
     * Get the list of users to exclude from the targets
     * for the specified resource
     *
     * @param \KBox\DocumentDescriptor|\KBox\Group $resource
     * @param \KBox\User $user
     * @return \Illuminate\Support\Collection
     */
    private function getExcludeListFor($resource, User $user)
    {
        $existing_shares = $resource->shares()->sharedByMe($user)->where('sharedwith_type', User::class)->get();

        return $existing_shares->pluck('sharedwith_id')->unique();
    }

    /**
     * Normalize the search terms to be used in the query
     *
     * it replaces % characters
     *
     * @param string $terms
     * @return string
     */
    private function normalizeTerms($terms)
    {
        $decoded = urldecode($terms);

        // in case of terms containing apostrophes or double quotes
        // we replace them with ? to match any character.
        // We replace also % and ? as the user should not
        // make use of them
        $cleaned = str_replace('%', '', $decoded);
        $cleaned = str_replace('?', '', $cleaned);
        $cleaned = str_replace('_', '', $cleaned);
        $cleaned = str_replace('\'', '_', $cleaned);
        $cleaned = str_replace('"', '_', $cleaned);

        return str_replace(' ', '%', e($cleaned));
    }
}
