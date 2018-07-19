<?php

namespace KBox\Http\Composers;

use Exception;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\File;
use KBox\Group;
use KBox\Project;

use Illuminate\Contracts\View\View;

use Illuminate\Support\Collection;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\KlinkFacets;
use Klink\DmsAdapter\KlinkDocumentUtils;
use OneOffTech\Licenses\Contracts\LicenseRepository;

class DocumentsComposer
{

    /**
     * @var \Klink\DmsDocuments\DocumentsService
     */
    private $documents = null;
    
    private $licenses = null;
    
    

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(\Klink\DmsDocuments\DocumentsService $documentsService, LicenseRepository $licenses)
    {
        $this->documents = $documentsService;
        $this->licenses = $licenses;
    }

    public function layout(View $view)
    {
        if (\Auth::check()) {
            $auth_user = \Auth::user();

            $view->with('can_import', $auth_user->can_capability(Capability::IMPORT_DOCUMENTS));

            $view->with('can_upload', $auth_user->can_capability(Capability::UPLOAD_DOCUMENTS));
            
            $view->with('can_create_collection', $auth_user->can_capability(Capability::MANAGE_OWN_GROUPS) || $auth_user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS));
            
            $view->with('can_make_public', $auth_user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY));
            
            $view->with('can_clean_trash', $auth_user->can_capability(Capability::CLEAN_TRASH));
            
            $view->with('can_share', $auth_user->can_capability([Capability::SHARE_WITH_PERSONAL, Capability::SHARE_WITH_PRIVATE]));

            $view->with('can_manage_documents', $auth_user->isContentManager());
            
            $view->with('can_delete_documents', $auth_user->can_capability(Capability::DELETE_DOCUMENT));

            $view->with('can_partner', $auth_user->isPartner());
            
            $view->with('can_see_private', $auth_user->isDMSManager());

