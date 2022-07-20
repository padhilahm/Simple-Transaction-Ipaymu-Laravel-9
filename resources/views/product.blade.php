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
    <div class="container mt-4">
        {{-- card products --}}
        <div class="row">
            @foreach ($products as $product)
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <img src="{{ $product->image }}" class="card-img-top" alt="...">
                        <div class="card-body">
                            <div class="h3">{{ $product->name }}</div>
                            <div class="mb-3">Rp.{{ number_format($product->price, 0, ',', '.') }}</div>
                            {{-- form buy now --}}
                            <form action="/add-to-cart/{{ $product->id }}" method="POST">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="id" value="{{ $product->id }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="number" name="quantity" class="form-control" value="0"
                                            min="0" required>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary">Add to cart</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
