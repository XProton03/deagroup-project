<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\EmployementStatus;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployementStatusResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\EmployementStatusResource\RelationManagers;

class EmployementStatusResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = EmployementStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $navigationGroup = 'Employees';
    protected static ?string $navigationLabel = 'Status';
    protected static ?string $label = 'Status';
    protected static ?string $slug = 'employee-status';
    protected static ?int $navigationSort = 22;

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
                Forms\Components\Section::make('Form Employement Status')
                    ->description('please fill the column')
                    ->schema([
                        Forms\Components\TextInput::make('status_name')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employess_count')
                    ->state(function (Model $record): int {
                        return $record->employees()->count();
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('activities')
                        ->url(fn($record) => EmployementStatusResource::getUrl('activities', ['record' => $record]))
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployementStatuses::route('/'),
            'create' => Pages\CreateEmployementStatus::route('/create'),
            'edit' => Pages\EditEmployementStatus::route('/{record}/edit'),
            'activities' => Pages\ListEmployementStatusActivities::route('/{record}/activities'),
        ];
    }
}
