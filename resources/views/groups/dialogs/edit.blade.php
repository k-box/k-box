@if(($user_can_edit_public_groups || $user_can_edit_private_groups))

<form method="POST" 
    x-data="AsyncForm({text: '{{ $group->name }}' })" 
    x-on:submit.prevent="submit" 
    @form-submitted="$dispatch('dialog-close', {});DMS.navigateReload();"
    class="" 
    action="{{route('documents.groups.update', $group->id)}}">

	{{ csrf_field() }}

    {{ method_field('PUT') }}

    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 sm:mx-0 sm:h-10 sm:w-10">
                @materialicon('action', 'label', 'h-6 w-6 text-gray-600 fill-current')
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                    {{ trans('groups.panel_edit_title') }}
                    <span class="" x-text="text">{{ $group->name }}</span>
                </h3>
                <div class="mt-2">
                    <p class="text-sm leading-5 text-gray-500">
                        {{trans('groups.form.collection_name_placeholder')}}
                    </p>
                </div>
                
                @include('groups.groupform')
            </div>
        </div>
    </div>
    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
            <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-blue-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                {{ trans('groups.save_btn') }}
            </button>
        </span>
        <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
            <button type="button" @click="$dispatch('dialog-close', {})" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                {{ trans('actions.cancel') }}
            </button>
        </span>
    </div>

</form>
@endif