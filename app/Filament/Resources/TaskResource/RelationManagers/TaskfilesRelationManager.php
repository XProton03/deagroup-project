<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
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
                    ->acceptedFileTypes(['application/pdf'])
                    ->deleteUploadedFileUsing(function ($file, $record) {
                        if ($record && $record->file) {
                            // Hapus file lama
                            Storage::disk('nas')->delete($record->file);
                        }
                    }),
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
                        ->url(fn($record) => asset('storage/file_upload/' . $record->file))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-document')
                        ->color('primary'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->action(function ($record) {
                        // Hapus file dengan disk storage
                        if ($record->file && Storage::disk('nas')->exists($record->file)) {
                            Storage::disk('nas')->delete($record->file);
                        }
                        // Hapus data dari database
                        $record->delete();
                    }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('delete_files')
                        ->label('Delete Files')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                // Hapus file dari storage
                                Storage::disk('nas')->delete($record->file);

                                // Hapus record dari database
                                $record->delete();
                            }
                            Notification::make()
                                ->title('Files deleted successfully!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-trash'),
                ]),
            ]);
    }
}
