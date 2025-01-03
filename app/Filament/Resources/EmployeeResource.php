<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Get as FormsGet;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\Split;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\EmployeeExporter;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Collection;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\EmployeeResource\RelationManagers\EmployementFilesRelationManager;

class EmployeeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Employees';
    protected static ?string $navigationLabel = 'Employee';
    protected static ?string $label = 'Employee';
    protected static ?string $slug = 'employee';
    protected static ?int $navigationSort = 21;

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
                Forms\Components\Section::make('Informasi Karyawan')
                    ->description('Isi form ini dengan informasi pribadi karyawan.')
                    ->schema([
                        Forms\Components\TextInput::make('employee_code')
                            ->label('NIP')
                            ->readOnly()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('phone')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->numeric()
                            ->tel()
                            ->maxLength(15),
                        Forms\Components\Radio::make('gender')
                            ->options([
                                'Laki-Laki' => 'Laki-Laki',
                                'Perempuan' => 'Perempuan',
                            ])
                            ->required()
                            ->inline()
                            ->inlineLabel(false),
                        Forms\Components\TextInput::make('email')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->columnSpan(2)
                            ->maxLength(255),
                    ])
                    ->columns('3'),
                Forms\Components\Section::make('Informasi Alamat Karyawan')
                    ->schema([
                        Forms\Components\Select::make('provinces_id')
                            ->label('Province')
                            ->relationship(name: 'provinces', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('regencies_id', null);
                                $set('districts_id', null);
                            }),
                        Forms\Components\Select::make('regencies_id')
                            ->label('Regency')
                            ->options(function (FormsGet $get) {
                                return \App\Models\Regency::where('provinces_id', $get('provinces_id'))->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('districts_id', null);
                            }),
                        Forms\Components\Select::make('districts_id')
                            ->label('District')
                            ->options(function (FormsGet $get) {
                                return \App\Models\District::where('regencies_id', $get('regencies_id'))->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('villages_id')
                            ->label('Villages')
                            ->options(function (FormsGet $get) {
                                return \App\Models\Village::where('districts_id', $get('districts_id'))->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        Forms\Components\RichEditor::make('address')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpan(3),
                    ])
                    ->columns('3'),
                Forms\Components\Section::make('Status Karyawan')
                    ->schema([
                        Forms\Components\Select::make('employement_statuses_id')
                            ->label('Status')
                            ->relationship(name: 'employement_statuses', titleAttribute: 'status_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('status_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Select::make('departments_id')
                            ->label('Department')
                            ->relationship(name: 'departments', titleAttribute: 'department_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Select::make('job_positions_id')
                            ->label('Jabatan')
                            ->relationship(name: 'job_positions', titleAttribute: 'position_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('position_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\DatePicker::make('contract_start_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('contract_end_date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                    ])
                    ->columns('3'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_code')
                    ->label('NIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departments.department_name')
                    ->label('Department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_positions.position_name')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employement_statuses.status_name')
                    ->label('Status')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('activities')
                        ->url(fn($record) => EmployeeResource::getUrl('activities', ['record' => $record]))
                        ->icon('heroicon-o-clock')
                        ->color('secondary')
                        ->label('Logs'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(EmployeeExporter::class),
                    BulkAction::make('delete')
                        ->label('Delete Selected')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->action(function (Collection $records) {
                            foreach ($records as $employee) {
                                // 🗂 Hapus semua file terkait di quotation_files
                                $employee->employement_files()->each(function ($file) {
                                    if (Storage::disk('public')->exists($file->file)) {
                                        Storage::disk('public')->delete($file->file);
                                    }
                                    $file->delete();
                                });
                                $employee->delete();

                                Notification::make()
                                    ->title('Data deleted successfully!')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Karyawan')
                    ->schema([
                        Fieldset::make('Informasi Karyawan')
                            ->schema([
                                TextEntry::make('employee_code')
                                    ->columnSpan('full')
                                    ->badge()
                                    ->label('NIP'),
                                TextEntry::make('name')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('primary')
                                    ->label('Nama'),
                                TextEntry::make('birth_date')
                                    ->date()
                                    ->label('Tanggal Lahir'),
                                TextEntry::make('gender')
                                    ->badge()
                                    ->label('Jenis Kelamin'),
                                TextEntry::make('phone')
                                    ->label('No. Telepon'),
                                TextEntry::make('email')
                                    ->label('Email'),
                                TextEntry::make('provinces.name')
                                    ->label('Provinsi'),
                                TextEntry::make('regencies.name')
                                    ->label('Kabupaten/Kota'),
                                TextEntry::make('districts.name')
                                    ->label('Kecamatan'),
                                TextEntry::make('villages.name')
                                    ->label('Kelurahan'),
                                TextEntry::make('address')
                                    ->label('Alamat')
                                    ->columnSpanFull()
                                    ->markdown(),
                            ])->columns(3),
                        Fieldset::make('Status Karyawan')
                            ->schema([
                                TextEntry::make('employement_statuses.status_name')
                                    ->badge()
                                    ->label('Status'),
                                TextEntry::make('departments.department_name')
                                    ->badge()
                                    ->label('Departemen'),
                                TextEntry::make('job_positions.position_name')
                                    ->badge()
                                    ->label('Jabatan'),
                                TextEntry::make('contract_start_date')
                                    ->label('Mulai Kontrak')
                                    ->date(),
                                TextEntry::make('contract_end_date')
                                    ->label('Selesai Kontrak')
                                    ->date(),
                            ])->columns(3),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmployementFilesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'activities' => Pages\ListEmployeeActivities::route('/{record}/activities'),
        ];
    }
}
