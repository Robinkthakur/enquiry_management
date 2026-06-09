<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
            {{ $this->form }}
        </div>

        <div class="flex justify-start">
            <button type="submit" 
                    class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-primary bg-primary-600 hover:bg-primary-500 text-white shadow-sm py-2.5 px-6 inline-flex text-sm">
                Save Settings
            </button>
        </div>
    </form>
</x-filament-panels::page>
