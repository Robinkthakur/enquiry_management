@php
    $report = $this->getReportData();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filters Card -->
        <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
            {{ $this->form }}
        </div>

        <!-- Preview & Export Card -->
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
            <!-- Header Actions -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <h3 class="text-md font-bold text-gray-900 dark:text-white">Report Preview Data</h3>
                <div class="flex items-center space-x-2">
                    <!-- Export CSV Button -->
                    <button type="button" 
                            wire:click="exportCsv"
                            class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-success bg-success-600 hover:bg-success-500 text-white shadow-sm py-2 px-4 inline-flex text-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </button>

                    <!-- Export PDF Button -->
                    <button type="button" 
                            wire:click="exportPdf"
                            class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-primary bg-primary-600 hover:bg-primary-500 text-white shadow-sm py-2 px-4 inline-flex text-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF
                    </button>
                </div>
            </div>

            <!-- Preview Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-800 dark:text-gray-400">
                        <tr>
                            @foreach($report['headers'] as $header)
                                <th class="px-6 py-4 font-semibold">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($report['rows'] as $row)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20">
                                @foreach($row as $val)
                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $val }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($report['headers']) ?: 1 }}" class="px-6 py-12 text-center text-gray-400 text-sm">
                                    No records found matching current filter options.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
