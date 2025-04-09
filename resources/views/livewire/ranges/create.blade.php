<div>
    <flux:heading size="xl">{{ $range ? 'Edit' : 'Create' }} Range</flux:heading>

    <form wire:submit.prevent="save" class="flex flex-col gap-y-6">
        <flux:input wire:model="name" label="Name" required />
        <flux:input wire:model="ip" label="IP" required />
        <flux:input wire:model="cidr" label="CIDR" />

        <flux:button type="submit" variant="primary">
            {{ $range ? 'Update' : 'Create' }} Range
        </flux:button>
    </form>
</div>
