<x-filament-panels::page>
    <form wire:submit.prevent="">
        {{ $this->form }}
    </form>

    @php
        $data = $this->getReportData();
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.workers.active_workers') }}</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $data['workersCount'] }}</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.workers.tasks_paid') }}</p>
                <p class="text-2xl font-bold text-info-600 dark:text-info-400">{{ number_format($data['totalTasksPaid'], 2) }} EGP</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.workers.other_payments') }}</p>
                <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ number_format($data['totalPayments'], 2) }} EGP</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.workers.total_paid') }}</p>
                <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ number_format($data['totalGrand'], 2) }} EGP</p>
            </div>
        </x-filament::section>
    </div>

    {{-- Table --}}
    <x-filament::section :heading="__('app.reports.workers.table_title')" class="mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">#</th>
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.workers.worker') }}</th>
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.workers.phone') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.workers.tasks') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.workers.tasks_paid_egp') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.workers.other_payments_egp') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.workers.total_egp') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @forelse($data['workers'] as $index => $worker)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 {{ $index % 2 === 0 ? '' : 'bg-gray-50/50 dark:bg-white/[0.02]' }}">
                            <td class="p-3 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-3 font-medium text-gray-900 dark:text-white">{{ $worker['name'] }}</td>
                            <td class="p-3 text-gray-600 dark:text-gray-400">{{ $worker['phone'] ?? '—' }}</td>
                            <td class="text-end p-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-primary-50 text-primary-700 dark:bg-primary-400/10 dark:text-primary-400">
                                    {{ $worker['tasks_count'] }}
                                </span>
                            </td>
                            <td class="text-end p-3 text-gray-700 dark:text-gray-300">{{ number_format($worker['tasks_paid'], 2) }}</td>
                            <td class="text-end p-3 text-gray-700 dark:text-gray-300">{{ number_format($worker['payments_total'], 2) }}</td>
                            <td class="text-end p-3 font-semibold text-gray-900 dark:text-white">{{ number_format($worker['grand_total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500 dark:text-gray-400">{{ __('app.reports.workers.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($data['workers']->isNotEmpty())
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-white/5">
                            <td class="p-3" colspan="4"><span class="font-bold text-gray-900 dark:text-white">{{ __('app.reports.totals') }}</span></td>
                            <td class="text-end p-3 font-bold text-gray-900 dark:text-white">{{ number_format($data['totalTasksPaid'], 2) }}</td>
                            <td class="text-end p-3 font-bold text-gray-900 dark:text-white">{{ number_format($data['totalPayments'], 2) }}</td>
                            <td class="text-end p-3 font-bold text-success-600 dark:text-success-400">{{ number_format($data['totalGrand'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
