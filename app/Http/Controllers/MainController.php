<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Dirape\Token\Token;

class MainController extends Controller
{
    # Панель администратора
    public function dashboard() {

        $orders = Order::all();
        $title = 'Dashboard';
        return view('dashboard', compact('orders', 'title'));
    }

    # Страница заказа
    public function order($id) {
        $order = Order::where('id', $id)->first();
        if(!$order) {
            return redirect(route('dashboard'));
        }
        $title = 'Order №' . $id;
        return view('order', compact('order', 'title'));
    }
}
