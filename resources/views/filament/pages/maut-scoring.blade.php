<x-filament::page>
    <div class="fi-page-content space-y-6">
        <!-- Batch Selection Card -->
        <div class="fi-section rounded-xl bg-white shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header-wrapper border-b border-gray-200 p-6 dark:border-white/10">
                <div class="fi-section-header">
                    <div class="flex items-center gap-x-3">
                        <x-heroicon-o-folder-open class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                        <h2 class="fi-section-heading text-base font-semibold text-gray-950 dark:text-white">
                            Batch Selection
                        </h2>
                    </div>
                    <p class="fi-section-description mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Choose an evaluation batch to analyze MAUT scoring results
                    </p>
                </div>
            </div>

            <div class="p-6">
                <form method="GET" class="space-y-6">
                    <div class="space-y-2">
                        <label for="batchId" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Select Evaluation Batch
                            </span>
                        </label>

                        <div class="fi-fo-select-wrapper">
                            <select
                                id="batchId"
                                name="batchId"
                                onchange="this.form.submit()"
                                class="fi-select-input block w-full border-none bg-transparent py-1.5 pe-8 ps-3 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 bg-white dark:bg-white/5 [&:not(:focus)]:shadow-sm border-gray-300 dark:border-white/20 rounded-lg shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:focus:ring-primary-500"
                            >
                                <option value="">-- Choose a batch --</option>
                                @foreach (\App\Models\Batch::with('employees')->get() as $batch)
                                    <option
                                        value="{{ $batch->id }}"
                                        {{ request('batchId') == $batch->id ? 'selected' : '' }}
                                    >
                                        {{ $batch->name }} ({{ $batch->employees->count() }} employees)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(request('batchId'))
                            @php
                                $selectedBatch = \App\Models\Batch::with('employees')->find(request('batchId'));
                            @endphp

                            @if($selectedBatch)
                                <div class="fi-fo-field-helper-text">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Selected: <strong>{{ $selectedBatch->name }}</strong> with {{ $selectedBatch->employees->count() }} employees
                                    </p>
                                </div>
                            @else
                                <div class="rounded-lg bg-danger-50 ring-1 ring-danger-600/10 dark:bg-danger-400/10 dark:ring-danger-400/20">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-danger-400" />
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-danger-800 dark:text-danger-200">
                                                Batch Not Found
                                            </h3>
                                            <div class="mt-2 text-sm text-danger-700 dark:text-danger-300">
                                                <p>The selected batch could not be loaded. Please choose a different batch.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if ($data['batch'])
            <!-- MAUT Results Single View -->
            <div class="space-y-8">

                <!-- Employee Ranking Section -->
                <div class="fi-section rounded-xl bg-white shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="fi-section-header-wrapper border-b border-gray-200 p-6 dark:border-white/10">
                        <h3 class="fi-section-heading text-lg font-semibold text-gray-950 dark:text-white">
                            MAUT Ranking Results
                        </h3>
                        <p class="fi-section-description mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Employees ranked by their overall MAUT scores
                        </p>
                    </div>

                    <div class="fi-table-wrapper">
                        <table class="fi-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="fi-table-header-cell px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Rank
                                    </th>
                                    <th class="fi-table-header-cell px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Employee
                                    </th>
                                    <th class="fi-table-header-cell px-6 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        MAUT Score
                                    </th>
                                    <th class="fi-table-header-cell px-6 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Performance Level
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/5 dark:bg-gray-900">
                                @php
                                    $ranking = $data['ranking'];
                                @endphp

                                @foreach($ranking as $index => $item)
                                    @php
                                        $rankBadgeColor = 'gray';
                                        $performanceLevel = 'Average';
                                        $performanceBadgeColor = 'warning';

                                        if($index === 0) {
                                            $rankBadgeColor = 'success';
                                            $performanceLevel = 'Excellent';
                                            $performanceBadgeColor = 'success';
                                        } elseif($index < 3) {
                                            $rankBadgeColor = 'info';
                                            $performanceLevel = 'Very Good';
                                            $performanceBadgeColor = 'info';
                                        } elseif($item['score'] < 0.5) {
                                            $performanceLevel = 'Needs Improvement';
                                            $performanceBadgeColor = 'danger';
                                        }
                                    @endphp

                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                        <td class="fi-table-cell px-6 py-4">
                                            <div class="flex items-center">
                                                @if($index === 0)
                                                    <x-heroicon-o-trophy class="h-5 w-5 text-yellow-500 mr-2" />
                                                @endif
                                                <x-filament::badge :color="$rankBadgeColor" size="lg">
                                                    #{{ $index + 1 }}
                                                </x-filament::badge>
                                            </div>
                                        </td>
                                        <td class="fi-table-cell px-6 py-4">
                                            <div class="flex items-center gap-x-3">

                                                <div>
                                                    <p class="text-sm font-medium text-gray-950 dark:text-white">
                                                        {{ $item['employee']->name }}
                                                    </p>
                                                    @if(isset($item['employee']->position))
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $item['employee']->position }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fi-table-cell px-6 py-4 text-center">
                                            <span class="text-lg font-semibold text-gray-950 dark:text-white">
                                                {{ number_format($item['score'], 4) }}
                                            </span>
                                        </td>
                                        <td class="fi-table-cell px-6 py-4 text-center">
                                            <x-filament::badge :color="$performanceBadgeColor">
                                                {{ $performanceLevel }}
                                            </x-filament::badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Detailed Raw Scores Section -->

                    <div class="fi-section-header-wrapper border-b border-gray-200 p-6 dark:border-white/10">
                        <h3 class="fi-section-heading text-lg font-semibold text-gray-950 dark:text-white">
                            Detailed Raw Scores
                        </h3>
                        <p class="fi-section-description mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Original evaluation scores for each criterion
                        </p>
                    </div>

                    @include('maut.partial-scoring-table', [
                        'criteria' => $data['criteria'],
                        'employees' => $data['batch']->employees,
                        'rawData' => $data['rawData'],
                        'normalized' => $data['normalized'],
                        'ranking' => $ranking ?? []
                    ])
            </div>
        @else
            <!-- Empty State -->
            <div class="fi-section rounded-xl bg-white shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-content-wrapper p-6 text-center">
                    <h3 class="mt-4 text-lg font-semibold text-gray-950 dark:text-white">
                        No Batch Selected
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Please select an evaluation batch from the dropdown above to view MAUT scoring results and employee rankings.
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-filament::page>
