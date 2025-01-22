<?php

namespace App\Filament\Resources\QuotationResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use App\Filament\Exports\TaskExporter;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

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
                        return $record->company_name . ' - ' . ($record->villages->districts->regencies->name ?? 'N/A');
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
                Tables\Columns\TextColumn::make('companies.villages.districts.regencies.name')
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
                        'Document Progress' => 'info',
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
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Action::make('in_progress')
                        ->label('In Progress')
                        ->color('info')
                        ->visible(fn($record): bool => $record->status === 'Planing')
                        ->form([
                            Forms\Components\DatePicker::make('start_date')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            Forms\Components\Select::make('employees_id')
                                ->relationship('employees', 'name')
                                ->preload()
                                ->searchable(),
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan untuk status inprogress...')
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            // Simpan data ke database
                            \App\Models\Task::where('id', $record->id)->update([
                                'status'        => 'In Progress',
                                'start_date'    => $data['start_date'],
                                'employees_id'  => $data['employees_id'],
                                'notes'         => $data['notes'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Set to In Progress successfully!')
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-o-cog-8-tooth'),
                    Action::make('document_progress')
                        ->label('Document Progress')
                        ->color('warning')
                        ->visible(fn($record): bool => $record->status === 'In Progress')
                        ->form([
                            Forms\Components\DatePicker::make('end_date')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            Forms\Components\TextInput::make('duration')
                                ->required()
                                ->numeric(),
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan untuk status completed...')
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            // Simpan data ke database
                            \App\Models\Task::where('id', $record->id)->update([
                                'status'    => 'Document Progress',
                                'end_date'  => $data['end_date'],
                                'duration'  => $data['duration'],
                                'notes'     => $data['notes'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Set to Document Progress successfully!')
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-o-currency-dollar'),
                    Action::make('completed')
                        ->label('Completed')
                        ->visible(fn($record): bool => $record->status === 'Document Progress')
                        ->color('success')
                        ->form([
                            Forms\Components\TextInput::make('mandays')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('transports')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('accomodations')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('file_name')
                                ->required()
                                ->columnSpanFull()
                                ->maxLength(255),
                            Forms\Components\FileUpload::make('file')
                                ->columnSpanFull()
                                ->directory('tasks')
                                ->preserveFilenames()
                                ->maxSize(2048)
                                ->openable()
                                ->acceptedFileTypes(['application/pdf']),
                        ])
                        ->action(function (array $data, $record) {
                            $record->update(['status' => 'Completed']);
                            // Simpan data ke database
                            \App\Models\JobCost::create([
                                'tasks_id'      => $record->id,
                                'mandays'       => $data['mandays'],
                                'transports'    => $data['transports'],
                                'accomodations' => $data['accomodations'],
                            ]);
                            \App\Models\TaskFile::create([
                                'tasks_id'  => $record->id,
                                'file_name' => $data['file_name'],
                                'file'      => $data['file'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Job Cost and file saved successfully!')
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-o-check-circle'),
                    Action::make('files')
                        ->label('Expenses')
                        ->color('primary')
                        ->form([
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
                        ])
                        ->action(function (array $data, $record) {
                            $record->update(['status' => 'Completed']);
                            // Simpan data ke database
                            \App\Models\TaskFile::create([
                                'tasks_id'      => $record->id,
                                'ammount'       => $data['ammount'],
                                'type'          => $data['type'],
                                'file'          => $data['file'],
                                'description'   => $data['description'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Data saved successfully!')
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-o-paper-clip'),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Cancel')
                        ->label('Cancel')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->form([
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan untuk status Cancel...')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'status' => 'Cancel',
                                    'schedule' => null,
                                    'notes' => $data['notes'], // Update kolom note dari form modal
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    ExportBulkAction::make()
                        ->exporter(TaskExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
