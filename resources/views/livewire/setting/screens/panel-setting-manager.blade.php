<div class="bg-white p-4 rounded-lg shadow-md w-full text-xs mx-auto border border-gray-300">
    @foreach ($availableSettings as $key => $defaultValue)
        <div class="flex flex-col p-2 border-b last:border-none">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-900">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                    @if ($key === 'powered_by_the_parchi')
                        <p class="text-gray-500 text-sm">{{ $settings[$key] ? 'Visible' : 'Not Visible' }}</p>
                    @else
                        <p class="text-gray-500 text-sm">{{ $settings[$key] ? 'Active' : 'Inactive' }}</p>
                    @endif
                </div>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="settings.{{ $key }}" wire:change="updateSetting('{{ $key }}')" class="sr-only peer {{ $errors->has($key) || isset($errorMessage[$key]) ? 'peer-not-allowed' : '' }}">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 peer-checked:bg-blue-600 relative {{ $errors->has($key) || isset($errorMessage[$key]) ? 'peer-not-allowed' : 'peer-checked:after:translate-x-full' }} after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                </label>
            </div>
            @if (isset($errorMessage[$key]))
                <p class="text-red-500 text-xs mt-2">{{ $errorMessage[$key] }}</p>
            @endif
        </div>
    @endforeach
</div>
