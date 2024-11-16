<div>
        <div class="flex justify-between">
            <h2 class="font-semibold text-sm">Add Unit</h2>
            <a x-data wire:click.prevent="openModal" class="hover:underline text-blue-600">View Unit</a>

        </div>
        {{-- @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" class="mt-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><strong class="font-bold">Success!</strong> {{ session('message') }}</span>
            <span x-on:click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 5.652a.5.5 0 00-.707 0L10 9.293 6.354 5.652a.5.5 0 10-.707.707l3.647 3.647-3.647 3.647a.5.5 0 10.707.707L10 10.707l3.646 3.646a.5.5 0 00.707-.707L10.707 10l3.646-3.646a.5.5 0 000-.707z"/></svg>
            </span>
        </div>
    @endif --}}
    <div id="successModal" style="display: none;">
        <div class="modal-content">
            <p class="mt-3 bg-green-100 border border-green-400 text-black px-4 py-3 rounded relative text-xs" id="successMessage"></p>
        </div>
    </div>
    <div id="errorModal" style="display: none;">
        <div class="modal-content flex items-end bg-red-100 border border-red-400 text-black px-4 py-3 rounded relative text-xs">
            <p class="mt-3 " id="errorMessage">\
            </p>
        </div>

    </div>

    {{-- <div id="successModal" style="display: none;">
        <div class="modal-content" >
            <p class="mt-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"  id="successMessage"></p>
        </div>
    </div> --}}

    <div x-data="{ unit: @entangle('unit').defer, shortName: @entangle('shortName').defer }" class="flex justify-between mt-3">
        <div class="w-72">
            <div class="relative w-full min-w-[200px] h-10">
                <input
                    x-model="unit"
                    class="peer w-full h-full bg-transparent text-gray-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-gray-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-gray-gray-200 placeholder-shown:border-t-gray-gray-200 border focus:border-2 focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-gray-gray-200 focus:border-gray-900"
                    placeholder=" " />
                <label class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-gray-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-gray-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-gray-gray-200 peer-focus:before:!border-gray-900 after:border-gray-gray-200 peer-focus:after:!border-gray-900">Unit Name
                </label>
            </div>
        </div>
        <div class="w-72">
            <div class="relative w-full min-w-[200px] h-10">
                <input
                    x-model="shortName"
                    class="peer w-full h-full bg-transparent text-gray-gray-700 font-sans font-normal outline outline-0 focus:outline-0 disabled:bg-gray-gray-50 disabled:border-0 transition-all placeholder-shown:border placeholder-shown:border-gray-gray-200 placeholder-shown:border-t-gray-gray-200 border focus:border-2 focus:border-t-transparent text-sm px-3 py-2.5 rounded-[7px] border-gray-gray-200 focus:border-gray-900"
                    placeholder=" " />
                <label class="flex w-full h-full select-none pointer-events-none absolute left-0 font-normal !overflow-visible truncate peer-placeholder-shown:text-gray-gray-500 leading-tight peer-focus:leading-tight peer-disabled:text-transparent peer-disabled:peer-placeholder-shown:text-gray-gray-500 transition-all -top-1.5 peer-placeholder-shown:text-sm text-[11px] peer-focus:text-[11px] before:content[' '] before:block before:box-border before:w-2.5 before:h-1.5 before:mt-[6.5px] before:mr-1 peer-placeholder-shown:before:border-transparent before:rounded-tl-md before:border-t peer-focus:before:border-t-2 before:border-l peer-focus:before:border-l-2 before:pointer-events-none before:transition-all peer-disabled:before:border-transparent after:content[' '] after:block after:flex-grow after:box-border after:w-2.5 after:h-1.5 after:mt-[6.5px] after:ml-1 peer-placeholder-shown:after:border-transparent after:rounded-tr-md after:border-t peer-focus:after:border-t-2 after:border-r peer-focus:after:border-r-2 after:pointer-events-none after:transition-all peer-disabled:after:border-transparent peer-placeholder-shown:leading-[3.75] text-gray-500 peer-focus:text-gray-900 before:border-gray-gray-200 peer-focus:before:!border-gray-900 after:border-gray-gray-200 peer-focus:after:!border-gray-900">Unit Short Name
                </label>
            </div>
        </div>

        <button
            x-bind:disabled="!unit || !shortName"
            wire:click="addUnit"
            class="px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-md hover:bg-gray-600 disabled:bg-gray-300"
            :class="{
                'bg-gray-900 hover:bg-gray-700 text-white': unit && shortName,
                'bg-gray-400 text-white': !unit || !shortName
            }"
        >
            Add Unit
        </button>
    </div>




    @if($isOpen)
    <div x-data="{
        isOpen: @entangle('isOpen'),
        search: '',
        allUnits: {{ json_encode(array_column($units, 'id')) }},
        selectedUnits: {{ json_encode(array_column(array_filter($units, function($unit) { return $unit['is_default'] == 1; }), 'id')) }},
        get unselectedUnits() {
            return this.allUnits.filter(id => !this.selectedUnits.includes(id));
        }
    }"
    x-show="isOpen"
    x-on:keydown.escape.window="isOpen = false"
    x-on:close.stop="isOpen = false"
    class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60"
    lazy>
        <div class="bg-white p-6 rounded shadow-lg h-96 w-80 sm:w-96 flex flex-col max-h-full">
            <div class="mb-4 sticky">
                <h1 class="text-lg text-black border-b border-gray-400">All Units</h1>
                <input type="text" x-model="search" placeholder="Search units..." class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
            </div>
            <ul class="overflow-auto">
                @foreach ($units as $unit)
                    <li class="p-1" x-show="'{{ $unit['unit'] }}'.toLowerCase().includes(search.toLowerCase())">
                        <input
                            class="rounded"
                            type="checkbox"
                            x-model="selectedUnits"
                            value="{{ $unit['id'] }}"
                        >
                        {{ $unit['unit'] }} ({{ $unit['short_name'] }})
                    </li>
                @endforeach
            </ul>
            <div class="flex items-center justify-end mt-4 shrink-0">
                <button x-on:click="isOpen = false"
                        class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500 transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                    Cancel
                </button>
                <button x-on:click="$wire.saveUnits(selectedUnits, unselectedUnits)"
                        class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                    Save
                </button>
            </div>
        </div>
    </div>
