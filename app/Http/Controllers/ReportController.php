<?php

/**
 * ReportController.php
 *
 * This controller handles all report-related requests for the library system.
 * It provides methods to display various reports and export them as PDF or CSV.
 *
 * Features:
 * - Report dashboard with quick access to all reports
 * - Daily transactions report
 * - Overdue books report
 * - Most borrowed books report with date filtering
 * - Inventory report
 * - Monthly statistics with charts
 * - PDF and CSV export functionality
 *
 * @package App\Http\Controllers
 * @author  Library Management System
 * @version 1.0
 */

namespace App\Http\Controllers;

use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * The report service instance.
     *
     * @var ReportService
     */
    protected ReportService $reportService;

    /**
     * Create a new controller instance.
     *
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the reports dashboard.
     *
     * Shows an overview of available reports with quick statistics
     * and navigation to individual report pages.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get quick statistics for the dashboard
        $statistics = $this->reportService->getDashboardStatistics();

        // Get today's transactions summary
        $todayTransactions = $this->reportService->getDailyTransactions(Carbon::today());

        // Get current overdue count
        $overdueCount = $this->reportService->getOverdueBooks()->count();

        return view('reports.index', [
            'statistics' => $statistics,
            'todayTransactions' => $todayTransactions,
            'overdueCount' => $overdueCount,
        ]);
    }

    /**
     * Display the daily transactions report.
     *
     * Shows all borrowed and returned books for a specific date.
     * Defaults to today's date if no date is provided.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function dailyTransactions(Request $request)
    {
        // Get date from request or default to today
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        // Get the report data
        $report = $this->reportService->getDailyTransactions($date);

        return view('reports.daily-transactions', [
            'report' => $report,
            'selectedDate' => $date,
        ]);
    }

    /**
     * Display the overdue books report.
     *
     * Shows all books that are currently overdue along with
     * student information and calculated fines.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function overdueBooks(Request $request)
    {
        // Get all overdue books
        $overdueBooks = $this->reportService->getOverdueBooks();

        // Calculate total fines
        $totalFines = $overdueBooks->sum('calculated_fine');

        // Get filters from request
        $sortBy = $request->input('sort', 'due_date');
        $sortOrder = $request->input('order', 'asc');

        // Sort the results
        if ($sortBy === 'days_overdue') {
            $overdueBooks = $sortOrder === 'desc'
                ? $overdueBooks->sortByDesc('days_overdue')
                : $overdueBooks->sortBy('days_overdue');
        } elseif ($sortBy === 'fine') {
            $overdueBooks = $sortOrder === 'desc'
                ? $overdueBooks->sortByDesc('calculated_fine')
                : $overdueBooks->sortBy('calculated_fine');
        } elseif ($sortBy === 'student') {
            $overdueBooks = $sortOrder === 'desc'
                ? $overdueBooks->sortByDesc(fn($t) => $t->student->full_name)
                : $overdueBooks->sortBy(fn($t) => $t->student->full_name);
        }

        return view('reports.overdue', [
            'overdueBooks' => $overdueBooks->values(),
            'totalFines' => $totalFines,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Display the most borrowed books report.
     *
     * Shows books ranked by how many times they've been borrowed,
     * with optional date range filtering.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function mostBorrowed(Request $request)
    {
        // Get filter parameters
        $limit = $request->input('limit', 10);
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : null;
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : null;

        // Get the report data
        $books = $this->reportService->getMostBorrowedBooks($limit, $startDate, $endDate);

        // Get books by category for chart
        $booksByCategory = $this->reportService->getBooksByCategory();

        return view('reports.most-borrowed', [
            'books' => $books,
            'booksByCategory' => $booksByCategory,
            'limit' => $limit,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Display the inventory report.
     *
     * Shows comprehensive inventory statistics including
     * total books, availability, condition breakdown, etc.
     *
     * @return \Illuminate\View\View
     */
    public function inventory()
    {
        // Get inventory statistics
        $inventory = $this->reportService->getInventoryReport();

        return view('reports.inventory', [
            'inventory' => $inventory,
        ]);
    }

    /**
     * Display the monthly statistics report.
     *
     * Shows detailed statistics for a specific month including
     * daily breakdown charts, top borrowers, and popular books.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function monthlyStats(Request $request)
    {
        // Get month and year from request or default to current month
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Get monthly statistics
        $statistics = $this->reportService->getMonthlyStatistics($month, $year);

        // Get annual statistics for comparison chart
        $annualStats = $this->reportService->getAnnualStatistics($year);

        return view('reports.monthly-stats', [
            'statistics' => $statistics,
            'annualStats' => $annualStats,
            'selectedMonth' => $month,
            'selectedYear' => $year,
        ]);
    }

    /**
     * Export a report as PDF.
     *
     * Generates a PDF file for the specified report type
     * and returns it for download or inline viewing.
     *
     * @param Request $request
     * @param string $reportType The type of report to export
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request, string $reportType)
    {
        // Build parameters from request
        $params = $this->buildExportParams($request);

        // Get formatted report data
        $reportData = $this->reportService->getReportForExport($reportType, $params);

        // Determine which PDF view to use
        $viewName = $this->getPdfViewName($reportType);

        // Generate PDF
        $pdf = Pdf::loadView($viewName, [
            'report' => $reportData,
        ]);

        // Set paper size and orientation based on report type
        if (in_array($reportType, ['inventory', 'monthly'])) {
            $pdf->setPaper('a4', 'landscape');
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        // Generate filename
        $filename = $this->generateFilename($reportType, 'pdf');

        // Return PDF for download
        return $pdf->download($filename);
    }

    /**
     * Export a report as CSV.
     *
     * Generates a CSV file for the specified report type
     * and returns it for download.
     *
     * @param Request $request
     * @param string $reportType The type of report to export
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request, string $reportType)
    {
        // Build parameters from request
        $params = $this->buildExportParams($request);

        // Get formatted report data
        $reportData = $this->reportService->getReportForExport($reportType, $params);

        // Generate filename
        $filename = $this->generateFilename($reportType, 'csv');

        // Build CSV content based on report type
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($reportType, $reportData) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Generate CSV rows based on report type
            $this->generateCsvContent($file, $reportType, $reportData);

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Build export parameters from request.
     *
     * @param Request $request
     * @return array
     */
    private function buildExportParams(Request $request): array
    {
        return [
            'date' => $request->input('date'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
            'limit' => $request->input('limit', 10),
        ];
    }

    /**
     * Get the PDF view name for a report type.
     *
     * @param string $reportType
     * @return string
     */
    private function getPdfViewName(string $reportType): string
    {
        $viewMap = [
            'daily' => 'reports.pdf.daily-transactions',
            'overdue' => 'reports.pdf.overdue',
            'most-borrowed' => 'reports.pdf.most-borrowed',
            'inventory' => 'reports.pdf.inventory',
            'monthly' => 'reports.pdf.monthly-stats',
            'students-with-fines' => 'reports.pdf.students-with-fines',
        ];

        return $viewMap[$reportType] ?? 'reports.pdf.generic';
    }

    /**
     * Generate filename for export.
     *
     * @param string $reportType
     * @param string $extension
     * @return string
     */
    private function generateFilename(string $reportType, string $extension): string
    {
        $date = Carbon::now()->format('Y-m-d');
        $reportNames = [
            'daily' => 'daily-transactions',
            'overdue' => 'overdue-books',
            'most-borrowed' => 'most-borrowed-books',
            'inventory' => 'inventory',
            'monthly' => 'monthly-statistics',
            'students-with-fines' => 'students-with-fines',
        ];

        $name = $reportNames[$reportType] ?? 'report';
        return "{$name}_{$date}.{$extension}";
    }

    /**
     * Generate CSV content based on report type.
     *
     * @param resource $file File handle
     * @param string $reportType
     * @param array $reportData
     */
    private function generateCsvContent($file, string $reportType, array $reportData): void
    {
        // Add report header
        fputcsv($file, [$reportData['title']]);
        fputcsv($file, [$reportData['description']]);
        fputcsv($file, ['Generated at: ' . $reportData['generated_at']]);
        fputcsv($file, ['Generated by: ' . $reportData['generated_by']]);
        fputcsv($file, []); // Empty row

        switch ($reportType) {
            case 'daily':
                $this->generateDailyCsv($file, $reportData['data']);
                break;
            case 'overdue':
                $this->generateOverdueCsv($file, $reportData['data']);
                break;
            case 'most-borrowed':
                $this->generateMostBorrowedCsv($file, $reportData['data']);
                break;
            case 'inventory':
                $this->generateInventoryCsv($file, $reportData['data']);
                break;
            case 'monthly':
                $this->generateMonthlyCsv($file, $reportData['data']);
                break;
            case 'students-with-fines':
                $this->generateStudentsWithFinesCsv($file, $reportData['data']);
                break;
        }
    }

    /**
     * Generate CSV content for daily transactions report.
     */
    private function generateDailyCsv($file, array $data): void
    {
        // Borrowed section
        fputcsv($file, ['BORROWED BOOKS']);
        fputcsv($file, ['Student', 'Book Title', 'Accession No.', 'Borrowed Date', 'Due Date', 'Processed By']);

        foreach ($data['borrowed'] as $transaction) {
            fputcsv($file, [
                $transaction->student->full_name,
                $transaction->book->title,
                $transaction->book->accession_number,
                $transaction->borrowed_date->format('M d, Y'),
                $transaction->due_date->format('M d, Y'),
                $transaction->librarian->name ?? 'N/A',
            ]);
        }

        fputcsv($file, []); // Empty row

        // Returned section
        fputcsv($file, ['RETURNED BOOKS']);
        fputcsv($file, ['Student', 'Book Title', 'Accession No.', 'Returned Date', 'Status', 'Fine']);

        foreach ($data['returned'] as $transaction) {
            fputcsv($file, [
                $transaction->student->full_name,
                $transaction->book->title,
                $transaction->book->accession_number,
                $transaction->returned_date->format('M d, Y'),
                $transaction->status,
                $transaction->fine_amount > 0 ? '₱' . number_format($transaction->fine_amount, 2) : 'None',
            ]);
        }

        fputcsv($file, []); // Empty row
        fputcsv($file, ['Summary']);
        fputcsv($file, ['Total Borrowed', $data['borrowed_count']]);
        fputcsv($file, ['Total Returned', $data['returned_count']]);
    }

    /**
     * Generate CSV content for overdue books report.
     */
    private function generateOverdueCsv($file, array $data): void
    {
        fputcsv($file, ['Student', 'Grade Level', 'Book Title', 'Accession No.', 'Borrowed Date', 'Due Date', 'Days Overdue', 'Fine Amount']);

        $totalFine = 0;
        foreach ($data['transactions'] as $transaction) {
            $totalFine += $transaction->calculated_fine;
            fputcsv($file, [
                $transaction->student->full_name,
                $transaction->student->grade_level,
                $transaction->book->title,
                $transaction->book->accession_number,
                $transaction->borrowed_date->format('M d, Y'),
                $transaction->due_date->format('M d, Y'),
                $transaction->days_overdue,
                '₱' . number_format($transaction->calculated_fine, 2),
            ]);
        }

        fputcsv($file, []); // Empty row
        fputcsv($file, ['Total Overdue Books', count($data['transactions'])]);
        fputcsv($file, ['Total Fines', '₱' . number_format($totalFine, 2)]);
    }

    /**
     * Generate CSV content for most borrowed books report.
     */
    private function generateMostBorrowedCsv($file, array $data): void
    {
        fputcsv($file, ['Rank', 'Title', 'Author', 'Accession No.', 'Category', 'Times Borrowed']);

        $rank = 1;
        foreach ($data['books'] as $book) {
            fputcsv($file, [
                $rank++,
                $book->title,
                $book->author,
                $book->accession_number,
                $book->category->name ?? 'N/A',
                $book->borrow_count,
            ]);
        }
    }

    /**
     * Generate CSV content for inventory report.
     */
    private function generateInventoryCsv($file, array $data): void
    {
        fputcsv($file, ['SUMMARY']);
        fputcsv($file, ['Total Titles', $data['total_titles']]);
        fputcsv($file, ['Total Copies', $data['total_copies']]);
        fputcsv($file, ['Available Copies', $data['available_copies']]);
        fputcsv($file, ['Borrowed Copies', $data['borrowed_copies']]);
        fputcsv($file, ['Utilization Rate', $data['utilization_rate'] . '%']);

        fputcsv($file, []); // Empty row
        fputcsv($file, ['BY CONDITION']);
        fputcsv($file, ['Condition', 'Count']);
        foreach ($data['by_condition'] as $condition => $count) {
            fputcsv($file, [ucfirst($condition), $count]);
        }

        fputcsv($file, []); // Empty row
        fputcsv($file, ['BY CATEGORY']);
        fputcsv($file, ['Category', 'Book Count']);
        foreach ($data['by_category'] as $category => $count) {
            fputcsv($file, [$category, $count]);
        }
    }

    /**
     * Generate CSV content for monthly statistics report.
     */
    private function generateMonthlyCsv($file, array $data): void
    {
        fputcsv($file, ['SUMMARY']);
        fputcsv($file, ['Total Borrowed', $data['summary']['total_borrowed']]);
        fputcsv($file, ['Total Returned', $data['summary']['total_returned']]);
        fputcsv($file, ['Unique Borrowers', $data['summary']['unique_borrowers']]);
        fputcsv($file, ['Overdue Count', $data['summary']['overdue_count']]);
        fputcsv($file, ['Fines Generated', '₱' . number_format($data['summary']['fines_generated'], 2)]);
        fputcsv($file, ['Fines Collected', '₱' . number_format($data['summary']['fines_collected'], 2)]);

        fputcsv($file, []); // Empty row
        fputcsv($file, ['DAILY BREAKDOWN']);
        fputcsv($file, ['Date', 'Borrowed', 'Returned']);
        foreach ($data['daily_breakdown'] as $date => $values) {
            fputcsv($file, [
                Carbon::parse($date)->format('M d, Y'),
                $values['borrowed'],
                $values['returned'],
            ]);
        }

        fputcsv($file, []); // Empty row
        fputcsv($file, ['TOP BORROWERS']);
        fputcsv($file, ['Student', 'Grade Level', 'Books Borrowed']);
        foreach ($data['top_borrowers'] as $borrower) {
            fputcsv($file, [
                $borrower->student->full_name ?? 'N/A',
                $borrower->student->grade_level ?? 'N/A',
                $borrower->borrow_count,
            ]);
        }

        fputcsv($file, []); // Empty row
        fputcsv($file, ['MOST POPULAR BOOKS']);
        fputcsv($file, ['Title', 'Times Borrowed']);
        foreach ($data['top_books'] as $book) {
            fputcsv($file, [
                $book->book->title ?? 'N/A',
                $book->borrow_count,
            ]);
        }
    }

    /**
     * Generate CSV content for students with fines report.
     */
    private function generateStudentsWithFinesCsv($file, array $data): void
    {
        fputcsv($file, ['Student Name', 'Grade Level', 'Section', 'Number of Fines', 'Total Fines']);

        $totalFines = 0;
        foreach ($data['students'] as $student) {
            $totalFines += $student->total_fines;
            fputcsv($file, [
                $student->full_name,
                $student->grade_level,
                $student->section,
                $student->fine_count,
                '₱' . number_format($student->total_fines, 2),
            ]);
        }

        fputcsv($file, []); // Empty row
        fputcsv($file, ['Total Students with Fines', count($data['students'])]);
        fputcsv($file, ['Total Unpaid Fines', '₱' . number_format($totalFines, 2)]);
    }
}
