<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Report - {{ $project->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563EB; padding-bottom: 10px; }
        .header h1 { color: #2563EB; font-size: 18px; margin-bottom: 4px; }
        .header h2 { color: #555; font-size: 14px; margin-bottom: 4px; }
        .header p { color: #888; font-size: 10px; }
        .section { margin-bottom: 20px; }
        .section-title { background: #2563EB; color: #fff; padding: 6px 12px; font-size: 13px; font-weight: bold; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #EFF6FF; color: #1E40AF; text-align: left; padding: 6px 8px; font-size: 10px; border: 1px solid #BFDBFE; }
        td { padding: 5px 8px; border: 1px solid #E5E7EB; font-size: 10px; }
        tr:nth-child(even) td { background: #F9FAFB; }
        .info-table td:first-child { font-weight: bold; width: 30%; background: #F3F4F6; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row td { font-weight: bold; background: #EFF6FF !important; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #E5E7EB; padding-top: 5px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-progress { background: #DBEAFE; color: #1E40AF; }
        .badge-completed { background: #D1FAE5; color: #065F46; }
        .badge-cancelled { background: #FEE2E2; color: #991B1B; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reflect Construction System</h1>
        <h2>{{ $project->name }}</h2>
        <p>Generated: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    {{-- Project Details --}}
    <div class="section">
        <div class="section-title">Project Details</div>
        <table class="info-table">
            <tr>
                <td>Client</td>
                <td>{{ $project->client?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td>Category</td>
                <td>{{ $project->category?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    @php
                        $statusText = match($project->status) { 0 => 'Pending', 1 => 'In Progress', 2 => 'Completed', 3 => 'Cancelled', default => 'Unknown' };
                        $statusClass = match($project->status) { 0 => 'badge-pending', 1 => 'badge-progress', 2 => 'badge-completed', 3 => 'badge-cancelled', default => '' };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                </td>
            </tr>
            <tr>
                <td>Start Date</td>
                <td>{{ $project->start_date ?? '—' }}</td>
            </tr>
            <tr>
                <td>End Date</td>
                <td>{{ $project->end_date ?? '—' }}</td>
            </tr>
            <tr>
                <td>Final Total</td>
                <td>{{ number_format((float) $project->final_total, 2) }} EGP</td>
            </tr>
            <tr>
                <td>Paid Total</td>
                <td>{{ number_format((float) $project->paid_total, 2) }} EGP</td>
            </tr>
            <tr>
                <td>Remaining</td>
                <td>{{ number_format((float) $project->rest_total, 2) }} EGP</td>
            </tr>
        </table>
    </div>

    {{-- Tasks --}}
    @if($project->tasks->isNotEmpty())
    <div class="section">
        <div class="section-title">Tasks ({{ $project->tasks->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th class="text-right">Final Total</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Remaining</th>
                    <th>Start</th>
                    <th>End</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->tasks as $index => $task)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $task->name }}</td>
                    <td>{{ $task->category?->name ?? '—' }}</td>
                    <td class="text-right">{{ number_format((float) $task->final_total, 2) }}</td>
                    <td class="text-right">{{ number_format((float) $task->paid_total, 2) }}</td>
                    <td class="text-right">{{ number_format((float) $task->rest_total, 2) }}</td>
                    <td>{{ $task->start_date ?? '—' }}</td>
                    <td>{{ $task->end_date ?? '—' }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($project->tasks->sum('final_total'), 2) }}</td>
                    <td class="text-right">{{ number_format($project->tasks->sum('paid_total'), 2) }}</td>
                    <td class="text-right">{{ number_format($project->tasks->sum('rest_total'), 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- Expenses --}}
    @if($project->expenses->isNotEmpty())
    <div class="section">
        <div class="section-title">Expenses ({{ $project->expenses->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Method</th>
                    <th class="text-right">Value</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->expenses as $index => $expense)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $expense->name }}</td>
                    <td>{{ $expense->expenseCategory?->name ?? '—' }}</td>
                    <td>{{ $expense->paymentMethod?->name ?? '—' }}</td>
                    <td class="text-right">{{ number_format((float) $expense->value, 2) }}</td>
                    <td>{{ $expense->date ?? '—' }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($project->expenses->sum('value'), 2) }} EGP</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- Payments --}}
    @if($project->payments->isNotEmpty())
    <div class="section">
        <div class="section-title">Payments ({{ $project->payments->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-right">Amount</th>
                    <th>Payment Method</th>
                    <th>Code</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->payments as $index => $payment)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-right">{{ number_format((float) $payment->paid, 2) }}</td>
                    <td>{{ $payment->paymentMethod?->name ?? '—' }}</td>
                    <td>{{ $payment->payment_code ?? '—' }}</td>
                    <td>{{ $payment->created_at?->format('Y-m-d') ?? '—' }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td class="text-right">Total</td>
                    <td class="text-right">{{ number_format($project->payments->sum('paid'), 2) }} EGP</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Reflect Construction System &bull; {{ now()->format('Y-m-d H:i') }} &bull; Page 1
    </div>
</body>
</html>
