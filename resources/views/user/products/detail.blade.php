@extends('layouts.app')

@section('content')
    <main class="pt-90">
        <div class="mb-md-1 pb-md-3"></div>
        <section class="product-single container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="product-single__media" data-media-type="vertical-thumbnail">
                        <div class="product-single__image">
                            <div class="swiper-container swiper-main" id="swiper-main">
                                <div class="swiper-wrapper">
                                    @if (count($galleryImages) > 0)
                                        @foreach ($galleryImages as $image)
                                            <div class="swiper-slide product-single__image-item">
                                                <div style="width: 100%; height: 600px; display: flex; align-items: center; justify-content: center; background: #f8f8f8;">
                                                    <img loading="lazy" src="{{ check_image_url($image) }}"
                                                        style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="" />
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="swiper-slide product-single__image-item">
                                            <div style="width: 100%; height: 600px; display: flex; align-items: center; justify-content: center; background: #f8f8f8;">
                                                <img loading="lazy" src="{{ asset('assets/images/no-image.png') }}"
                                                    style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="No image" />
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <use href="#icon_prev_sm" />
                                    </svg></div>
                                <div class="swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <use href="#icon_next_sm" />
                                    </svg></div>
                            </div>
                        </div>
                        <div class="product-single__thumbnail">
                            <div class="swiper-container swiper-thumbs" id="swiper-thumbs">
                                <div class="swiper-wrapper">
                                    @foreach ($galleryImages as $image)
                                        <div class="swiper-slide product-single__image-item" style="cursor: pointer;">
                                            <img loading="lazy" class="h-max" src="{{ check_image_url($image) }}" width="104"
                                                height="104" alt="" style="object-fit: cover;" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex justify-content-between mb-4 pb-md-2">
                        <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                            <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Trang chủ</a>
                            <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                            <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Cửa hàng</a>
                        </div><!-- /.breadcrumb -->
                    </div>
                    <h1 class="product-single__name">{{ $sanPham->ten }}</h1>

                    <div class="product-card__price d-flex">
                        @if ($sanPham->gia_giam)
                            <span
                                class="price me-1 pc__category text-decoration-line-through fs-3">{{ number_format($sanPham->gia, 0, ',', '.') }}đ</span>
                            <span class="money price text-red fs-3">{{ number_format($sanPham->gia_giam, 0, ',', '.') }}đ</span>
                        @else
                            <span class="money price text-red fs-3">{{ number_format($sanPham->gia, 0, ',', '.') }}đ</span>
                        @endif
                    </div>
                    <div class="product-single__short-desc">
                        <p>{{ $sanPham->mo_ta_ngan }}</p>
                    </div>
                    <form action="{{ route('cart.add') }}" name="addtocart-form" method="post">
                        @csrf
                        <div class="product-single__addtocart">
                            <div class="qty-control position-relative">
                                <input type="number" name="quantity" value="1" min="1"
                                    class="qty-control__number text-center">
                                <div class="qty-control__reduce">-</div>
                                <div class="qty-control__increase">+</div>
                                <input type="hidden" name="product_id" value="{{ $sanPham->id }}">
                            </div><!-- .qty-control -->
                            <button type="submit" class="btn btn-primary btn-addtocart">Thêm vào giỏ</button>
                        </div>
                    </form>
                    <div class="product-single__addtolinks">
                        <share-button class="share-button">
                            <button
                                class="menu-link menu-link_us-s to-share border-0 bg-transparent d-flex align-items-center">
                                <svg width="16" height="19" viewBox="0 0 16 19" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_sharing" />
                                </svg>
                                <span>Chia sẻ</span>
                            </button>
                            <details id="Details-share-template__main" class="m-1 xl:m-1.5" hidden="">
                                <summary class="btn-solid m-1 xl:m-1.5 pt-3.5 pb-3 px-5">+</summary>
                                <div id="Article-share-template__main"
                                    class="share-button__fallback flex items-center absolute top-full left-0 w-full px-2 py-4 bg-container shadow-theme border-t z-10">
                                    <div class="field grow mr-4">
                                        <label class="field__label sr-only" for="url">Link</label>
                                        <input type="text" class="field__input w-full" id="url"
                                            value="https://uomo-crystal.myshopify.com/blogs/news/go-to-wellness-tips-for-mental-health"
                                            placeholder="Link" onclick="this.select();" readonly="">
                                    </div>
                                    <button class="share-button__copy no-js-hidden">
                                        <svg class="icon icon-clipboard inline-block mr-1" width="11" height="13"
                                            fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                                            focusable="false" viewBox="0 0 11 13">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2 1a1 1 0 011-1h7a1 1 0 011 1v9a1 1 0 01-1 1V1H2zM1 2a1 1 0 00-1 1v9a1 1 0 001 1h7a1 1 0 001-1V3a1 1 0 00-1-1H1zm0 10V3h7v9H1z"
                                                fill="currentColor"></path>
                                        </svg>
                                        <span class="sr-only">Copy link</span>
                                    </button>
                                </div>
                            </details>
                        </share-button>
                        
                        <form action="{{ route('wishlist.add') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $sanPham->id }}">
                            <button type="submit" class="menu-link menu-link_us-s border-0 bg-transparent d-flex align-items-center">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_heart" />
                                </svg>
                                <span>Thêm vào yêu thích</span>
                            </button>
                        </form>
                        <script src="js/details-disclosure.html" defer="defer"></script>
                        <script src="js/share.html" defer="defer"></script>
                    </div>
                    <div class="product-single__meta-info">
                        <div class="meta-item">
                            <label>SKU:</label>
                            <span>{{ $sanPham->ma_sp }}</span>
                        </div>
                        <div class="meta-item">
                            <label>Danh mục:</label>
                            <span>{{ $sanPham->danh_muc->ten }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-single__details-tab">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
                            href="#tab-description" role="tab" aria-controls="tab-description"
                            aria-selected="true">Mô tả</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-description" role="tabpanel"
                        aria-labelledby="tab-description-tab">
                        <div class="product-single__description">
                            {!! $sanPham->mo_ta !!}
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <section class="products-carousel container">
            <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Sản phẩm <strong>liên quan</strong></h2>

            <div id="related_products" class="position-relative">
                <div class="swiper-container js-swiper-slider"
                    data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": true,
            "pagination": {
              "el": "#related_products .products-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": "#related_products .products-carousel__next",
              "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 2,
                "slidesPerGroup": 2,
                "spaceBetween": 14
              },
              "768": {
                "slidesPerView": 3,
                "slidesPerGroup": 3,
                "spaceBetween": 24
              },
              "992": {
                "slidesPerView": 4,
                "slidesPerGroup": 4,
                "spaceBetween": 30
              }
            }
          }'>
                    <div class="swiper-wrapper">
                        @foreach ($relatedProducts as $r_sp)
                            <div class="swiper-slide product-card">
                                <div class="pc__img-wrapper">
                                    <a href="{{ route('product.detail', ['slug' => $r_sp->slug]) }}">
                                        <img loading="lazy" src="{{ check_image_url($r_sp->main_image) }}" width="330"
                                            height="400" alt="{{ $r_sp->ten }}" class="pc__img">
                                        <img loading="lazy" src="{{ check_image_url($r_sp->main_image) }}" width="330"
                                            height="400" alt="{{ $r_sp->ten }}"
                                            class="pc__img pc__img-second">
                                    </a>
                                </div>

                                <div class="pc__info position-relative">
                                    <p class="pc__category">{{ $r_sp->danh_muc->ten }}</p>
                                    <h6 class="pc__title text-truncate">
                                        <a href="{{ route('product.detail', ['slug' => $r_sp->slug]) }}">{{ $r_sp->ten }}</a>
                                    </h6>
                                    <div class="product-card__price d-flex">
                                        @if ($r_sp->gia_giam)
                                            <span
                                                class="price me-1 pc__category text-decoration-line-through">{{ number_format($r_sp->gia, 0, ',', '.') }}đ</span>
                                            <span class="money price text-red">{{ number_format($r_sp->gia_giam, 0, ',', '.') }}đ</span>
                                        @else
                                            <span class="money price text-red">{{ number_format($r_sp->gia, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>

                                    <div class="mt-2">
                                        <form action="{{ route('cart.add') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="product_id" value="{{ $r_sp->id }}">
                                            <button type="submit" class="btn btn-primary w-100 py-2 fs-6">Thêm vào giỏ</button>
                                        </form>
                                    </div>

                                    <form action="{{ route('wishlist.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $r_sp->id }}">
                                        <button type="submit" class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist" title="Thêm vào yêu thích">
                                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_heart" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div><!-- /.swiper-wrapper -->
                </div><!-- /.swiper-container js-swiper-slider -->

                <div
                    class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_prev_md" />
                    </svg>
                </div><!-- /.products-carousel__prev -->
                <div
                    class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_next_md" />
                    </svg>
                </div><!-- /.products-carousel__next -->

                <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
                <!-- /.products-pagination -->
            </div><!-- /.position-relative -->

        </section><!-- /.products-carousel container -->

        {{-- ===== RECENTLY VIEWED PRODUCTS ===== --}}
        @if ($recentlyViewed->isNotEmpty())
        <section class="products-carousel container" id="recently-viewed-section">
            <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Sản phẩm đã xem <strong>gần đây</strong></h2>

            <div id="recently_viewed_products" class="position-relative">
                <div class="swiper-container js-swiper-slider"
                    data-settings='{
                        "autoplay": false,
                        "slidesPerView": 4,
                        "slidesPerGroup": 4,
                        "effect": "none",
                        "loop": false,
                        "pagination": {
                            "el": "#recently_viewed_products .products-pagination",
                            "type": "bullets",
                            "clickable": true
                        },
                        "navigation": {
                            "nextEl": "#recently_viewed_products .products-carousel__next",
                            "prevEl": "#recently_viewed_products .products-carousel__prev"
                        },
                        "breakpoints": {
                            "320": { "slidesPerView": 2, "slidesPerGroup": 2, "spaceBetween": 14 },
                            "768": { "slidesPerView": 3, "slidesPerGroup": 3, "spaceBetween": 24 },
                            "992": { "slidesPerView": 4, "slidesPerGroup": 4, "spaceBetween": 30 }
                        }
                    }'>
                    <div class="swiper-wrapper">
                        @foreach ($recentlyViewed as $rv)
                            <div class="swiper-slide product-card">
                                <div class="pc__img-wrapper">
                                    <a href="{{ route('product.detail', ['slug' => $rv->slug]) }}">
                                        <img loading="lazy"
                                             src="{{ check_image_url($rv->main_image) }}"
                                             width="330" height="400"
                                             alt="{{ $rv->ten }}"
                                             class="pc__img">
                                        <img loading="lazy"
                                             src="{{ check_image_url($rv->main_image) }}"
                                             width="330" height="400"
                                             alt="{{ $rv->ten }}"
                                             class="pc__img pc__img-second">
                                    </a>
                                </div>

                                <div class="pc__info position-relative">
                                    <p class="pc__category">{{ $rv->danh_muc->ten }}</p>
                                    <h6 class="pc__title text-truncate">
                                        <a href="{{ route('product.detail', ['slug' => $rv->slug]) }}">{{ $rv->ten }}</a>
                                    </h6>
                                    <div class="product-card__price d-flex">
                                        @if ($rv->gia_giam)
                                            <span class="price me-1 pc__category text-decoration-line-through">{{ number_format($rv->gia, 0, ',', '.') }}đ</span>
                                            <span class="money price text-red">{{ number_format($rv->gia_giam, 0, ',', '.') }}đ</span>
                                        @else
                                            <span class="money price text-red">{{ number_format($rv->gia, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>

                                    <div class="mt-2">
                                        <form action="{{ route('cart.add') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="product_id" value="{{ $rv->id }}">
                                            <button type="submit" class="btn btn-primary w-100 py-2 fs-6">Thêm vào giỏ</button>
                                        </form>
                                    </div>

                                    <form action="{{ route('wishlist.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $rv->id }}">
                                        <button type="submit"
                                                class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist"
                                                title="Thêm vào yêu thích">
                                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_heart" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div><!-- /.swiper-wrapper -->
                </div><!-- /.swiper-container -->

                <div class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_prev_md" />
                    </svg>
                </div>
                <div class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_next_md" />
                    </svg>
                </div>

                <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
            </div><!-- /.position-relative -->
        </section><!-- /#recently-viewed-section -->
        @endif

    </main>
@endsection

@section('js')
    <style>
        /* Hiệu ứng cho ảnh chờ ở hai bên */
        #swiper-main .swiper-slide {
            opacity: 0.4;
            transform: scale(0.7);
            transition: all 0.4s ease;
        }

        #swiper-main .swiper-slide-active {
            opacity: 1;
            transform: scale(1);
        }

        /* Đảm bảo container không cắt mất phần ảnh thu nhỏ */
        #swiper-main {
            overflow: visible !important;
            padding: 0 10%; /* Tạo khoảng trống để lộ ảnh hai bên */
        }
        
        .product-single__image {
            overflow: hidden; /* Cắt phần thừa ra ngoài khung lớn */
        }

        /* Làm cho dải ảnh nhỏ dẹp và thanh thoát hơn */
        .product-single__media[data-media-type="vertical-thumbnail"] {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .product-single__image {
            flex: 1;
            order: 2;
        }

        .product-single__thumbnail {
            width: 80px; /* Thu hẹp độ rộng cột ảnh nhỏ */
            order: 1;
        }

        #swiper-thumbs .product-single__image-item {
            height: 100px !important; /* Cố định chiều cao ảnh nhỏ */
            border: 1px solid #eee;
            border-radius: 4px;
            overflow: hidden;
            opacity: 0.5;
            transition: 0.3s;
        }

        #swiper-thumbs .swiper-slide-thumb-active {
            opacity: 1;
            border-color: #000;
        }

        #swiper-thumbs img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    <script>
        $(document).ready(function() {
            if ($("#swiper-thumbs").length > 0 && $("#swiper-main").length > 0) {
                var thumbsSwiper = new Swiper("#swiper-thumbs", {
                    slidesPerView: 4,
                    spaceBetween: 10,
                    direction: "vertical",
                    watchSlidesProgress: true,
                    freeMode: true,
                    breakpoints: {
                        0: {
                            direction: "horizontal",
                            slidesPerView: 4,
                        },
                        992: {
                            direction: "vertical",
                            slidesPerView: 4,
                        }
                    }
                });

                new Swiper("#swiper-main", {
                    slidesPerView: 1, /* Hiện 1 ảnh chính */
                    centeredSlides: true, /* Đưa ảnh vào giữa */
                    loop: true, /* Lặp vô tận để luôn thấy ảnh kế tiếp/trước đó */
                    spaceBetween: 10,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    thumbs: {
                        swiper: thumbsSwiper,
                    },
                });
            }
        });
    </script>
@endsection


