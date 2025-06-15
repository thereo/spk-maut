<x-filament::page>
    {{ $this->form }}

    <div class="fi-btn-label">
        <x-filament::button
            type="save"
            wire:click="save"
        >
            Save Scores
        </x-filament::button>
    </div>
</x-filament::page>
