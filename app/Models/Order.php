<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Order extends Model
{
    use HasFactory;

    public static $yandexApiUrl = 'https://geocode-maps.yandex.ru/1.x';
    public static $yandexApiKey = '553ff084-f7c4-4c69-86f3-db918c3d46b7';

    protected $fillable = ['email', 'phone', 'address', 'price'];

    public function products() {
        return $this->belongsToMany(Product::class)->withPivot('count');
    }

    # Перерасчёт стоимости заказа
    public static function getFullPrice($orderId) {
        $products = Order::where('id', $orderId)->first()->products;
        $summ = 0;
        foreach ($products as $product) {
            $summ+= $product->price * $product->pivot->count;
        }
        return $summ;
    }

    public static function addressPointsMap($address) {
        $http = Http::accept('application/json')->get(self::$yandexApiUrl, [
            'geocode' => $address,
            'apikey' => self::$yandexApiKey,
            'format' => 'json',
        ])->json();

        if(empty($http['response']['GeoObjectCollection']['featureMember'])) {
            return null;
        }

        $coordinates = $http['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];

        # Обработать ответ для загрузки в БД
        $points = array_reverse(explode(' ', $coordinates));

        foreach ($points as &$point) {
            $point = (double) $point;
        }

        return json_encode($points);
    }

    public static function minPrice($jsonProducts) {
        $price = 0;
        foreach (json_decode($jsonProducts) as $key => $number) {
            if(is_int((int) $number)) {
                # Проверка товара
                $price += Product::where('id', $key)->first()->price * (int) $number;
            }
        }

        if($price < 3000) {
            return false;
        }

        return true;
    }
}
