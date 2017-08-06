<?php

namespace KlinkDMS\Http\Controllers;

use Illuminate\Http\JsonResponse;

use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Group;
use KlinkDMS\Shared;
use KlinkDMS\PublicLink;

use DB;
use KlinkDMS\Http\Requests\CreatePublicLinkRequest;
use KlinkDMS\Http\Requests\UpdatePublicLinkRequest;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KlinkDMS\Exceptions\ForbiddenException;
use KlinkDMS\Events\ShareCreated;

class PublicLinksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // ???
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AuthGuard $auth, CreatePublicLinkRequest $request)
    {
        // create a PublicLink for the user
        // create a shared
        // attach the PublicLink to the sharedwith morph field
        // return the link

        $user = $auth->user();

        $to_id = $request->input('to_id');
        $to_type = $request->input('to_type');
        
        $slug = $request->input('slug', null);
        $expiration = $request->input('expiration', null);

        $target = $to_type === 'document' ? DocumentDescriptor::findOrFail($to_id) : Group::findOrFail($to_id);

        // check if user already has a link for that resource

        $already_shared = Shared::notExpired()
              ->sharedByMe($user)
              ->where('sharedwith_id', $target->id)
              ->where('sharedwith_type', get_class($target))
              ->where('sharedwith_type', PublicLink::class)
              ->exists();

        if ($already_shared) {
            throw new ForbiddenException(
                trans('share.publiclinks.already_exist', [
                    'name' => ($to_type === 'document' ? $target->title : $target->name)
                ]));
        }

        $token = $user->id.$target->id.get_class($target).time().PublicLink::class;

        
        $res = DB::transaction(function () use ($target, $user, $slug, $expiration, $token) {
            $link_params = [
                'user_id' => $user->id,
                'slug' => $slug,
            ];
            
            $link = PublicLink::create($link_params);

            $share_params = [
                'user_id' => $user->id,
                'sharedwith_id' => $link->id, //the id
                'sharedwith_type' => get_class($link), //the class
                'token' => hash('sha256', $token),
                'expiration' => $expiration
            ];

            $share = $target->shares()->create($share_params);

            event(new ShareCreated($share));

            return $link->load('share');
        });

        return new JsonResponse($res, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AuthGuard $auth, UpdatePublicLinkRequest $request, $id)
    {
        $user = $auth->user();

        $link = PublicLink::findOrFail($id);
        
        if ($user->id !== $link->user_id) {
            throw new ForbiddenException(
                trans('share.publiclinks.edit_forbidden_not_your'));
        }

        $slug = $request->input('slug', null);
        $expiration = $request->input('expiration', null);

        $res = DB::transaction(function () use ($link, $slug, $expiration) {
            if (! is_null($expiration)) {
                $share = $link->share;
                $share->expiration = $expiration;
                $share->save();
            }

            if (! is_null($slug)) {
                $link->slug = $slug;
                $link->save();
            }

            return $link;
        });

        return new JsonResponse($link, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AuthGuard $auth, $id)
    {
        $user = $auth->user();

        $link = PublicLink::findOrFail($id);
        
        if ($user->id !== $link->user_id) {
            throw new ForbiddenException(
                trans('share.publiclinks.delete_forbidden_not_your'));
        }

        $res = DB::transaction(function () use ($link) {
            // destroy both the link and the associated share
            $link->share->delete();
            $link->delete();

            return true;
        });

        return new JsonResponse(['status' => 'ok'], 200);
    }
}
