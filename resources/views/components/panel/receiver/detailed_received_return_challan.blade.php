<div class="">
    <div class="rounded-lg bg-gray-100 p-2">
        <div wire:init="loadData" class="rounded-md pb-2 shadow-sm" role="group">
          
      
                    @if ($this->isLoading)
                    <!-- Show nothing or a minimal loading state -->
                    @include('livewire.sender.screens.placeholders')
                @else
                <livewire:receiver.content.detailed-received-challan>
                @endif
                
    </div>
</div>
</div>
