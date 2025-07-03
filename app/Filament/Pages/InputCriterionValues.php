<?php

namespace App\Filament\Pages;

use App\Models\Batch;
use App\Models\Criterion;
use App\Models\EmployeeCriterionValue;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\Employee;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InputCriterionValues extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static string $view = 'filament.pages.input-criterion-values';
    protected static ?string $navigationLabel = 'Evaluation Entry';
    protected static ?string $navigationGroup = 'Evaluation';

    public $batchId;
    public $inputs = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('batchId')
                ->label('Select Batch')
                ->options(Batch::all()->pluck('name', 'id'))
                ->reactive()
                ->afterStateUpdated(fn() => $this->loadInputs())
                ->required(),

            Forms\Components\Section::make('Employee Evaluations')
                ->statePath('inputs')
                ->schema(function () {
                    if (!$this->batchId) return [];

                    $criteria = Criterion::all();
                    $employees = Batch::find($this->batchId)?->employees ?? [];

                    return $employees->map(function ($employee) use ($criteria) {
                        return Forms\Components\Fieldset::make($employee->name)
                            ->schema(
                                $criteria->map(function ($criterion) use ($employee) {
                                    return Forms\Components\Select::make("{$employee->id}.{$criterion->id}")
                                        ->label($criterion->name)
                                        ->options([
                                            1 => '1',
                                            2 => '2',
                                            3 => '3',
                                            4 => '4',
                                            5 => '5',
                                        ])
                                        ->default(1)
                                        ->required();
                                })->toArray()
                            )
                            ->columns(3);
                    })->toArray();
                }),
        ];
    }

    public function loadInputs()
    {
        $this->inputs = [];

        if (!$this->batchId) return;

        $criteria = Criterion::all();
        $employees = Batch::find($this->batchId)?->employees ?? [];

        foreach ($employees as $employee) {
            foreach ($criteria as $criterion) {
                $existing = EmployeeCriterionValue::where([
                    'employee_id' => $employee->id,
                    'criterion_id' => $criterion->id,
                    'batch_id' => $this->batchId,
                ])->first();

                $this->inputs[$employee->id][$criterion->id] = $existing?->value ?? 0;
            }
        }

        // 👇 This is important
        $this->form->fill([
            'inputs' => $this->inputs,
        ]);
    }

    public function save()
    {
        DB::beginTransaction();

        try {
            foreach ($this->inputs as $employeeId => $criteriaValues) {
                foreach ($criteriaValues as $criterionId => $value) {
                    $value = trim((string)$value);

                    if (!is_numeric($value)) {
                        Log::warning("Invalid value for Employee $employeeId / Criterion $criterionId: '$value'");
                        continue;
                    }

                    EmployeeCriterionValue::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'criterion_id' => $criterionId,
                            'batch_id' => $this->batchId,
                        ],
                        [
                            'value' => floatval($value),
                        ]
                    );
                }
            }


            DB::commit();

            Notification::make()
                ->title('Values saved successfully!')
                ->success()
                ->send();

            return redirect()->to('/admin/evaluation-list'); // default admin prefix
        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error saving values')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }


    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('Save')->action('save'),
        ];
    }
}
