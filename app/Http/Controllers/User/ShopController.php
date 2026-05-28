<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

/**
 * Controller hiển thị sản phẩm cho user
 * Chức năng: tìm kiếm, filter theo danh mục/giá, sắp xếp, phân trang
 */
class ShopController extends Controller
{
    // Danh sách sản phẩm (có tìm kiếm, filter, sắp xếp, phân trang)
    public function index(Request $request)
    {
        $query = SanPham::query();

        // Tìm kiếm theo từ khóa
        if ($request->has('q')) {
            $keyword = $request->input('q');
            $query->where('ten', 'like', "%{$keyword}%");
        }

        // Lọc theo danh mục
        if ($request->has('danh_muc_id')) {
            $query->where('danh_muc_id', $request->input('danh_muc_id'));
        }

        // Lọc theo nhóm danh mục (nam, nữ, phụ kiện)
        if ($request->has('category_group')) {
            $group = $request->input('category_group');
            $query->whereHas('danh_muc', function ($q) use ($group) {
                if ($group == 'nam') {
                    $q->where('slug', 'like', '%-nam%')->orWhere('slug', 'like', '%nam-%');
                } elseif ($group == 'nu') {
                    $q->where('slug', 'like', '%-nu%')->orWhere('slug', 'like', '%nu-%');
                } elseif ($group == 'phu-kien') {
                    $q->where('slug', 'like', '%phu-kien%');
                }
            });
        }

        // Lọc theo khoảng giá
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('gia', [$request->min_price, $request->max_price]);
        }

        // Sắp xếp sản phẩm theo tham số `sort`
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest('id');
                break;
            case 'name_asc':
                $query->orderBy('ten', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('ten', 'desc');
                break;
            case 'price_asc':
                // Ưu tiên gia_giam nếu có, không thì dùng gia
                $query->orderByRaw('COALESCE(gia_giam, gia) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(gia_giam, gia) DESC');
                break;
            case 'discount_desc':
                // Sắp xếp theo % giảm giá nhiều nhất (có gia_giam lên trước)
                $query->orderByRaw('(gia - COALESCE(gia_giam, gia)) DESC');
                break;
            case 'newest':
            default:
                $query->latest('id');
                break;
        }

        $danhMucs = DanhMuc::all();
        $currentPage = $request->input('page', 1);
        $totalProducts = $query->count();
        $sanPhams = $query->paginate(8)->appends($request->except('page'));

        // Redirect về trang 1 nếu page vượt quá số trang
        if ($currentPage > $sanPhams->lastPage() && $sanPhams->lastPage() > 0) {
            return redirect()->route('shop.index', array_merge($request->except('page'), ['page' => 1]));
        }

        return view('user.products.home', compact('danhMucs', 'sanPhams', 'sort', 'totalProducts'));
    }

    // Chi tiết sản phẩm (có sản phẩm liên quan + recently viewed)
    public function product_detail($slug)
    {
        $sanPham = SanPham::with('danh_muc')->where('slug', $slug)->first();

        if (!$sanPham) {
            abort(404, 'Sản phẩm không tồn tại');
        }

        // --- Recently Viewed: cập nhật session ---
        $viewed = session()->get('recently_viewed', []);

        // Xóa ID nếu đã tồn tại (để đưa lên đầu)
        $viewed = array_filter($viewed, fn($id) => $id !== $sanPham->id);

        // Đưa sản phẩm hiện tại lên đầu mảng
        array_unshift($viewed, $sanPham->id);

        // Giới hạn tối đa 8 sản phẩm
        $viewed = array_slice($viewed, 0, 8);

        session()->put('recently_viewed', array_values($viewed));

        // --- Lấy danh sách recently viewed để hiển thị (bỏ sản phẩm hiện tại) ---
        $recentlyViewedIds = array_filter($viewed, fn($id) => $id !== $sanPham->id);

        $recentlyViewed = collect(); // mặc định rỗng
        if (!empty($recentlyViewedIds)) {
            // Lấy từ DB, giữ đúng thứ tự đã xem bằng cách sort theo thứ tự trong mảng
            $products = SanPham::with('danh_muc')
                ->whereIn('id', $recentlyViewedIds)
                ->get()
                ->keyBy('id');

            // Sắp xếp theo thứ tự session (xem gần nhất lên đầu)
            $recentlyViewed = collect($recentlyViewedIds)
                ->map(fn($id) => $products->get($id))
                ->filter(); // loại bỏ null nếu sản phẩm đã bị xoá khỏi DB
        }

        // --- Sản phẩm liên quan (cùng danh mục) ---
        $relatedProducts = SanPham::with('danh_muc')
            ->where('danh_muc_id', $sanPham->danh_muc_id)
            ->where('slug', '!=', $slug)
            ->get();

        // Nếu không đủ 4 sản phẩm, lấy thêm
        if ($relatedProducts->count() < 4) {
            $additionalProducts = SanPham::where('danh_muc_id', $sanPham->danh_muc_id)
                ->where('slug', '!=', $slug)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->take(4 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->merge($additionalProducts);
        }

        // Xử lý gallery images (string phân cách bằng dấu phẩy)
        $galleryImages = !empty($sanPham->hinh_anh_chi_tiet)
            ? array_filter(explode(',', $sanPham->hinh_anh_chi_tiet))
            : [];

        return view('user.products.detail', compact(
            'sanPham',
            'galleryImages',
            'relatedProducts',
            'recentlyViewed'
        ));
    }

    public function store(Request $request)
    {
        //
    }

    public function show(SanPham $sanPham)
    {
        //
    }

    public function edit(SanPham $sanPham)
    {
        //
    }

    public function update(Request $request, SanPham $sanPham)
    {
        //
    }

    public function destroy(SanPham $sanPham)
    {
        //
    }
}
