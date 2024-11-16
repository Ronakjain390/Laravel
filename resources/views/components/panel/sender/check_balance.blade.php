 <script>
     function confirmAcceptMargin(challanId) {
         Swal.fire({
             title: 'Are you sure?',
             text: "You won't be able to revert this!",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Yes, accept it!'
         }).then((result) => {
             if (result.isConfirmed) {
                 // Call your function to accept the margin here
                 @this.acceptMargin(challanId);
             }
         })
     }
 </script>

 <div wire:init="loadData">
     @if ($this->isLoading)
         <!-- Show nothing or a minimal loading state -->
         @include('livewire.sender.screens.placeholders')
     @else
         <livewire:sender.screens.check-balance lazy />
     @endif
 </div>
