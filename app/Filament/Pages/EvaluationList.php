<?php

namespace App\Filament\Pages;

use App\Models\Batch;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class EvaluationList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string $view = 'filament.pages.evaluation-list';
    protected static ?string $navigationGroup = 'Evaluation';

    protected function getTableQuery()
    {
        return Batch::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Batch Name')
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('View')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->modalHeading(fn(Batch $record) => "{$record->name}")
                ->modalContent(function (Batch $record) {
                    $employees = $record->employees()
                        ->with(['criterionValues' => function ($query) use ($record) {
                            $query->where('batch_id', $record->id)->with('criterion');
                        }])
                        ->get();

                    return view('filament.components.employee-modal-table', [
                        'employees' => $employees,
                        'criteria' => \App\Models\Criterion::all(),
                        'batchId' => $record->id,
                        'batch' => $record,
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }
}
