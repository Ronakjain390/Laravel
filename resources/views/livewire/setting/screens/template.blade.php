<div>
    {{-- <h2 class="font-semibold text-sm">Receipt Note Template</h2> --}}
    <div id="successModalTemplate" style="display: none;">
        <div class="modal-content">
            <p class="mt-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" id="successMessageTemplate" style="color: #155724;"></p>
        </div>
    </div>
    
    <div class="mt-4 text-xs">
        <label class="flex items-center mb-2 ml-2">
            <input type="radio" wire:model="template" value="default" class="mr-2 text-xs">
            Default
        </label>
        <label class="flex items-center mb-2 ml-2">
            <input type="radio" wire:model="template" value="form" class="mr-2 text-xs">
           Form
        </label>
        {{-- <label class="flex items-center mb-2 ml-2">
            <input type="radio" wire:model="selectedOptionReceiptNote" value="None" class="mr-2 text-xs">
            None
        </label> --}}
    </div>
    <script>
        window.addEventListener('show-success-message-template', event => {
            console.log('Event received:', event.detail); // Debugging line
    
            // Set the message in the modal
            document.getElementById('successMessageTemplate').textContent = event.detail.messageTemplate;
            
            // Show the modal (you might need to use your specific modal's show method)
            document.getElementById('successModalTemplate').style.display = 'block';
            
            // Optionally, hide the modal after a few seconds
            setTimeout(() => {
                document.getElementById('successModalTemplate').style.display = 'none';
            }, 5000);
        });
    </script>
</div>
