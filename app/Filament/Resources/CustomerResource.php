<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class CustomerResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Customers';
    protected static ?string $navigationLabel = 'Customers';
    protected static ?string $label = 'Customer';
    protected static ?string $slug = 'customers';
    protected static ?int $navigationSort = 31;

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
                Forms\Components\Section::make('Form Customer')
                    ->description('please fill the column')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required(),
                        Forms\Components\Select::make('companies_id')
                            ->relationship('companies', 'company_name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return $record->company_name . ' - Location: ' . ($record->villages->name ?? 'N/A');
                            })
                            ->createOptionForm([
                                Forms\Components\Section::make('Form Company')
                                    ->description('please fill the column')
                                    ->schema([
                                        Forms\Components\Select::make('villages_id')
                                            ->label('Village')
                                            ->relationship('villages', 'name')
                                            ->required()
                                            ->searchable(),
                                        Forms\Components\TextInput::make('company_name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\RichEditor::make('company_address')
                                            ->required(),
                                    ])
                            ]),
                        Forms\Components\Select::make('customer_type')
                            ->options([
                                'Perusahaan' => 'Perusahaan',
                                'Perorangan' => 'Perorangan',
                            ])
                            ->required()
                            ->searchable()
                            ->live(),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companies.company_name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('activities')
                        ->url(fn($record) => CustomerResource::getUrl('activities', ['record' => $record]))
                        ->icon('heroicon-o-clock')
                        ->color('secondary')
                        ->label('Logs'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Karyawan')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),
                        TextEntry::make('phone')
                            ->label('No. Telepon'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('customer_type')
                            ->label('Tipe')
                            ->badge()
                            ->label('Tipe'),
                        TextEntry::make('companies.company_name')
                            ->label('Perusahaan')
                            ->badge()
                            ->label('Perusahaan'),
                        TextEntry::make('companies.company_address')
                            ->label('Alamat')
                            ->markdown()
                            ->columnSpanFull()
                            ->label('Alamat'),
                    ])->columns(3),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'activities' => Pages\ListCustomerActivities::route('/{record}/activities'),
        ];
    }
}
