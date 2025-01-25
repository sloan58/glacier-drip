<x-filament-panels::page>
    <p class="font-semibold">Before you get started, you'll need to connect your AWS account.</p>

    <form wire:submit="submit">
        {{ $this->form }}
    </form>

    <script>
        localStorage.setItem('isOpen', 'false')
    </script>
</x-filament-panels::page>
