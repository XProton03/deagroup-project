<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EmployementFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'employement_files';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('file_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file')
                    ->required()
                    ->columnSpan(2)
                    ->preserveFilenames()
                    ->maxSize(5120)
                    ->openable()
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->imageEditor()
                    ->directory('employees')
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file')
            ->columns([
                Tables\Columns\TextColumn::make('file_name'),
                Tables\Columns\TextColumn::make('file')
                    ->label('File')
                    ->url(fn($record) => asset('storage/' . $record->file)) // Menyesuaikan dengan path file Anda
                    ->openUrlInNewTab(),
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
                    Tables\Actions\DeleteAction::make()->action(function ($record) {
                        // Hapus file dengan disk storage
                        if ($record->file && Storage::disk('public')->exists($record->file)) {
                            Storage::disk('public')->delete($record->file);
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
                                Storage::disk('public')->delete($record->file);

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
