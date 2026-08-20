<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewStorefront')
                ->label('View storefront')
                ->url(fn (): string => route('product.show', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => (bool) $this->record?->is_active),
            DeleteAction::make()
                ->modalDescription('Only products that have never been ordered can be deleted. Otherwise, deactivate the product instead.'),
        ];
    }
}
