@extends('layouts.main')

@section('content')
    <h1 style="text-align: center">{{ $title }}</h1>

    <div class="d-flex mb-3" style="gap: 20px;">
        <a target="_blank" href="https://github.com/Shepych/Opt6-Example" class="container bg-dark rounded d-flex text-center justify-content-center align-items-center" style="height: 200px;font-size: 40px;color:white;text-decoration: none">GIT</a>
        <a target="_blank" href="/api/documentation" class="container bg-info rounded d-flex text-center justify-content-center align-items-center" style="font-size: 40px; color: black;text-decoration: none">API</a>
    </div>

    <table id="orders">
        <thead>
        <tr>
            <th>E-Mail</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Price</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td><a href="{{ route('order', $order->id) }}">{{ $order->email }}</a></td>
                <td>{{ $order->phone }}</td>
                <td>{{ $order->address }}</td>
                <td>{{ $order->price }}</td>
                <td>
                    {{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d.m.Y') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <form action="{{ route('logout') }}" method="post" class="text-center">
        @csrf
        <input type="submit" class="btn btn-danger" value="Выход">
    </form>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready( function () {
            // $('#orders').DataTable();

            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                "ruDate-asc": function ( a, b ) {
                    var ruDatea = $.trim(a).split('.');
                    var ruDateb = $.trim(b).split('.');

                    if(ruDatea[2]*1 < ruDateb[2]*1)
                        return 1;
                    if(ruDatea[2]*1 > ruDateb[2]*1)
                        return -1;
                    if(ruDatea[2]*1 == ruDateb[2]*1)
                    {
                        if(ruDatea[1]*1 < ruDateb[1]*1)
                            return 1;
                        if(ruDatea[1]*1 > ruDateb[1]*1)
                            return -1;
                        if(ruDatea[1]*1 == ruDateb[1]*1)
                        {
                            if(ruDatea[0]*1 < ruDateb[0]*1)
                                return 1;
                            if(ruDatea[0]*1 > ruDateb[0]*1)
                                return -1;
                        }
                        else
                            return 0;
                    }
                },

                "ruDate-desc": function ( a, b ) {
                    var ruDatea = $.trim(a).split('.');
                    var ruDateb = $.trim(b).split('.');

                    if(ruDatea[2]*1 < ruDateb[2]*1)
                        return -1;
                    if(ruDatea[2]*1 > ruDateb[2]*1)
                        return 1;
                    if(ruDatea[2]*1 == ruDateb[2]*1)
                    {
                        if(ruDatea[1]*1 < ruDateb[1]*1)
                            return -1;
                        if(ruDatea[1]*1 > ruDateb[1]*1)
                            return 1;
                        if(ruDatea[1]*1 == ruDateb[1]*1)
                        {
                            if(ruDatea[0]*1 < ruDateb[0]*1)
                                return -1;
                            if(ruDatea[0]*1 > ruDateb[0]*1)
                                return 1;
                        }
                        else
                            return 0;
                    }
                }
            });

            $('#orders').DataTable({
                "aoColumns": [
                    null,
                    null,
                    null,
                    null,
                    { "sType": "ruDate"}
                ]
            });


        });
    </script>
@endsection
