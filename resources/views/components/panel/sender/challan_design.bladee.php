<div class="flex justify-center mt-14 h-screen bg-[#f2f3f4] ">
    <div class="justify-center rounded-lg px-6 w-2/3">
        {{-- <div class="row mb-1">
            <div class="col-12 text-right">
                <button type="button" wire:click='addInput' class="btn btn-sm btn-primary rounded-0 bg-[#007bff] text-white p-1"
                    title="Save terms">Add</button>
            </div>

        </div> --}}
        {{-- <form wire:submit.prevent="challanDesign"> --}}
            {{-- @dd($additionalInputs) --}}
            <div class="form-group more-inputs">
                @for ($n = 0; $n <= $additionalInputs; $n++)

                    @php
                        $no = $n;
                    @endphp
                    <label for="column1" class="block">Column {{ ++$no }}</label>
                    <div class="input-group my-2">
                        <input wire:model="challanDesignData.{{ $n }}.panel_column_display_name"
                            class="hsn-box h-7 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                            type="text" name="column{{ $n }}"
                            placeholder="Enter column {{ $no }} name" />
                    </div>
                @endfor

            </div>
            <div class="row mt-2">
                <div class="col-12 text-right">
                    <button type="button"  wire:click='createChallanDesign'
                    class="rounded-full w-full bg-gray-900 px-8 py-2 mt-4 text-white hover:bg-yellow-200 hover:text-black">Save</button>
                    {{-- <button type="submit" wire:click='createChallanDesign' class="btn btn-sm btn-primary rounded-0 bg-[#007bff] text-white p-1"
                        title="Save terms">Save</button> --}}
                </div>
            </div>
            {{-- <button type="submit">neduifn</button> --}}
        {{-- </form> --}}
    </div>
</div>
