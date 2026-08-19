<?php

namespace App\Filament\Resources\Products;

use App\Enums\ProductGender;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (?string $state, Set $set) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Leave empty to generate from the name.'),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('gender')
                            ->options(ProductGender::class)
                            ->required(),
                        TextInput::make('sku')
                            ->required()
                            ->maxLength(64)
                            ->unique(ignoreRecord: true),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('MAD')
                            ->minValue(0)
                            ->step(0.01),
                        TextInput::make('compare_at_price')
                            ->numeric()
                            ->prefix('MAD')
                            ->minValue(0)
                            ->step(0.01),
                        TextInput::make('stock')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Used only when this product has no variants.'),
                        Toggle::make('is_active')->default(true),
                        Toggle::make('is_featured')->default(false),
                        TextInput::make('short_description')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Variants')
                    ->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->schema([
                                TextInput::make('size')->maxLength(50),
                                TextInput::make('color')->maxLength(50),
                                TextInput::make('sku')
                                    ->required()
                                    ->maxLength(64)
                                    ->distinct(),
                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('MAD')
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Leave empty to use the product price.'),
                                TextInput::make('stock')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),
                                Toggle::make('is_active')->default(true),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->defaultItems(0)
                            ->addActionLabel('Add variant')
                            ->itemLabel(fn (array $state): string => trim(($state['size'] ?? '').' / '.($state['color'] ?? ''), ' /') ?: ($state['sku'] ?? 'Variant')),
                    ])
                    ->collapsed(),
                Section::make('Images')
                    ->description('Upload fashion photos. They are stored on Cloudinary and optimized for the storefront. Mark one image as primary.')
                    ->schema([
                        Repeater::make('images')
                            ->relationship()
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Image')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->directory('products')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->required()
                                    ->imagePreviewHeight('180')
                                    ->openable()
                                    ->downloadable(false)
                                    ->getUploadedFileUsing(function ($component, string $file): ?array {
                                        if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
                                            return [
                                                'name' => basename((string) (parse_url($file, PHP_URL_PATH) ?: $file)),
                                                'size' => 0,
                                                'type' => 'image/jpeg',
                                                'url' => $file,
                                            ];
                                        }

                                        $storage = Storage::disk('public');

                                        if (! $storage->exists($file)) {
                                            return null;
                                        }

                                        return [
                                            'name' => basename($file),
                                            'size' => $storage->size($file),
                                            'type' => $storage->mimeType($file) ?: 'image/jpeg',
                                            'url' => $storage->url($file),
                                        ];
                                    })
                                    ->columnSpanFull(),
                                Hidden::make('public_id'),
                                Toggle::make('is_primary')
                                    ->label('Primary image')
                                    ->helperText('Shown first on product cards and the product page.'),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->reorderable('sort_order')
                            ->defaultItems(0)
                            ->addActionLabel('Add image')
                            ->itemLabel(fn (array $state): string => ! empty($state['is_primary']) ? 'Primary image' : 'Gallery image'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_image')
                    ->label('Image')
                    ->getStateUsing(fn (Product $record): ?string => $record->primaryImage?->urlFor('thumb')
                        ?? $record->images->first()?->urlFor('thumb'))
                    ->square(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->sortable(),
                TextColumn::make('gender')->badge(),
                TextColumn::make('price')->money('MAD')->sortable(),
                TextColumn::make('sku')->searchable()->toggleable(),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name'),
                SelectFilter::make('gender')->options(ProductGender::class),
                TernaryFilter::make('is_active')->label('Active'),
                TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'primaryImage', 'images']);
    }
}
