<div>

    <div 
        x-data="{ show: true }"
        x-show="show"
        x-on:open-modal.window="show = true"
        x-on:close-modal.window="show = false"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full""    
    >
    <div class="relative p-4 w-full max-w-md max-h-full">

    </div>

    <div class="bg-white rounded m-auto fixed inset-0 max-w-md" style="max-height: 500px">
        <div>Header</div>
        <div>Body</div>
    </div>

    </div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    {{-- <div x-show="$wire.show" id="signature" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <!-- ... existing code ... -->

        <form id="signature-form" method="POST" action="{{ url('/signature/save') }}">
            @csrf
            <input type="hidden" name="signatureData" id="signature-data" value="">
            <input type="hidden" name="id" value="{{ $signatureId }}">

            <button type="submit">Save Signature</button>
        </form>
    </div> --}}
    <script>
        var canvas = document.getElementById('signature-board');
    var ctx = canvas.getContext('2d');

    var mouse = { x: 0, y: 0 };
    var isDrawing = false;

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    function startDrawing(event) {
        isDrawing = true;
        var rect = canvas.getBoundingClientRect();
        mouse.x = event.clientX - rect.left;
        mouse.y = event.clientY - rect.top;
    }

    function draw(event) {
        if (isDrawing) {
            var rect = canvas.getBoundingClientRect();
            ctx.beginPath();
            ctx.moveTo(mouse.x, mouse.y);
            mouse.x = event.clientX - rect.left;
            mouse.y = event.clientY - rect.top;
            ctx.lineTo(mouse.x, mouse.y);
            ctx.stroke();
        }
    }

    function stopDrawing() {
        isDrawing = false;
        document.getElementById('signature-data').value = canvas.toDataURL();
    }
    </script>
</div>
