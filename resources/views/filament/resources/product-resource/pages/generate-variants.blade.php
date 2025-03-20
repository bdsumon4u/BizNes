<div>
GV
@dump($this)
GV {{ $this->record->id }}
    <button wire:click="unfound">Unfound</button>
    {{-- @livewire(\App\Filament\Resources\ProductResource\Pages\GenerateVariants::class, ['record' => 1]) --}}
</div>