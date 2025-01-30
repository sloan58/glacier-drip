<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ManifestFile;
use App\Enums\ManifestStatus;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Middleware\OnboardingMiddleware;
use App\Filament\Resources\ManifestFileResource\Pages;

class ManifestFileResource extends Resource
{
    protected static string|array $withoutRouteMiddleware = [
        OnboardingMiddleware::class
    ];

    protected static ?string $model = ManifestFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('archive_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('size')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('sha256_tree_hash')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('creation_date'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->poll()
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                if (!$user->hasRole('admin')) {
                    return $query->where('user_id', $user->id);
                }

                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => strtoupper($state)) // Optional: Convert to uppercase
                    ->color(fn($state) => match ($state) {
                        ManifestStatus::PROCESSING->value => 'warning',
                        ManifestStatus::FINISHED->value => 'success',
                        ManifestStatus::ERROR->value => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('archive_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sha256_tree_hash')
                    ->searchable(),
                Tables\Columns\TextColumn::make('creation_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Request On')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Time Elapsed')
                    ->getStateUsing(fn($record) => $record->created_at->diffForHumans())
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListManifestFiles::route('/'),
            'create' => Pages\CreateManifestFile::route('/create'),
            'edit' => Pages\EditManifestFile::route('/{record}/edit'),
        ];
    }
}
