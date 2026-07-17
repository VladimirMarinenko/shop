@forelse($products as $product)
    <div class="col-md-6 col-xl-4">
        <div class="card card-product h-100 js-product-card" data-url="{{ route('product.show', $product->slug) }}">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
            @else
                <img src="https://placehold.co/300x220/e0e0e0/6c5ce7?text=Нет+фото" class="card-img-top" alt="Нет фото">
            @endif
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ $product->name }}</h5>
                <p class="text-muted small">{{ $product->category ? $product->category->name : 'Без категории' }}</p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="price">{{ number_format($product->price, 2) }} ₽</span>
                    @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline js-add-to-cart">
                            @csrf
                            <button type="submit" class="btn-add"><i class="bi bi-cart-plus"></i></button>
                        </form>
                    @else
                        <span class="badge bg-secondary">Нет</span>
                    @endif
                </div>
                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-add w-100 mt-2">Подробнее</a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-box display-1 text-muted"></i>
        <p class="mt-3">Товары не найдены.</p>
    </div>
@endforelse
