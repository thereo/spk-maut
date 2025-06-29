<div class="fi-modal-content">
        <!-- Scoring Table -->
        <div class="fi-table-wrapper rounded-xl bg-white shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-table-header-wrapper p-4 border-b border-gray-200 dark:border-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="fi-table-header-heading text-base font-semibold text-gray-950 dark:text-white">
                        Evaluation Scores
                    </h3>
                </div>
            </div>

            <div class="fi-table-content overflow-x-auto">
                <table class="fi-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr class="divide-x divide-gray-200 dark:divide-white/5">
                            <th class="fi-table-header-cell px-3 py-3.5 text-left">
                                <div class="flex items-center gap-x-2">
                                    <x-heroicon-o-user class="h-4 w-4 text-gray-400" />
                                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Employee
                                    </span>
                                </div>
                            </th>
                            @foreach ($criteria as $criterion)
                                <th class="fi-table-header-cell px-3 py-3.5 text-center min-w-[120px]">
                                    <div class="flex flex-col items-center gap-y-1">
                                        <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            {{ $criterion->name }}
                                        </span>
                                        @if(isset($criterion->weight))
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                                {{ number_format($criterion->weight * 100, 0) }}%
                                            </span>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                            <th class="fi-table-header-cell px-3 py-3.5 text-center">
                                <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Average
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/5 dark:bg-gray-900">
                        @foreach ($employees as $employee)
                            @php
                                $totalScore = 0;
                                $scoreCount = 0;
                                $employeeScores = [];
                            @endphp
                            <tr class="divide-x divide-gray-200 dark:divide-white/5">
                                <!-- Employee Info -->
                                <td class="fi-table-cell p-3">
                                    <div class="flex items-center gap-x-3">

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-950 dark:text-white truncate">
                                                {{ $employee->name }}
                                            </p>
                                            @if(isset($employee->position))
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ $employee->position }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Scores -->
                                @foreach ($criteria as $criterion)
                                    @php
                                        $value = $employee->criterionValues
                                            ->where('criterion_id', $criterion->id)
                                            ->where('batch_id', $batchId)
                                            ->first()
                                            ?->value;

                                        if ($value !== null) {
                                            $totalScore += $value;
                                            $scoreCount++;
                                            $employeeScores[] = $value;
                                        }

                                        $badgeColor = 'gray';

                                        if ($value !== null) {
                                            if ($criterion->type === 'benefit') {
                                                // Higher is better
                                                if ($value >= 4.5) {
                                                    $badgeColor = 'success';
                                                } elseif ($value >= 3.5) {
                                                    $badgeColor = 'info';
                                                } elseif ($value >= 2.5) {
                                                    $badgeColor = 'warning';
                                                } else {
                                                    $badgeColor = 'danger';
                                                }
                                            } else {
                                                // Cost/Error: Lower is better
                                                if ($value <= 2.5) {
                                                    $badgeColor = 'success';
                                                } elseif ($value <= 5) {
                                                    $badgeColor = 'info';
                                                } elseif ($value <= 7.5) {
                                                    $badgeColor = 'warning';
                                                } else {
                                                    $badgeColor = 'danger';
                                                }
                                            }
                                        }
                                    @endphp

                                    <td class="fi-table-cell p-3 text-center">
                                        @if ($value !== null)
                                            <x-filament::badge :color="$badgeColor" size="sm">
                                                {{ number_format($value, 1) }}
                                            </x-filament::badge>
                                        @else
                                            <span class="text-gray-400 text-sm">–</span>
                                        @endif
                                    </td>
                                @endforeach


                                <!-- Average Score -->
                                <td class="fi-table-cell p-3 text-center">
                                    @if($scoreCount > 0)
                                        @php
                                            $average = $totalScore / $scoreCount;
                                            $avgBadgeColor = 'gray';
                                            if ($average >= 4.5) {
                                                $avgBadgeColor = 'success';
                                            } elseif ($average >= 3.5) {
                                                $avgBadgeColor = 'info';
                                            } elseif ($average >= 2.5) {
                                                $avgBadgeColor = 'warning';
                                            } else {
                                                $avgBadgeColor = 'danger';
                                            }
                                        @endphp
                                        <x-filament::badge :color="$avgBadgeColor" size="lg">
                                            {{ number_format($average, 1) }}
                                        </x-filament::badge>
                                    @else
                                        <span class="text-gray-400 text-sm">–</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-3 fi-section rounded-xl bg-gray-50 p-4 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-medium text-gray-950 dark:text-white">Performance Scale</h4>
                <div class="flex items-center gap-x-4">
                    <div class="flex items-center gap-x-2">
                        <x-filament::badge color="success" size="sm">Excellent</x-filament::badge>
                    </div>
                    <div class="flex items-center gap-x-2">
                        <x-filament::badge color="info" size="sm">Good</x-filament::badge>
                    </div>
                    <div class="flex items-center gap-x-2">
                        <x-filament::badge color="warning" size="sm">Average</x-filament::badge>
                    </div>
                    <div class="flex items-center gap-x-2">
                        <x-filament::badge color="danger" size="sm">Needs Improvement</x-filament::badge>
                    </div>
                </div>
            </div>
        </div>
</div>
