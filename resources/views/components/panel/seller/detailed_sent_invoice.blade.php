<div class="">
    <div class="rounded-lg bg-gray-100 p-2" wire:init="loadData">
        
        @if ($this->isLoading)
                    <!-- Show nothing or a minimal loading state -->
                    @include('livewire.sender.screens.placeholders')
                @else
        <livewire:seller.screens.detailed-sent-invoice>
        @endif
    </div>
</div>
</div>
