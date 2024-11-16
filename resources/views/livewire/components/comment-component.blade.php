<div x-data="{ isOpen: @entangle('isOpen') }" x-show="isOpen" x-on:keydown.escape.window="isOpen = false" x-on:close.stop="isOpen = false"
    class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
    <div class="bg-white p-6 rounded shadow-lg w-80 sm:w-96">
        <div class="mb-4">
            <h1 class="text-lg text-black border-b border-gray-400">{{ $modalHeading }}</h1>
            <div class="">
                <div class="relative w-full min-w-[200px] h-10 mt-5">
                    <input
                        class="peer w-full text-black h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                        placeholder=" " wire:model.defer="status_comment" />
                    <label
                        class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">
                        Comment
                    </label>
                </div>
            </div>
            @error('comment')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
            <button x-on:click="isOpen = false"
                class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                Cancel
            </button>
            <button wire:click="{{ $modalAction }}"
                class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs   text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                {{ $modalButtonText }}
            </button>
        </div>
    </div>
    <div x-on:click.self="isOpen = false" class="inset-0 bg-black opacity-50"></div>
</div>
