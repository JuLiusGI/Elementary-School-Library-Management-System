{{--
    Most Borrowed Books PDF Report
--}}
@extends('reports.pdf.layout')

@section('content')
{{-- Summary --}}
@php
    $totalBorrows = $report['data']['books']->sum('borrow_count');
@endphp

<div class="summary-row">
    <div class="summary-box">
        <div class="label">Books Listed</div>
        <div class="value" style="color: #3B82F6;">{{ $report['data']['books']->count() }}</div>
    </div>
    <div class="summary-box">
        <div class="label">Total Borrows</div>
        <div class="value" style="color: #10B981;">{{ $totalBorrows }}</div>
    </div>
</div>

{{-- Most Borrowed Books Table --}}
<div class="section-header">Most Borrowed Books Ranking</div>

@if($report['data']['books']->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Rank</th>
                <th>Book Title</th>
                <th>Author</th>
                <th>Accession No.</th>
                <th style="text-align: right;">Times Borrowed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['data']['books'] as $index => $book)
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
                    <td class="text-bold">{{ Str::limit($book->title, 40) }}</td>
                    <td>{{ $book->author }}</td>
                    <td>{{ $book->accession_number }}</td>
                    <td class="amount text-bold" style="color: #3B82F6;">{{ $book->borrow_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted text-center" style="padding: 20px;">No borrowing data available for this period.</p>
@endif
@endsection
