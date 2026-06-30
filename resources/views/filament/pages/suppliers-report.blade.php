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
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.suppliers.active_suppliers') }}</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $data['suppliersCount'] }}</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.suppliers.total_purchases') }}</p>
                <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ number_format($data['totalPurchases'], 2) }} IDR</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.suppliers.total_payments') }}</p>
                <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ number_format($data['totalPayments'], 2) }} IDR</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.suppliers.outstanding_balance') }}</p>
                <p class="text-2xl font-bold {{ $data['totalBalance'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">{{ number_format($data['totalBalance'], 2) }} IDR</p>
            </div>
        </x-filament::section>
    </div>

    {{-- Table --}}
    <x-filament::section :heading="__('app.reports.suppliers.table_title')" class="mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">#</th>
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.suppliers.supplier') }}</th>
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.suppliers.phone') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.suppliers.purchases_IDR') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.suppliers.payments_IDR') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.suppliers.balance_IDR') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @forelse($data['suppliers'] as $index => $supplier)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 {{ $index % 2 === 0 ? '' : 'bg-gray-50/50 dark:bg-white/[0.02]' }}">
                            <td class="p-3 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-3 font-medium text-gray-900 dark:text-white">{{ $supplier['name'] }}</td>
                            <td class="p-3 text-gray-600 dark:text-gray-400">{{ $supplier['phone'] ?? '—' }}</td>
                            <td class="text-end p-3 text-gray-700 dark:text-gray-300">{{ number_format($supplier['purchases_total'], 2) }}</td>
                            <td class="text-end p-3 text-gray-700 dark:text-gray-300">{{ number_format($supplier['payments_total'], 2) }}</td>
                            <td class="text-end p-3 font-semibold {{ $supplier['balance'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                                {{ number_format($supplier['balance'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500 dark:text-gray-400">{{ __('app.reports.suppliers.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($data['suppliers']->isNotEmpty())
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-white/5">
                            <td class="p-3" colspan="3"><span class="font-bold text-gray-900 dark:text-white">{{ __('app.reports.totals') }}</span></td>
                            <td class="text-end p-3 font-bold text-gray-900 dark:text-white">{{ number_format($data['totalPurchases'], 2) }}</td>
                            <td class="text-end p-3 font-bold text-gray-900 dark:text-white">{{ number_format($data['totalPayments'], 2) }}</td>
                            <td class="text-end p-3 font-bold {{ $data['totalBalance'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                                {{ number_format($data['totalBalance'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
