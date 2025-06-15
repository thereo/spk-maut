<div class="space-y-8">
    {{-- Step 1: Raw Scores --}}
    <div class="mt-3 fi-section rounded-xl bg-white shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header-wrapper border-b border-gray-200 p-6 dark:border-white/10">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">1</span>
                </div>
                <div>
                    <h2 class="fi-section-heading text-lg font-semibold text-gray-950 dark:text-white">
                        Raw Evaluation Scores
                    </h2>
                    <p class="fi-section-description mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Original scores collected during the evaluation process
                    </p>
                </div>
            </div>
        </div>

        <div class="fi-table-wrapper overflow-x-auto">
            <table class="fi-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="fi-table-header-cell px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Employee
                        </th>
                        @foreach ($criteria as $criterion)
                            <th class="fi-table-header-cell px-4 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <span>{{ $criterion->name }}</span>
                                    @if(isset($criterion->weight))
                                        <span class="text-xs text-gray-400 mt-1">(Weight: {{ intval($criterion->weight * 100) }}%)</span>
                                    @endif
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/5 dark:bg-gray-900">
                    @foreach ($employees as $employee)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="fi-table-cell px-6 py-4">
                                <div class="flex items-center gap-x-3">
                                    <span class="text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $employee->name }}
                                    </span>
                                </div>
                            </td>
                            @foreach ($criteria as $criterion)
                                <td class="fi-table-cell px-4 py-4 text-center">
                                    @php
                                        $rawScore = $rawData[$employee->id][$criterion->id] ?? 0;
                                        $scoreColor = 'text-gray-950 dark:text-white';
                                        if ($rawScore >= 80) $scoreColor = 'text-green-600 dark:text-green-400';
                                        elseif ($rawScore >= 60) $scoreColor = 'text-blue-600 dark:text-blue-400';
                                        elseif ($rawScore >= 40) $scoreColor = 'text-yellow-600 dark:text-yellow-400';
                                        elseif ($rawScore > 0) $scoreColor = 'text-red-600 dark:text-red-400';
                                    @endphp
                                    <span class="text-sm font-medium {{ $scoreColor }}">
                                        {{ $rawScore }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Step 2: Normalized Scores --}}
    <div class="mt-3 fi-section rounded-xl bg-white shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header-wrapper border-b border-gray-200 p-6 dark:border-white/10">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900">
                    <span class="text-sm font-bold text-purple-600 dark:text-purple-400">2</span>
                </div>
                <div>
                    <h2 class="fi-section-heading text-lg font-semibold text-gray-950 dark:text-white">
                        Normalized Scores (0-1 Scale)
                    </h2>
                    <p class="fi-section-description mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Raw scores converted to a standardized 0-1 scale for fair comparison
                    </p>
                </div>
            </div>
        </div>

        <div class="fi-table-wrapper overflow-x-auto">
            <table class="fi-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="fi-table-header-cell px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Employee
                        </th>
                        @foreach ($criteria as $criterion)
                            <th class="fi-table-header-cell px-4 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                {{ $criterion->name }}
                            </th>
                        @endforeach
                        <th class="fi-table-header-cell px-4 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Average
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/5 dark:bg-gray-900">
                    @foreach ($employees as $employee)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="fi-table-cell px-6 py-4">
                                <div class="flex items-center gap-x-3">
                                    <span class="text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $employee->name }}
                                    </span>
                                </div>
                            </td>
                            @php $totalNormalized = 0; $criteriaCount = 0; @endphp
                            @foreach ($criteria as $criterion)
                                @php
                                    $normalizedScore = $normalized[$employee->id][$criterion->id] ?? 0;
                                    $totalNormalized += $normalizedScore;
                                    $criteriaCount++;

                                    // Progress bar styling based on score
                                    $progressWidth = $normalizedScore * 100;
                                    $progressColor = 'bg-gray-300';
                                    if ($normalizedScore >= 0.8) $progressColor = 'bg-green-500';
                                    elseif ($normalizedScore >= 0.6) $progressColor = 'bg-blue-500';
                                    elseif ($normalizedScore >= 0.4) $progressColor = 'bg-yellow-500';
                                    elseif ($normalizedScore > 0) $progressColor = 'bg-red-500';
                                @endphp
                                <td class="fi-table-cell px-4 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-sm font-mono text-gray-950 dark:text-white">
                                            {{ number_format($normalizedScore, 3) }}
                                        </span>
                                        <div class="w-12 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="{{ $progressColor }} h-full rounded-full transition-all duration-300"
                                                 style="width: {{ $progressWidth }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                            <td class="fi-table-cell px-4 py-4 text-center">
                                @php $average = $criteriaCount > 0 ? $totalNormalized / $criteriaCount : 0; @endphp
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ number_format($average, 3) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Step 3: Final Weighted Score & Ranking --}}
    <div class="mt-3 fi-section rounded-xl bg-white shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header-wrapper border-b border-gray-200 p-6 dark:border-white/10">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                    <span class="text-sm font-bold text-green-600 dark:text-green-400">3</span>
                </div>
                <div>
                    <h2 class="fi-section-heading text-lg font-semibold text-gray-950 dark:text-white">
                        Final MAUT Scores & Employee Ranking
                    </h2>
                    <p class="fi-section-description mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Weighted final scores determining the overall employee performance ranking
                    </p>
                </div>
            </div>
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
                            Final Score
                        </th>
                        <th class="fi-table-header-cell px-6 py-3 text-center text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Performance Level
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/5 dark:bg-gray-900">
                    @foreach ($ranking as $index => $result)
                        @php
                            $rankBadgeColor = 'gray';
                            $performanceLevel = 'Average';
                            $performanceBadgeColor = 'warning';
                            $score = $result['score'];

                            if($index === 0) {
                                $rankBadgeColor = 'success';
                                $performanceLevel = 'Excellent';
                                $performanceBadgeColor = 'success';
                            } elseif($index < 3) {
                                $rankBadgeColor = 'info';
                                $performanceLevel = 'Very Good';
                                $performanceBadgeColor = 'info';
                            } elseif($score < 0.5) {
                                $performanceLevel = 'Needs Improvement';
                                $performanceBadgeColor = 'danger';
                            }

                            // Score bar styling
                            $scoreWidth = $score * 100;
                            $scoreBarColor = 'bg-gray-400';
                            if ($score >= 0.8) $scoreBarColor = 'bg-green-500';
                            elseif ($score >= 0.6) $scoreBarColor = 'bg-blue-500';
                            elseif ($score >= 0.4) $scoreBarColor = 'bg-yellow-500';
                            elseif ($score > 0) $scoreBarColor = 'bg-red-500';
                        @endphp

                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="fi-table-cell px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($index === 0)
                                        <x-heroicon-o-trophy class="h-5 w-5 text-yellow-500" />
                                    @elseif($index === 1)
                                        <x-heroicon-o-star class="h-5 w-5 text-gray-400" />
                                    @elseif($index === 2)
                                        <x-heroicon-o-star class="h-5 w-5 text-amber-600" />
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
                                            {{ $result['employee']->name }}
                                        </p>
                                        @if(isset($result['employee']->position))
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $result['employee']->position }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="fi-table-cell px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-lg font-bold text-gray-950 dark:text-white">
                                        {{ number_format($score, 4) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ number_format($score * 100, 2) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="fi-table-cell px-6 py-4 text-center">
                                <x-filament::badge :color="$performanceBadgeColor" size="lg">
                                    {{ $performanceLevel }}
                                </x-filament::badge>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Summary Statistics --}}
    <div class="mt-3 fi-section rounded-xl bg-white shadow ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $scores = collect($ranking)->pluck('score');
            $highestScore = $scores->max();
            $lowestScore = $scores->min();
            $averageScore = $scores->avg();
        @endphp

        <div class="fi-section rounded-xl bg-green-50 dark:bg-green-900/20 p-6">
            <div class="flex items-center gap-3">
                <x-heroicon-o-arrow-trending-up class="h-8 w-8 text-green-600" />
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Highest Score</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                        {{ number_format($highestScore, 4) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-blue-50 dark:bg-blue-900/20 p-6">
            <div class="flex items-center gap-3">
                <x-heroicon-o-calculator class="h-8 w-8 text-blue-600" />
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Average Score</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                        {{ number_format($averageScore, 4) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-red-50 dark:bg-red-900/20 p-6">
            <div class="flex items-center gap-3">
                <x-heroicon-o-arrow-trending-down class="h-8 w-8 text-red-600" />
                <div>
                    <p class="text-sm text-red-600 dark:text-red-400 font-medium">Lowest Score</p>
                    <p class="text-2xl font-bold text-red-700 dark:text-red-300">
                        {{ number_format($lowestScore, 4) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
