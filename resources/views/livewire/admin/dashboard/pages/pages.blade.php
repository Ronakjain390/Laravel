<div>
  

<div class="max-w-4xl  mx-auto p-4 text-black">
    {{-- <form class="grid gap-4" wire:submit.prevent="updateData"> --}}
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700 p-6 rounded-lg bg-[#e9e6e6] dark:bg-gray-800">
        @if ($successMessage)
        <div class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400"
            role="alert">
            <span class="font-medium">Success:</span> {{ $successMessage }}
        </div>
    @endif
        Add Pages

        {{-- @livewire('livewire-ui.debug') --}}
        <div id="myTabContent" class="p-4 rounded-lg dark:bg-gray-800">
            <div class="" id="seller-manually" role="tabpanel" aria-labelledby="seller-manually-tab">
                <div class="mt-2"> 
                    <form wire:submit.prevent="createPage">
                        <div class="relative">
                            <!-- Other elements if needed -->
                        </div>
                        <div>
                            <label for="seller_name" class="block text-sm font-medium">Title <span class="text-red-600">*</span></label>
                            <input type="text" wire:model.defer="pageData.title" name="name" class="mt-1 p-2 h-8 block w-full rounded-md bg-[#aeaaaa] border-transparent focus: ">
                        </div>
                        <div>
                            <label for="content" class="block text-sm font-medium">Content</label>
                            <textarea id="editor" name="content" cols="30" rows="10" wire:model.defer="pageData.content" ></textarea>
                        </div>
                        {{-- <div x-data="{ content: @entangle('pageData.content') }">
                            <label for="content" class="block text-sm font-medium">Content</label>
                            <textarea name="content" id="editor" cols="30" rows="10" x-model="content" @input="() => content = $event.target.value"></textarea>
                        </div> --}}
                        <div class="flex justify-center">
                            <button type="submit" class="rounded-full w-full bg-gray-900 px-8 py-2 mt-2 text-white hover:bg-yellow-200 hover:text-black">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
   
    

</div>
<script>
	ClassicEditor.create(document.querySelector('#editor'))
    .then(editor => {
        console.log('Editor was initialized', editor);
    })
    .catch(error => {
        console.error(error.stack);
    });

//     document.addEventListener('DOMContentLoaded', () => {
//     ClassicEditor
//         .create(document.querySelector('.wysiwyg'))
//         .catch(error => {
//             console.error('Error initializing the editor', error);
//         });
// });




// <script src="path/to/ckeditor/ckeditor.js"> 
 
    // document.addEventListener('DOMContentLoaded', () => {
    //     ClassicEditor
    //         .create(document.querySelector('#editor'))
    //         .then(editor => {
    //             console.log('Editor initialized successfully', editor);
    //         })
    //         .catch(error => {
    //             console.error('Error initializing the editor', error);
    //         });
    // });
 


</script>
</div>