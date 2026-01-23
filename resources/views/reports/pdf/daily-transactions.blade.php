{{--
    Daily Transactions PDF Report
--}}
@extends('reports.pdf.layout')

@section('content')
{{-- Summary --}}
<div class="summary-row">
    <div class="summary-box">
        <div class="label">Books Borrowed</div>
        <div class="value" style="color: #3B82F6;">{{ $report['data']['borrowed_count'] }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Books Returned</div>
        <div class="value" style="color: #10B981;">{{ $report['data']['returned_count'] }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Total Transactions</div>
        <div class="value">{{ $report['data']['borrowed_count'] + $report['data']['returned_count'] }}</div>
    </div>
</div>

{{-- Borrowed Books Section --}}
<div class="section-header">Books Borrowed</div>

@if($report['data']['borrowed']->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Grade/Section</th>
                <th>Book Title</th>
                <th>Accession No.</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['data']['borrowed'] as $transaction)
                <tr>
                    <td>{{ $transaction->student->full_name }}</td>
                    <td>{{ $transaction->student->grade_level }} - {{ $transaction->student->section }}</td>
                    <td>{{ Str::limit($transaction->book->title, 35) }}</td>
                    <td>{{ $transaction->book->accession_number }}</td>
                    <td>{{ $transaction->due_date->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted text-center" style="padding: 20px;">No books were borrowed on this date.</p>
@endif

{{-- Returned Books Section --}}
<div class="section-header">Books Returned</div>

@if($report['data']['returned']->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Grade/Section</th>
                <th>Book Title</th>
                <th>Accession No.</th>
                <th>Status</th>
                <th style="text-align: right;">Fine</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['data']['returned'] as $transaction)
                <tr>
                    <td>{{ $transaction->student->full_name }}</td>
                    <td>{{ $transaction->student->grade_level }} - {{ $transaction->student->section }}</td>
                    <td>{{ Str::limit($transaction->book->title, 35) }}</td>
                    <td>{{ $transaction->book->accession_number }}</td>
                    <td>
                        @if($transaction->status === 'returned')
                            <span class="badge badge-success">On Time</span>
                        @else
                            <span class="badge badge-warning">{{ ucfirst($transaction->status) }}</span>
                        @endif
                    </td>
                    <td class="amount">
                        @if($transaction->fine_amount > 0)
                            <span class="text-danger">P{{ number_format($transaction->fine_amount, 2) }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted text-center" style="padding: 20px;">No books were returned on this date.</p>
@endif
@endsection
