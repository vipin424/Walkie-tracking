@extends('storefront.layout')

@section('content')

<!-- Hero Section -->
<section class="hero" style="background: url('https://via.placeholder.com/1920x600.png?text=SharePal+Style+Background') center/cover; padding-top: 140px; padding-bottom: 80px;">
    <div class="container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
        <h1 class="hero-title" style="font-size: 4rem; color: var(--dark); max-width: 900px; font-weight: 800; line-height: 1.2;">
            "Own the <span style="position: relative; display: inline-block;">Experience<svg width="100%" height="20" style="position: absolute; bottom: -10px; left: 0;" viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M0,10 Q50,0 100,10" stroke="var(--whatsapp)" stroke-width="3" fill="none"/></svg></span><br>
            Rent the Gear"
        </h1>
        
        <div style="margin-top: 2rem; display: flex; gap: 2rem; background: var(--white); padding: 0.75rem 2rem; border-radius: 50px; box-shadow: var(--shadow-md);">
            <div style="font-weight: 600; color: var(--dark); display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-globe" style="color: var(--primary);"></i> Good for our <span style="color: var(--primary);">Planet</span>
            </div>
            <div style="width: 1px; background: var(--border);"></div>
            <div style="font-weight: 600; color: var(--dark); display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-wallet2" style="color: var(--primary);"></i> Good for your <span style="color: var(--primary);">Pocket</span>
            </div>
        </div>
    </div>
</section>

<!-- Top Sub-Categories (Carousel Style) -->
<section class="py-12" style="background: var(--light);">
    <div class="container text-center">
        <h2 class="section-title" style="margin-bottom: 2rem;"><span style="color: var(--secondary);">Top Categories</span> to choose from</h2>
        
        <div class="flex justify-center gap-6 flex-wrap">
            @foreach($topCategories as $cat)
                <a href="{{ route('storefront.category', $cat->slug) }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <div style="width: 120px; height: 120px; background: var(--white); border-radius: 12px; padding: 1rem; box-shadow: var(--shadow-sm); transition: transform 0.2s;">
                        <img src="{{ $cat->image_path }}" alt="{{ $cat->name }}" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <span style="font-weight: 600; color: var(--dark-muted); font-size: 0.9rem;">{{ $cat->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Broad Categories (Large Cards) -->
<section class="py-16">
    <div class="container">
        <div class="grid grid-cols-4 gap-6">
            @foreach($featuredCategories as $cat)
                <a href="{{ route('storefront.category', $cat->slug) }}" style="text-decoration: none; display: block; border-radius: 20px; overflow: hidden; background: var(--white); box-shadow: var(--shadow-sm); border: 1px solid var(--border); transition: transform 0.2s;">
                    <!-- Content -->
                    <div style="padding: 1.5rem; height: 140px;">
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--dark); margin-bottom: 0.5rem;">{{ $cat->name }}</h3>
                        <p style="color: var(--dark-muted); font-size: 0.85rem; line-height: 1.4;">{{ $cat->description }}</p>
                    </div>
                    
                    <!-- Image Area -->
                    <div style="height: 200px; position: relative;">
                        <img src="{{ $cat->image_path }}" alt="{{ $cat->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <!-- Colored Strip at bottom -->
                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 40px; background: {{ $cat->badge_color ?? 'var(--primary)' }};"></div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Trending Items Section -->
<section class="py-12" style="background: var(--white);">
    <div class="container">
        <div class="flex justify-between items-center mb-12">
            <h2 class="section-title" style="margin-bottom:0;">Trending Now</h2>
            <a href="#" class="text-primary font-semibold text-sm">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        
        <div class="grid grid-cols-4 gap-6">
            @forelse($trendingItems as $item)
                <div class="product-card">
                    <span class="product-badge">Trending</span>
                    <div class="product-image-wrap">
                        @if($item->image_path)
                            <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        @else
                            <img src="https://via.placeholder.com/200x200.png?text=No+Image" alt="{{ $item->name }}">
                        @endif
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">{{ $item->name }}</h3>
                        <div class="product-stats">
                            <i class="bi bi-graph-up-arrow"></i> 120 booked this month
                        </div>
                        
                        <div class="product-price-wrap">
                            <span class="price-label">Rent starting from</span>
                            <div class="price-amount dynamic-price" data-base-price="{{ $item->unit_price }}">
                                ₹{{ number_format($item->unit_price, 0) }} <span class="price-duration">/ 1 day</span>
                            </div>
                        </div>
                        
                        <div class="product-action">
                            <button class="btn btn-outline" style="padding: 0.5rem 1rem;">
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="font-semibold" style="display:flex; align-items:center; justify-content:center; width:30px;">1</span>
                            <button class="btn btn-outline" style="padding: 0.5rem 1rem;">
                                <i class="bi bi-plus"></i>
                            </button>
                            <button class="btn btn-cart">
                                Go to Cart <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Demo Items -->
                @for($i=1; $i<=4; $i++)
                <div class="product-card">
                    <span class="product-badge">Trending</span>
                    <div class="product-image-wrap">
                        <img src="https://via.placeholder.com/200x200.png?text=Item+{{$i}}" alt="Demo">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">Premium Camera Model {{$i}}</h3>
                        <div class="product-stats">
                            <i class="bi bi-graph-up-arrow"></i> 120 booked this month
                        </div>
                        <div class="product-price-wrap">
                            <span class="price-label">Rent starting from</span>
                            <div class="price-amount dynamic-price" data-base-price="1999">₹1,999 <span class="price-duration">/ 1 day</span></div>
                        </div>
                        <div class="product-action">
                            <button class="btn btn-outline" style="padding: 0 1rem;"><i class="bi bi-dash"></i></button>
                            <span class="font-semibold" style="display:flex; align-items:center;">1</span>
                            <button class="btn btn-outline" style="padding: 0 1rem;"><i class="bi bi-plus"></i></button>
                            <button class="btn btn-cart">Go to Cart <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
                @endfor
            @endforelse
        </div>
    </div>
</section>

@endsection
