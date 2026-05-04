<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProductServiceCacheTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверяет, что после первого вызова продукт сохраняется в кэше.
     * @return void
     */
    public function test_product_is_cached_after_first_call(): void
    {
        // Отключаем события, чтобы Observer не очищал кэш
        Event::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name'    => 'Test Product',
            'price'   => 200.0,
            'user_id' => $user->id,
        ]);

        $productService = new ProductService();
        $cacheKey = "product_{$product->id}";
        $tagCache = Cache::tags(['products_list']);

        // Убедимся, что кэш пуст перед первым вызовом
        $tagCache->forget($cacheKey);

        // Первый вызов — данные берутся из БД и сохраняются в кэш
        $result = $productService->getSingleProduct($product->id);
        $this->assertInstanceOf(Product::class, $result);

        // Проверяем, что продукт появился в кэше
        $this->assertTrue($tagCache->has($cacheKey));
    }
}
