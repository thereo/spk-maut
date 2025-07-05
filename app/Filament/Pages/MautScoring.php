<?php

namespace App\Filament\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use App\Models\Batch;
use App\Models\Criterion;
use Filament\Pages\Page;

class MautScoring extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static string $view = 'filament.pages.maut-scoring';
    protected static ?string $navigationLabel = 'MAUT Scoring';
    protected static ?string $navigationGroup = 'Calculate';

    public $batchId;
    public $data = [];

    public function mount(): void
    {
        $this->batchId = request('batchId');

        if (!$this->batchId) {
            $this->data = ['batch' => null];
            return;
        }

        $batch = Batch::with(['employees.criterionValues', 'employees'])->find($this->batchId);
        if (!$batch) {
            $this->data = ['batch' => null];
            return;
        }

        $criteria = Criterion::all();

        $raw = [];
        foreach ($batch->employees as $employee) {
            foreach ($criteria as $criterion) {
                $value = $employee->criterionValues
                    ->where('criterion_id', $criterion->id)
                    ->where('batch_id', $this->batchId)
                    ->first()?->value ?? 0;

                $raw[$employee->id][$criterion->id] = $value;
            }
        }

        // ✅ PERBAIKAN: Normalisasi MAUT yang benar
        // Hitung min dan max untuk setiap kriteria (X- dan X+)
        $normalized = [];
        foreach ($criteria as $criterion) {
            $column = array_column($raw, $criterion->id);
            $max = max($column) ?: 1;  // X+
            $min = min($column) ?: 1;  // X-

            foreach ($batch->employees as $employee) {
                $val = $raw[$employee->id][$criterion->id];

                // ✅ Formula MAUT standar untuk SEMUA kriteria: (Xi - X-) / (X+ - X-)
                $normalized[$employee->id][$criterion->id] =
                    ($max - $min > 0) ? ($val - $min) / ($max - $min) : 0;
            }
        }

        // ✅ PERBAIKAN: Weighting dengan konversi persentase
        $weighted = [];
        foreach ($normalized as $empId => $values) {
            foreach ($values as $criterionId => $val) {
                $weight = $criteria->firstWhere('id', $criterionId)?->weight ?? 0;

                // ✅ Konversi persentase ke desimal
                $weightDecimal = $weight / 100;

                $weighted[$empId][$criterionId] = $val * $weightDecimal;
            }
        }

        // Scoring
        $totals = [];
        foreach ($weighted as $empId => $values) {
            $totals[$empId] = array_sum($values);
        }

        arsort($totals);

        $this->data = [
            'batch' => $batch,
            'criteria' => $criteria,
            'employees' => $batch->employees,
            'rawData' => $raw,
            'normalized' => $normalized,
            'ranking' => collect($totals)
                ->map(fn($score, $id) => [
                    'employee' => $batch->employees->firstWhere('id', $id),
                    'score' => $score,
                ])
                ->sortByDesc('score')
                ->values()
                ->all(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return $this->batchId ? [
            Action::make('Export to pdf')
                ->action('exportPdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray'),
        ] : [];
    }

    public function exportPdf()
    {
        if (!$this->batchId) {
            return back()->with('error', 'No batch selected.');
        }

        $batch = $this->data['batch'];
        $criteria = $this->data['criteria'];
        $employees = $this->data['employees'];
        $rawData = $this->data['rawData'];
        $normalized = $this->data['normalized'];
        $ranking = $this->data['ranking'];

        $pdf = Pdf::loadView('maut.export', compact(
            'batch',
            'criteria',
            'employees',
            'rawData',
            'normalized',
            'ranking'
        ))->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'maut-batch-' . $batch->id . '.pdf');
    }
}
