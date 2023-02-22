<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * @OA\Info(title="ТЕСТОВОЕ API", version="1.0"),
     * * @OA\Get(
     *     tags={"Заказы"},
     *     path="/api/order/create",
     *     description="Создание заказа",
     *     @OA\Response(response="200", description="The data"),
     *     @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Почта"),
     *     @OA\Parameter(
     *     name="phone",
     *     in="query",
     *     description="Телефон"),
     *     @OA\Parameter(
     *     name="address",
     *     in="query",
     *     description="Адрес"),
     *     @OA\Parameter(
     *     name="products",
     *     in="query",
     *     description="Продукты в формате JSON: {""product_id"": count} где product_id - это id продукта, а count - это его количество. Пример: {""1"": 3, ""2"": 5}"),
     *     @OA\Parameter(
     *     name="api_key",
     *     in="query",
     *     description="API ключ"),
     * ),
     *
     * * * @OA\Get(
     *     tags={"Заказы"},
     *     path="/api/order/update",
     *     description="Изменение заказа",
     *     @OA\Response(response="200", description="The data"),
     *     @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="ID Заказа"),
     *     @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Почта"),
     *     @OA\Parameter(
     *     name="phone",
     *     in="query",
     *     description="Телефон"),
     *     @OA\Parameter(
     *     name="address",
     *     in="query",
     *     description="Адрес"),
     *     @OA\Parameter(
     *     name="products",
     *     in="query",
     *     description="Продукты в формате JSON: {""product_id"": count} где product_id - это id продукта, а count - это его количество. Пример: {""1"": 3, ""2"": 5}"),
     *     @OA\Parameter(
     *     name="api_key",
     *     in="query",
     *     description="API ключ"),
     * ),
     *
     *  @OA\Get(
     *     tags={"Заказы"},
     *     path="/api/order/read",
     *     description="Информация о заказе",
     *     @OA\Response(response="200", description="The data"),
     *     @OA\Response(response="404", description="error"),
     *     @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="ID Заказа",),
     *     @OA\Parameter(
     *     name="api_key",
     *     in="query",
     *     description="API ключ")
     * ),
     * *  @OA\Get(
     *     tags={"Заказы"},
     *     path="/api/order/delete",
     *     description="Удаление заказа",
     *     @OA\Response(response="200", description="The data"),
     *     @OA\Response(response="404", description="error"),
     *     @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="ID Заказа",),
     *     @OA\Parameter(
     *     name="api_key",
     *     in="query",
     *     description="API ключ",),
     * ),
     * * *  @OA\Get(
     *     tags={"Заказы"},
     *     path="/api/orders",
     *     description="Список заказов",
     *     @OA\Response(response="200", description="The data"),
     *     @OA\Response(response="404", description="error"),
     *     @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Номер страницы",),
     *     @OA\Parameter(
     *     name="paginate",
     *     in="query",
     *     description="Количество отображаемых заказов",),
     *     @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="Фильтрация заказов по дате (принимает любое значение)",),
     *     @OA\Parameter(
     *     name="api_key",
     *     in="query",
     *     description="API ключ",),
     * ),
     */

    private $paginate = 2;

    # Создание заказа
    public function orderCreate(Request $request) {
        $validate = Validator::make($request->all(), [
            'phone' => 'required|integer',
            'address' => 'required|max:500',
            'email' => 'required|email',
            'products' => 'required|json',
        ])->errors();
        if($validate->any()) {
            return ['status' => ['error' => $validate->all()]];
        }

        # Валидация цены заказа
        if(!Order::minPrice($request->products)) {
            return ['status' => ['error' => 'Min price is 3000']];
        }

        # Добавить заказ в базу
        $order = Order::create([
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
        ]);

        # API запрос на яндекс карты
        $order->point = Order::addressPointsMap($order->address);

        # Добавить продукты из JSON в базу
        if($request->products) {
            # Обновление товаров
            foreach (json_decode($request->products) as $key => $number) {
                # Проверка товара
                if(Product::where('id', $key)->first()) {
                    # Проверка количества товара на число
                    if(is_int((int) $number)) {
                        if(!$order->products->contains($key)) {
                            $order->products()->attach($key);
                        }

                        $product = $order->products()->where('product_id', $key)->first()->pivot;
                        $product->count += $number;
                        $product->update();
                    }
                }
            }

            $order->price = Order::getFullPrice($order->id);
        }
        $order->update();

        # Вывод результата
        $order = Order::where('id', $order->id)->first();
        $order->products;

        return $order;
    }

    # Обновление заказа
    public function orderUpdate(Request $request) {
        $order = Order::where('id', $request->id)->first();

        if(!$order) {
            return ['error' => 'Order not found'];
        }

        # Валидация
        $validate = Validator::make($request->all(), [
            'id' => 'required|integer',
            'phone' => 'integer',
            'address' => 'max:500',
            'email' => 'email',
            'products' => 'json',
        ])->errors();
        if($validate->any()) {
            return ['status' => ['error' => $validate->all()]];
        }

        # Валидация цены заказа
        if(!Order::minPrice($request->products)) {
            return ['status' => ['error' => 'Min price is 3000']];
        }

        # Обновление данных о заказе
        if($request->phone) {
            $order->phone = $request->phone;
        }
        if($request->address) {
            $order->address = $request->address;
            #API MAPS
            $order->point = Order::addressPointsMap($order->address);
        }
        if($request->email) {
            $order->email = $request->email;
        }

        if($request->products) {
            $order->products()->detach();
            # Обновление товаров
            foreach (json_decode($request->products) as $key => $number) {
                # Проверка товара
                if(Product::where('id', $key)->first()) {
                    # Проверка количества товара на число
                    if(is_int((int) $number)) {
                        $order->products()->attach($key);

                        $product = $order->products()->where('product_id', $key)->first()->pivot;
                        $product->count += $number;
                        $product->update();
                    }
                }
            }

            $order->price = Order::getFullPrice($order->id);
        }

        $order->update();

        $resultOrder = Order::where('id', $order->id)->first();
        $resultOrder->products;

        return [
            'order' => $resultOrder,
        ];
    }

    # Информация о заказе
    public function orderRead(Request $request) {
        # Обработать не найденный заказ ответом
        $order = Order::where('id', $request->id)->first();

        if(!$order) {
            return ['error' => 'Order not found'];
        }
        # Подгрузка продуктов
        $order->products;
        return $order;
    }

    # Удаление заказа
    public function orderDelete(Request $request) {
        $order = Order::where('id', $request->id)->first();

        if(!$order) {
            return ['error' => 'Order not found'];
        }

        $order->products()->detach();
        $order->delete();

        return ['error' => 'Order deleted'];
    }

    # Список заказов
    public function ordersList(Request $request) {
        $paginate = is_int((int) $request->paginate) ? $request->paginate : $this->paginate;
        $filterDate = $request->filter ? true : false;
        if($filterDate) {
            $list = Order::orderBy('created_at')->paginate($paginate);
        } else {
            $list = Order::paginate($paginate);
        }

        return $list->items();
    }
}
