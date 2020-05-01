@if(isset($is_in_collection) && $is_in_collection && isset($collections))


    @foreach($collections as $group)

        <div class="badge" data-group-id="{{$group->id}}" data-document-id="{{$document_id}}" @if($group->color) style="background-color:#{{$group->color}}" @endif>
            <a href="{{route( $use_groups_page ? 'documents.groups.show' : 'shares.group' , [ 'id' => $group->id, 'highlight' => $document_id])}}" class="badge-link" title="{{ trans('panels.collection_open', ['collection' => $group->getAncestors()->sortByDesc('depth')->push($group)->implode('name', ' > ')])}}"> 
                {{$group->name}}
            </a>
            @if(isset($document_is_trashed) && !$document_is_trashed && (($group->is_private && $user_can_edit_private_groups) || (!$group->is_private && $user_can_edit_public_groups)) && isset($document_id) && $document_id)
                <a href="#remove" data-action="removeGroup" data-group-id="{{$group->id}}" data-document-id="{{$document_id}}" class="badge-remove" title="{{ trans('panels.collection_remove', ['collection' => $group->name ])}}">
                    X
                </a>
            @endif
        </div>

    @endforeach

@elseif(!isset($hide_empty_message) || (isset($hide_empty_message) && !$hide_empty_message))

    <p>{{trans('panels.not_in_collection')}}</p>

@endif