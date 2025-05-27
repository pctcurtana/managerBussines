<?php

namespace App\Http\Controllers;

use App\Models\CameraOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CameraOrderController extends Controller
{
    // Số lượng đơn hàng tối đa trả về
    protected $maxOrdersLimit = 100;

    /**
     * Hiển thị danh sách đơn hàng, giới hạn số lượng
     */
    public function index()
    {
        $orders = CameraOrder::orderBy('order_date', 'desc')
            ->limit($this->maxOrdersLimit)
            ->get();
        return response()->json($orders)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Hiển thị thông tin một đơn hàng
     */
    public function show($id)
    {
        $order = CameraOrder::findOrFail($id);
        return response()->json($order);
    }

    /**
     * Lưu đơn hàng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'camera_name' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'is_sold' => 'boolean',
        ]);

        $is_sold = $request->has('is_sold') ? $request->is_sold : true;
        
        $order = CameraOrder::create([
            'camera_name' => $request->camera_name,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'profit' => $request->selling_price - $request->cost_price,
            'order_date' => Carbon::now()->toDateString(),
            'is_sold' => $is_sold
        ]);

        return response()->json($order, 201);
    }

    /**
     * Cập nhật đơn hàng
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'camera_name' => 'sometimes|required|string|max:255',
            'cost_price' => 'sometimes|required|numeric|min:0',
            'selling_price' => 'sometimes|required|numeric|min:0',
            'is_sold' => 'sometimes|required|boolean',
        ]);

        $order = CameraOrder::findOrFail($id);
        
        // Cập nhật thông tin
        if ($request->has('camera_name')) {
            $order->camera_name = $request->camera_name;
        }
        
        if ($request->has('cost_price')) {
            $order->cost_price = $request->cost_price;
        }
        
        if ($request->has('selling_price')) {
            $order->selling_price = $request->selling_price;
        }
        
        // Nếu giá thu lại hoặc giá bán đi thay đổi, tính lại lợi nhuận
        if ($request->has('cost_price') || $request->has('selling_price')) {
            $order->profit = $order->selling_price - $order->cost_price;
        }
        
        if ($request->has('is_sold')) {
            $order->is_sold = $request->is_sold;
        }
        
        $order->save();
        
        return response()->json($order);
    }

    /**
     * Xóa đơn hàng
     */
    public function destroy($id)
    {
        $order = CameraOrder::findOrFail($id);
        $order->delete();
        
        return response()->json(['message' => 'Đã xóa đơn hàng thành công'], 200);
    }

    /**
     * Lấy thống kê - REAL TIME không cache
     */
    public function getStats()
    {
        // LOẠI BỎ HOÀN TOÀN CACHE để có real-time data
        
        // Chỉ tính doanh thu và lợi nhuận cho đơn hàng đã bán được
        $totalRevenue = CameraOrder::where('is_sold', true)->sum('selling_price');
        $totalProfit = CameraOrder::where('is_sold', true)->sum('profit');
        $totalOrders = CameraOrder::count();
        $soldOrders = CameraOrder::where('is_sold', true)->count();
        $unsoldOrders = CameraOrder::where('is_sold', false)->count();
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $monthlyRevenue = CameraOrder::whereMonth('order_date', $currentMonth)
            ->whereYear('order_date', $currentYear)
            ->where('is_sold', true)
            ->sum('selling_price');
            
        $stats = [
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'monthly_revenue' => $monthlyRevenue,
            'total_orders' => $totalOrders,
            'sold_orders' => $soldOrders,
            'unsold_orders' => $unsoldOrders
        ];
        
        // KHÔNG cache - trả về ngay lập tức với headers no-cache   
        return response()->json($stats)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    
    /**
     * Tìm kiếm đơn hàng - tối ưu số lượng kết quả
     */
    public function search(Request $request)
    {
        $searchTerm = $request->search;
        $status = $request->has('status') ? $request->status : null;
        
        $query = CameraOrder::query();
        
        // Tìm kiếm theo tên hoặc ID
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('camera_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('id', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Lọc theo trạng thái bán
        if ($status !== null) {
            if ($status === 'sold') {
                $query->where('is_sold', true);
            } elseif ($status === 'unsold') {
                $query->where('is_sold', false);
            }
        }
        
        $orders = $query->orderBy('order_date', 'desc')
            ->limit($this->maxOrdersLimit)
            ->get();
            
        return response()->json($orders)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    
    /**
     * Thay đổi trạng thái đơn hàng (đã bán/chưa bán)
     */
    public function toggleSoldStatus($id)
    {
        $order = CameraOrder::findOrFail($id);
        $order->is_sold = !$order->is_sold;
        $order->save();
        
        // Không cần xóa cache vì không còn cache
        
        return response()->json([
            'message' => $order->is_sold ? 'Đã đánh dấu là đã bán' : 'Đã đánh dấu là chưa bán',
            'order' => $order
        ]);
    }
} 