<div class="flex justify-center mt-14 h-screen bg-[#f2f3f4] ">
    <div class="justify-center rounded-lg px-6 w-2/3">
            <div class="form-group more-inputs">  
                @for ($n = 0; $n <= $additionalInputs; $n++) 
                    @php
                        $no = $n;
                    @endphp
                    <label for="column1" class="block">Column {{ ++$no }}</label>
                    <div class="input-group my-2">
                        <input wire:model="poDesignData.{{ $n }}.panel_column_display_name"
                            class="hsn-box h-7 w-full rounded-lg bg-gray-300 text-center font-mono text-xs font-normal text-black focus:outline-none"
                            type="text" name="column{{ $n }}"
                            placeholder="Enter column {{ $no }} name" />
                    </div>
                @endfor 
            </div>
            <div class="row mt-2">
                <div class="col-12 text-right">
                    <button type="button"  wire:click='createPurchaseOrderDesign'
                    class="rounded-full w-full bg-gray-900 px-8 py-2 mt-4 text-white hover:bg-yellow-200 hover:text-black">Save</button>
                  
                </div>
            </div>
    </div>
</div>
