{{--
    Inventory PDF Report
--}}
@extends('reports.pdf.layout')

@section('content')
{{-- Summary --}}
<div class="summary-row">
    <div class="summary-box">
        <div class="label">Total Titles</div>
        <div class="value" style="color: #3B82F6;">{{ number_format($report['data']['total_titles']) }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Total Copies</div>
        <div class="value" style="color: #8B5CF6;">{{ number_format($report['data']['total_copies']) }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Available</div>
        <div class="value" style="color: #10B981;">{{ number_format($report['data']['available_copies']) }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Borrowed</div>
        <div class="value" style="color: #F59E0B;">{{ number_format($report['data']['borrowed_copies']) }}</div>
    </div>
</div>

<div style="text-align: center; margin-bottom: 20px;">
    <p style="font-size: 14px;">
        <strong>Utilization Rate:</strong>
        <span style="color: {{ $report['data']['utilization_rate'] > 80 ? '#DC2626' : ($report['data']['utilization_rate'] > 50 ? '#F59E0B' : '#10B981') }}; font-size: 18px;">
            {{ $report['data']['utilization_rate'] }}%
        </span>
    </p>
    <p class="text-muted" style="font-size: 10px;">
        {{ $report['data']['borrowed_copies'] }} out of {{ $report['data']['total_copies'] }} copies are currently checked out
    </p>
</div>

{{-- Books by Condition --}}
<div class="section-header">Books by Condition</div>

<table>
    <thead>
        <tr>
            <th>Condition</th>
            <th style="text-align: right;">Count</th>
            <th style="text-align: right;">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalCondition = array_sum($report['data']['by_condition']);
            $conditionColors = [
                'new' => '#10B981',
                'good' => '#3B82F6',
                'fair' => '#F59E0B',
                'poor' => '#F97316',
                'damaged' => '#DC2626',
            ];
        @endphp
        @foreach($report['data']['by_condition'] as $condition => $count)
            <tr>
                <td>
                    <span style="color: {{ $conditionColors[$condition] ?? '#6B7280' }}; font-weight: bold;">
                        {{ ucfirst($condition) }}
                    </span>
                </td>
                <td class="amount">{{ number_format($count) }}</td>
                <td class="amount">{{ $totalCondition > 0 ? round(($count / $totalCondition) * 100, 1) : 0 }}%</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f3f4f6;">
            <td class="text-bold">Total</td>
            <td class="amount text-bold">{{ number_format($totalCondition) }}</td>
            <td class="amount text-bold">100%</td>
        </tr>
    </tfoot>
</table>

{{-- Books by Category --}}
<div class="section-header">Books by Category</div>

<table>
    <thead>
        <tr>
            <th>Category</th>
            <th style="text-align: right;">Count</th>
            <th style="text-align: right;">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalCategory = array_sum($report['data']['by_category']);
        @endphp
        @foreach($report['data']['by_category'] as $category => $count)
            <tr>
                <td>{{ $category }}</td>
                <td class="amount">{{ number_format($count) }}</td>
                <td class="amount">{{ $totalCategory > 0 ? round(($count / $totalCategory) * 100, 1) : 0 }}%</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f3f4f6;">
            <td class="text-bold">Total</td>
            <td class="amount text-bold">{{ number_format($totalCategory) }}</td>
            <td class="amount text-bold">100%</td>
        </tr>
    </tfoot>
</table>
@endsection
