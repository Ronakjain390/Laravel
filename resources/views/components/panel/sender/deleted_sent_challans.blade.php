{{-- <div wire:init="loadData"> --}}
    {{-- @if ($this->isLoading) --}}
    {{-- @dd("$this->isLoading") --}}
        <!-- Show nothing or a minimal loading state -->
        {{-- @include('livewire.sender.screens.placeholders') --}}
    {{-- @else --}}
        <livewire:sender.screens.deleted-sent-challans lazy />
    {{-- @endif --}}
{{-- </div> --}}