@endif




{{-- <div class="bg-white p-6 rounded shadow-lg h-96 w-80 sm:w-96 flex flex-col max-h-full" x-data="{ search: '', selectedUnit: null, isOpen: false, units: @json($units) }">
    <div class="mb-4 sticky">
        <h1 class="text-lg text-black border-b border-gray-400">All Units</h1>
        <input type="text" x-model="search" placeholder="Search or add unit..." class="w-full p-2 mt-2 border border-gray-300 rounded-lg" @focus="isOpen = true" @blur="setTimeout(() => isOpen = false, 200)">
    </div>
    <div class="relative">
        <div class="relative">
            <button @click="isOpen = !isOpen" class="w-full p-2 mt-2 border border-gray-300 rounded-lg bg-white">
                <span x-text="selectedUnit ? selectedUnit.unit : 'Select or add a unit'"></span>
            </button>
            <ul x-show="isOpen" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-40 overflow-auto">
                <template x-for="unit in units.filter(u => u.unit.toLowerCase().includes(search.toLowerCase()))" :key="unit.id">
                    <li @click="selectedUnit = unit; search = unit.unit; isOpen = false" class="p-2 cursor-pointer hover:bg-gray-200">
                        <span x-text="unit.unit + ' (' + unit.short_name + ')'"></span>
                    </li>
                </template>
                <li x-show="!units.some(u => u.unit.toLowerCase() === search.toLowerCase())" @click="selectedUnit = { unit: search, short_name: '' }; isOpen = false" class="p-2 cursor-pointer hover:bg-gray-200">
                    <span x-text="'Add new unit: ' + search"></span>
                </li>
            </ul>
        </div>
    </div>
</div> --}}

<script>
     function saveUnits() {
        // Collect the selected unit IDs
        const selectedUnits = this.selectedUnits;

        console.log(selectedUnits);
        @this.emit('saveUnits', selectedUnits);
    }
    document.addEventListener('livewire:load', function () {
        Livewire.on('fields-reset', function () {
            let unitInput = document.querySelector('input[x-model="unit"]');
            let shortNameInput = document.querySelector('input[x-model="shortName"]');
            if (unitInput) unitInput._x_model.set('');
            if (shortNameInput) shortNameInput._x_model.set('');
        });
    });
    window.addEventListener('show-success-message', event => {
            // Set the message in the modal
            document.getElementById('successMessage').textContent = event.detail.message;

            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('successModal').style.display = 'block';

            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 5000);
        });

</script>
</div>
