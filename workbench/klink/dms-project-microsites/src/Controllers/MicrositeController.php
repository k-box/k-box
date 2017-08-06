<?php

namespace Klink\DmsMicrosites\Controllers;

use KlinkDMS\Project;
use Klink\DmsMicrosites\Microsite;
use Klink\DmsMicrosites\MicrositeContent;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Capability;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

use Klink\DmsMicrosites\Requests\MicrositeCreationRequest;
use Klink\DmsMicrosites\Requests\MicrositeUpdateRequest;

class MicrositeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * Set up authentication middleware and capabilities middleware
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);

        $this->middleware('capabilities', ['except' => ['show']]);
    }

    /**
     * returns ok, not used
     *
     * @internal
     * @return Response
     */
    public function index(AuthGuard $auth, Request $request)
    {
        return 'ok';
    }

    /**
     * Show the form for creating a new microsite.
     *
     * @return Response
     */
    public function create(AuthGuard $auth, Request $request)
    {
        $user = $auth->user();
        
        if (! $user->can_capability(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH)) {
            throw new ForbiddenException(trans('microsites.errors.forbidden'), 401);
        }
        
        $project_id = $request->input('project', false);
        
        
        if (! $project_id) {
            throw new HttpResponseException(redirect()->back()->withErrors(
                ['error' => trans('microsites.errors.create_no_project')]
              ));
        }
        
        $project = Project::findOrFail($project_id);
        
        if ($project->user_id !== $user->id) {
            throw new ForbiddenException(trans('microsites.errors.forbidden'), 401);
        }
        
        if (! is_null($project->microsite()->first())) {
            throw new HttpResponseException(redirect()->back()->withErrors(
                ['error' => trans('microsites.errors.create_already_exists', ['project' => $project->name ])]
              ));
        }
        
        if (is_null($user->institution_id)) {
            throw new HttpResponseException(redirect()->back()->withErrors(
                ['error' => trans('microsites.errors.user_not_affiliated_to_an_institution')]
              ));
        }
        
        
        return view('sites::create', [
            'pagetitle' => trans('microsites.pages.create', ['project' => $project->name]),
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created microsite.
     *
     * @param MicrositeCreationRequest $request the request
     * @return Response
     */
    public function store(AuthGuard $auth, MicrositeCreationRequest $request)
    {
        try {
            $user = $auth->user();
            
            $project = Project::findOrFail($request->input('project', false));
            
            if ($project->microsite()->count() > 0) {
                throw new \Exception(trans('microsites.errors.create_already_exists', ['project' => $project->name]), 422);
            }

            
            $site_request = array_merge([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'institution_id' => $user->institution_id,
            ], $request->except(['project', 'content', 'menu']));
            
            $site_content_request = $request->only(['content', 'menu']);
                        
            $pages = [];
            $menus = [];
            
            if (isset($site_content_request['content']) && ! empty($site_content_request['content'])) {
                foreach ($site_content_request['content'] as $language => $page) {
                    $page['language'] = $language;
                    $page['type'] = MicrositeContent::TYPE_PAGE;
                    $page['user_id'] = $user->id;
                    $pages[] = new MicrositeContent($page);
                }
            }
            
            if (isset($site_content_request['menu']) && ! empty($site_content_request['menu'])) {
                foreach ($site_content_request['menu'] as $language => $page) {
                    $page['language'] = $language;
                    $page['type'] = MicrositeContent::TYPE_MENU;
                    $page['user_id'] = $user->id;
                    $menus[] = new MicrositeContent($page);
                }
            }
            
            $site = \DB::transaction(function () use ($site_request, $pages, $menus) {
                $st = Microsite::create($site_request);
                
                if (! empty($pages)) {
                    foreach ($pages as $page) {
                        $st->contents()->save($page);
                    }
                }
                
                if (! empty($menus)) {
                    foreach ($menus as $menu) {
                        $st->contents()->save($menu);
                    }
                }
                
                return $st;
            });

            // if ($request->wantsJson())
            // {
            //     return response()->json($project);
            // }
            
            return redirect()->route('microsites.edit', ['id' => $site->id])->with([
                'flash_message' => trans('microsites.messages.created', [
                    'title' => $site->title,
                    'site_url' => route('projects.site', ['slug' => $site->slug]),
                    'slug' => $site->slug
                 ])
            ]);
        } catch (\Exception $ex) {
            \Log::error('Error creating microsite', ['params' => $request->all(), 'exception' => $ex]);

            // if ($request->wantsJson())
            // {
            //     return new JsonResponse(array('status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])), 500);
            // }
            
            return redirect()->back()->withInput()->withErrors(
                ['error' => trans('microsites.errors.create', ['error' => $ex->getMessage()])]
              );
        }
    }

    /**
     * Display a microsite by the slug.
     *
     * This method can be invoked by the route projects.site and from /microsites/{ID}
     *
     * @param  int|string  $id The id or the slug of the microsite
     * @param  string  $language The language to show the microsite. Two letters code.
     * @return Response
     */
    public function show(AuthGuard $auth, Request $request, $id, $language = null)
    {
        $is_slug = $id === $request->route('slug');
        
        
        
        $microsite = Microsite::with('contents', 'project');
        
        if ($is_slug) {
            $microsite = $microsite->where('slug', $id)->first();
        } else {
            $microsite = $microsite->findOrFail($id);
        }
        
        if (is_null($microsite)) {
            return '404 ERROR';
        }
        
        
        if (is_null($language)) {
            $language = $microsite->default_language;
        }
        
        $available_languages = $microsite->contents->where('type', MicrositeContent::TYPE_PAGE)->map(function ($el) {
            return $el->language;
        });
        $available_languages = array_unique($available_languages->all());
        
        $raw_content = $microsite->contents->where('type', MicrositeContent::TYPE_PAGE)->where('language', $language)->first();

        // @rss:https://url-of-a-feed
        // Extract every @rss: and pass to Google feedapi to retrieve the json
        // ^@rss:([https:\/\/].*)$
        

        $content = ! is_null($raw_content) ? app()->make('micrositeparser')->toHtml($raw_content) : '';
        
        \App::setLocale($language); // change respose locale based on $language
        
        $is_loggedin = $auth->check();
        
        return view('sites::site.site', [
            'title' => $microsite->title,
            'slug' => $microsite->slug,
            'logo' => $microsite->logo,
            'description' => $microsite->description,
            'available_languages' => $available_languages,
            'language' => $language,
            'content' => $content,
            'search_action' => $is_loggedin ? route('documents.groups.show', ['id' => $microsite->project->collection_id]) : route('search'),
            'isloggedin' => $is_loggedin,
            'project_collection_id' => $microsite->project->collection_id
        ]);
    }

    /**
     * Show the form for editing an existing microsite.
     *
     * @return Response
     */
    public function edit(AuthGuard $auth, $id)
    {
        $microsite = Microsite::findOrFail($id)->load('project', 'contents');
        
        
        $en_content = $microsite->contents->where('language', 'en')->first();
        $ru_content = $microsite->contents->where('language', 'ru')->first();
        
        $project = $microsite->project;
        
        return view('sites::edit', [
            'pagetitle' => trans('microsites.pages.edit', ['project' => $project->name]),
            'project' => $project,
            'microsite' => $microsite,
            'en_entity' => $en_content,
            'ru_entity' => $ru_content,
        ]);
    }

    /**
     * Update the specified microsite.
     *
     * @param  int  $id
     * @param  MicrositeUpdateRequest  $request
     * @return Response
     */
    public function update(AuthGuard $auth, MicrositeUpdateRequest $request, $id)
    {
        try {
            $user = $auth->user();
            
            $microsite = Microsite::findOrFail($id)->load('project', 'contents');
            
            $site_request = $request->except(['content', 'menu', '_method', '_token']);
            
            $site_content_request = $request->only(['content', 'menu']);
                        
            $pages = [];
            $menus = [];
            
            if (isset($site_content_request['content']) && ! empty($site_content_request['content'])) {
                foreach ($site_content_request['content'] as $language => $page) {
                    $page['language'] = $language;
                    $pages[] = $page;
                }
            }
            
            if (isset($site_content_request['menu']) && ! empty($site_content_request['menu'])) {
                foreach ($site_content_request['menu'] as $language => $page) {
                    $page['language'] = $language;
                    $menus[] = $page;
                }
            }
            
            $site = \DB::transaction(function () use ($microsite, $site_request, $pages, $menus) {
                $st = $microsite->update($site_request);

                if (! empty($pages)) {
                    foreach ($pages as $page) {
                        MicrositeContent::findOrFail($page['id'])->update(array_except($page, ['id']));
                    }
                }
                
                if (! empty($menus)) {
                    foreach ($menus as $menu) {
                        MicrositeContent::findOrFail($menu['id'])->update(array_except($menu, ['id']));
                    }
                }
                
                return $st;
            });
            
            return redirect()->back()->with([
                'flash_message' => trans('microsites.messages.updated', [
                    'title' => $microsite->title
                 ])
            ]);
        } catch (\Exception $ex) {

            // if ($request->wantsJson())
            // {
            // 	return new JsonResponse(array('error' => $fe->getMessage()), 500);
            // }

            \Log::error('Microsite update error', ['id' => $id, 'request' => $request->all(), 'error' => $ex]);

            return redirect()->back()->withInput()->withErrors(
                ['error' => trans('microsites.errors.update', ['error' => $ex->getMessage()])]
              );
        }
    }

    /**
     * Remove the microsite.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(AuthGuard $auth, Request $request, $id)
    {
        try {
            $user = $auth->user();
            
            if (! $user->can_capability(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH)) {
                throw new ForbiddenException(trans('microsites.errors.forbidden'), 401);
            }
            
            $site = Microsite::findOrFail($id);
            
            if ($site->user_id !== $user->id) {
                if ($request->wantsJson()) {
                    return new JsonResponse(['error' => trans('microsites.errors.delete_forbidden', [
                        'title' => $site->title
                    ])], 200);
                }

                return redirect()->back()->withErrors(
                    ['error' => trans('microsites.errors.delete_forbidden', [
                        'title' => $site->title
                    ])]
                );
            }
            
            $site->delete();
            
            if ($request->wantsJson()) {
                return new JsonResponse(['status' => 'ok', 'message' => trans('microsites.messages.deleted', [
                    'title' => $site->title
                 ])], 200);
            }
            
            
            return redirect()->back()->with([
                'flash_message' => trans('microsites.messages.deleted', [
                    'title' => $site->title
                 ])
            ]);
        } catch (\Exception $ex) {
            if ($request->wantsJson()) {
                return new JsonResponse(['error' => trans('microsites.errors.delete', ['error' => $ex->getMessage()])], 500);
            }
            
            return redirect()->back()->withErrors(
                ['error' => trans('microsites.errors.delete', ['error' => $ex->getMessage()])]
              );
        }
    }
}
