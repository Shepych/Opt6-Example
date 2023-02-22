<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 1000; $i++) {
            $order = Order::create([
                'phone' => '899999' . $i,
                'email' => $i . '@ro.ru',
                'address' => 'Адрес ' .$i,
                'point' => '[55, 37]',
            ]);

            $products = Product::all()->random(rand(1, 4));

            foreach ($products as $product) {
                $order->products()->attach($product->id);

                $prod = $order->products()->where('product_id', $product->id)->first()->pivot;
                $prod->count += rand(1, 5);
                $prod->update();

                $order->price = $order->getFullPrice($order->id);
                $order->update();
            }
        }
    }
}
