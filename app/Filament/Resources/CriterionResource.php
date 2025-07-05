<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CriterionResource\Pages;
use App\Filament\Resources\CriterionResource\RelationManagers;
use App\Models\Criterion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CriterionResource extends Resource
{
    protected static ?string $model = Criterion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('weight')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'benefit' => 'Benefit (Higher is better)',
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('weight')->sortable(),
                // Tables\Columns\BadgeColumn::make('type')
                //     ->colors([
                //         'success' => 'benefit',
                //         'danger' => 'cost',
                //     ])
                //     ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCriteria::route('/'),
            'create' => Pages\CreateCriterion::route('/create'),
            'edit' => Pages\EditCriterion::route('/{record}/edit'),
        ];
    }
}
