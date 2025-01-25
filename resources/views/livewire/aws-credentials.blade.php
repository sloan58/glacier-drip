<x-filament-breezy::grid-section md=2 title="AWS Credentials" description="Enter your AWS API credentials to integrate with Glacier and S3">
    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">

            {{ $this->form }}

            <div class="text-right">
{{--                <x-filament::button color="danger" type="submit" form="submit" class="align-right">--}}
{{--                    Delete--}}
{{--                </x-filament::button>--}}
                {{ $this->deleteAction }}
            </div>
        </form>
    </x-filament::card>
    <x-filament-actions::modals />
</x-filament-breezy::grid-section>
