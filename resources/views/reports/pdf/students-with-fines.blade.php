{{--
    Students with Fines PDF Report
--}}
@extends('reports.pdf.layout')

@section('content')
{{-- Summary --}}
@php
    $totalFines = $report['data']['students']->sum('total_fines');
    $totalCount = $report['data']['students']->count();
@endphp

<div class="summary-row">
    <div class="summary-box">
        <div class="label">Students with Fines</div>
        <div class="value" style="color: #DC2626;">{{ $totalCount }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Total Unpaid Fines</div>
        <div class="value" style="color: #F59E0B;">P{{ number_format($totalFines, 2) }}</div>
    </div>
</div>

{{-- Students Table --}}
<div class="section-header">Students with Unpaid Fines</div>

@if($report['data']['students']->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Grade Level</th>
                <th>Section</th>
                <th style="text-align: right;">No. of Fines</th>
                <th style="text-align: right;">Total Fines</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['data']['students'] as $student)
                <tr>
                    <td class="text-bold">{{ $student->full_name }}</td>
                    <td>{{ $student->grade_level }}</td>
                    <td>{{ $student->section }}</td>
                    <td class="amount">{{ $student->fine_count }}</td>
                    <td class="amount text-danger text-bold">P{{ number_format($student->total_fines, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #fee2e2;">
                <td colspan="3" class="text-bold">Total</td>
                <td class="amount text-bold">{{ $report['data']['students']->sum('fine_count') }}</td>
                <td class="amount text-bold text-danger">P{{ number_format($totalFines, 2) }}</td>
            </tr>
        </tfoot>
    </table>
@else
    <p class="text-muted text-center" style="padding: 20px;">No students with unpaid fines.</p>
@endif
@endsection
