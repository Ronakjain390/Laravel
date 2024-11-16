<div x-data="{
    openSearchModal: @entangle('openSearchModal'),
    selectedTags: @entangle('selectedTags'),
    temporarySelectedTags: [],
    searchTerm: '',
    allTags: {{ json_encode($allTags) }},
    newTags: [],
    currentPage: 1,
    perPage: 10,
    init() {
        this.temporarySelectedTags = Array.isArray(this.selectedTags) ? [...this.selectedTags] : [];
    },
    createTag() {
        const newTag = {
            id: 'new_' + Date.now(),
            name: this.searchTerm,
            panel_id: {{ $panelId }},
            table_id: {{ $tableId }}
        };
        this.allTags.push(newTag);
        this.newTags.push(newTag);
        this.temporarySelectedTags.push(newTag.id);
        this.searchTerm = '';
    },
    isSaveDisabled() {
        const selectedSet = new Set(Array.isArray(this.selectedTags) ? this.selectedTags : []);
        const tempSet = new Set(this.temporarySelectedTags);
        return selectedSet.size === tempSet.size &&
               [...selectedSet].every(value => tempSet.has(value));
    },
    filteredTags() {
        return this.allTags.filter(tag => tag.name.toLowerCase().includes(this.searchTerm.toLowerCase()));
    },
    paginatedTags() {
        const start = (this.currentPage - 1) * this.perPage;
        const end = start + this.perPage;
        return this.filteredTags().slice(start, end);
    },
    totalPages() {
        return Math.ceil(this.filteredTags().length / this.perPage);
    },
    nextPage() {
        if (this.currentPage < this.totalPages()) {
            this.currentPage++;
        }
    },
    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
        }
    },
    toggleTag(tagId) {
        const index = this.temporarySelectedTags.indexOf(tagId);
        if (index === -1) {
            this.temporarySelectedTags.push(tagId);
        } else {
            this.temporarySelectedTags.splice(index, 1);
        }
    },
    isTagSelected(tagId) {
        return this.temporarySelectedTags.includes(tagId);
    },
    saveTags() {
        const tagsToSave = [...this.allTags.filter(tag => this.temporarySelectedTags.includes(tag.id)), ...this.newTags];
        $wire.saveTags(tagsToSave);
    }
}"
    x-init="init()"
    x-show="openSearchModal"
    x-on:keydown.escape.window="openSearchModal = false"
    x-on:close.stop="openSearchModal = false"
    class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60" wire:ignore.self>    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <div class="mb-4">
            <h1 class="text-lg text-black border-b border-gray-400">{{ $searchModalHeading }}</h1>
            <div class="relative w-full min-w-[200px] h-10 mt-5">
                <input class="peer w-full text-black h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-blue-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 border focus:border-2 focus:border-t-transparent text-xs px-3 py-2.5 rounded-[7px] border-blue-gray-200 focus:border-gray-900"
                    placeholder=" "
                    x-model="searchTerm"
                    maxlength="20"
                    x-on:input="searchTerm = $event.target.value.slice(0, 20)" />
                <label class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-blue-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-xs text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-blue-gray-200 peer-focus:before:!border-gray-900 after:border-blue-gray-200 peer-focus:after:!border-gray-900">
                    Search (max 20 characters)
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                <template x-if="searchTerm && !filteredTags().some(tag => tag.name.toLowerCase() === searchTerm.toLowerCase())">
                    <button class="flex text-black text-xs" @click="createTag()">
                        <span x-text="searchTerm" class="whitespace-nowrap"></span>
                        <span class="text-green-500 ml-2 whitespace-nowrap flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Create Tag
                        </span>
                    </button>
                </template>
            </div>

            <div class="mt-4">
                <p class="text-black text-base">Available Tags:</p>
                <ul>
                    <template x-for="tag in paginatedTags()" :key="tag.id">
                        <li>
                            <label class="inline-flex items-center mt-3 text-xs text-black">
                                <input type="checkbox" class="form-checkbox h-4 rounded text-gray-600"
                                       :checked="isTagSelected(tag.id)"
                                       @click="toggleTag(tag.id)" />
                                <span class="ml-2" x-text="tag.name"></span>
                            </label>
                        </li>
                    </template>
                </ul>

                <nav aria-label="Page navigation" class="mt-4  whitespace-nowrap flex justify-between ">
                    <span x-text="`Page ${currentPage} of ${totalPages()}`" class="text-xs text-gray-600 mt-2 block"></span>
                    <ul class="flex items-center -space-x-px h-8 text-xs">
                        <li>
                            <button @click="prevPage()" :disabled="currentPage === 1" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" :class="{ 'cursor-not-allowed': currentPage === 1 }">
                                <span class="sr-only">Previous</span>
                                <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                                </svg>
                            </button>
                        </li>
                        <template x-for="page in totalPages()" :key="page">
                            <li>
                                <button @click="currentPage = page" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" :class="{ 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-white': currentPage === page }">
                                    <span x-text="page"></span>
                                </button>
                            </li>
                        </template>
                        <li>
                            <button @click="nextPage()" :disabled="currentPage === totalPages()" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" :class="{ 'cursor-not-allowed': currentPage === totalPages() }">
                                <span class="sr-only">Next</span>
                                <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>


            @error('comment')
            <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

            <div class="flex justify-end mt-4 text-sm">
                <button x-on:click="openSearchModal = false" wire:click="closeTagModal" class="px-4 py-1.5 rounded mr-1 text-red-500 hover:bg-red-500/10">
                    Cancel
                </button>
                <button @click="saveTags()"
                        x-bind:disabled="isSaveDisabled()"
                        :class="{'bg-gray-300': isSaveDisabled(), 'bg-gray-900': !isSaveDisabled()}"
                        class="px-4 py-1.5 rounded text-white">
                    {{ $searchModalButtonText }}
                </button>
            </div>
        </div>
    </div>
</div>
