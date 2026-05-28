@extends('layouts.app')

@section('content')
    <main class="pt-90">
        <section class="shop-main container d-flex pt-4 pt-xl-5">
            <div class="shop-sidebar side-sticky bg-body" id="shopFilter">
                <div class="aside-header d-flex d-lg-none align-items-center">
                    <h3 class="text-uppercase fs-6 mb-0">Lọc theo</h3>
                    <button class="btn-close-lg js-close-aside btn-close-aside ms-auto"></button>
                </div>

                <div class="pt-4 pt-lg-0"></div>

                <div class="accordion" id="categories-list">
                    <div class="accordion-item mb-4 pb-3">
                        <h5 class="accordion-header" id="accordion-heading-1">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accordion-filter-1" aria-expanded="true"
                                aria-controls="accordion-filter-1">
                                Danh mục sản phẩm
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-1" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-1" data-bs-parent="#categories-list">
                            <div class="accordion-body px-0 pb-0 pt-3">
                                <ul class="list list-inline mb-0">
                                    @foreach ($danhMucs as $dm)
                                        <li class="list-item">
                                            <a href="{{ request()->fullUrlWithQuery(['danh_muc_id' => $dm->id]) }}"
                                                class="menu-link py-1">{{ $dm->ten }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion" id="price-filters">
                    <div class="accordion-item mb-4">
                        <h5 class="accordion-header mb-2" id="accordion-heading-price">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accordion-filter-price" aria-expanded="true"
                                aria-controls="accordion-filter-price">
                                Giá
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-price" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-price" data-bs-parent="#price-filters">
                            <input class="price-range-slider" type="text" name="price_range" value=""
                                data-slider-min="0" data-slider-max="10000000" data-slider-step="100000"
                                data-slider-value="[{{ request('min_price', 0) }},{{ request('max_price', 10000000) }}]"
                                data-currency="đ" />
                            <div class="price-range__info d-flex align-items-center mt-2">
                                <div class="me-auto">
                                    <span class="text-secondary">Thấp nhất: </span>
                                    <span class="price-range__min">{{ number_format(request('min_price', 0), 0, ',', '.') }}đ</span>
                                </div>
                                <div>
                                    <span class="text-secondary">Cao nhất: </span>
                                    <span class="price-range__max">{{ number_format(request('max_price', 10000000), 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-buynow mt-3">Lọc theo giá</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="shop-list flex-grow-1">

                {{-- Thanh thông tin: đếm sản phẩm + nút xóa bộ lọc --}}
                @php
                    $hasFilter = request()->hasAny(['q', 'danh_muc_id', 'category_group', 'min_price', 'max_price'])
                                || (request('sort') && request('sort') !== 'newest');
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-3">
                    {{-- Breadcrumb --}}
                    <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                        <a href="{{ route('home.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Trang chủ</a>
                        <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                        <a href="{{ route('shop.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Cửa hàng</a>
                    </div>

                    {{-- Thông tin số sản phẩm + nút xóa bộ lọc --}}
                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                        <span class="text-secondary small">
                            Tìm thấy <strong class="text-dark">{{ $totalProducts }}</strong> sản phẩm
                        </span>
                        @if ($hasFilter)
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Xóa tất cả bộ lọc">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                                Xóa bộ lọc
                            </a>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-4 pb-md-2">
                    <div class="shop-acs d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">

                        {{-- Dropdown sắp xếp --}}
                        <select id="sort-select"
                                class="shop-acs__select form-select w-auto border-0 py-0 order-1 order-md-0"
                                aria-label="Sắp xếp sản phẩm">
                            <option value="newest"       {{ $sort === 'newest'       ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest"       {{ $sort === 'oldest'       ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="name_asc"     {{ $sort === 'name_asc'     ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="name_desc"    {{ $sort === 'name_desc'    ? 'selected' : '' }}>Tên Z-A</option>
                            <option value="price_asc"    {{ $sort === 'price_asc'    ? 'selected' : '' }}>Giá thấp đến cao</option>
                            <option value="price_desc"   {{ $sort === 'price_desc'   ? 'selected' : '' }}>Giá cao đến thấp</option>
                            <option value="discount_desc" {{ $sort === 'discount_desc' ? 'selected' : '' }}>Giảm giá nhiều nhất</option>
                        </select>

                        <div class="shop-asc__seprator mx-3 bg-light d-none d-md-block order-md-0"></div>

                        <div class="col-size align-items-center order-1 d-none d-lg-flex">
                            <span class="text-uppercase fw-medium me-2">Xem</span>
                            <button class="btn-link fw-medium me-2 js-cols-size" data-target="products-grid" data-cols="2">2</button>
                            <button class="btn-link fw-medium me-2 js-cols-size" data-target="products-grid" data-cols="3">3</button>
                            <button class="btn-link fw-medium js-cols-size btn-link_active" data-target="products-grid" data-cols="4">4</button>
                        </div>

                        <div class="shop-filter d-flex align-items-center order-0 order-md-3 d-lg-none">
                            <button class="btn-link btn-link_f d-flex align-items-center ps-0 js-open-aside" data-aside="shopFilter">
                                <svg class="d-inline-block align-middle me-2" width="14" height="10"
                                    viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_filter" />
                                </svg>
                                <span class="text-uppercase fw-medium d-inline-block align-middle">Bộ lọc</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="products-grid row row-cols-2 row-cols-md-3" id="products-grid">
                    @forelse ($sanPhams as $sp)
                        <div class="product-card-wrapper rounded">
                            <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                                <div class="pc__img-wrapper">
                                    <div class="swiper-container background-img js-swiper-slider"
                                        data-settings='{"resizeObserver": true}'>
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <a href="{{ route('product.detail', ['slug' => $sp->slug]) }}"><img
                                                        loading="lazy" src="{{ check_image_url($sp->main_image) }}"
                                                        width="330" height="400" alt="Cropped Faux leather Jacket"
                                                        class="pc__img"></a>
                                            </div>
                                            <div class="swiper-slide">
                                                <a href="{{ route('product.detail', ['slug' => $sp->slug]) }}"><img
                                                        loading="lazy" src="{{ check_image_url($sp->main_image) }}"
                                                        width="330" height="400" alt="Cropped Faux leather Jacket"
                                                        class="pc__img"></a>
                                            </div>
                                        </div>
                                        <span class="pc__img-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_prev_sm" />
                                            </svg></span>
                                        <span class="pc__img-next"><svg width="7" height="11" viewBox="0 0 7 11"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_next_sm" />
                                            </svg></span>
                                    </div>
                                    {{-- <button
                                        class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium js-add-cart js-open-aside"
                                        data-aside="cartDrawer" title="Add To Cart">Add To Cart</button> --}}
                                </div>

                                <div class="pc__info position-relative">
                                    <p class="pc__category">{{ $sp->danh_muc->ten }}</p>
                                    <h6 class="pc__title text-truncate"><a
                                            href="{{ route('product.detail', ['slug' => $sp->slug]) }}">{{ $sp->ten }}</a>
                                        {{-- <a href="{{ route('product.detail', ['slug' => $sp->slug]) }}">detail</a> --}}
                                    </h6>
                                    <div class="product-card__price d-flex">
                                        @if ($sp->gia_giam)
                                            <span
                                                class="price me-1 pc__category text-decoration-line-through">{{ number_format($sp->gia, 0, ',', '.') }}đ</span>
                                            <span class="money price text-red">{{ number_format($sp->gia_giam, 0, ',', '.') }}đ</span>
                                        @else
                                            <span class="money price text-red">{{ number_format($sp->gia, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <form action="{{ route('cart.add') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="product_id" value="{{ $sp->id }}">
                                            <button type="submit" class="btn btn-primary w-100 py-2 fs-6">Thêm vào giỏ</button>
                                        </form>
                                    </div>

                                    <form action="{{ route('wishlist.add') }}" method="POST" style="position: absolute; top: 0; right: 0; z-index: 10;">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $sp->id }}">
                                        <button type="submit" class="pc__btn-wl bg-transparent border-0 js-add-wishlist" title="Thêm vào yêu thích">
                                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_heart" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Thông báo khi không có sản phẩm --}}
                        <div class="col-12 text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ccc" viewBox="0 0 16 16" class="mb-3">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4M5 13a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                            </svg>
                            <h5 class="text-muted mb-2">Không tìm thấy sản phẩm nào</h5>
                            <p class="text-secondary mb-4">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary px-4">Xem tất cả sản phẩm</a>
                        </div>
                    @endforelse
                </div>

                <nav class="shop-pages d-flex justify-content-between mt-3" aria-label="Page navigation">
                    @if ($sanPhams->onFirstPage())
                        <span class="btn-link d-inline-flex align-items-center disabled">
                            <svg class="me-1" width="7" height="11" viewBox="0 0 7 11"
                                xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_prev_sm" />
                            </svg>
                            <span class="fw-medium">Trước</span>
                        </span>
                    @else
                        <a href="{{ $sanPhams->appends(request()->except('page'))->previousPageUrl() }}"
                            class="btn-link d-inline-flex align-items-center">
                            <svg class="me-1" width="7" height="11" viewBox="0 0 7 11"
                                xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_prev_sm" />
                            </svg>
                            <span class="fw-medium">Trước</span>
                        </a>
                    @endif
                    <ul class="pagination mb-0">
                        @foreach ($sanPhams->links()->elements[0] as $page => $url)
                            @if ($page == $sanPhams->currentPage())
                                <li class="page-item active"><span
                                        class="btn-link px-1 mx-2 btn-link_active">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="btn-link px-1 mx-2"
                                        href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                    @if ($sanPhams->hasMorePages())
                        <a href="{{ $sanPhams->appends(request()->except('page'))->nextPageUrl() }}"
                            class="btn-link d-inline-flex align-items-center">
                            <span class="fw-medium me-1">Sau</span>
                            <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_next_sm" />
                            </svg>
                        </a>
                    @else
                        <span class="btn-link d-inline-flex align-items-center disabled">
                            <span class="fw-medium me-1">Sau</span>
                            <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_next_sm" />
                            </svg>
                        </span>
                    @endif
                </nav>

            </div>
        </section>
    </main>
@endsection

@push('scripts')
<script>
    $(function() {
        // Slider initialization is handled by theme.js (initRangeSlider)
        // We only handle the filtering action here

        $('.btn-buynow').on('click', function(e) {
            e.preventDefault();
            var range = $(".price-range-slider").val().split(',');
            var min = range[0];
            var max = range[1];

            var url = new URL(window.location.href);
            url.searchParams.set('min_price', min);
            url.searchParams.set('max_price', max);
            url.searchParams.delete('page'); // Reset pagination

            window.location.href = url.toString();
        });

        // Xử lý thay đổi tùy chọn sắp xếp – giữ lại các bộ lọc hiện tại
        $('#sort-select').on('change', function() {
            var url = new URL(window.location.href);
            url.searchParams.set('sort', $(this).val());
            url.searchParams.delete('page'); // Reset về trang 1 khi đổi sort
            window.location.href = url.toString();
        });
    });
</script>
@endpush
