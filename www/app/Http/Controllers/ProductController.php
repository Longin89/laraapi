<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Validatable;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use App\Services\ProductService;

class ProductController extends Controller implements HasMiddleware
{
    use Validatable;

    /**
     * Определение middleware для контроллера. Все методы, кроме index и show, требуют аутентификации.
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * Вывод списка продуктов с пагинацией. Доступно без аутентификации.
     * @param Request $request
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, ProductService $productService)
    {
        $products = $productService->getPaginatedProducts($request);

        return response()->json([
            'status' => true,
            'message' => 'Список продуктов',
            'data' => [
                'products' => ProductResource::collection($products),
                'pagination' => [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ],
            ],
        ], 200);
    }

    /**
     * Создание нового продукта. Требует аутентификации. Валидация входящих данных осуществляется через Validatable trait.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        if ($validated) {
            return $validated;
        }

        $product = $request->user()->products()->create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Продукт успешно создан',
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * Вывод информации о конкретном продукте по ID. Доступно без аутентификации. Если продукт не найден, возвращается 404.
     * @param int $id
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id, ProductService $productService)
    {
        $product = $productService->getSingleProduct($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Продукт не найден',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Продукт найден',
            'data' => new ProductResource($product),
        ], 200);
    }

    /**
     * Вывод информации о конкретном продукте по ID. Доступно без аутентификации. Если продукт не найден, возвращается 404.
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function update(Request $request, int $id)
    {
        Gate::authorize('modify', Product::find($id));
        $validated = $this->validateRequest($request);

        if ($validated) {
            return $validated;
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Продукт не найден',
            ], 404);
        }

        $product->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Продукт успешно обновлен',
            'data' => new ProductResource($product),
        ], 200);
    }

    /**
     * Удаление продукта по ID. Требует аутентификации. Если продукт не найден, возвращается 404.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function destroy(int $id)
    {
        Gate::authorize('modify', Product::find($id));
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Продукт не найден',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Продукт успешно удален',
        ], 200);
    }
}
