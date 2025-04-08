<div>


    <flux:heading size="xl">Create New Range</flux:heading>

    <form wire:submit="save" class="flex flex-col space-y-6">

        <flux:input wire:model="name" label="Name" required/>
        <flux:input wire:model="ip" label="IP" required />
        <flux:input wire:model.live="cidr" label="CIDR" />
        <flux:button type="submit" class="mt-4" variant="primary">Add</flux:button>
    </form>






</div>
