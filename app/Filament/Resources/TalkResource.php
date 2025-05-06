<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Talk::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersTriggerAction(fn($action) => $action->button()->label('Filters'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => Str::limit($record->abstract , 30 )),

                Tables\Columns\ImageColumn::make('speaker.avatar')
                    ->label('Speaker Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=8A2BE2&color=fff&name=' . urlencode($record->speaker?->name);
                    }),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('new_talk'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),

                Tables\Columns\IconColumn::make('length')
                    ->icon(fn ($state) => $state->getIcon()),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('new_talk'),
                Tables\Filters\SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('has_avatar')
                    ->query(function ($query) {
                        return $query->whereHas('speaker',function (Builder $q) {
                            $q->whereNotNull('avatar');
                        });
                    })->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Talk $record) => $record->approved())
                        ->after(function () {
                            Notification::make()->success()->title('Your Talk is Approved')->body('The Speaker Has Been Approved Talk in This Conference')->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Talk $record) => $record->rejected())
                        ->after(function () {
                            Notification::make()->danger()->title('Your Talk is Rejected')->body('The Speaker Has Been Rejected Talk in This Conference')->send();
                        }),
                ])->visible(fn (Talk $record) => $record->status === TalkStatus::SUBMITTED),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->action(fn(Collection $records) => $records->each->approved()),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
//            'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
