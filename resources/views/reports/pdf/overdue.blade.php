{{--
    Overdue Books PDF Report
--}}
@extends('reports.pdf.layout')

@section('content')
{{-- Summary --}}
@php
    $totalFines = $report['data']['transactions']->sum('calculated_fine');
    $uniqueStudents = $report['data']['transactions']->unique('student_id')->count();
@endphp

<div class="summary-row">
    <div class="summary-box">
        <div class="label">Overdue Books</div>
        <div class="value" style="color: #DC2626;">{{ $report['data']['transactions']->count() }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Students Affected</div>
        <div class="value" style="color: #F59E0B;">{{ $uniqueStudents }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Total Fines</div>
        <div class="value" style="color: #DC2626;">P{{ number_format($totalFines, 2) }}</div>
    </div>
</div>

{{-- Overdue Books Table --}}
<div class="section-header">Overdue Books List</div>

@if($report['data']['transactions']->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Grade/Section</th>
                <th>Book Title</th>
                <th>Accession No.</th>
                <th>Due Date</th>
                <th>Days Overdue</th>
                <th style="text-align: right;">Fine</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['data']['transactions'] as $transaction)
                <tr>
                    <td>{{ $transaction->student->full_name }}</td>
                    <td>{{ $transaction->student->grade_level }} - {{ $transaction->student->section }}</td>
                    <td>{{ Str::limit($transaction->book->title, 30) }}</td>
                    <td>{{ $transaction->book->accession_number }}</td>
                    <td>{{ $transaction->due_date->format('M d, Y') }}</td>
                    <td>
                        @php
                            $daysOverdue = $transaction->days_overdue;
                        @endphp
                        @if($daysOverdue <= 7)
                            <span class="badge badge-warning">{{ $daysOverdue }} days</span>
                        @else
                            <span class="badge badge-danger">{{ $daysOverdue }} days</span>
                        @endif
                    </td>
                    <td class="amount text-danger">P{{ number_format($transaction->calculated_fine, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #fee2e2;">
                <td colspan="6" class="text-bold">Total Fines</td>
                <td class="amount text-bold text-danger">P{{ number_format($totalFines, 2) }}</td>
            </tr>
        </tfoot>
    </table>
@else
    <p class="text-muted text-center" style="padding: 20px;">No overdue books at this time.</p>
@endif
@endsection
