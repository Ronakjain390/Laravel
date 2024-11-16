 {{-- @include('components.assets.tableComponent.table') --}}
 <div wire:init="loadData">
     @if ($this->isLoading)
         <!-- Show nothing or a minimal loading state -->
         @include('livewire.sender.screens.placeholders')
     @else
         <livewire:sender.screens.detailed-received-challan lazy />
     @endif
 </div>
