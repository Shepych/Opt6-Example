@extends('layouts.main')

@section('content')
    <h1 class="text-center">{{ $title }}</h1>

    <div class="container">
        Phone: {{ $order->phone }}
        <hr>
        E-Mail: {{ $order->email }}
        <hr>
        Price: {{ $order->price }}
        <hr>
        Date: {{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d.m.Y') }}
        <hr>
        Address: {{ $order->address }}
        <hr>
        <div>
            Товары:
            @foreach($order->products as $product)
                <ul>
                    <li>Название: {{ $product->name }}</li>
                    <li>Количество: {{ $product->pivot->count }}</li>
                </ul>
            @endforeach
        </div>

        <div id="map" class="mt-3" style="width: 600px; height: 400px;margin: 0 auto;margin-bottom: 15px"></div>
    </div>

    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Назад</a>
    </div>
@endsection

@section('js')
    <script src="https://api-maps.yandex.ru/2.1/?apikey=553ff084-f7c4-4c69-86f3-db918c3d46b7&lang=ru_RU"></script>
    <script>
        let point = {{ $order->point }};

        // Функция ymaps.ready() будет вызвана, когда
        // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
        ymaps.ready(init);
        function init(){
            // Создание карты.
            let myMap = new ymaps.Map("map", {
                // Координаты центра карты.
                // Порядок по умолчанию: «широта, долгота».
                // Чтобы не определять координаты центра карты вручную,
                // воспользуйтесь инструментом Определение координат.
                center: point,
                // Уровень масштабирования. Допустимые значения:
                // от 0 (весь мир) до 19.
                zoom: 16
            });

            let placemark = new ymaps.Placemark(point, {}, {

            });

            myMap.geoObjects.add(placemark);
        }

        ymaps.Geo
    </script>
@endsection
