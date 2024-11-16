{{-- <div class="flex justify-center mt-16 h-screen bg-[#f2f3f4] ">
    <div class="justify-center rounded-lg p-6 w-2/3">
        <input type="hidden" value="48" name="user_id">
        <div class="form-group more-inputs">
            <label for="column1" class="block">Column 1</label>
            <div class="input-group my-2">
                <input wire:model="columnData.0.name" class="hsn-box h-7 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" name="column1" placeholder="Enter column 1 name" />
            </div>
            <label for="column2" class="block">Column 2</label>
            <div class="input-group my-2">
                <input wire:model="columnData.1.name" class="hsn-box h-7 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" name="column2" placeholder="Enter column 2 name" />
            </div>
            <label for="column3" class="block">Column 3</label>
            <div class="input-group my-2">
                <input wire:model="column3" class="hsn-box h-7 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" name="column3" placeholder="Enter column 3 name" />
            </div>
            <label for="column4" class="block">Column 4</label>
            <div class="input-group my-2">
                <input wire:model="column4" class="hsn-box h-7 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none" type="text" name="column4" placeholder="Enter column 4 name" />
            </div>
        </div>
        <div class="row my-3">
            <div class="col-12 text-center">
                <button wire:click="invoiceDesign" class="btn btn-sm btn-primary rounded-0" title="Save terms">Save</button>
            </div>
        </div>


    </div>
</div> --}}
<div class="flex justify-center mt-14 h-screen bg-[#f2f3f4] ">
    <div class="justify-center rounded-lg px-6 w-2/3">
            <div class="form-group more-inputs">
               

                @for ($n = 0; $n <= $additionalInputs; $n++)

                    @php
                        $no = $n;
                    @endphp
                    <label for="column1" class="block">Column {{ ++$no }}</label>
                    <div class="input-group my-2">
                        <input wire:model="invoiceDesignData.{{ $n }}.panel_column_display_name"
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
                  
                </div>
            </div>
    </div>
</div>
