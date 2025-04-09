<div wire:poll.5s class="space-y-6">
   @if ($range->hosts->isNotEmpty())
       <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
           @foreach ($range->hosts as $host)
               <x-host-card :host="$host" />
           @endforeach
       </div>
   @else
       <div class="text-gray-600 text-center">No available hosts, scan</div>
   @endif

   <div class="text-center">
       <flux:button wire:click="scanHosts" variant="primary">
           Scan Hosts
       </flux:button>
   </div>
</div>
