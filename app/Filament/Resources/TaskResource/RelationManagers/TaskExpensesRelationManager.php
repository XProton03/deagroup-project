<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'task_expenses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ammount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('type')
                    ->options([
                        'MCU' => 'MCU',
                        'Surat Sehat' => 'Surat Sehat',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->searchable(),
                Forms\Components\FileUpload::make('file')
                    ->columnSpanFull()
                    ->directory('tasks')
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->openable()
                    ->acceptedFileTypes(['application/pdf']),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('expenses')
            ->columns([
                Tables\Columns\TextColumn::make('expenses'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
