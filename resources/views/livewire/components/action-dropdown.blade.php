<!-- resources/views/livewire/components/action-dropdown.blade.php -->

<div class="dropdown">
    <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        Action
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <li>
            <button class="dropdown-item" wire:click="edit({{ $id }})">
                Edit
            </button>
        </li>
        <li>
            <button class="dropdown-item text-danger" wire:click="delete({{ $id }})">
                Delete
            </button>
        </li>
    </ul>
</div>
