@if(isset($is_in_collection) && $is_in_collection && isset($collections))


    @foreach($collections as $group)

        <div class="badge" @if($group->color) data-color="{{$group->color}}" @endif>
            <a href="{{route( $use_groups_page ? 'documents.groups.show' : 'shares.group' , $group->id)}}" class="badge-link hint--bottom" data-hint="{{ trans('panels.collection_open', ['collection' => $group->name ])}}">
                {{$group->name}}
            </a>
            @if(isset($document_is_trashed) && !$document_is_trashed && (($group->is_private && $user_can_edit_private_groups) || (!$group->is_private && $user_can_edit_public_groups)) && isset($document_id) && $document_id)
                <a href="#remove" data-action="removeGroup" data-group-id="{{$group->id}}" data-document-id="{{$document_id}}" class="badge-remove hint--bottom" data-hint="{{ trans('panels.collection_remove', ['collection' => $group->name ])}}">
                    X
                </a>
            @endif
        </div>

    @endforeach

@else

    <p>{{trans('panels.not_in_collection')}}</p>

@endif