            $view->with('list_style_current', $auth_user->optionListStyle());
        } else {
            $view->with('list_style_current', 'tiles');
        }
        
        $view->with('is_klink_public_enabled', network_enabled());
    }
    
    public function menu(View $view)
    {
        $view->with('is_klink_public_enabled', network_enabled());
    }

    /**
     * Document Descriptor page with actual DocumentDescriptor instance
     *
     * @param  View  $view
     * @return void
     */
    public function descriptor(View $view)
    {
        $docOrItem = isset($view['item']) ? $view['item'] : (isset($view['document']) ? $view['document'] : null);

        if (is_array($docOrItem) && isset($docOrItem['descriptor'])) {
            $docOrItem = $docOrItem['descriptor'];
            if (isset($view['item'])) {
                $view->with('item', $docOrItem);
            } elseif (isset($view['document'])) {
                $view->with('document', $docOrItem);
            }
        }

        if (\Auth::check() && ! is_null($docOrItem)) {
            if (class_basename(get_class($docOrItem)) === 'DocumentDescriptor') {
                $document = $docOrItem;

                $auth_user = \Auth::user();

                $view->with('badge_private', $document->isPrivate());
                $view->with('badge_public', $document->isPublic());

                $view->with('is_starrable', true);

                if ($document->isStarred($auth_user->id)) {
                    $view->with('is_starred', true);

                    $star = $document->getStar($auth_user->id);

                    $view->with('star_id', $star->id);
                } else {
                    $view->with('is_starred', false);
                }

                $view->with('badge_shared', $document->isShared());

                if ($auth_user->can_capability(Capability::EDIT_DOCUMENT)) {
                    $view->with('badge_error', $document->status === DocumentDescriptor::STATUS_ERROR);
                } else {
                    $view->with('badge_error', false);
                }
                
                $view->with('badge_duplicate', $document->duplicates()->of($auth_user)->exists());
            }
        }
    }

    public function descriptorPanel(View $view)
    {
        $docOrItem = isset($view['item']) ? $view['item'] : (isset($view['document']) ? $view['document'] : null);

        $this->descriptor($view);
        
        $auth_check = \Auth::check();
        
        
        $view->with('is_user_logged', $auth_check);
        
        if (! is_null($docOrItem) && is_array($docOrItem) && isset($docOrItem['descriptor'])) {
            $docOrItem = $docOrItem['descriptor'];
            if (isset($view['item'])) {
                $view->with('item', $docOrItem);
            } elseif (isset($view['document'])) {
                $view->with('document', $docOrItem);
            }
        }

        if ($auth_check && ! is_null($docOrItem)) {
            if (class_basename(get_class($docOrItem)) === 'DocumentDescriptor') {
                $document = $docOrItem;

                $auth_user = \Auth::user();

                $view->with('stars_count', $document->stars()->count());

                $collections = $this->documents->getDocumentCollections($document, $auth_user);
                
                $view->with('is_in_collection', ! $collections->isEmpty());

                $view->with('groups', $collections);

                $view->with('user_can_edit_private_groups', $auth_user->can_capability(Capability::MANAGE_OWN_GROUPS));
                $view->with('user_can_edit_public_groups', $auth_user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS));

                $view->with('can_share', $auth_user->can_capability([Capability::SHARE_WITH_PERSONAL, Capability::SHARE_WITH_PRIVATE]));

                
                // if($document->isMine()){
                // // the document is shared by me

                //     $existing_shares = $document->shares()->sharedByMe($auth_user)->where('sharedwith_type', 'KBox\User')->count();
                //     $users_from_projects = $this->documents->getUsersWithAccess($document, $auth_user)->count();
    
                //     $view->with('access_by_count', $existing_shares+$users_from_projects);
                
                // }
                
                if ($auth_user->can_capability(Capability::EDIT_DOCUMENT)) {
                    $view->with('user_can_edit', true);
                } else {
                    $view->with('user_can_edit', false);
                }
                
                
                $view->with('use_groups_page', $auth_user->can_capability(Capability::MANAGE_OWN_GROUPS));

                if ($auth_user->can_capability(Capability::UPLOAD_DOCUMENTS) && ! is_null($document->file)) {
                    $view->with('show_versions', true);

                    $view->with('has_versions', ! is_null($document->file->revision_of));
                } else {
                    $view->with('show_versions', false);
                }

                $view->with('badge_duplicate', $document->duplicates()->of($auth_user)->exists());
            }
        }
    }

    public static function _flatten_revisions(File $file, &$revisions = [])
    {
        $revisions[] = $file;

        if (is_null($file->revision_of)) {
            return $revisions;
        } else {
            return self::_flatten_revisions($file->revisionOf()->first(), $revisions);
        }
    }

    public function versionInfo(View $view)
    {
        $docOrItem = isset($view['item']) ? $view['item'] : isset($view['document']) ? $view['document'] : null;

        if (is_array($docOrItem) && isset($docOrItem['descriptor'])) {
            $docOrItem = $docOrItem['descriptor'];
            if (isset($view['item'])) {
                $view->with('item', $docOrItem);
            } elseif (isset($view['document'])) {
                $view->with('document', $docOrItem);
            }
        }

        if (\Auth::check() && ! is_null($docOrItem)) {
            if (class_basename(get_class($docOrItem)) === 'DocumentDescriptor') {
                $document = $docOrItem;

                $auth_user = \Auth::user();

                if ($auth_user->can_capability(Capability::UPLOAD_DOCUMENTS) && ! is_null($document->file)) {
                    
                    // $view->with('show_versions', true);

                    $view->with('has_versions', ! is_null($document->file->revision_of));

                    $alls = self::_flatten_revisions($document->file);

                    $view->with('versions_count', count($alls));
                    $view->with('versions', $alls);
                } else {
                    $view->with('versions_count', 0);
                    $view->with('versions', []);
                }
            }
        }
    }
    
    
    public function preview(View $view)
    {
        $view->with('is_user_logged', \Auth::check());
    }

    public function facets(View $view)
    {
        $auth_user = \Auth::user();
        
        
        $group_instance = isset($view['context_group_instance']) ? $view['context_group_instance'] : null;

        $group_instance_descendants = [];

        if (! is_null($group_instance)) {
            $group_instance_descendants = $group_instance->getDescendants()->map(function ($grp) {
                return $grp->id;
            })->all();
            array_push($group_instance_descendants, $group_instance->id);
        }

        $context = isset($view['context']) ? $view['context'] : false;
        $facets = isset($view['facets']) ? $view['facets'] : null;
        $filters = isset($view['filters']) ? $view['filters'] : null;
        $current_visibility = isset($view['current_visibility']) ? $view['current_visibility'] : 'private';
        $are_filters_empty = empty($filters);

        $show_personal_collections_in_filters = ! is_null($auth_user) ? $auth_user->optionPersonalInProjectFilters() : false;
        $is_projectspage = $context && $context==='projectspage';
        
        if ($current_visibility=='private') {
            $cols = [
                KlinkFacets::LANGUAGE => ['label' => trans('search.facets.language')],
                KlinkFacets::MIME_TYPE => ['label' => trans('search.facets.documentType')],
                KlinkFacets::TAGS => ['label' => trans('search.facets.projects')],
                KlinkFacets::COLLECTIONS => ['label' => trans('search.facets.collections')],
            ];
        } else {
            $cols = [
                KlinkFacets::COPYRIGHT_USAGE_SHORT => ['label' => trans('search.facets.copyright_usage')],
                // KlinkFacets::UPLOADER => ['label' => trans('search.facets.institutionId')],
                KlinkFacets::LANGUAGE => ['label' => trans('search.facets.language')],
                KlinkFacets::MIME_TYPE => ['label' => trans('search.facets.documentType')],
            ];
        }
        
        if (! is_null($facets)) {

        //   array:1 [▼
        //     "properties.language" => array:1 [▼
        //       0 => AggregationResult {#733 ▼
        //         +value: "en"
        //         +count: 1
        //       }
        //     ]
        //   ]

            $group_facets = array_key_exists(KlinkFacets::COLLECTIONS, $facets) ?  $facets[KlinkFacets::COLLECTIONS] : [];

            if (! empty($group_facets)) {
                $private = [];
                
                foreach ($group_facets as $group_facet) {
                    try {
                        if ($group_facet->count > 0) {
                            $grp_id = $group_facet->value;
                            
                            $grp = Group::findOrFail($grp_id);

                            // boxing the collections to descendant of the collection
                            // currently browsed by the user (if any)
                        
if ($is_projectspage && (! $grp->is_private && ! $show_personal_collections_in_filters) || ! $is_projectspage) {
    if ((is_null($group_instance) &&
                                    $this->documents->isCollectionAccessible($auth_user, $grp)) ||
                                (! is_null($group_instance) &&
                                    in_array($grp_id, $group_instance_descendants))) {
                                
                                // considering only really accessible collections
                                
                                $group_facet->label = $grp->name;
        $group_facet->selected = false;
                                

        if ($grp->countAncestors() > 0) {
            $group_facet->parents = $grp->getAncestors()->sortByDesc('depth')->implode('name', ' > ');
        }
                                
        $group_facet->collapsed = $group_facet->count == 0;
        $group_facet->institution = ! $grp->is_private;
        $group_facet->is_project = ! $grp->is_private;
        $private[] = $group_facet;
    }
}
                        }
                    } catch (\Exception $exc) {
                    }
                }

                $cols[KlinkFacets::COLLECTIONS] = [
                    'label' => trans('search.facets.documentGroups'),
                    'items' => $private
                ];
            }
            
            foreach ($facets as $name => $f) {
                if (array_key_exists($name, $cols)) {
                    $cols[$name]['items'] = array_filter(array_map(function ($f_items) use ($f, $filters, $are_filters_empty, $auth_user, $name) {
                        if ($name == KlinkFacets::LANGUAGE) {
                            $f_items->label =  trans('languages.'.$f_items->value);
                        } elseif ($name == KlinkFacets::MIME_TYPE) {
                            try {
                                $f_items->label =  trans_choice('documents.type.'.KlinkDocumentUtils::documentTypeFromMimeType($f_items->value), 1).' ('.KlinkDocumentUtils::getExtensionFromMimeType($f_items->value).')';
                            } catch (Exception $ex) {
                                $f_items->label =  trans_choice('documents.type.document', 1).' ('.$f_items->value.')';
                            }
                        } elseif ($name == KlinkFacets::TAGS) {
                            $prj = Project::find($f_items->value);

                            if (! is_null($prj) && Project::isAccessibleBy($prj, $auth_user)) {
                                $f_items->label = $prj->name;
                            }
                        } elseif ($name == KlinkFacets::COPYRIGHT_USAGE_SHORT) {
                            $license = $this->licenses->find($f_items->value);
                            
                            $f_items->label = $license ? $license->title : $f_items->value;
                        } else {
                            $lang_group = $f_items->value;
                        }
                        
                        if (! $are_filters_empty) {
                            if (array_key_exists($name, $filters) && in_array($f_items->value, $filters[$name])) {
                                $f_items->selected = true;
                                $f_items->collapsed = $f_items->count == 0;
                            } elseif (array_key_exists($name, $filters)) {
                                $f_items->selected = false;
                                $f_items->collapsed = $f_items->count == 0;
                            } else {
                                $f_items->selected = false;
                                $f_items->collapsed = $f_items->count == 0;
                            }
                        } else {
                            $f_items->selected = false;
                            $f_items->collapsed = $f_items->count == 0;
                        }

                        if ($name===KlinkFacets::COLLECTIONS && ! property_exists($f_items, 'label')) {
                            return false;
                        }
                        
                        if ($name===KlinkFacets::TAGS && ! property_exists($f_items, 'label')) {
                            return false;
                        }

                        return $f_items;
                    }, $f));
                }
            }
        }

        $view->with('columns', $cols);
        
        $view->with('width', 100/count($cols));
        
        
        $current_visibility = isset($view['current_visibility']) ? $view['current_visibility'] : 'private';
        
        $search_terms =  isset($view['search_terms']) && ! empty($view['search_terms']) ? $view['search_terms'] : '*';
        
        //include active facets/filters
        
        $url_components = [];

        if ($search_terms !=='*') {
            $url_components[] = 's='.$search_terms;
        }

        if ($current_visibility !== KlinkVisibilityType::KLINK_PRIVATE) {
            $url_components[] = 'visibility='.$current_visibility;
        }

        $b_url = (! empty($url_components) ? '?' : '').implode('&', $url_components);
        
        $view->with('facet_filters_url', $b_url);
        
        $view->with('current_active_filters', $filters);

        $view->with('clear_filter_url', \URL::current().$b_url);
    }
    
    public function groupFacets(View $view)
    {
        $auth_user = \Auth::user();
        
        $facets = isset($view['facets']) ? $view['facets'] : null;
        
        
        if (! is_null($facets)) {
            $group_facets = array_values(array_filter($facets, function ($f) {
                return $f->name === 'documentGroups';
            }));
            
            $private = [];
            $personal = [];
            
            $items = $group_facets[0]->items;
            
            foreach ($items as $group_facet) {
                if ($group_facet->count > 0) {
                    if (starts_with($group_facet->term, '0:')) {
                        // private
                        $private[] = \KBox\Group::findOrFail(str_replace('0:', '', $group_facet->term));
                    } elseif (starts_with($group_facet->term, $auth_user->id.':')) {
                        //personal
                        $personal[] = $group_facet;
                    }
                }
            }
            
            $view->with('facets_groups_personal', $personal);
        
            $view->with('facets_groups_private', $private);
        }
    }
    
    
    public function shared(View $view)
    {
        if (\Auth::check()) {
            $auth_user = \Auth::user();

            $view->with('can_share_with_personal', $auth_user->can_capability(Capability::SHARE_WITH_PERSONAL));

            $view->with('can_share_with_private', $auth_user->can_capability(Capability::SHARE_WITH_PRIVATE));
            
            $view->with('can_see_share', $auth_user->can_capability(Capability::RECEIVE_AND_SEE_SHARE));

            $view->with('can_upload', $auth_user->can_capability(Capability::UPLOAD_DOCUMENTS));
            

            $view->with('list_style_current', $auth_user->optionListStyle());
        } else {
            $view->with('list_style_current', 'tiles');
        }
    }
}
