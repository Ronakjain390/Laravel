
 <div class=" max-w-6xl gap-4 h-full mx-auto">


@if($showModal)

<div  id="alert-border-3" class="flex items-center p-2 mb-4 text-green-800 border-t-4 border-green-300 bg-[#d4edda] dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
      <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
    </svg>
    <div class="ms-3 text-sm font-medium">
      Your Demo has been scheduled and our team shall connect with you shortly.
    </div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-border-3" aria-label="Close">
      <span class="sr-only">Dismiss</span>
      <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
      </svg>
    </button>
</div>
@endif

<div class="flex flex-wrap ">
  
        <div class="max-w-sm mt-2 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <a href="#" class="flex items-center mb-2 ">
                <svg class="h-4 w-6 mr-1" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ScheduleSendIcon" tabindex="-1" title="ScheduleSend"><path d="M16.5 12.5H15v4l3 2 .75-1.23-2.25-1.52zM16 9 2 3v7l9 2-9 2v7l7.27-3.11C10.09 20.83 12.79 23 16 23c3.86 0 7-3.14 7-7s-3.14-7-7-7m0 12c-2.75 0-4.98-2.22-5-4.97v-.07c.02-2.74 2.25-4.97 5-4.97 2.76 0 5 2.24 5 5S18.76 21 16 21"></path></svg>
                <h5 class=" text-xl font-bold tracking-tight text-gray-900 dark:text-white">Book a Demo </h5>
            </a>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Allow us to guide you through all the featueres of TheParchi</p>
            <button type="button" 
                wire:click="bookDemo"
                class="rounded-xl bg-[#f0ac49] px-5 py-1.5 text-sm  text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
                Book Demo
            </button>
        </div>
   
   
        <div class="max-w-sm mt-2 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <a href="#" class="flex items-center mb-2 ">
                <svg class="h-4 w-6 mr-1" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="MailIcon" tabindex="-1" title="Mail"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2m0 4-8 5-8-5V6l8 5 8-5z"></path></svg>
                <h5 class=" text-xl font-bold tracking-tight text-gray-900 dark:text-white">Mail Us </h5>
            </a>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Write us for any help we will revert you soon</p>
            <a href="mailto:contact@theparchi.com">
                <button type="button" 
                        class="rounded-xl bg-[#f0ac49] px-5 py-1.5 text-sm text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
                    Mail
                </button>
            </a>
        </div>
    
        <div class="max-w-sm mt-2 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <a href="#" class="flex items-center mb-2 ">
                <svg class="h-4 w-6 mr-1" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="WhatsAppIcon" tabindex="-1" title="WhatsApp"><path d="M16.75 13.96c.25.13.41.2.46.3.06.11.04.61-.21 1.18-.2.56-1.24 1.1-1.7 1.12-.46.02-.47.36-2.96-.73-2.49-1.09-3.99-3.75-4.11-3.92-.12-.17-.96-1.38-.92-2.61.05-1.22.69-1.8.95-2.04.24-.26.51-.29.68-.26h.47c.15 0 .36-.06.55.45l.69 1.87c.06.13.1.28.01.44l-.27.41-.39.42c-.12.12-.26.25-.12.5.12.26.62 1.09 1.32 1.78.91.88 1.71 1.17 1.95 1.3.24.14.39.12.54-.04l.81-.94c.19-.25.35-.19.58-.11l1.67.88M12 2a10 10 0 0 1 10 10 10 10 0 0 1-10 10c-1.97 0-3.8-.57-5.35-1.55L2 22l1.55-4.65A9.969 9.969 0 0 1 2 12 10 10 0 0 1 12 2m0 2a8 8 0 0 0-8 8c0 1.72.54 3.31 1.46 4.61L4.5 19.5l2.89-.96A7.95 7.95 0 0 0 12 20a8 8 0 0 0 8-8 8 8 0 0 0-8-8z"></path></svg>
                <h5 class=" text-xl font-bold tracking-tight text-gray-900 dark:text-white">WhatsApp Us   </h5>
            </a>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Allow us to guide you through all the featueres of TheParchi</p>
            <a href="https://wa.me/message/6A2OOL4CK53KH1">
                <button type="button" 
                        class="rounded-xl bg-[#f0ac49] px-5 py-1.5 text-sm text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
                    WhatsApp
                </button>
            </a>
        </div>
  
    </div>
</div>
</div>