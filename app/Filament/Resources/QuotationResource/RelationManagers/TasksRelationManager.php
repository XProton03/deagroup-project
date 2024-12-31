<?php

namespace App\Filament\Resources\QuotationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('task_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('companies_id')
                    ->relationship('companies', 'company_name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        return $record->company_name . ' - ' . ($record->villages->name ?? 'N/A');
                    }),
                Forms\Components\TextInput::make('pic')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required(),
                Forms\Components\RichEditor::make('short_description')
                    ->columnSpan(2),
                Forms\Components\RichEditor::make('job_description')
                    ->columnSpan(2),
                Forms\Components\DatePicker::make('schedule')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->required(),
                Forms\Components\Select::make('employees_id')
                    ->relationship('employees', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\DatePicker::make('start_date')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                Forms\Components\DatePicker::make('end_date')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                Forms\Components\TextInput::make('duration')
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'Planing' => 'Planing',
                        'In Progress' => 'In Progress',
                        'Completed' => 'Completed',
                        'Cancel' => 'Cancel',
                    ])
                    ->required()
                    ->searchable()
                    ->default('Planing'),
                Forms\Components\RichEditor::make('notes')
                    ->columnSpan(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tasks')
            ->columns([
                Tables\Columns\TextColumn::make('task_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companies.company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companies.villages.name')
                    ->label('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('schedule')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employees.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_costs.mandays')
                    ->label('Mandays')
                    ->money('IDR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_costs.transports')
                    ->label('Transport')
                    ->money('IDR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_costs.accomodations')
                    ->label('Accomodation')
                    ->money('IDR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn($state) => [
                        'Planing' => 'primary',
                        'In Progress' => 'warning',
                        'Completed' => 'success',
                        'Cancel' => 'danger',
                    ][$state] ?? 'secondary'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
