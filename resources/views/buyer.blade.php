@extends('main')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="container mt-4">
        @php
            $total = 0;
        @endphp
        {{-- form buyer --}}
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="h3">Cart</div>
                        <div class="mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
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
                                            </tr>
                                            @php
                                                $total += $cart['price'] * $cart['quantity'];
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <td colspan="3">Fee Admin</th>
                                            <td>Rp.{{ number_format(5000, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total</th>
                                        <th>
                                            Rp.{{ number_format($total + 5000, 0, ',', '.') }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="h3">Buyer</div>
                        <div class="mb-3">
                            <form action="/transaction" method="POST">
                                @csrf
                                @method('POST')

                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" class="form-control" id="name"
                                        placeholder="Enter name" value="{{ old('name') }}">
                                    <small class="text-danger">
                                        {{ $errors->first('name') }}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" id="email"
                                        placeholder="Enter email" value="{{ old('email') }}">
                                    <small class="text-danger">
                                        {{ $errors->first('email') }}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" class="form-control" id="phone"
                                        placeholder="Enter phone" value="{{ old('phone') }}">
                                    <small class="text-danger">
                                        {{ $errors->first('phone') }}
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Buy Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
