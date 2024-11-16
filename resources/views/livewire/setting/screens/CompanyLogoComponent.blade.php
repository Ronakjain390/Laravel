<div id="dynamic-view">
            @if ($errorMessage)
            @foreach (json_decode($errorMessage) as $error)
            <div class="p-2 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Error:</span> {{ $error[0] }}
            </div>
            @endforeach
            @endif
            @if ($successMessage)
            <div class="p-2 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">Success:</span> {{ $successMessage }}
            </div>
            @endif
        <div class="text-black bg-white">
            <div class="border-b border-gray-400 text-black text-sm flex flex-col sm:flex-row sm:hidden">
                <select class="px-2 my-2 w-full text-center rounded-lg text-xs" wire:model="activeTab">
                    <option value="tab1">Sender</option>
                    <option value="tab2">Receiver</option>
                    <option value="tab3">Seller</option>
                    <option value="tab4">Buyer</option>
                    <option value="tab5">Receipt Note</option>
                    <option value="tab6">Quotation</option>
                </select>
            </div>
            <div class="border-b p-1.5 border-gray-400 text-black text-base font-semibold hidden sm:flex">
                <button class="px-4 p-1.5 w-auto text-center text-base my-2 {{ $activeTab === 'tab1' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab1')">Sender</button>
                <button class="px-4 p-1.5 w-auto text-center text-base my-2 {{ $activeTab === 'tab2' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab2')">Receiver</button>
                <button class="px-4 p-1.5 w-auto text-center text-base my-2 {{ $activeTab === 'tab3' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab3')">Seller</button>
                <button class="px-4 p-1.5 w-auto text-center text-base my-2 {{ $activeTab === 'tab4' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab4')">Buyer</button>
                <button class="px-4 p-1.5 w-auto text-center text-base my-2 {{ $activeTab === 'tab5' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab5')">Receipt Note</button>
                <button class="px-4 p-1.5 w-auto text-center text-base my-2 {{ $activeTab === 'tab6' ? 'bg-orange text-white rounded-lg' : '' }}" wire:click="$set('activeTab', 'tab6')">Quotation</button>
            </div>

            <style>
                .arrow {
                    margin-left: 0.5rem;
                    font-size: 0.8rem;
                    transition: transform 0.3s ease;
                }
                details[open] .arrow {
                    transform: rotate(180deg);
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const receiverDropdown = document.getElementById('receiverDropdown');
                    const arrow = receiverDropdown.querySelector('.arrow');

                    receiverDropdown.addEventListener('toggle', () => {
                        if (receiverDropdown.open) {
                            arrow.textContent = '▼';
                        } else {
                            arrow.textContent = '▼';
                        }
                    });
                });
            </script>

            @if ($activeTab === 'tab1')
                <!-- Content for Tab 1 -->
                <div class="flex-grow  sm:p-3 p-1 md:px-10 lg:px-10 max-w-4xl mx-auto">
                    <!-- Add new products form -->
                    <div wire:loading class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  bg-opacity-50 ">
                        <span class="loading loading-spinner loading-md"></span>
                    </div>
                    {{-- Challan Logo --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow">
                        {{-- <h1>Challan Logo</h1> --}}

                        <div class="w-2/3 flex  ">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>


                                <form wire:submit.prevent="companyChallanLogo" enctype="multipart/form-data" class="mt-2" >
                                    <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                    <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                        <input wire:model.defer="companyLogoDataset.challan_logo_url" class="block w-full ml-2 md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="small_size" type="file" style="width: 100%;">
                                        <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                            @if ($showUploadButton)
                                            <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                type="submit">
                                                Upload
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                        {{-- @dump($companyLogoData['companyLogo']['challanTemporaryImageUrl']) --}}
                        <div id="image-preview" class="w-1/4 mx-auto">
                            <h2 class="font-semibold text-sm">Preview</h2>
                            <div class="relative">
                                @if(isset($companyLogoData['companyLogo']['challanTemporaryImageUrl']))
                                <img src="{{ $companyLogoData['companyLogo']['challanTemporaryImageUrl'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                @else
                                <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                @endif
                                @if(isset($companyLogoData['companyLogo']['challanTemporaryImageUrl']))
                                <button wire:click="removePreviewImage('challan')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                    <div class="tooltip" data-tip="Remove Logo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Challan Heading --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm"> {{ ($companyLogoDataset['challan_heading'] ?? '') . ' Heading' }}</div>
                        <div class="items-center">
                            <div class="gap-4 flex items-center">
                                <input type="text"
                                    id="challan_heading_input"
                                    wire:model.defer="companyLogoDataset.challan_heading"
                                    class="bg-gray-50 w-3/4 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block mt-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Challan"
                                    required>
                                <button type="submit"
                                        id="update_challan_heading_button"
                                        wire:click="challanHeading"
                                        class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-400">Update</button>
                            </div>
                        </div>
                    </div>

                    {{-- Terms And Conditions --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm">Terms and Conditions </div>
                        <div class="items-center">
                            <div class="grid gap-4">
                                <div class="gap-4 flex items-center">
                                    <textarea
                                        id="terms_conditions_textarea"
                                        placeholder="Terms & Conditions"
                                        name="content"
                                        class="mt-1 p-2 text-xs block w-3/4 border border-gray-300 text-dark rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        wire:model.defer="termsAndConditionsData.content"
                                    ></textarea>
                                    <button
                                        type="button"
                                        id="add_terms_conditions_button"
                                        wire:click="addTerms"
                                        class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-300 dark:bg-gray-300"
                                    >
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative shadow-md sm:rounded-lg mt-3 overflow-auto mb-10" wire:ignore.self>
                            <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400 mb-10 mt-10">
                                <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 whitespace-nowrap py-1 normal-case">#</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Terms</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Date</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($challanData))
                                    @foreach($challanData as $index => $item)
                                    <tr class="fixed-width bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                                        <td class="w-4 px-4">
                                            <div class="font-normal text-gray-500"> {{ $index+1 }} </div>
                                        </td>
                                        <td class="px-2 w-1/3 whitespace-nowrap">{{ $item->content ?? ''}}</td>
                                        <td class="px-2 w-5/12 whitespace-nowrap">{{ date('d-m-Y', strtotime($item->created_at ?? '')) }}</td>
                                        <td class="">
                                            <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg py-0.5 text-sm px-5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg></button>
                                            <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                    <li>
                                                        <a x-data wire:click="tagModal({{ json_encode($item) }}, 'addTags')" class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">Edit Terms</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $item->id ?? '' }})" class="block px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Default Modal -->
                        <div id="edit-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore>
                            <div class="relative w-full max-w-lg max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <!-- Modal header -->
                                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                            Edit Terms and Conditions
                                            <button type="button" wire:click='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
                                        </h3>
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-6 space-y-6">
                                        <div>
                                            <label for="terms" class="block text-sm font-medium ">Terms</label>
                                            <input wire:model.defer="selectedContent" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div class="relative">
                                            <label for="receiver_user_id" class="block text-sm font-medium">Update</label>
                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button data-modal-hide="edit-modal" type="button" wire:click='updatePanelSeries(1)' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                                        <button data-modal-hide="edit-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Upload Signature --}}

                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">Signature Upload</h2>
                        <div class="mt-4 text-xs">
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOption" value="FooterStamp" class="mr-2 text-xs">
                                This is a computer-generated Challan and does not require a physical signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOption" value="Signature" class="mr-2 text-xs">
                                Signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOption" value="None" class="mr-2 text-xs">
                                None
                            </label>
                        </div>
                        {{-- @dump($companyLogoData['companyLogo']['signature_sender']) --}}
                        @if($selectedOption == 'Signature')
                        <div class="flex">
                            <div class="w-2/3 flex mt-3">
                                <div>
                                    <form wire:submit.prevent="signatureSender" enctype="multipart/form-data" class="mt-2" >
                                        <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" >Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                        <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                            <input wire:model.defer="companyLogoDataset.signature_sender" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"   type="file" style="width: 100%;">
                                            <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                                @if ($showUploadButton)
                                                <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                    type="submit">
                                                    Upload
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="w-1/4 mx-auto">
                                <h2 class="font-semibold text-sm">Preview</h2>
                                <div class="relative">
                                    @if(isset($companyLogoData['companyLogo']['signature_sender']))
                                        @php
                                            $signatureSenderPath = $companyLogoData['companyLogo']['signature_sender'];
                                            $signatureSenderUrl = Storage::disk('s3')->url($signatureSenderPath);
                                        @endphp
                                        <img src="{{ $signatureSenderUrl }}" class="img-responsive w-3/4 h-auto object-contain">
                                    @else
                                        <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                    @endif
                                    @if(isset($companyLogoData['companyLogo']['signature_sender']))
                                        <button wire:click="removePreviewImage('challan')" class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="mt-3">
                        {{-- <h1>Sender Panel Settings</h1> --}}
                        @livewire('setting.screens.panel-setting-manager', ['panel' => 'sender'])
                    </div>


                     {{-- Powred By the Parchi --}}
                     @php
                     $activePlan = json_decode($activePlan);
                     @endphp
                     @if (isset($activePlan->Sender) && is_array($activePlan->Sender) && !empty($activePlan->Sender))
                     @php
                     $showPoweredBy = false;
                    @endphp
                     @foreach ($activePlan->Sender as $sender)
                     {{-- @dump($sender) --}}
                     @if (isset($sender->plan) && $sender->plan->price > 0)
                         @php
                             $showPoweredBy = true;
                             break;
                         @endphp
                     @endif
                 @endforeach
                 {{-- @if ($showPoweredBy)
                     <div class="bg-white border border-gray-300 rounded-lg p-2  shadow mt-3">


                                <div class="font-semibold mb-0 text-sm">Powered By The Parchi </div>
                                <div class="mt-2">
                                    <div class="flex items-center justify-between border-b pb-2">
                                        <p class="text-xs ml-2 text-gray-400 mr-4">
                                            @if($companyLogoDataset['challan_stamp'])
                                            Visible
                                        @else
                                            Not Visible
                                        @endif
                                        </p>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" wire:model="companyLogoDataset.challan_stamp" wire:click="challanToggle">
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>

                                    </div>
                                    <div class="font-semibold mb-0 text-sm mt-2" >Self Delivery </div>
                                    <div class="flex items-center justify-between border-b pb-2">
                                        <p class="text-xs ml-2 text-gray-400 mr-4">
                                            @if($self_delivery)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                        </p>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" wire:model="self_delivery" >
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>

                                    </div>
                                    <div class="font-semibold mb-0 text-sm mt-2" >Tag </div>
                                    <div class="flex items-center justify-between border-b pb-2">
                                        <p class="text-xs ml-2 text-gray-400 mr-4">
                                            @if($tags)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                        </p>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" wire:model="tags" >
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>

                                    </div>
                                    <div class="font-semibold mb-0 text-sm mt-2" >Payment Status </div>
                                    <div class="flex items-center justify-between border-b pb-2">
                                        <p class="text-xs ml-2 text-gray-400 mr-4">
                                            @if($payment_status)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                        </p>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" wire:model="payment_status" >
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>

                                    </div>
                                    <div class="font-semibold mb-0 text-sm mt-2" >Barcode </div>
                                    <div class="flex items-center justify-between border-b pb-2">
                                        <p class="text-xs ml-2 text-gray-400 mr-4">
                                            @if($barcode)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                        </p>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" wire:model="barcode">
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>

                                    </div>
                                </div>


                    </div>
                    @endif --}}

                    @endif

                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">{{ ($companyLogoDataset['challan_heading'] ?? '') . ' Templates' }}</h2>
                        <div class="mt-4 text-xs">
                            @php
                            // Move Template 3 to the end of the array
                            $template3 = null;
                            foreach ($pdfFiles as $key => $pdf) {
                                if ($pdf['id'] == 3) {
                                    $template3 = $pdf;
                                    unset($pdfFiles[$key]); // Remove it from the original position
                                    break;
                                }
                            }
                            if ($template3) {
                                $pdfFiles[] = $template3; // Append it to the end
                            }
                        @endphp

                        {{-- @dump($pdfFiles) --}}

                        @foreach($pdfFiles as $pdf)
                            <label class="flex items-center mb-2 ml-2 text-xs">
                                <input type="radio" wire:model="selectedChallanTemplate" value="{{ $pdf['id'] }}" class="mr-2 cursor-pointer">

                                @if($pdf['id'] == 1)
                                    Default (single address & without tax)
                                @elseif($pdf['id'] == 2)
                                    Template 2 (with send to & ship to address, without tax)
                                @elseif($pdf['id'] == 3)
                                    <span>Template 3 (special visibility format)</span>
                                @elseif($pdf['id'] == 4)
                                    Template 4 (with send to & ship to address, with tax)
                                @elseif($pdf['id'] == 5)
                                    POS machine format-A
                                @elseif($pdf['id'] == 6)
                                    POS machine format-B
                                @else
                                    Template {{ $pdf['id'] }}
                                @endif

                                <a href="{{ $pdf['path'] }}" target="_blank" class="ml-2 text-blue-500">
                                    <div class="tooltip" data-tip="View Template">
                                        <svg class="w-[20px] h-[20px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-width="1" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                            <path stroke="currentColor" stroke-width="1" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </div>
                                </a>
                            </label>
                        @endforeach

                        </div>
                    </div>
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3 overflow-auto">
                        <h2 class="font-semibold text-sm">{{ ($companyLogoDataset['challan_heading'] ?? '') . ' Columns' }}</h2>
                        <div class="mt-4 text-xs">
                            <livewire:sender.screens.challan-columns />
                        </div>
                    </div>
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3 overflow-auto">

                        <div class="mt-4 text-xs">
                            <livewire:setting.screens.add-unit :panel_type="'sender'"/>
                        </div>
                    </div>
                </div>
                <script>
                    function attachEventListeners() {
                        // Challan Heading
                        const challanHeadingInput = document.getElementById('challan_heading_input');
                        const updateChallanHeadingButton = document.getElementById('update_challan_heading_button');
                        const initialChallanHeadingValue = challanHeadingInput.value;

                        challanHeadingInput.addEventListener('input', function() {
                            const hasChanged = challanHeadingInput.value !== initialChallanHeadingValue;
                            updateChallanHeadingButton.disabled = !hasChanged;
                            updateChallanHeadingButton.classList.toggle('bg-black', hasChanged);
                            updateChallanHeadingButton.classList.toggle('bg-gray-400', !hasChanged);
                        });

                        // Terms and Conditions
                        const termsConditionsTextarea = document.getElementById('terms_conditions_textarea');
                        const addTermsConditionsButton = document.getElementById('add_terms_conditions_button');

                        termsConditionsTextarea.addEventListener('input', function() {
                            const hasContent = termsConditionsTextarea.value.trim() !== '';
                            addTermsConditionsButton.disabled = !hasContent;
                            addTermsConditionsButton.classList.toggle('bg-gray-800', hasContent);
                            addTermsConditionsButton.classList.toggle('hover:bg-gray-900', hasContent);
                            addTermsConditionsButton.classList.toggle('dark:bg-gray-800', hasContent);
                            addTermsConditionsButton.classList.toggle('dark:hover:bg-gray-700', hasContent);
                            addTermsConditionsButton.classList.toggle('bg-gray-300', !hasContent);
                            addTermsConditionsButton.classList.toggle('dark:bg-gray-300', !hasContent);
                        });
                    }

                    document.addEventListener('DOMContentLoaded', attachEventListeners);
                    document.addEventListener('livewire:load', attachEventListeners);
                    document.addEventListener('livewire:update', attachEventListeners);
                </script>
            @elseif ($activeTab === 'tab2')
                <!-- Content for Tab 2 -->

                <div class="flex-grow  sm:p-3 p-1  md:px-10 lg:px-10 max-w-4xl mx-auto">
                    <!-- Add new products form -->

                    {{-- Challan Logo --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow">
                        {{-- <h1>Challan Logo</h1> --}}

                        <div class="w-2/3 flex  ">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>
                                {{-- <form wire:submit.prevent="companyChallanLogo" enctype="multipart/form-data">
                                    <div>
                                        <div class="mb-4">
                                            <p class="mt-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>

                                            <input wire:model.defer="companyLogoDataset.challan_logo_url" class="block w-11/12 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="buyer_logo" type="file">
                                        </div>
                                        <button type="submit" class=" text-white bg-[#007bff] hover:bg-[#0069d9] focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-1.5  focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600 hover:text-black">Upload</button>
                                    </div>
                                </form> --}}

                                <form wire:submit.prevent="companyReturnChallanLogo" enctype="multipart/form-data" class="mt-2" >
                                    <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                    <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                        <input wire:model.defer="companyLogoDataset.return_challan_logo_url" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="small_size" type="file" style="width: 100%;">
                                        <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                            @if ($showUploadButton)
                                            <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                type="submit">
                                                Upload
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>


                        <div id="image-preview" class="w-1/4 mx-auto">
                            <h2 class="font-semibold text-sm">Preview</h2>
                            <div class="relative">
                                @if(isset($companyLogoData['companyLogo']['returnChallanTemporaryImageUrl']))
                                <img src="{{ $companyLogoData['companyLogo']['returnChallanTemporaryImageUrl'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                @else
                                <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                @endif
                                @if(isset($companyLogoData['companyLogo']['returnChallanTemporaryImageUrl']))
                                <button wire:click="removePreviewImage('return_challan')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                    <div class="tooltip" data-tip="Remove Logo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Return Challan Heading --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm"> {{ ($companyLogoDataset['return_challan_heading'] ?? '') . ' Heading' }}</div>
                        <div class="items-center">
                            <div class="gap-4 flex items-center">
                                <input type="text"
                                    id="return_challan_heading_input"
                                    wire:model.defer="companyLogoDataset.return_challan_heading"
                                    class="bg-gray-50 w-3/4 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block mt-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Challan"
                                    required>
                                <button type="submit"
                                        id="update_challan_heading_button"
                                        wire:click="challanHeading"
                                        class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-400">Update</button>
                            </div>
                        </div>
                    </div>
                    {{-- Terms And Conditions --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2  shadow mt-3">

                        <div class="font-semibold mb-0 text-sm">Terms and Conditions </div>
                        <div class="items-center">
                            <div class="grid gap-4">
                                <div class="gap-4 flex items-center">
                                    <textarea
                                        id="terms_conditions_textarea"
                                        placeholder="Terms & Conditions"
                                        name="content"
                                        class="mt-1 p-2 text-xs block w-3/4 border border-gray-300 text-dark rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        wire:model.defer="termsAndConditionsData.content"
                                    ></textarea>
                                    <button
                                        type="button"
                                        id="add_terms_conditions_button"
                                        wire:click="addReturnChallanTerms"
                                        class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-300 dark:bg-gray-300"
                                    >
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative  shadow-md sm:rounded-lg mt-3 overflow-auto overflow-auto" wire:ignore.self>

                            <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400 mb-10 mt-10 ">
                                <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 whitespace-nowrap py-1 normal-case">
                                            #
                                        </th>

                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">
                                            Terms
                                        </th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">
                                            Date
                                        </th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">
                                        Action
                                        </th>
                                    </tr>
                                </thead>



                                <tbody>
                                    {{-- @dd($termsIndexData); --}}
                                    @if(isset($returnChallanData))
                                    @foreach($returnChallanData as $index => $item)
                                    <tr class=" bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                                        <td class="w-4 px-4">
                                            <div class="font-normal text-gray-500"> {{ $index+1 }} </div>
                                        </td>
                                        <td class="px-2 w-1/3 whitespace-nowrap">{{ $item->content ?? ''}}
                                        </td>

                                        <td class="px-2 w-5/12 whitespace-nowrap">

                                            {{ date('d-m-Y', strtotime($item->created_at ?? '')) }}

                                        </td>
                                        </td>
                                        <td class="">
                                            <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg py-0.5 text-sm px-5  m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg></button>
                                            <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                    <li>
                                                        <a x-data
                                                        wire:click="tagModal({{ json_encode($item) }}, 'addTags')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                           Edit Terms
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $item->id ?? '' }})" class="block px-4  hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>

                        </div>
                        <!-- Default Modal -->
                        <div id="edit-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore>
                            <div class="relative w-full max-w-lg max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <!-- Modal header -->
                                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                            Edit Terms and Conditions
                                            <button type="button" wire:click='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
                                        </h3>
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-6 space-y-6">
                                        <div>
                                            <label for="terms" class="block text-sm font-medium ">Terms</label>
                                            <input wire:model.defer="selectedContent" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>



                                        <div class="relative">
                                            <label for="receiver_user_id" class="block text-sm font-medium">Update</label>


                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button data-modal-hide="edit-modal" type="button" wire:click='updatePanelSeries()' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                                        <button data-modal-hide="edit-modal" type="button"  class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">Signature Upload</h2>
                        <div class="mt-4 text-xs">
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionReceiver" value="FooterStamp" class="mr-2 text-xs">
                                This is a computer-generated Challan and does not require a physical signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionReceiver" value="Signature" class="mr-2 text-xs">
                                Signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionReceiver" value="None" class="mr-2 text-xs">
                                None
                            </label>
                        </div>


                        @if($selectedOptionReceiver == 'Signature')
                        <div class="flex">
                            <div class="w-2/3 flex mt-3">
                                <div>
                                    <form wire:submit.prevent="signatureReceiver" enctype="multipart/form-data" class="mt-2" >
                                        <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" >Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                        <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                            <input wire:model.defer="companyLogoDataset.signature_receiver" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"   type="file" style="width: 100%;">
                                            <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                                <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto" type="submit">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div  class="w-1/4 mx-auto">
                                <h2 class="font-semibold text-sm">Preview</h2>
                                <div class="relative">
                                    @if(isset($companyLogoData['companyLogo']['signature_receiver']))
                                    <img src="{{ $companyLogoData['companyLogo']['signature_receiver'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                    @else
                                    <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                    @endif
                                    @if(isset($companyLogoData['companyLogo']['signature_sender']))
                                    <button wire:click="removePreviewImage('challan')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                            {{--<div class="tooltip" data-tipRemove Logoedit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    </button> --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>


                    <div class="mt-3">
                        {{-- <h1>Sender Panel Settings</h1> --}}
                        @livewire('setting.screens.panel-setting-manager', ['panel' => 'receiver'])
                    </div>
                    {{-- Upload Signature --}}

                    {{-- <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow mt-3">

                        <div id="image-preview" class="w-1/4 mx-auto">
                            @if(isset($companyLogoData['companyLogo']['challanTemporaryImageUrl']))
                            <img src="{{ $companyLogoData['companyLogo']['challanTemporaryImageUrl'] }}" class="img-responsive w-3/4 h-auto object-contain">
                            @else
                            <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                            @endif
                        </div>
                        <div class="w-2/3 flex justify-end">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>
                                <form wire:submit.prevent="companyChallanLogo" enctype="multipart/form-data">
                                    <div>
                                        <div class="mb-4">
                                            <p class="mt-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>

                                            <input wire:model.defer="companyLogoDataset.challan_logo_url" class="block w-11/12 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="buyer_logo" type="file">
                                        </div>
                                        <button type="submit" class=" text-white bg-[#007bff] hover:bg-[#0069d9] focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-1.5  focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600 hover:text-black">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}

                     {{-- Powred By the Parchi --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2  shadow mt-3">

                        {{-- <div class="font-semibold mb-0 text-sm">Terms and Conditions </div>  --}}
                        @php
                            $activePlan = json_decode($activePlan);
                        @endphp

                        @if (!empty($activePlan) && is_array($activePlan) && count($activePlan) > 0)
                        @foreach ($activePlan as $sender)
                            @if (!empty($sender->plan) && $sender->plan->price > 0)
                                <!-- Your code for displaying the sender's information here -->

                                <div class="font-semibold mb-0 text-sm">Powered By The Parchi </div>
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" wire:model="companyLogoDataset.return_challan_stamp" wire:click="challanToggle">
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                        </label>
                                        <p class="text-xs ml-2 text-gray-400">
                                            @if($companyLogoDataset['return_challan_stamp'])
                                                Visible
                                            @else
                                                Not Visible
                                            @endif
                                        </p>
                                    </div>

                                </div>

                            @endif
                            @break;
                        @endforeach

                        @endif
                    </div>

                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3 overflow-auto">

                        <div class="mt-4 text-xs">
                            <livewire:setting.screens.add-unit :panel_type="'receiver'"/>
                        </div>
                    </div>
                    <script>
                        function attachEventListeners() {
                            // Challan Heading
                            const challanHeadingInput = document.getElementById('return_challan_heading_input');
                            const updateChallanHeadingButton = document.getElementById('update_challan_heading_button');
                            const initialChallanHeadingValue = challanHeadingInput.value;

                            challanHeadingInput.addEventListener('input', function() {
                                const hasChanged = challanHeadingInput.value !== initialChallanHeadingValue;
                                updateChallanHeadingButton.disabled = !hasChanged;
                                updateChallanHeadingButton.classList.toggle('bg-black', hasChanged);
                                updateChallanHeadingButton.classList.toggle('bg-gray-400', !hasChanged);
                            });

                            // Terms and Conditions
                            const termsConditionsTextarea = document.getElementById('terms_conditions_textarea');
                            const addTermsConditionsButton = document.getElementById('add_terms_conditions_button');

                            termsConditionsTextarea.addEventListener('input', function() {
                                const hasContent = termsConditionsTextarea.value.trim() !== '';
                                addTermsConditionsButton.disabled = !hasContent;
                                addTermsConditionsButton.classList.toggle('bg-gray-800', hasContent);
                                addTermsConditionsButton.classList.toggle('hover:bg-gray-900', hasContent);
                                addTermsConditionsButton.classList.toggle('dark:bg-gray-800', hasContent);
                                addTermsConditionsButton.classList.toggle('dark:hover:bg-gray-700', hasContent);
                                addTermsConditionsButton.classList.toggle('bg-gray-300', !hasContent);
                                addTermsConditionsButton.classList.toggle('dark:bg-gray-300', !hasContent);
                            });
                        }

                        document.addEventListener('DOMContentLoaded', attachEventListeners);
                        document.addEventListener('livewire:load', attachEventListeners);
                        document.addEventListener('livewire:update', attachEventListeners);
                    </script>
                </div>
            @elseif ($activeTab === 'tab3')

                <!-- Content for Tab 3 -->
                <div class="flex-grow  sm:p-3 p-1  md:px-10 lg:px-10 max-w-4xl mx-auto">
                    <!-- Add new products form -->

                    {{-- Invoice Logo --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow">
                        {{-- <h1>Invoice Logo</h1> --}}

                        <div class="w-2/3 flex  ">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>
                                {{-- <form wire:submit.prevent="companyInvoiceLogo" enctype="multipart/form-data">
                                    <div>
                                        <div class="mb-4">
                                            <p class="mt-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>

                                            <input wire:model.defer="companyLogoDataset.Invoice_logo_url" class="block w-11/12 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="buyer_logo" type="file">
                                        </div>
                                        <button type="submit" class=" text-white bg-[#007bff] hover:bg-[#0069d9] focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-1.5  focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600 hover:text-black">Upload</button>
                                    </div>
                                </form> --}}

                                <form wire:submit.prevent="companyInvoiceLogo" enctype="multipart/form-data" class="mt-2" >
                                    <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                    <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                        <input wire:model.defer="companyLogoDataset.invoice_logo_url" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="small_size" type="file" style="width: 100%;">
                                        <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                            @if ($showUploadButton)
                                            <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                type="submit">
                                                Upload
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>


                        <div id="image-preview" class="w-1/4 mx-auto">
                            <h2 class="font-semibold text-sm">Preview</h2>
                            <div class="relative">
                                @if(isset($companyLogoData['companyLogo']['invoiceTemporaryImageUrl']))
                                <img src="{{ $companyLogoData['companyLogo']['invoiceTemporaryImageUrl'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                @else
                                <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                @endif
                                @if(isset($companyLogoData['companyLogo']['invoiceTemporaryImageUrl']))
                                <button wire:click="removePreviewImage('po')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                    <div class="tooltip" data-tip="Remove Logo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                   {{-- Invoice Heading --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm">Invoice Heading </div>
                        <div class="items-center">
                            <div class="gap-4 flex items-center">
                                <input type="text" wire:model.defer="companyLogoDataset.invoice_heading" id="invoice_heading_input" class="bg-gray-50 w-3/4 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block mt-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Invoice " required>
                                {{-- <button type="submit" id="update_invoice_heading_button" wire:click="challanHeading" class="text-black hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-300 text-xs px-5 py-1.5 mt-3 ml-5 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-600 dark:focus:ring-gray-600 hover:text-black">Update</button> --}}
                                <button type="submit"
                                        id="update_invoice_heading_button"
                                        wire:click="challanHeading"
                                        class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-400">Update</button>
                            </div>
                        </div>
                    </div>

                    {{-- Terms And Conditions --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm">Terms and Conditions </div>
                        <div class="items-center">
                            <div class="grid gap-4">
                                <div class="gap-4 flex items-center">
                                    <textarea id="terms_conditions_textarea" placeholder="Terms & Conditions" name="content" class="mt-1 p-2 text-xs block w-3/4 border border-gray-300 text-dark rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.defer="termsAndConditionsData.content"></textarea>
                                    <button type="button" id="add_terms_conditions_button" wire:click="addInvoiceTerms" class="rounded-lg border border-gray-300 px-5 py-1.5 mt-3 ml-5 text-black hover:bg-orange text-xs hover:text-black">Add</button>
                                </div>
                            </div>
                        </div>
                        <div class="relative shadow-md sm:rounded-lg mt-3 overflow-auto" wire:ignore.self>
                            <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400 mb-10 mt-10">
                                <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 whitespace-nowrap py-1 normal-case">#</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Terms</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Date</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($invoiceData))
                                    @foreach($invoiceData as $index => $item)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                                        <td class="w-4 px-4">
                                            <div class="font-normal text-gray-500"> {{ $index+1 }} </div>
                                        </td>
                                        <td class="px-2 w-1/3 whitespace-nowrap">{{ $item->content ?? ''}}</td>
                                        <td class="px-2 w-5/12 whitespace-nowrap">{{ date('d-m-Y', strtotime($item->created_at ?? '')) }}</td>
                                        <td class="">
                                            <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg py-0.5 text-sm px-5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg></button>
                                            <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                    <li>
                                                        <a wire:click="tagModal({{ json_encode($item) }}, 'addTags')" class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">Edit Terms</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $item->id ?? '' }})" class="block px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">Signature Upload</h2>

                        <div class="mt-4 text-xs">
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionSeller" value="FooterStamp" class="mr-2 text-xs">
                                This is a computer-generated Challan and does not require a physical signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionSeller" value="Signature" class="mr-2 text-xs">
                                Signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionSeller" value="None" class="mr-2 text-xs">
                                None
                            </label>
                        </div>

                        @if($selectedOptionSeller == 'Signature')
                        <div class="flex">
                            <div class="w-2/3 flex mt-3">
                                <div>
                                    <form wire:submit.prevent="signatureSeller" enctype="multipart/form-data" class="mt-2" >
                                        <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" >Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                        <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                            <input wire:model.defer="companyLogoDataset.signature_seller" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"   type="file" style="width: 100%;">
                                            <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                                <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto" type="submit">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div  class="w-1/4 mx-auto">
                                <h2 class="font-semibold text-sm">Preview</h2>
                                <div class="relative">
                                    @if(isset($companyLogoData['companyLogo']['signature_seller']))
                                    <img src="{{ $companyLogoData['companyLogo']['signature_seller'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                    @else
                                    <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                    @endif
                                    @if(isset($companyLogoData['companyLogo']['signature_seller']))
                                    <button wire:click="removePreviewImage('challan')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                            {{--<div class="tooltip" data-tipRemove Logoedit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    </button> --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    {{-- Upload Signature --}}

                    {{-- <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow mt-3">

                        <div id="image-preview" class="w-1/4 mx-auto">
                            @if(isset($companyLogoData['companyLogo']['challanTemporaryImageUrl']))
                            <img src="{{ $companyLogoData['companyLogo']['challanTemporaryImageUrl'] }}" class="img-responsive w-3/4 h-auto object-contain">
                            @else
                            <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                            @endif
                        </div>
                        <div class="w-2/3 flex justify-end">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>
                                <form wire:submit.prevent="companyChallanLogo" enctype="multipart/form-data">
                                    <div>
                                        <div class="mb-4">
                                            <p class="mt-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>

                                            <input wire:model.defer="companyLogoDataset.challan_logo_url" class="block w-11/12 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="buyer_logo" type="file">
                                        </div>
                                        <button type="submit" class=" text-white bg-[#007bff] hover:bg-[#0069d9] focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-1.5  focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600 hover:text-black">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}

                     {{-- Powred By the Parchi --}}
                     {{-- <div class="bg-white border border-gray-300 rounded-lg p-2  shadow mt-3">
                                <div class="font-semibold mb-0 text-sm">Powered By The Parchi </div>
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer"  wire:model="companyLogoDataset.invoice_stamp" wire:click="challanToggle">
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="text-xs ml-2 text-gray-400">
                                                @if($companyLogoDataset['invoice_stamp'])
                                                    Visible
                                                @else
                                                    Not Visible
                                                @endif
                                            </span>
                                        </label>
                                    </div>
                                </div>
                    </div> --}}

                    <div class="mt-3">
                        {{-- <h1>Sender Panel Settings</h1> --}}
                        @livewire('setting.screens.panel-setting-manager', ['panel' => 'seller'])
                    </div>
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3 overflow-auto">
                        <h2 class="font-semibold text-sm">{{ ($companyLogoDataset['challan_heading'] ?? '') . ' Columns' }}</h2>
                        <div class="mt-4 text-xs">
                            <livewire:seller.screens.invoice-columns />
                        </div>
                    </div>

                    {{-- Unit --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3 overflow-auto">

                        <div class="mt-4 text-xs">
                            <livewire:setting.screens.add-unit :panel_type="'seller'"/>
                        </div>
                    </div>
                    <script>
                        function attachEventListeners() {
                            // Invoice Heading
                            const invoiceHeadingInput = document.getElementById('invoice_heading_input');
                            const updateInvoiceHeadingButton = document.getElementById('update_invoice_heading_button');
                            const initialInvoiceHeadingValue = invoiceHeadingInput.value;

                            invoiceHeadingInput.addEventListener('input', function() {
                                const hasChanged = invoiceHeadingInput.value !== initialInvoiceHeadingValue;
                                updateInvoiceHeadingButton.disabled = !hasChanged;
                                updateInvoiceHeadingButton.classList.toggle('bg-black', hasChanged);
                                updateInvoiceHeadingButton.classList.toggle('bg-gray-400', !hasChanged);
                            });

                            // Terms and Conditions
                            const termsConditionsTextarea = document.getElementById('terms_conditions_textarea');
                            const addTermsConditionsButton = document.getElementById('add_terms_conditions_button');

                            termsConditionsTextarea.addEventListener('input', function() {
                                const hasContent = termsConditionsTextarea.value.trim() !== '';
                                addTermsConditionsButton.disabled = !hasContent;
                                addTermsConditionsButton.classList.toggle('bg-gray-800', hasContent);
                                addTermsConditionsButton.classList.toggle('hover:bg-gray-900', hasContent);
                                addTermsConditionsButton.classList.toggle('dark:bg-gray-800', hasContent);
                                addTermsConditionsButton.classList.toggle('dark:hover:bg-gray-700', hasContent);
                                addTermsConditionsButton.classList.toggle('bg-gray-300', !hasContent);
                                addTermsConditionsButton.classList.toggle('dark:bg-gray-300', !hasContent);
                            });

                            // Initialize dropdowns
                            document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
                                const dropdownId = button.getAttribute('data-dropdown-toggle');
                                const dropdown = document.getElementById(dropdownId);

                                button.addEventListener('click', () => {
                                    dropdown.classList.toggle('hidden');
                                });
                            });
                        }

                        document.addEventListener('DOMContentLoaded', attachEventListeners);
                        document.addEventListener('livewire:load', attachEventListeners);
                        document.addEventListener('livewire:update', attachEventListeners);
                    </script>
                </div>

            @elseif ($activeTab === 'tab4')
                <!-- Content for Tab 4 -->
                <div class="flex-grow  sm:p-3 p-1  md:px-10 lg:px-10 max-w-4xl mx-auto">
                    <!-- Add new products form -->

                    {{-- Invoice Logo --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow">
                        {{-- <h1>Invoice Logo</h1> --}}

                        <div class="w-2/3 flex  ">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>
                                {{-- <form wire:submit.prevent="companyInvoiceLogo" enctype="multipart/form-data">
                                    <div>
                                        <div class="mb-4">
                                            <p class="mt-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>

                                            <input wire:model.defer="companyLogoDataset.Invoice_logo_url" class="block w-11/12 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="buyer_logo" type="file">
                                        </div>
                                        <button type="submit" class=" text-white bg-[#007bff] hover:bg-[#0069d9] focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-1.5  focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600 hover:text-black">Upload</button>
                                    </div>
                                </form> --}}

                                <form wire:submit.prevent="companyPOLogo" enctype="multipart/form-data" class="mt-2" >
                                    <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                    <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                        <input wire:model.defer="companyLogoDataset.po_logo_url" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="small_size" type="file" style="width: 100%;">
                                        <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                            @if ($showUploadButton)
                                            <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                type="submit">
                                                Upload
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>


                        <div id="image-preview" class="w-1/4 mx-auto">
                            <h2 class="font-semibold text-sm">Preview</h2>
                            <div class="relative">
                                @if(isset($companyLogoData['companyLogo']['poTemporaryImageUrl']))
                                <img src="{{ $companyLogoData['companyLogo']['poTemporaryImageUrl'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                @else
                                <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                @endif
                                @if(isset($companyLogoData['companyLogo']['poTemporaryImageUrl']))
                                <button wire:click="removePreviewImage('invoice')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                    <div class="tooltip" data-tip="Remove Logo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                </button>
                                @endif
                            </div>
                        </div>

                    </div>

                    {{-- Purchase Order Heading --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm">Purchase Order Heading </div>
                        <div class="items-center">
                            <div class="gap-4 flex items-center">
                                <input type="text" wire:model.defer="companyLogoDataset.po_heading" id="po_heading_input" class="bg-gray-50 w-3/4 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block mt-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Purchase Order " required>
                                 <button type="submit"
                                id="update_po_heading_button"
                                wire:click="challanHeading"
                                class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-400">Update</button>
                            </div>
                        </div>
                    </div>

                    {{-- Terms And Conditions --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm">Terms and Conditions </div>
                        <div class="items-center">
                            <div class="grid gap-4">
                                <div class="gap-4 flex items-center">
                                    <textarea id="terms_conditions_textarea" placeholder="Terms & Conditions" name="content" class="mt-1 p-2 text-xs block w-3/4 border border-gray-300 text-dark rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.defer="termsAndConditionsData.content"></textarea>
                                    <button type="button" id="add_terms_conditions_button" wire:click="addPoTerms" class="rounded-lg border border-gray-300 px-5 py-1.5 mt-3 ml-5 text-black hover:bg-orange text-xs hover:text-black">Add</button>
                                </div>
                            </div>
                        </div>
                        <div class="relative shadow-md sm:rounded-lg mt-3 overflow-auto" wire:ignore.self>
                            <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400 mb-10 mt-10">
                                <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 whitespace-nowrap py-1 normal-case">#</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Terms</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Date</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($poData))
                                    @foreach($poData as $index => $item)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                                        <td class="w-4 px-4">
                                            <div class="font-normal text-gray-500"> {{ $index+1 }} </div>
                                        </td>
                                        <td class="px-2 w-1/3 whitespace-nowrap">{{ $item->content ?? ''}}</td>
                                        <td class="px-2 w-5/12 whitespace-nowrap">{{ date('d-m-Y', strtotime($item->created_at ?? '')) }}</td>
                                        <td class="">
                                            <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg py-0.5 text-sm px-5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg></button>
                                            <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                    <li>
                                                        <a wire:click="tagModal({{ json_encode($item) }}, 'addTags')" class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">Edit Terms</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $item->id ?? '' }})" class="block px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Default Modal -->
                        <div id="edit-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore>
                            <div class="relative w-full max-w-lg max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <!-- Modal header -->
                                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                            Edit Terms and Conditions
                                            <button type="button" wire:click='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
                                        </h3>
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-6 space-y-6">
                                        <div>
                                            <label for="terms" class="block text-sm font-medium ">Terms</label>
                                            <input wire:model.defer="selectedContent" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div class="relative">
                                            <label for="receiver_user_id" class="block text-sm font-medium">Update</label>
                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button data-modal-hide="edit-modal" type="button" wire:click='updatePanelSeries()' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                                        <button data-modal-hide="edit-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">Signature Upload</h2>

                        <div class="mt-4 text-xs">
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionBuyer" value="FooterStamp" class="mr-2 text-xs">
                                This is a computer-generated Challan and does not require a physical signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionBuyer" value="Signature" class="mr-2 text-xs">
                                Signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionBuyer" value="None" class="mr-2 text-xs">
                                None
                            </label>
                        </div>

                        @if($selectedOptionBuyer == 'Signature')
                        <div class="flex">
                            <div class="w-2/3 flex mt-3">
                                <div>
                                    <form wire:submit.prevent="signatureBuyer" enctype="multipart/form-data" class="mt-2" >
                                        <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" >Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                        <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                            <input wire:model.defer="companyLogoDataset.signature_buyer" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"   type="file" style="width: 100%;">
                                            <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                                <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto" type="submit">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div  class="w-1/4 mx-auto">
                                <h2 class="font-semibold text-sm">Preview</h2>
                                <div class="relative">
                                    @if(isset($companyLogoData['companyLogo']['signature_buyer']))
                                    <img src="{{ $companyLogoData['companyLogo']['signature_buyer'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                    @else
                                    <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                    @endif
                                    @if(isset($companyLogoData['companyLogo']['signature_buyer']))
                                    <button wire:click="removePreviewImage('challan')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                            {{--<div class="tooltip" data-tipRemove Logoedit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    </button> --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Upload Signature --}}

                    {{-- <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow mt-3">

                        <div id="image-preview" class="w-1/4 mx-auto">
                            @if(isset($companyLogoData['companyLogo']['challanTemporaryImageUrl']))
                            <img src="{{ $companyLogoData['companyLogo']['challanTemporaryImageUrl'] }}" class="img-responsive w-3/4 h-auto object-contain">
                            @else
                            <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                            @endif
                        </div>
                        <div class="w-2/3 flex justify-end">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>
                                <form wire:submit.prevent="companyChallanLogo" enctype="multipart/form-data">
                                    <div>
                                        <div class="mb-4">
                                            <p class="mt-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>

                                            <input wire:model.defer="companyLogoDataset.challan_logo_url" class="block w-11/12 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="buyer_logo" type="file">
                                        </div>
                                        <button type="submit" class=" text-white bg-[#007bff] hover:bg-[#0069d9] focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-1.5  focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600 hover:text-black">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}

                     {{-- Powred By the Parchi --}}
                     {{-- <div class="bg-white border border-gray-300 rounded-lg p-2  shadow mt-3">
                                <div class="font-semibold mb-0 text-sm">Powered By The Parchi </div>
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer"  wire:model="companyLogoDataset.po_stamp" wire:click="challanToggle">
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="text-xs ml-2 text-gray-400">
                                                @if($companyLogoDataset['po_stamp'])
                                                    Visible
                                                @else
                                                    Not Visible
                                                @endif
                                            </span>
                                        </label>
                                    </div>
                                </div>
                    </div> --}}

                    <div class="mt-3">
                        {{-- <h1>Sender Panel Settings</h1> --}}
                        @livewire('setting.screens.panel-setting-manager', ['panel' => 'buyer'])
                    </div>

                    {{-- Unit --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3 overflow-auto">

                        <div class="mt-4 text-xs">
                            <livewire:setting.screens.add-unit :panel_type="'buyer'"/>
                        </div>
                    </div>

                    <script>
                        function attachEventListeners() {
                            // Purchase Order Heading
                            const poHeadingInput = document.getElementById('po_heading_input');
                            const updatePoHeadingButton = document.getElementById('update_po_heading_button');
                            const initialPoHeadingValue = poHeadingInput.value;

                            poHeadingInput.addEventListener('input', function() {
                                const hasChanged = poHeadingInput.value !== initialPoHeadingValue;
                                updatePoHeadingButton.disabled = !hasChanged;
                                updatePoHeadingButton.classList.toggle('bg-black', hasChanged);
                                updatePoHeadingButton.classList.toggle('bg-gray-400', !hasChanged);
                            });

                            // Terms and Conditions
                            const termsConditionsTextarea = document.getElementById('terms_conditions_textarea');
                            const addTermsConditionsButton = document.getElementById('add_terms_conditions_button');

                            termsConditionsTextarea.addEventListener('input', function() {
                                const hasContent = termsConditionsTextarea.value.trim() !== '';
                                addTermsConditionsButton.disabled = !hasContent;
                                addTermsConditionsButton.classList.toggle('bg-gray-800', hasContent);
                                addTermsConditionsButton.classList.toggle('hover:bg-gray-900', hasContent);
                                addTermsConditionsButton.classList.toggle('dark:bg-gray-800', hasContent);
                                addTermsConditionsButton.classList.toggle('dark:hover:bg-gray-700', hasContent);
                                addTermsConditionsButton.classList.toggle('bg-gray-300', !hasContent);
                                addTermsConditionsButton.classList.toggle('dark:bg-gray-300', !hasContent);
                            });

                            // Initialize dropdowns
                            document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
                                const dropdownId = button.getAttribute('data-dropdown-toggle');
                                const dropdown = document.getElementById(dropdownId);

                                button.addEventListener('click', () => {
                                    dropdown.classList.toggle('hidden');
                                });
                            });
                        }

                        document.addEventListener('DOMContentLoaded', attachEventListeners);
                        document.addEventListener('livewire:load', attachEventListeners);
                        document.addEventListener('livewire:update', attachEventListeners);
                    </script>

                </div>
                @elseif ($activeTab === 'tab5')
                <!-- Content for Tab 1 -->
                <div class="flex-grow  sm:p-3 p-1 md:px-10 lg:px-10 max-w-4xl mx-auto">
                    <!-- Add new products form -->

                    {{-- Challan Logo --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow">
                        {{-- <h1>Challan Logo</h1> --}}

                        <div class="w-2/3 flex  ">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>


                                <form wire:submit.prevent="receiptNoteLogo" enctype="multipart/form-data" class="mt-2" >
                                    <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                    <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                        <input wire:model.defer="companyLogoDataset.receipt_note_logo_url" class="block w-full ml-2 md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="small_size" type="file" style="width: 100%;">
                                        <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                            @if ($showUploadButton)
                                            <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                type="submit">
                                                Upload
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>

                        <div id="image-preview" class="w-1/4 mx-auto">
                            <h2 class="font-semibold text-sm">Preview</h2>
                            <div class="relative">
                                @if(isset($companyLogoData['companyLogo']['receiptNoteTemporaryImageUrl']))
                                <img src="{{ $companyLogoData['companyLogo']['receiptNoteTemporaryImageUrl'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                @else
                                <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                @endif
                                @if(isset($companyLogoData['companyLogo']['receiptNoteTemporaryImageUrl']))
                                <button wire:click="removePreviewImage('goodsReceipt')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                    <div class="tooltip" data-tip="Remove Logo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                        {{-- Challan Heading --}}
                        {{-- <div class="bg-white border border-gray-300 rounded-lg p-2  shadow mt-3">

                            <div class="font-semibold mb-0 text-sm"> {{ ($companyLogoDataset['receipt_note_heading'] ?? '') . ' Heading' }}</div>
                            <div class="items-center">
                                <div class="  gap-4 flex items-center">
                                    <input type="text" wire:model="companyLogoDataset.receipt_note_heading" id="receipt_note_heading" class="bg-gray-50 w-3/4 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block  mt-3  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Receipt Note" required>
                                    <button type="submit" wire:click="challanHeading" class="text-black  hover:bg-orange focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-300 text-xs font-medium px-5 py-1.5 mt-3 ml-5 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500  dark:hover:bg-gray-600 dark:focus:ring-gray-600 hover:text-black">Update</button>
                                </div>
                            </div>
                        </div> --}}
                        <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                            <div class="font-semibold mb-0 text-sm"> {{ ($companyLogoDataset['receipt_note_heading'] ?? '') . ' Heading' }}</div>
                            <div class="items-center">
                                <div class="gap-4 flex items-center">
                                    <input type="text"
                                        wire:model.defer="companyLogoDataset.receipt_note_heading"
                                        id="receipt_note_heading_input"
                                        class="bg-gray-50 w-3/4 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block mt-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Receipt Note"
                                        required>
                                    <button type="submit"
                                            id="update_receipt_note_heading_button"
                                            wire:click="challanHeading"
                                            class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-400">Update</button>
                                </div>
                            </div>
                        </div>

                        {{-- Terms And Conditions --}}
                        <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                            <div class="font-semibold mb-0 text-sm">Terms and Conditions </div>
                            <div class="items-center">
                                <div class="grid gap-4">
                                    <div class="gap-4 flex items-center">
                                        <textarea
                                            id="terms_conditions_textarea"
                                            placeholder="Terms & Conditions"
                                            name="content"
                                            class="mt-1 p-2 text-xs block w-3/4 border border-gray-300 text-dark rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            wire:model.defer="termsAndConditionsData.content"
                                        ></textarea>
                                        <button
                                            type="button"
                                            id="add_terms_conditions_button"
                                            wire:click="addReceiptNoteTerms"
                                            class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-300 dark:bg-gray-300"
                                        >
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <div class="relative  shadow-md sm:rounded-lg mt-3 overflow-auto mb-10" wire:ignore.self>

                            <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400 mb-10 mt-10">
                                <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 whitespace-nowrap py-1 normal-case">
                                            #
                                        </th>

                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">
                                            Terms
                                        </th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">
                                            Date
                                        </th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">
                                        Action
                                        </th>
                                    </tr>
                                </thead>



                                <tbody>
                                    {{-- @dd($challanData); --}}
                                    @if(isset($receiptNoteData))
                                    @foreach($receiptNoteData as $index => $item)
                                    <tr class="fixed-width bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                                        <td class="w-4 px-4">
                                            <div class="font-normal text-gray-500"> {{ $index+1 }} </div>
                                        </td>
                                        <td class="px-2 w-1/3 whitespace-nowrap">{{ $item->content ?? ''}}
                                        </td>

                                        <td class="px-2 w-5/12 whitespace-nowrap">

                                            {{ date('d-m-Y', strtotime($item->created_at ?? '')) }}

                                        </td>
                                        </td>
                                        <td class="">
                                            <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg py-0.5 text-sm px-5  m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg></button>
                                            <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                    <li>
                                                        <a x-data
                                                        wire:click="tagModal({{ json_encode($item) }}, 'addTags')"
                                                        class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                                                           Edit Terms
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $item->id ?? '' }})" class="block px-4   hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>

                        </div>
                        <!-- Default Modal -->
                        <div id="edit-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore>
                            <div class="relative w-full max-w-lg max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <!-- Modal header -->
                                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                            Edit Terms and Conditions
                                            <button type="button" wire:click='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
                                        </h3>
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-6 space-y-6">
                                        <div>
                                            <label for="terms" class="block text-sm font-medium ">Terms</label>
                                            <input wire:model.defer="selectedContent" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full  h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>



                                        <div class="relative">
                                            <label for="receiver_user_id" class="block text-sm font-medium">Update</label>


                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button data-modal-hide="edit-modal" type="button" wire:click='updatePanelSeries()' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                                        <button data-modal-hide="edit-modal" type="button"  class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Signature --}}

                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">Signature Upload</h2>
                        <div class="mt-4 text-xs">
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionReceiptNote" value="FooterStamp" class="mr-2 text-xs">
                                This is a computer-generated Receipt Note and does not require a physical signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionReceiptNote" value="Signature" class="mr-2 text-xs">
                                Signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOptionReceiptNote" value="None" class="mr-2 text-xs">
                                None
                            </label>
                        </div>

                        @if($selectedOption == 'Signature')
                        <div class="flex">
                            <div class="w-2/3 flex mt-3">
                                <div>
                                    <form wire:submit.prevent="signatureReceiptNote" enctype="multipart/form-data" class="mt-2" >
                                        <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" >Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                        <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                            <input wire:model.defer="companyLogoDataset.signature_receipt_note" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"   type="file" style="width: 100%;">
                                            <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                                <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto" type="submit">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div  class="w-1/4 mx-auto">
                                <h2 class="font-semibold text-sm">Preview</h2>
                                <div class="relative">
                                    @if(isset($companyLogoData['companyLogo']['signature_receipt_note']))
                                    <img src="{{ $companyLogoData['companyLogo']['signature_receipt_note'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                    @else
                                    <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                    @endif
                                    @if(isset($companyLogoData['companyLogo']['signature_receipt_note']))
                                    <button wire:click="removePreviewImage('challan')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                            {{--<div class="tooltip" data-tipRemove Logoedit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    </button> --}}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-3">
                        {{-- <h1>Sender Panel Settings</h1> --}}
                        @livewire('setting.screens.panel-setting-manager', ['panel' => 'receipt_note'])
                    </div>
                        {{-- Upload Signature --}}

                        {{-- <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                            <h2 class="font-semibold text-sm">Signature Upload</h2>
                            <div class="mt-4 text-xs">
                                <label class="flex items-center mb-2 ml-2">
                                    <input type="radio" wire:model="selectedOption"  value="FooterStamp" class="mr-2 text-xs">
                                    This is a computer-generated Challan and does not require a physical signature
                                </label>
                                <label class="flex items-center mb-2 ml-2">
                                    <input type="radio" wire:model="selectedOption"  value="Signature" class="mr-2 text-xs">
                                    Signature
                                </label>
                            </div>

                            @if($selectedOption == 'Signature')
                            <div class="flex">
                                <div class="w-2/3 flex mt-3">
                                    <div>
                                        <form wire:submit.prevent="signatureSender" enctype="multipart/form-data" class="mt-2" >
                                            <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" >Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                            <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                                <input wire:model.defer="companyLogoDataset.signature_sender" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"   type="file" style="width: 100%;">
                                                <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                                    <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto" type="submit">Upload</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div  class="w-1/4 mx-auto">
                                    <h2 class="font-semibold text-sm">Preview</h2>
                                    <div class="relative">
                                        @if(isset($companyLogoData['companyLogo']['signature_sender']))
                                        <img src="{{ $companyLogoData['companyLogo']['signature_sender'] }}" class="img-responsive w-3/4 h-auto object-contain">
                                        @else
                                        <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                        @endif
                                        @if(isset($companyLogoData['companyLogo']['signature_sender']))
                                        <button wire:click="removePreviewImage('challan')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">

                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        --}}


                        {{-- Powred By the Parchi --}}
                            {{-- @php
                            $activePlan = json_decode($activePlan)->Sender;
                            @endphp
                            @if (!empty($activePlan) && count($activePlan) > 0)
                            @foreach ($activePlan as $sender)
                                @if (!empty($sender->plan) && $sender->plan->price > 0)
                            <div class="bg-white border border-gray-300 rounded-lg p-2  shadow mt-3">



                                        <div class="font-semibold mb-0 text-sm">Powered By The Parchi </div>
                                        <div class="mt-2">
                                            <div class="flex items-center justify-between border-b pb-2">
                                                <p class="text-xs ml-2 text-gray-400 mr-4">
                                                    @if($companyLogoDataset['challan_stamp'])
                                                    Visible
                                                @else
                                                    Not Visible
                                                @endif
                                                </p>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer" wire:model="companyLogoDataset.challan_stamp" wire:click="challanToggle">
                                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                                </label>

                                            </div>
                                            <div class="font-semibold mb-0 text-sm mt-2" >Self Delivery </div>
                                            <div class="flex items-center justify-between border-b pb-2">
                                                <p class="text-xs ml-2 text-gray-400 mr-4">
                                                    @if($self_delivery)
                                                    Active
                                                @else
                                                    Inactive
                                                @endif
                                                </p>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer" wire:model="self_delivery" >
                                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                                </label>

                                            </div>
                                            <div class="font-semibold mb-0 text-sm mt-2" >Tag </div>
                                            <div class="flex items-center justify-between border-b pb-2">
                                                <p class="text-xs ml-2 text-gray-400 mr-4">
                                                    @if($tags)
                                                    Active
                                                @else
                                                    Inactive
                                                @endif
                                                </p>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer" wire:model="tags" >
                                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                                </label>

                                            </div>
                                            <div class="font-semibold mb-0 text-sm mt-2" >Payment Status </div>
                                            <div class="flex items-center justify-between border-b pb-2">
                                                <p class="text-xs ml-2 text-gray-400 mr-4">
                                                    @if($payment_status)
                                                    Active
                                                @else
                                                    Inactive
                                                @endif
                                                </p>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer" wire:model="payment_status" >
                                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                                </label>

                                            </div>
                                            <div class="font-semibold mb-0 text-sm mt-2" >Barcode </div>
                                            <div class="flex items-center justify-between border-b pb-2">
                                                <p class="text-xs ml-2 text-gray-400 mr-4">
                                                    @if($barcode)
                                                    Active
                                                @else
                                                    Inactive
                                                @endif
                                                </p>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer" wire:model="barcode">
                                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"></span>
                                                </label>

                                            </div>
                                        </div>


                            </div>
                            @endif
                            @break
                        @endforeach

                    @endif --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">{{ ($companyLogoDataset['receipt_note_heading'] ?? '') . ' Templates' }}</h2>
                        <div class="mt-4 text-xs">
                            @foreach($grnPdfFiles as $pdf)
                                <label class="flex items-center mb-2 ml-2 text-xs">

                                    <input type="radio" wire:model="selectedGrnTemplate" value="{{ $pdf['id'] }}" class="mr-2 cursor-pointer">
                                    Template {{ $pdf['id'] }}
                                    <a href="{{ $pdf['path'] }}" target="_blank" class="ml-2 text-blue-500">
                                        <div class="tooltip" data-tip="View Template">
                                            <svg class="w-[20px] h-[20px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-width="1" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                            <path stroke="currentColor" stroke-width="1" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </div>

                                    </a>
                                </label>
                            @endforeach
                        </div>

                        {{-- Unit --}}




                    </div>
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3 overflow-auto">

                        <div class="mt-4 text-xs">
                            <livewire:setting.screens.add-unit :panel_type="'receipt_note'"/>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3 overflow-auto">

                        <div class="mt-4 text-xs">
                            <h2 class="font-semibold text-sm"> {{ ($companyLogoDataset['receipt_note_heading'] ?? '') . ' Templates' }} </h2>

                            <livewire:setting.screens.template :panel_type="'receipt_note'"/>
                        </div>
                    </div>
                    {{-- <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">{{ ($companyLogoDataset['challan_heading'] ?? '') . ' Columns' }}</h2>
                        <div class="mt-4 text-xs">
                            <livewire:sender.screens.challan-columns />
                        </div>
                    </div> --}}
                    @endif
                    <script>
                        function attachEventListeners() {
                            // Receipt Note Heading
                            const receiptNoteHeadingInput = document.getElementById('receipt_note_heading_input');
                            const updateReceiptNoteHeadingButton = document.getElementById('update_receipt_note_heading_button');
                            const initialReceiptNoteHeadingValue = receiptNoteHeadingInput.value;

                            receiptNoteHeadingInput.addEventListener('input', function() {
                                const hasChanged = receiptNoteHeadingInput.value !== initialReceiptNoteHeadingValue;
                                updateReceiptNoteHeadingButton.disabled = !hasChanged;
                                updateReceiptNoteHeadingButton.classList.toggle('bg-black', hasChanged);
                                updateReceiptNoteHeadingButton.classList.toggle('bg-gray-400', !hasChanged);
                            });

                            // Terms and Conditions
                            const termsConditionsTextarea = document.getElementById('terms_conditions_textarea');
                            const addTermsConditionsButton = document.getElementById('add_terms_conditions_button');

                            termsConditionsTextarea.addEventListener('input', function() {
                                const hasContent = termsConditionsTextarea.value.trim() !== '';
                                addTermsConditionsButton.disabled = !hasContent;
                                addTermsConditionsButton.classList.toggle('bg-gray-800', hasContent);
                                addTermsConditionsButton.classList.toggle('hover:bg-gray-900', hasContent);
                                addTermsConditionsButton.classList.toggle('dark:bg-gray-800', hasContent);
                                addTermsConditionsButton.classList.toggle('dark:hover:bg-gray-700', hasContent);
                                addTermsConditionsButton.classList.toggle('bg-gray-300', !hasContent);
                                addTermsConditionsButton.classList.toggle('dark:bg-gray-300', !hasContent);
                            });
                        }

                        document.addEventListener('DOMContentLoaded', attachEventListeners);
                        document.addEventListener('livewire:load', attachEventListeners);
                        document.addEventListener('livewire:update', attachEventListeners);
                    </script>
                </div>

                @if ($openSearchModal == true)
                <div x-data="{ openSearchModal: @entangle('openSearchModal') }"
                x-show="openSearchModal"
                x-on:keydown.escape.window="openSearchModal = false"
                x-on:close.stop="openSearchModal = false"
                class="fixed inset-0 flex items-center justify-center z-50 max-w-full backdrop-blur-sm bg-black bg-opacity-60">
                <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                    <div class="mb-4">
                        <h1 class="text-lg text-black border-b border-gray-400">{{ $searchModalHeading }}</h1>
                        <div class="">
                            <div class="relative w-full min-w-[200px]  mt-5">
                                <textarea class="peer w-full text-black h-full bg-transparent text-blue-gray-700 font-sans font-normal outline outline-0 transition-all text-sm px-3 py-2.5 rounded-[7px] focus:border-gray-900"
                                placeholder=" "
                                wire:model.defer="selectedContent"></textarea>

                            </div>


                            </div>
                        @error('comment')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-wrap items-center justify-end shrink-0 text-blue-gray-500">
                        <button x-on:click="openSearchModal = false" wire:click="closeTagModal"
                                class="px-4 py-2.5 mr-1 font-sans text-xs text-red-500   transition-all rounded-lg middle none center hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                            Cancel
                        </button>
                        <button wire:click="editChallan"
                                class="middle none center rounded-lg bg-gray-900 py-2.5 px-4 font-sans text-xs   text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                            {{ $searchModalButtonText }}
                        </button>
                    </div>
                </div>
                </div>
                @elseif($activeTab === 'tab6')
                <div class="flex-grow  sm:p-3 p-1 md:px-10 lg:px-10 max-w-4xl mx-auto">
                    <!-- Add new products form -->
                    <div wire:loading class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2  bg-opacity-50 ">
                        <span class="loading loading-spinner loading-md"></span>
                    </div>
                    {{-- Challan Logo --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 flex flex-wrap md:flex-nowrap shadow">
                        {{-- <h1>Challan Logo</h1> --}}

                        <div class="w-2/3 flex  ">
                            <div>
                                <h2 class="font-semibold text-sm">Logo Upload</h2>


                                <form wire:submit.prevent="companyEstimateLogo" enctype="multipart/form-data" class="mt-2" >
                                    <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" id="file_input_help">Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                    <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                        <input wire:model.defer="companyLogoDataset.estimate_logo_url" class="block w-full ml-2 md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="small_size" type="file" style="width: 100%;">
                                        <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                            @if ($showUploadButton)
                                            <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                type="submit">
                                                Upload
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                        {{-- @dump($companyLogoData['companyLogo']['challanTemporaryImageUrl']) --}}
                        <div id="image-preview" class="w-1/4 mx-auto">
                            <h2 class="font-semibold text-sm">Preview</h2>
                            <div class="relative">
                                @if(isset($companyLogoData['companyLogo']['estimate_logo_url']))
                                    @php
                                        $logoPath = $companyLogoData['companyLogo']['estimate_logo_url'];
                                        try {
                                            // Generate a temporary URL that's valid for 5 minutes
                                            $logoUrl = Storage::disk('s3')->temporaryUrl($logoPath, now()->addMinutes(5));
                                        } catch (\Exception $e) {
                                            $logoUrl = null;
                                            $error = $e->getMessage();
                                        }
                                    @endphp
                                    @if($logoUrl)
                                        <img src="{{ $logoUrl }}" class="img-responsive w-3/4 h-auto object-contain" alt="Company Logo">
                                    @else
                                        <p class="text-red-500">Error loading image: {{ $error ?? 'Unknown error' }}</p>
                                    @endif
                                @else
                                    <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain" alt="Placeholder Logo">
                                @endif
                                @if(isset($companyLogoData['companyLogo']['estimate_logo_url']))
                                <button wire:click="removePreviewImage('challan')"  class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                    <div class="tooltip" data-tip="Remove Logo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Challan Heading --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm"> {{ ($companyLogoDataset['estimate_heading'] ?? '') . ' Heading' }}</div>
                        <div class="items-center">
                            <div class="gap-4 flex items-center">
                                <input type="text"
                                    id="estimate_heading_input"
                                    wire:model.defer="companyLogoDataset.estimate_heading"
                                    class="bg-gray-50 w-3/4 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block mt-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"

                                    required>
                                <button type="submit"
                                        id="update_estimate_heading_button"
                                        wire:click="challanHeading"
                                        class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-400">Update</button>
                            </div>
                        </div>
                    </div>

                    {{-- Terms And Conditions --}}
                    <div class="bg-white border border-gray-300 rounded-lg p-2 shadow mt-3">
                        <div class="font-semibold mb-0 text-sm">Terms and Conditions </div>
                        <div class="items-center">
                            <div class="grid gap-4">
                                <div class="gap-4 flex items-center">
                                    <textarea
                                        id="terms_conditions_textarea"
                                        placeholder="Terms & Conditions"
                                        name="content"
                                        class="mt-1 p-2 text-xs block w-3/4 border border-gray-300 text-dark rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        wire:model.defer="termsAndConditionsData.content"
                                    ></textarea>
                                    <button
                                        type="button"
                                        id="add_terms_conditions_button"
                                        wire:click="addEstimateTerms"
                                        class="middle none center rounded-lg py-2 px-4 mt-3 font-sans text-xs text-white shadow-md transition-all hover:shadow-lg active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-gray-300 dark:bg-gray-300"
                                    >
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative shadow-md sm:rounded-lg mt-3 overflow-auto mb-10" wire:ignore.self>
                            <table class="border dark:border-gray-600 w-full text-xs text-left text-gray-500 dark:text-gray-400 mb-10 mt-10">
                                <thead class="text-xs text-black bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 whitespace-nowrap py-1 normal-case">#</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Terms</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Date</th>
                                        <th scope="col" class="px-2 capitalize whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($estimateData))
                                    @foreach($estimateData as $index => $item)
                                    <tr class="fixed-width bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-3">
                                        <td class="w-4 px-4">
                                            <div class="font-normal text-gray-500"> {{ $index+1 }} </div>
                                        </td>
                                        <td class="px-2 w-1/3 whitespace-nowrap">{{ $item->content ?? ''}}</td>
                                        <td class="px-2 w-5/12 whitespace-nowrap">{{ date('d-m-Y', strtotime($item->created_at ?? '')) }}</td>
                                        <td class="">
                                            <button id="dropdownDefaultButton-{{ $index }}" data-dropdown-toggle="dropdown-{{ $index }}" class="text-white bg-gray-900 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg py-0.5 text-sm px-5 m-1 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800" type="button">Action <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg></button>
                                            <div id="dropdown-{{ $index }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 border-2">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton-{{ $index }}">
                                                    <li>
                                                        <a x-data wire:click="tagModal({{ json_encode($item) }}, 'addTags')" class="block px-4 py-1 hover:bg-orange dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">Edit Terms</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" wire:click="$emit('triggerDelete', {{ $item->id ?? '' }})" class="block px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Default Modal -->
                        <div id="edit-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full" wire:ignore>
                            <div class="relative w-full max-w-lg max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <!-- Modal header -->
                                    <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                            Edit Terms and Conditions
                                            <button type="button" wire:click='resetChallanSeries()' class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-modal">
                                        </h3>
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-6 space-y-6">
                                        <div>
                                            <label for="terms" class="block text-sm font-medium ">Terms</label>
                                            <input wire:model.defer="selectedContent" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full h-10 rounded-lg focus:ring-blue-500 focus:border-blue-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        </div>
                                        <div class="relative">
                                            <label for="receiver_user_id" class="block text-sm font-medium">Update</label>
                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button data-modal-hide="edit-modal" type="button" wire:click='updatePanelSeries(1)' class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                                        <button data-modal-hide="edit-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{-- <h1>Sender Panel Settings</h1> --}}
                        @livewire('setting.screens.panel-setting-manager', ['panel' => 'seller'])
                    </div>

                    {{-- Upload Signature --}}

                    {{-- <div class="bg-white border border-gray-300 rounded-lg p-2 flex-wrap md:flex-nowrap shadow mt-3">
                        <h2 class="font-semibold text-sm">Signature Upload</h2>
                        <div class="mt-4 text-xs">
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOption" value="FooterStamp" class="mr-2 text-xs">
                                This is a computer-generated Challan and does not require a physical signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOption" value="Signature" class="mr-2 text-xs">
                                Signature
                            </label>
                            <label class="flex items-center mb-2 ml-2">
                                <input type="radio" wire:model="selectedOption" value="None" class="mr-2 text-xs">
                                None
                            </label>
                        </div>
                        @if($selectedOption == 'Signature')
                        <div class="flex">
                            <div class="w-2/3 flex mt-3">
                                <div>
                                    <form wire:submit.prevent="signatureSender" enctype="multipart/form-data" class="mt-2" >
                                        <p class="mb-1 text-[0.6rem] text-gray-500 dark:text-gray-300" >Select Image (PNG/JPG/JPEG , MAX 200 KB, Max dimension: 700*100 pxl)</p>
                                        <div class="relative text-xs md:w-96 flex flex-col sm:flex-row">
                                            <input wire:model.defer="companyLogoDataset.signature_sender" class="block w-full md:w-96 mb-5 p-1 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"   type="file" style="width: 100%;">
                                            <div class="flex items-center pr-3 mt-2 sm:mt-0 sm:absolute sm:inset-y-0 sm:right-0 pb-4">
                                                @if ($showUploadButton)
                                                <button class="bg-gray-800 hover:bg-orange text-white hover:text-black py-2 px-3 rounded w-full sm:w-auto"
                                                    type="submit">
                                                    Upload
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="w-1/4 mx-auto">
                                <h2 class="font-semibold text-sm">Preview</h2>
                                <div class="relative">
                                    @if(isset($companyLogoData['companyLogo']['signature_sender']))
                                        @php
                                            $signatureSenderPath = $companyLogoData['companyLogo']['signature_sender'];
                                            $signatureSenderUrl = Storage::disk('s3')->url($signatureSenderPath);
                                        @endphp
                                        <img src="{{ $signatureSenderUrl }}" class="img-responsive w-3/4 h-auto object-contain">
                                    @else
                                        <img src="https://theparchi.com/sender_assets/thumbnails/placeholder.jpg" class="img-responsive w-3/4 h-auto object-contain">
                                    @endif
                                    @if(isset($companyLogoData['companyLogo']['signature_sender']))
                                        <button wire:click="removePreviewImage('challan')" class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div> --}}





                </div>
                <script>
                    function attachEventListeners() {
                        initFlowbite();
                        // Challan Heading
                        const challanHeadingInput = document.getElementById('estimate_heading_input');
                        const updateChallanHeadingButton = document.getElementById('update_estimate_heading_button');
                        const initialChallanHeadingValue = challanHeadingInput.value;

                        challanHeadingInput.addEventListener('input', function() {
                            const hasChanged = challanHeadingInput.value !== initialChallanHeadingValue;
                            updateChallanHeadingButton.disabled = !hasChanged;
                            updateChallanHeadingButton.classList.toggle('bg-black', hasChanged);
                            updateChallanHeadingButton.classList.toggle('bg-gray-400', !hasChanged);
                        });

                        // Terms and Conditions
                        const termsConditionsTextarea = document.getElementById('terms_conditions_textarea');
                        const addTermsConditionsButton = document.getElementById('add_terms_conditions_button');

                        termsConditionsTextarea.addEventListener('input', function() {
                            const hasContent = termsConditionsTextarea.value.trim() !== '';
                            addTermsConditionsButton.disabled = !hasContent;
                            addTermsConditionsButton.classList.toggle('bg-gray-800', hasContent);
                            addTermsConditionsButton.classList.toggle('hover:bg-gray-900', hasContent);
                            addTermsConditionsButton.classList.toggle('dark:bg-gray-800', hasContent);
                            addTermsConditionsButton.classList.toggle('dark:hover:bg-gray-700', hasContent);
                            addTermsConditionsButton.classList.toggle('bg-gray-300', !hasContent);
                            addTermsConditionsButton.classList.toggle('dark:bg-gray-300', !hasContent);
                        });
                    }

                    document.addEventListener('DOMContentLoaded', attachEventListeners);
                    document.addEventListener('livewire:load', attachEventListeners);
                    document.addEventListener('livewire:update', attachEventListeners);
                </script>
                @endif

                <script type="text/javascript">
                    document.addEventListener('livewire:load', function(e) {
                        @this.on('triggerDelete', id => {
                            Swal.fire({
                                title: "Are you sure?",
                                text: "Are you sure you want to delete?",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#6fc5e0",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Delete",
                            }).then((result) => {
                                if (result.value) {
                                    @this.call('deleteChallanTerms', id);
                                    location.reload();
                                    console.log('hello');
                                } else {
                                    console.log("Canceled");
                                }
                            });
                        });
                    });

                    document.addEventListener('livewire:load', function(e) {
                    @this.on('triggerDelete', id => {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "Are you sure you want to delete?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#6fc5e0",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Delete",
                        }).then((result) => {
                            if (result.value) {
                                @this.call('deleteInvoiceTerms', id);
                                location.reload();
                                console.log('hello');
                            } else {
                                console.log("Canceled");
                            }
                        });
                    });
                    document.addEventListener('livewire:load', function () {
                        Livewire.hook('message.processed', (message, component) => {
                            initFlowbite(); // Your existing initialization for Flowbite

                            // Attempt to safely interact with Alpine components
                            Alpine.discoverUninitializedComponents((el) => {
                                Alpine.initializeComponent(el);
                            });
                        });
                    });
                });
                document.addEventListener('livewire:load', function () {
                    initFlowbite();
                    Livewire.on('reloadTab1', function (tabName) {
                        console.log('Tab 1 reloaded');
                    });
                    Livewire.on('reloadTab2', function (tabName) {
                        console.log('Tab 2 reloaded');
                    });
                    Livewire.on('reloadTab3', function (tabName) {
                        console.log('Tab 3 reloaded');
                    });
                    Livewire.on('reloadTab4', function (tabName) {
                        console.log('Tab 4 reloaded');
                    });
                    Livewire.on('reloadTab5', function (tabName) {
                        console.log('Tab 5 reloaded');
                    });

                    Livewire.on('redirectWithTab', function (tabName) {
                        const currentUrl = new URL(window.location.href);
                        console.log(currentUrl);
                        const currentTab = currentUrl.searchParams.get('tab');
                        console.log(currentTab);
                        if (currentTab !== tabName) {
                            window.location.href = currentUrl.origin + currentUrl.pathname + '?tab=' + tabName;
                            console.log('redirected');
                        }
                    });
                });
                </script>
        </div>
</div>
