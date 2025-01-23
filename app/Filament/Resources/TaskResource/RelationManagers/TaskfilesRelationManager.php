<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'task_files';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('file_name')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file')
                    ->columnSpanFull()
                    ->disk('nas')
                    ->directory('tasks')
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->openable()
                    ->acceptedFileTypes(['application/pdf']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file')
            ->columns([
                Tables\Columns\TextColumn::make('file_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('file')
                        ->label('Open File')
                        ->url(fn($record) => 'http://192.168.2.56/files/' . $record->file)
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-document')
                        ->color('primary'),
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
