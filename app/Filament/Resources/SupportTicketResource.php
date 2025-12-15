<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Support Tickets';

    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $pluralModelLabel = 'Support Tickets';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label('Ticket Number')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\Textarea::make('message')
                            ->label('User Message')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(5),
                        
                        Forms\Components\Placeholder::make('status_display')
                            ->label('Current Status')
                            ->content(fn ($record) => $record ? 
                                ($record->status === 'pending' ? 'Awaiting Reply' : 'Answered') 
                                : 'New'
                            ),
                        
                        Forms\Components\Hidden::make('status')
                            ->default('pending'),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('Conversation')
                    ->schema([
                        Forms\Components\Placeholder::make('conversation')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->replies()->exists()) {
                                    return 'No replies yet.';
                                }
                                
                                $html = '<div class="space-y-3">';
                                foreach ($record->replies()->orderBy('created_at')->get() as $reply) {
                                    $color = $reply->is_admin ? 'bg-green-50 border-green-500' : 'bg-blue-50 border-blue-500';
                                    $sender = $reply->is_admin ? 'Support' : ($reply->user->name ?? 'User');
                                    $time = $reply->created_at->format('Y/m/d H:i');
                                    
                                    $html .= "<div class='p-3 rounded border-r-4 {$color}'>";
                                    $html .= "<div class='flex justify-between mb-2'>";
                                    $html .= "<strong class='text-sm'>{$sender}</strong>";
                                    $html .= "<span class='text-xs text-gray-500'>{$time}</span>";
                                    $html .= "</div>";
                                    $html .= "<p class='text-sm whitespace-pre-wrap'>" . e($reply->message) . "</p>";
                                    $html .= "</div>";
                                }
                                $html .= '</div>';
                                
                                return new \Illuminate\Support\HtmlString($html);
                            }),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => $record && $record->replies()->exists()),
                
                Forms\Components\Section::make('New Reply')
                    ->schema([
                        Forms\Components\Textarea::make('admin_reply')
                            ->label('Reply')
                            ->rows(6)
                            ->helperText('Write your reply to the user'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket Number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->user ? "/admin/users/{$record->user_id}/edit" : null)
                    ->color('primary')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'answered',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Awaiting Reply',
                        'answered' => 'Answered',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('replied_at')
                    ->label('Replied At')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Awaiting Reply',
                        'answered' => 'Answered',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Reply')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSupportTickets::route('/'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('user');
    }
}
