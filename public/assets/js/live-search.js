/**
 * Live Search Suggestions
 * Chức năng: Gợi ý sản phẩm realtime khi người dùng nhập vào ô tìm kiếm
 * Sử dụng: Fetch API + debounce, không reload trang
 */

(function () {
    'use strict';

    // ── Cấu hình ──────────────────────────────────────────────────
    const SEARCH_URL   = '/search-suggestions'; // route Laravel
    const DEBOUNCE_MS  = 280;                   // delay trước khi gửi request
    const MIN_CHARS    = 1;                     // ký tự tối thiểu để trigger

    // ── Helpers ───────────────────────────────────────────────────

    /**
     * Debounce: trì hoãn thực thi hàm `fn` sau `delay` ms
     */
    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    /**
     * Format số tiền VNĐ
     * @param {number} amount
     * @returns {string} "1.200.000đ"
     */
    function formatPrice(amount) {
        return Number(amount).toLocaleString('vi-VN') + 'đ';
    }

    /**
     * Escape HTML để tránh XSS
     * @param {string} str
     * @returns {string}
     */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // ── Render ────────────────────────────────────────────────────

    /**
     * Hiển thị dropdown loading
     * @param {HTMLElement} dropdown
     */
    function showLoading(dropdown) {
        dropdown.innerHTML = `
            <div class="ls-loading">
                <div class="ls-spinner"></div>
                <span>Đang tìm kiếm...</span>
            </div>
        `;
        dropdown.classList.add('ls-dropdown--visible');
    }

    /**
     * Render danh sách sản phẩm gợi ý
     * @param {HTMLElement} dropdown
     * @param {Array}       products   - mảng từ JSON response
     * @param {string}      keyword    - từ khóa hiện tại
     */
    function renderResults(dropdown, products, keyword) {
        if (!products || products.length === 0) {
            dropdown.innerHTML = `
                <div class="ls-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                    <p>Không tìm thấy sản phẩm</p>
                </div>
            `;
            dropdown.classList.add('ls-dropdown--visible');
            return;
        }

        const escapedKeyword = escapeHtml(keyword);

        const itemsHtml = products.map(function (product) {
            const name     = escapeHtml(product.ten);
            const imgSrc   = escapeHtml(product.hinh_anh);
            const url      = escapeHtml(product.url);
            const hasDiscount = product.gia_giam && product.gia_giam > 0 && product.gia_giam < product.gia;

            const priceHtml = hasDiscount
                ? `<span class="ls-price--original">${formatPrice(product.gia)}</span>
                   <span class="ls-price--sale">${formatPrice(product.gia_giam)}</span>`
                : `<span class="ls-price--normal">${formatPrice(product.gia)}</span>`;

            // Bold từ khóa khớp trong tên sản phẩm
            const highlighted = name.replace(
                new RegExp(`(${escapedKeyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi'),
                '<mark class="ls-highlight">$1</mark>'
            );

            return `
                <a href="${url}" class="ls-item" role="option">
                    <div class="ls-item__img-wrap">
                        <img src="${imgSrc}" alt="${name}" class="ls-item__img" loading="lazy"
                             onerror="this.src='/assets/images/no-image.png'">
                    </div>
                    <div class="ls-item__info">
                        <p class="ls-item__name">${highlighted}</p>
                        <div class="ls-item__price">${priceHtml}</div>
                    </div>
                    <svg class="ls-item__arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                </a>
            `;
        }).join('');

        dropdown.innerHTML = `
            <div class="ls-header">
                <span>Kết quả cho "<strong>${escapeHtml(keyword)}</strong>"</span>
                <a href="/shop?q=${encodeURIComponent(keyword)}" class="ls-view-all">Xem tất cả</a>
            </div>
            <div class="ls-list" role="listbox">${itemsHtml}</div>
        `;
        dropdown.classList.add('ls-dropdown--visible');
    }

    /**
     * Ẩn dropdown
     * @param {HTMLElement} dropdown
     */
    function hideDropdown(dropdown) {
        dropdown.classList.remove('ls-dropdown--visible');
    }

    // ── Core logic ────────────────────────────────────────────────

    /**
     * Khởi tạo live search cho một ô input
     * @param {HTMLElement} input    - ô tìm kiếm
     * @param {HTMLElement} dropdown - container hiển thị gợi ý
     */
    function initLiveSearch(input, dropdown) {
        let currentKeyword = '';
        let abortController = null;

        // Hàm gửi request (được debounce)
        const fetchSuggestions = debounce(async function (keyword) {
            if (!keyword || keyword.length < MIN_CHARS) {
                hideDropdown(dropdown);
                return;
            }

            // Hủy request trước nếu còn đang chạy
            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();

            showLoading(dropdown);

            try {
                const url = `${SEARCH_URL}?q=${encodeURIComponent(keyword)}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: abortController.signal
                });

                if (!response.ok) {
                    throw new Error('Lỗi server: ' + response.status);
                }

                const products = await response.json();
                // Chỉ render nếu từ khóa vẫn còn khớp (tránh race condition)
                if (keyword === currentKeyword) {
                    renderResults(dropdown, products, keyword);
                }
            } catch (err) {
                if (err.name !== 'AbortError') {
                    // Lỗi mạng/server: ẩn dropdown, không hiển thị lỗi
                    hideDropdown(dropdown);
                    console.warn('[LiveSearch] Lỗi fetch:', err.message);
                }
            }
        }, DEBOUNCE_MS);

        // Event: người dùng nhập vào ô search
        input.addEventListener('input', function () {
            const keyword = this.value.trim();
            currentKeyword = keyword;

            if (keyword.length < MIN_CHARS) {
                hideDropdown(dropdown);
                return;
            }

            fetchSuggestions(keyword);
        });

        // Event: người dùng focus vào input (có từ khóa thì mở lại)
        input.addEventListener('focus', function () {
            const keyword = this.value.trim();
            if (keyword.length >= MIN_CHARS && dropdown.querySelector('.ls-item')) {
                dropdown.classList.add('ls-dropdown--visible');
            }
        });

        // Event: click bên ngoài → đóng dropdown
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                hideDropdown(dropdown);
            }
        });

        // Event: phím Escape → đóng dropdown
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                hideDropdown(dropdown);
                this.blur();
            }
        });

        // Ngăn submit form khi click vào item gợi ý (dùng event delegation)
        dropdown.addEventListener('click', function (e) {
            const item = e.target.closest('.ls-item');
            if (item) {
                // Cho phép điều hướng bình thường qua href
                hideDropdown(dropdown);
            }
        });
    }

    // ── Khởi động ─────────────────────────────────────────────────

    /**
     * Tìm tất cả cặp (input, dropdown) và khởi tạo live search
     */
    function bootstrap() {
        // Selector dựa trên cấu trúc HTML đã phân tích trong header.blade.php
        const searchGroups = document.querySelectorAll('.ls-search-group');

        searchGroups.forEach(function (group) {
            const input    = group.querySelector('input[type="search"], input[name="q"]');
            const dropdown = group.querySelector('.ls-dropdown');

            if (input && dropdown) {
                initLiveSearch(input, dropdown);
            }
        });
    }

    // Chạy sau khi DOM sẵn sàng
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }

})();
