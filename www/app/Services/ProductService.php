<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    /**
     * Получаем список продуктов с поддержкой фильтрации, сортировки и пагинации. Результаты кэшируются на 5 минут.
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedProducts(Request $request)
    {
        // Создаем уникальный ключ для каждой комбинации фильтров и страницы
        $params = $request->all();
        ksort($params); // Сортируем, чтобы порядок параметров не влиял на ключ
        $cacheKey = 'products_index_' . md5(serialize($params));

        // Используем теги чтобы потом очистить весь кэш продуктов
        return Cache::tags(['products_list'])->remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            $query = Product::query();

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->filled('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }
            if ($request->filled('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }
            if ($request->filled('name')) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            }

            $sortBy = $request->query('sort', 'created_at');
            $order = $request->query('order', 'asc');
            if (in_array($sortBy, ['price', 'created_at'])) {
                $query->orderBy($sortBy, $order);
            }

            return $query->paginate(15);
        });
    }

    /**
     * Получаем один продукт по ID. Результат кэшируется на 5 минут.
     * @param int $id
     * @return Product|null
     */
    public function getSingleProduct(int $id)
    {
        $cacheKey = "product_{$id}";

        return Cache::tags(['products_list'])->remember($cacheKey, now()->addMinutes(5), function () use ($id) {
            return Product::find($id);
        });
    }
}
