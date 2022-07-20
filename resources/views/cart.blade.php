{{-- @dd($carts) --}}
@extends('main')

@section('content')
    {{-- succes added --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @php
    $total = 0;
    @endphp
    <div class="container mt-4">
        {{-- card products --}}
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($carts))
                    @foreach ($carts as $cart)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $cart['name'] }}</td>
                            <td>{{ $cart['quantity'] }}</td>
                            <td>Rp.{{ number_format($cart['price'], 0, ',', '.') }}</td>
                            <td>
                                <form action="/delete-cart/{{ $cart['id'] }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @php
                            $total += $cart['price'] * $cart['quantity'];
                        @endphp
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Total</th>
                    <th>
                        Rp.{{ number_format($total, 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        </table>

        {{-- form buy now --}}
        <div class="d-flex justify-content-end">
            <a href="/buyer" class="btn btn-primary">Checkout</a>
        </div>

    </div>
@endsection
