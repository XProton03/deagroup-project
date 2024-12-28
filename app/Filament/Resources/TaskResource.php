<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TaskResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TaskResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class TaskResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Project Management';
    protected static ?string $navigationLabel = 'Task';
    protected static ?string $label = 'Task';
    protected static ?string $slug = 'task';
    protected static ?int $navigationSort = 12;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Task')
                    ->description('please fill the column')
                    ->schema([
                        Forms\Components\TextInput::make('task_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('quotations_id')
                            ->columnSpan(2)
                            ->relationship('quotations', 'quotation_number', function ($query) {
                                $query->where('status', '!=', 'Selesai'); // Filter status yang belum selesai
                            })
                            ->required()
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $customerName = $record->customers->name ?? 'N/A';
                                $companyName = $record->customers->companies->company_name ?? 'N/A'; // Akses relasi company melalui customers
                                return $record->quotation_number . ' - ' . $record->project_name . ' - ' . $customerName . ' - ' . $companyName;
                            }),
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
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('job_description')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('schedule')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('duration')
                            ->numeric(),
                        Forms\Components\Select::make('employees_id')
                            ->relationship('employees', 'name')
                            ->preload()
                            ->searchable(),
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
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quotations.quotation_number')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('employees.name')
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
                SelectFilter::make('quotations_id')
                    ->relationship('quotations', 'quotation_number')
                    ->label('Quotation Number')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('In Progress')
                        ->label('In Progress')
                        ->color('warning')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\DatePicker::make('start_date')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            Forms\Components\Select::make('employees_id')
                                ->required()
                                ->relationship('employees', 'name')
                                ->preload()
                                ->searchable(),
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan untuk status in progress...')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            // Filter records dengan status 'Completed' atau 'Cancel'
                            $completedRecords = $records->filter(fn($record) => $record->status === 'Completed' || $record->status === 'Cancel');

                            // Jika ada record dengan status Completed atau Cancel, beri notifikasi
                            if ($completedRecords->isNotEmpty()) {
                                Notification::make()
                                    ->title('Perhatian')
                                    ->body('Beberapa data yang Anda pilih sudah memiliki status Completed atau Cancel dan tidak dapat diubah.')
                                    ->warning()
                                    ->send();
                            }

                            // Update status menjadi 'In Progress' hanya jika statusnya adalah 'Planning'
                            $records->each(function ($record) use ($data) {
                                if ($record->status == 'Planning') {
                                    $record->update([
                                        'status' => 'In Progress',
                                        'start_date' => $data['start_date'],
                                        'employees_id' => $data['employees_id'],
                                        'notes' => $data['notes'], // Update kolom note dari form modal
                                    ]);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('Completed')
                        ->label('Completed')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
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
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'status' => 'Completed',
                                    'end_date' => $data['end_date'],
                                    'duration' => $data['duration'],
                                    'notes' => $data['notes'], // Update kolom note dari form modal
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TaskfilesRelationManager::class,
            RelationManagers\TaskExpensesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
