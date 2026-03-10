<x-filament-panels::page>
    <style>
        .dir-btn { padding:10px 16px;border-radius:8px;font-size:14px;font-weight:600;border:1px solid #e5e7eb;text-align:center;cursor:pointer;background:#fff;color:#6b7280; }
        .dir-btn-active { background:var(--primary-600);color:#fff;border-color:var(--primary-600); }
        .dark .dir-btn { background:rgba(255,255,255,.05);color:#9ca3af;border-color:rgba(255,255,255,.1); }
        .dark .dir-btn-active { background:var(--primary-500);border-color:var(--primary-500);color:#fff; }

        .dir-card { border-radius:12px;padding:20px;color:#fff;box-shadow:0 1px 3px rgba(0,0,0,.1);cursor:pointer; }
        .dir-card-income { background:var(--primary-600); }
        .dir-card-expense { background:var(--danger-600); }
        .dir-card-method { background:var(--primary-600);cursor:default; }
        .dark .dir-card-income { background:var(--primary-900); }
        .dark .dir-card-expense { background:var(--danger-900); }
        .dark .dir-card-method { background:var(--primary-900); }

        .dir-card-label { font-size:14px;opacity:.85;margin:0; }
        .dir-card-value { font-size:24px;font-weight:700;margin:6px 0 0; }
        .dir-card-value-sm { font-size:20px;font-weight:700;margin:10px 0 0; }
        .dir-card-hint { font-size:12px;opacity:.6;margin:8px 0 0; }

        .dir-grid-2 { display:grid;grid-template-columns:repeat(2,1fr);gap:16px; }
        .dir-grid-3 { display:grid;grid-template-columns:repeat(3,1fr);gap:16px; }
        .dir-grid-4 { display:grid;grid-template-columns:repeat(4,1fr);gap:12px; }
        .dir-flex { display:flex;align-items:center;gap:10px; }

        .dir-table { width:100%;font-size:14px;border-collapse:collapse; }
        .dir-th { text-align:start;padding:12px;font-weight:600;color:#4b5563; }
        .dir-th-end { text-align:end;padding:12px;font-weight:600;color:#4b5563; }
        .dir-td { padding:12px;color:#374151; }
        .dir-td-end { text-align:end;padding:12px;font-weight:600;color:#111827; }
        .dir-tr-alt { background:#f9fafb; }
        .dir-tr { border-top:1px solid #e5e7eb; }
        .dir-thead { background:#f9fafb; }
        .dir-empty { padding:24px;text-align:center;color:#9ca3af; }

        .dark .dir-th, .dark .dir-th-end { color:#d1d5db; }
        .dark .dir-td { color:#d1d5db; }
        .dark .dir-td-end { color:#f3f4f6; }
        .dark .dir-tr-alt { background:rgba(255,255,255,.02); }
        .dark .dir-tr { border-color:rgba(255,255,255,.1); }
        .dark .dir-thead { background:rgba(255,255,255,.05); }
    </style>

    {{-- Filter Buttons --}}
    <div class="dir-grid-4">
        @foreach ([
            'all'       => __('app.daily_income_report.all'),
            'today'     => __('app.daily_income_report.today'),
            'yesterday' => __('app.daily_income_report.yesterday'),
            'custom'    => __('app.daily_income_report.custom'),
        ] as $key => $label)
            <button
                wire:click="setPreset('{{ $key }}')"
                class="dir-btn {{ $this->activePreset === $key ? 'dir-btn-active' : '' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Custom Date Range --}}
    @if ($this->activePreset === 'custom')
        <form wire:submit.prevent="">
            {{ $this->form }}
        </form>
    @endif

    @php
        $data = $this->getReportData();
    @endphp

    {{-- Summary Cards: Income & Expenses --}}
    <div class="dir-grid-2">
        <div wire:click="toggleIncome" class="dir-card dir-card-income">
            <p class="dir-card-label">{{ __('app.daily_income_report.total_income') }}</p>
            <p class="dir-card-value">{{ number_format($data['income'], 2) }} {{ __('app.daily_income_report.currency') }}</p>
            <p class="dir-card-hint">
                {{ $this->showIncome ? __('app.daily_income_report.hide_details') : __('app.daily_income_report.view_details') }}
            </p>
        </div>
        <div wire:click="toggleExpenses" class="dir-card dir-card-expense">
            <p class="dir-card-label">{{ __('app.daily_income_report.expenses') }}</p>
            <p class="dir-card-value">{{ number_format($data['expenses'], 2) }} {{ __('app.daily_income_report.currency') }}</p>
            <p class="dir-card-hint">
                {{ $this->showExpenses ? __('app.daily_income_report.hide_details') : __('app.daily_income_report.view_details') }}
            </p>
        </div>
    </div>

    {{-- Payment Methods Cards --}}
    @if($data['paymentMethods']->isNotEmpty())
        <div class="dir-grid-3">
            @foreach ($data['paymentMethods'] as $method)
                <div class="dir-card dir-card-method">
                    <div class="dir-flex">
                        @if($method['image'])
                            <img src="{{ url($method['image']) }}" alt="{{ $method['name'] }}" style="height:35px;width:auto;max-width:60px;object-fit:contain;" />
                        @endif
                        <p class="dir-card-label">{{ $method['name'] }}</p>
                    </div>
                    <p class="dir-card-value-sm">{{ number_format($method['total'], 2) }} {{ __('app.daily_income_report.currency') }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Income Details Table --}}
    @if ($this->showIncome)
        @php $incomeDetails = $this->getIncomeDetails(); @endphp
        <x-filament::section>
            <x-slot name="heading">
                {{ __('app.daily_income_report.total_income') }} — {{ __('app.daily_income_report.details') }}
            </x-slot>
            <div style="overflow-x:auto;">
                <table class="dir-table">
                    <thead>
                        <tr class="dir-thead">
                            <th class="dir-th">{{ __('app.daily_income_report.date') }}</th>
                            <th class="dir-th-end">{{ __('app.daily_income_report.amount') }}</th>
                            <th class="dir-th">{{ __('app.daily_income_report.payment_method') }}</th>
                            <th class="dir-th">{{ __('app.daily_income_report.project') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomeDetails as $index => $item)
                            <tr class="dir-tr {{ $index % 2 !== 0 ? 'dir-tr-alt' : '' }}">
                                <td class="dir-td">{{ $item->created_at->format('Y-m-d') }}</td>
                                <td class="dir-td-end">{{ number_format($item->paid, 2) }}</td>
                                <td class="dir-td">{{ $item->paymentMethod?->name ?? '—' }}</td>
                                <td class="dir-td">{{ $item->paymentable?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="dir-empty">{{ __('app.daily_income_report.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif

    {{-- Expenses Details Tables --}}
    @if ($this->showExpenses)
        @php $expenseDetails = $this->getExpenseDetails(); @endphp

        @if($expenseDetails['paid']->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('app.daily_income_report.expenses') }} — {{ __('app.daily_income_report.paid_details') }}
                </x-slot>
                <div style="overflow-x:auto;">
                    <table class="dir-table">
                        <thead>
                            <tr class="dir-thead">
                                <th class="dir-th">{{ __('app.daily_income_report.date') }}</th>
                                <th class="dir-th-end">{{ __('app.daily_income_report.amount') }}</th>
                                <th class="dir-th">{{ __('app.daily_income_report.payment_method') }}</th>
                                <th class="dir-th">{{ __('app.daily_income_report.paid_for') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenseDetails['paid'] as $index => $item)
                                <tr class="dir-tr {{ $index % 2 !== 0 ? 'dir-tr-alt' : '' }}">
                                    <td class="dir-td">{{ $item->created_at->format('Y-m-d') }}</td>
                                    <td class="dir-td-end">{{ number_format($item->paid, 2) }}</td>
                                    <td class="dir-td">{{ $item->paymentMethod?->name ?? '—' }}</td>
                                    <td class="dir-td">{{ $item->paymentable?->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        @if($expenseDetails['systemExpenses']->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('app.daily_income_report.expenses') }} — {{ __('app.daily_income_report.system_expenses') }}
                </x-slot>
                <div style="overflow-x:auto;">
                    <table class="dir-table">
                        <thead>
                            <tr class="dir-thead">
                                <th class="dir-th">{{ __('app.daily_income_report.date') }}</th>
                                <th class="dir-th">{{ __('app.daily_income_report.name') }}</th>
                                <th class="dir-th-end">{{ __('app.daily_income_report.value') }}</th>
                                <th class="dir-th">{{ __('app.daily_income_report.payment_method') }}</th>
                                <th class="dir-th">{{ __('app.daily_income_report.added_by') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenseDetails['systemExpenses'] as $index => $item)
                                <tr class="dir-tr {{ $index % 2 !== 0 ? 'dir-tr-alt' : '' }}">
                                    <td class="dir-td">{{ $item->created_at->format('Y-m-d') }}</td>
                                    <td class="dir-td">{{ $item->name }}</td>
                                    <td class="dir-td-end">{{ number_format($item->value, 2) }}</td>
                                    <td class="dir-td">{{ $item->paymentMethod?->name ?? '—' }}</td>
                                    <td class="dir-td">{{ $item->addedBy?->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        @if($expenseDetails['paid']->isEmpty() && $expenseDetails['systemExpenses']->isEmpty())
            <x-filament::section>
                <p class="dir-empty">{{ __('app.daily_income_report.no_data') }}</p>
            </x-filament::section>
        @endif
    @endif
</x-filament-panels::page>
