{{--
    Skeleton Table Component
    ========================

    A loading placeholder for tables while data is being fetched.

    Usage:
    <x-skeleton-table :rows="5" :cols="4" />
    <x-skeleton-table rows="10" cols="6" />

    @props int $rows - Number of rows to display (default: 5)
    @props int $cols - Number of columns to display (default: 4)
--}}

@props([
    'rows' => 5,
    'cols' => 4,
])

<div class="animate-pulse">
    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            {{-- Header --}}
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    @for($i = 0; $i < $cols; $i++)
                        <th class="px-6 py-3">
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-20"></div>
                        </th>
                    @endfor
                </tr>
            </thead>

            {{-- Body --}}
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @for($row = 0; $row < $rows; $row++)
                    <tr>
                        @for($col = 0; $col < $cols; $col++)
                            <td class="px-6 py-4">
                                @if($col === 0)
                                    {{-- First column - wider --}}
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                                @elseif($col === $cols - 1)
                                    {{-- Last column - action buttons --}}
                                    <div class="flex space-x-2">
                                        <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                        <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                    </div>
                                @else
                                    {{-- Middle columns --}}
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
