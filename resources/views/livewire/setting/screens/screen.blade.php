<div id="dynamic-view"> 
        @if ($errorMessage)
        {{-- {{dd($errorMessage)}} --}}
        @foreach (json_decode($errorMessage) as $error)
        <div class="p-4 text-sm text-red-800 mb-1 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
            <span class="font-medium">Error:</span> {{ $error[0] }}
        </div>
        @endforeach
        @endif
        @if ($successMessage)
        <div class="p-4 text-sm text-[#155724] rounded-lg bg-[#d4edda] dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Success:</span> {{ $successMessage }}
        </div>
        @endif

     

<div class="max-w-5xl mt-4 mx-auto ">
    <div class="max-w-sm mt-2 p-3 mb-2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <h3 class="text-xl font-bold text-black">Permissions</h3>
        <p class="text-gray-600 text-sm">Manage what users can see or do in your store.</p>
    </div>
    <div class="rounded-xl border bg-white p-3 border-gray-200  shadow  dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
        <div class="border-gray-300">
            <!-- <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Store owner</h2>
                <button class="text-blue-500 hover:text-blue-700">Transfer ownership</button>
            </div> -->
            <div class="mt-2 flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-300">
                    <span class="text-sm text-gray-600">{{ strtoupper(substr(Auth::user()->name, 0, 1) . substr(strrchr(Auth::user()->name, ' '), 1, 1)) }}
                    </span>
                </div>
                <div class="ml-4">
                    <p>
                        <a href="/store/0f4218/settings/account/87796646128" class="text-blue-500"> {{ucwords(Auth::user()->name)}} </a>
                    </p>
                    <p class="text-xs text-gray-500">Last login was {{ \Carbon\Carbon::parse(Auth::user()->last_login_at)->format('l, F j, Y g:i A e') }}</p>
    {{-- <p> {{Auth::user()->last_login_at}} </p> --}}
                    {{-- <p>Last login was {{ \Carbon\Carbon::parse(Auth::user()->last_login_at)->format('l, F j, Y g:i A e') }}</p> --}}
                </div>
            </div>
            {{-- <div class="mt-4">
                <p class="text-gray-500 text-sm">Admin have some permissions that can't be assigned to team. Learn more about <a href="" target="_blank" class="text-blue-500 hover:underline">admin permissions</a>.</p>
            </div> --}}
        </div>
        
    </div>
    <div class="max-w-sm mt-2 p-3 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <a href="#" class="flex items-center mb-2 ">
            <svg class="h-4 w-6 mr-1" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ScheduleSendIcon" tabindex="-1" title="ScheduleSend"><path d="M16.5 12.5H15v4l3 2 .75-1.23-2.25-1.52zM16 9 2 3v7l9 2-9 2v7l7.27-3.11C10.09 20.83 12.79 23 16 23c3.86 0 7-3.14 7-7s-3.14-7-7-7m0 12c-2.75 0-4.98-2.22-5-4.97v-.07c.02-2.74 2.25-4.97 5-4.97 2.76 0 5 2.24 5 5S18.76 21 16 21"></path></svg>
            {{-- <h5 class=" text-xl font-bold tracking-tight text-gray-900 dark:text-white">Delete my account </h5> --}}
            <h3 class="text-xl font-bold text-black">Delete my account</h3>
        </a>
        <p class="mb-3 font-normal text-sm text-gray-700 dark:text-gray-400">Request For Account Deletion</p>
        <button type="button" 
           
            class="rounded-xl bg-[#f0ac49] px-5 py-1.5 text-sm  text-white hover:bg-[#a0553e] focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900">
            Delete
        </button>
    </div>

    {{-- <div class="rounded-xl mt-4 border bg-white p-3 border-gray-200  shadow  dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
        <div class="border-gray-300 ">
            <h2 class="text-lg font-semibold">Team (0 of 2)</h2>

            <div class="mt-4">
                <p>Customize what your team members can edit and access. You can add up to 2 team members on this plan. <a href="/store/0f4218/settings/account/plan" class="text-blue-500 hover:underline">Compare plans.</a></p>
                <br />
                <a href="{{route('teams')}}" class="rounded-xl bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">Add Team</a>
            </div>
        </div>

    </div> --}}

    {{-- <div class="rounded-xl mt-4 border bg-white p-3 border-gray-200  shadow  dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
        <div class="border-gray-300 py-4">
            <h2 class="text-lg font-semibold">Collaborators</h2>
            <div class="mt-4">
                <p>These give designers, developers, and marketers access to your Shopify admin. They don't count toward your staff limit. Learn more about <a href="https://help.shopify.com/en/manual/your-account/staff-accounts/collaborator-accounts" target="_blank" class="text-blue-500 hover:underline">collaborators</a>.</p>
            </div>

            <div class="mt-4">
                <fieldset>
                    <legend class="text-sm font-semibold">Collaborator Requests</legend>
                    <label class="mt-2 block">
                        <input type="radio" name="collaborator_request" value="Anyone" class="mr-2" />
                        Anyone can send a collaborator request
                    </label>
                    <label class="mt-2 block">
                        <input type="radio" name="collaborator_request" value="WithCode" class="mr-2" />
                        Only people with a collaborator request code can send a collaborator request
                    </label>
                </fieldset>
            </div>
        </div>
    </div> --}}
</div>
 
 
</div>
