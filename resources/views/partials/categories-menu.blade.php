<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            <!-- Все товары -->
            <li class="list-group-item border-0 py-2">
                <a href="{{ route('home') }}"
                   class="text-decoration-none d-flex align-items-center gap-2 {{ request()->routeIs('home') ? 'fw-semibold text-primary' : 'text-dark' }}">
                    <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                    <span>Все товары</span>
                </a>
            </li>
            <li class="list-group-item border-0 py-2">
                <a href="{{ route('products.new') }}"
                   class="text-decoration-none d-flex align-items-center gap-2 {{ request()->routeIs('products.new') ? 'fw-semibold text-primary' : 'text-dark' }}">
                    <i class="bi bi-stars fs-5"></i>
                    <span>⭐ Новинки</span>
                </a>
            </li>

            @foreach($categories as $category)
                <li class="list-group-item border-0 py-1">
                    <a href="{{ route('category.products', $category->id) }}"
                       class="text-decoration-none d-flex align-items-center gap-2 text-dark {{ isset($category) && request()->route('id') == $category->id ? 'fw-semibold text-primary' : '' }}">
                        <i class="bi bi-folder fs-5 text-secondary"></i>
                        <span>{{ $category->name }}</span>
                    </a>
                    @if($category->children->count())
                        <ul class="list-unstyled ms-4 mt-1">
                            @foreach($category->children as $child)
                                @if($child->hasProductsRecursive())
                                    <li class="py-1">
                                        <a href="{{ route('category.products', $child->id) }}"
                                           class="text-decoration-none d-flex align-items-center gap-2 text-muted {{ isset($category) && request()->route('id') == $child->id ? 'fw-semibold text-primary' : '' }}">
                                            <i class="bi bi-arrow-right-short fs-5"></i>
                                            <span>{{ $child->name }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
