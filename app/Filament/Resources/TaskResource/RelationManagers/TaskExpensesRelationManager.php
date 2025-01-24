<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

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
                        'Jasa' => 'Jasa',
                        'Transport' => 'Transport',
                        'Penginapan' => 'Penginapan',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->searchable(),
                Forms\Components\FileUpload::make('file')
                    ->columnSpanFull()
                    ->disk('nas')
                    ->directory('expenses')
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->openable()
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('expenses')
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('ammount')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('description')
                    ->markdown(),
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
                    Tables\Actions\Action::make('file')
                        ->label('Open File')
                        ->url(fn($record) => asset('storage/file_upload/' . $record->file))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-document')
                        ->color('primary'),
                        Tables\Actions\DeleteAction::make()->action(function ($record) {
                            // Hapus file dengan disk storage
                            if ($record->file && Storage::disk('nas')->exists($record->file)) {
                                Storage::disk('nas')->delete($record->file);
                            }
                            // Hapus data dari database
                            $record->delete();
                        }),
                ]),
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
