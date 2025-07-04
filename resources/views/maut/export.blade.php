<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MAUT Scoring Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            margin: 20px;
        }
        h1, h2, h3 {
            color: #2c5aa0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f2f6fc;
            font-weight: bold;
        }
        .text-left { text-align: left; }
        .highlight {
            background-color: #fffde7;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            background-color: #ff8f00;
            color: #fff;
            font-size: 10px;
            border-radius: 4px;
        }
        .stat-block {
            margin: 10px 0;
            font-size: 13px;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 10px;
            color: #856404;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h1 style="text-align: center;">MAUT Scoring Report</h1>

    <h2>Batch: {{ $batch->name }}</h2>
    <p><strong>Generated:</strong> {{ now()->format('Y-m-d H:i') }}</p>

    @if(count($employees) === 0 || count($criteria) === 0)
        <div class="alert">
            <strong>Warning:</strong> This batch does not have enough data. Please ensure employees and criteria are assigned.
        </div>
        @php return; @endphp
    @endif

    @php
        $highestScore = $ranking[0]['score'] ?? 0;
        $lowestScore = $ranking[count($ranking) - 1]['score'] ?? 0;
        $averageScore = collect($ranking)->avg('score');
        $medianScore = collect($ranking)->median('score');
        $scores = collect($ranking)->pluck('score');
        $mean = $scores->avg();
        $variance = $scores->map(fn($s) => pow($s - $mean, 2))->avg();
        $stdDev = sqrt($variance);
    @endphp

    <div class="stat-block">
        <strong>Summary:</strong><br>
        Total Employees: {{ count($employees) }} |
        Criteria Used: {{ count($criteria) }} |
        Highest Score: {{ number_format($highestScore, 4) }} |
        Lowest Score: {{ number_format($lowestScore, 4) }} |
        Avg: {{ number_format($averageScore, 4) }} |
        Median: {{ number_format($medianScore, 4) }} |
        Std Dev: {{ number_format($stdDev, 4) }}
    </div>

    {{-- Step 1: Raw Values --}}
    <h3>Step 1: Raw Performance Values</h3>
    <table>
        <thead>
            <tr>
                <th class="text-left">Employee</th>
                @php $colWidth = round(75 / max(1, count($criteria)), 2); @endphp
                @foreach ($criteria as $criterion)
                    <th style="width: {{ $colWidth }}%;">{{ $criterion->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr>
                    <td class="text-left">{{ $employee->name }}</td>
                    @foreach ($criteria as $criterion)
                        <td>{{ number_format($rawData[$employee->id][$criterion->id] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Step 2: Normalized Values --}}
    <h3>Step 2: Normalized Values</h3>
    <table>
        <thead>
            <tr>
                <th class="text-left">Employee</th>
                @foreach ($criteria as $criterion)
                    <th>
                        {{ $criterion->name }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr>
                    <td class="text-left">{{ $employee->name }}</td>
                    @foreach ($criteria as $criterion)
                        <td>{{ number_format($normalized[$employee->id][$criterion->id] ?? 0, 4) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Step 3: Weights --}}
    <h3>Step 3: Criterion Weights</h3>
    <table>
        <thead>
            <tr>
                @foreach ($criteria as $criterion)
                    <th>{{ $criterion->name }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($criteria as $criterion)
                    <td>{{ number_format($criterion->weight) }}%</td>
                @endforeach
                <td><strong>{{ number_format($criteria->sum('weight')) }}%</strong></td>
            </tr>
        </tbody>
    </table>

    @if(abs($criteria->sum('weight') - 100) > 0.001)
        <div class="alert">
            <strong>Warning:</strong> Weights do not sum to 1.0. This may affect scoring accuracy.
        </div>
    @endif

    {{-- Step 4: Final Ranking --}}
    <h3>Step 4: Final Scoring & Ranking</h3>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th class="text-left">Employee</th>
                <th>Score</th>
                <th>Performance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ranking as $index => $row)
                <tr @if($index === 0) class="highlight" @endif>
                    <td>
                        {{ $index + 1 }}
                        @if($index === 0)
                            <span class="badge">Top Performer</span>
                        @endif
                    </td>
                    <td class="text-left">{{ $row['employee']->name }}</td>
                    <td>{{ number_format($row['score'], 4) }}</td>
                    <td>
                        @if($index === 0)
                            Excellent
                        @elseif($index <= 2)
                            Very Good
                        @elseif($row['score'] >= 0.6)
                            Good
                        @else
                            Fair
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr style="margin-top: 40px; border-top: 1px dashed #aaa;">
    <p style="text-align: center; font-size: 10px; margin-top: 10px;">
        Generated using Multi-Attribute Utility Theory (MAUT)<br>
        <em>Confidential - Internal Use Only</em>
    </p>
</body>
</html>
