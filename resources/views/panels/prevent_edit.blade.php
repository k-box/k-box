
<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
    <div class="sm:flex sm:items-start">
        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
            @materialicon('alert', 'error_outline', 'h-6 w-6 text-red-600 fill-current')
        </div>
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                {{ trans('errors.panels.prevent_edit', ['name' => $name]) }}
            </h3>
            <div class="mt-2">
                <p class="text-sm leading-5 text-gray-500">
                    {!!$message!!}
                </p>
            </div>
        </div>
    </div>
</div>
<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
        <button type="submit" @click="$dispatch('dialog-close', {})" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-blue-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-500 transition ease-in-out duration-150 sm:text-sm sm:leading-5">
            {{ trans('panels.close_btn') }}
        </button>
    </span>
</div>
