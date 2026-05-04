<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Очищаем кэш списка продуктов при сохранении продукта, чтобы обеспечить актуальность данных при следующем запросе.
     * @param Product $product
     * @return void
     */
    public function saved(Product $product): void
    {
        Cache::tags(['products_list'])->flush();
    }

    /**
     * Очищаем кэш списка продуктов при удалении продукта, чтобы обеспечить актуальность данных при следующем запросе.
     * @param Product $product
     * @return void
     */
    public function deleted(Product $product): void
    {
        Cache::tags(['products_list'])->flush();
    }
}
