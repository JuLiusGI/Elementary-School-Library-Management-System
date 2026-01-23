{{--
    Monthly Statistics PDF Report
--}}
@extends('reports.pdf.layout')

@section('content')
{{-- Summary --}}
<div class="summary-row">
    <div class="summary-box">
        <div class="label">Borrowed</div>
        <div class="value" style="color: #3B82F6;">{{ $report['data']['summary']['total_borrowed'] }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Returned</div>
        <div class="value" style="color: #10B981;">{{ $report['data']['summary']['total_returned'] }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Borrowers</div>
        <div class="value" style="color: #8B5CF6;">{{ $report['data']['summary']['unique_borrowers'] }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Overdue</div>
        <div class="value" style="color: #DC2626;">{{ $report['data']['summary']['overdue_count'] }}</div>
    </div>
</div>

<div style="text-align: center; margin-bottom: 20px; padding: 10px; background-color: #f3f4f6; border-radius: 4px;">
    <p style="margin: 5px 0;">
        <strong>Fines Generated:</strong>
        <span style="color: #F97316;">P{{ number_format($report['data']['summary']['fines_generated'], 2) }}</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Fines Collected:</strong>
        <span style="color: #10B981;">P{{ number_format($report['data']['summary']['fines_collected'], 2) }}</span>
    </p>
    <p style="margin: 5px 0; font-size: 10px; color: #6b7280;">
        Average: {{ $report['data']['summary']['average_daily_borrows'] }} books borrowed per day
    </p>
</div>

{{-- Top Borrowers --}}
<div class="section-header">Top Borrowers</div>

@if($report['data']['top_borrowers']->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Rank</th>
                <th>Student Name</th>
                <th>Grade Level</th>
                <th style="text-align: right;">Books Borrowed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['data']['top_borrowers'] as $index => $borrower)
                <tr>
                    <td class="text-center text-bold">
                        @if($index < 3)
                            <span style="color: {{ $index === 0 ? '#F59E0B' : ($index === 1 ? '#6B7280' : '#D97706') }};">
                                #{{ $index + 1 }}
                            </span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ $borrower->student->full_name ?? 'Unknown' }}</td>
                    <td>{{ $borrower->student->grade_level ?? 'N/A' }}</td>
                    <td class="amount text-bold" style="color: #3B82F6;">{{ $borrower->borrow_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted text-center" style="padding: 15px;">No borrowing data for this month.</p>
@endif

{{-- Most Popular Books --}}
<div class="section-header">Most Popular Books</div>

@if($report['data']['top_books']->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Rank</th>
                <th>Book Title</th>
                <th>Author</th>
                <th style="text-align: right;">Times Borrowed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['data']['top_books'] as $index => $book)
                <tr>
                    <td class="text-center text-bold">
                        @if($index < 3)
                            <span style="color: {{ $index === 0 ? '#F59E0B' : ($index === 1 ? '#6B7280' : '#D97706') }};">
                                #{{ $index + 1 }}
                            </span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ Str::limit($book->book->title ?? 'Unknown', 40) }}</td>
                    <td>{{ $book->book->author ?? 'N/A' }}</td>
                    <td class="amount text-bold" style="color: #10B981;">{{ $book->borrow_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted text-center" style="padding: 15px;">No borrowing data for this month.</p>
@endif

{{-- Borrowing by Grade Level --}}
<div class="section-header">Borrowing by Grade Level</div>

@if(count($report['data']['by_grade_level']) > 0)
    <table>
        <thead>
            <tr>
                <th>Grade Level</th>
                <th style="text-align: right;">Books Borrowed</th>
                <th style="text-align: right;">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalByGrade = array_sum($report['data']['by_grade_level']);
            @endphp
            @foreach($report['data']['by_grade_level'] as $grade => $count)
                <tr>
                    <td>{{ $grade }}</td>
                    <td class="amount">{{ $count }}</td>
                    <td class="amount">{{ $totalByGrade > 0 ? round(($count / $totalByGrade) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f3f4f6;">
                <td class="text-bold">Total</td>
                <td class="amount text-bold">{{ $totalByGrade }}</td>
                <td class="amount text-bold">100%</td>
            </tr>
        </tfoot>
    </table>
@else
    <p class="text-muted text-center" style="padding: 15px;">No data available.</p>
@endif
@endsection
