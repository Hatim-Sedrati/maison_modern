<?php

namespace App\Filament\Resources\Orders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options(OrderStatus::class)
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order')
                    ->schema([
                        TextEntry::make('order_number'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('payment_method')->badge(),
                        TextEntry::make('created_at')->dateTime(),
                    ])
                    ->columns(2),
                Section::make('Customer')
                    ->schema([
                        TextEntry::make('customer_name'),
                        TextEntry::make('phone'),
                        TextEntry::make('email')->placeholder('—'),
                        TextEntry::make('city'),
                        TextEntry::make('address')->columnSpanFull(),
                        TextEntry::make('postal_code')->placeholder('—'),
                        TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('product_name'),
                                TextEntry::make('sku')->placeholder('—'),
                                TextEntry::make('selected_size')->placeholder('—'),
                                TextEntry::make('selected_color')->placeholder('—'),
                                TextEntry::make('quantity'),
                                TextEntry::make('unit_price')->money('MAD'),
                                TextEntry::make('total')->money('MAD'),
                            ])
                            ->columns(4),
                    ]),
                Section::make('Totals')
                    ->schema([
                        TextEntry::make('subtotal')->money('MAD'),
                        TextEntry::make('delivery_fee')->money('MAD'),
                        TextEntry::make('total')->money('MAD'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->searchable()->sortable(),
                TextColumn::make('customer_name')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('city')->toggleable(),
                TextColumn::make('total')->money('MAD')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('payment_method')->badge()->toggleable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(OrderStatus::class),
                SelectFilter::make('payment_method')->options(PaymentMethod::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->label('Update status'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('items');
    }
}
