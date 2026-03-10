<x-filament-panels::page>
    <form wire:submit.prevent="">
        {{ $this->form }}
    </form>

    @php
        $data = $this->getReportData();
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-6">
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.clients.clients') }}</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $data['clientsCount'] }}</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.clients.projects') }}</p>
                <p class="text-2xl font-bold text-info-600 dark:text-info-400">{{ $data['totalProjects'] }}</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.clients.total_value') }}</p>
                <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ number_format($data['grandFinal'], 2) }} EGP</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.clients.total_paid') }}</p>
                <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ number_format($data['grandPaid'], 2) }} EGP</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('app.reports.clients.outstanding') }}</p>
                <p class="text-2xl font-bold {{ $data['grandRest'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">{{ number_format($data['grandRest'], 2) }} EGP</p>
            </div>
        </x-filament::section>
    </div>

    {{-- Table --}}
    <x-filament::section :heading="__('app.reports.clients.table_title')" class="mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">#</th>
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.clients.client') }}</th>
                        <th class="text-start p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.clients.phone') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.clients.projects') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.clients.total_egp') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.clients.paid_egp') }}</th>
                        <th class="text-end p-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('app.reports.clients.rest_egp') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @forelse($data['clients'] as $index => $client)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 {{ $index % 2 === 0 ? '' : 'bg-gray-50/50 dark:bg-white/[0.02]' }}">
                            <td class="p-3 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-3 font-medium text-gray-900 dark:text-white">{{ $client['name'] }}</td>
                            <td class="p-3 text-gray-600 dark:text-gray-400">{{ $client['phone'] ?? '—' }}</td>
                            <td class="text-end p-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-primary-50 text-primary-700 dark:bg-primary-400/10 dark:text-primary-400">
                                    {{ $client['projects_count'] }}
                                </span>
                            </td>
                            <td class="text-end p-3 text-gray-700 dark:text-gray-300">{{ number_format($client['total_final'], 2) }}</td>
                            <td class="text-end p-3 text-success-600 dark:text-success-400">{{ number_format($client['total_paid'], 2) }}</td>
                            <td class="text-end p-3 font-semibold {{ $client['total_rest'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                                {{ number_format($client['total_rest'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500 dark:text-gray-400">{{ __('app.reports.clients.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($data['clients']->isNotEmpty())
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-white/5">
                            <td class="p-3" colspan="3"><span class="font-bold text-gray-900 dark:text-white">{{ __('app.reports.totals') }}</span></td>
                            <td class="text-end p-3 font-bold text-gray-900 dark:text-white">{{ $data['totalProjects'] }}</td>
                            <td class="text-end p-3 font-bold text-gray-900 dark:text-white">{{ number_format($data['grandFinal'], 2) }}</td>
                            <td class="text-end p-3 font-bold text-success-600 dark:text-success-400">{{ number_format($data['grandPaid'], 2) }}</td>
                            <td class="text-end p-3 font-bold {{ $data['grandRest'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                                {{ number_format($data['grandRest'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
