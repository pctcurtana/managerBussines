<?php

namespace App\Http\Controllers;

use App\Models\CameraOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CameraOrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index()
    {
        $orders = CameraOrder::orderBy('order_date', 'desc')->get();
        return response()->json($orders);
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
     * Lấy thống kê
     */
    public function getStats()
    {
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
            
        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'monthly_revenue' => $monthlyRevenue,
            'total_orders' => $totalOrders,
            'sold_orders' => $soldOrders,
            'unsold_orders' => $unsoldOrders
        ]);
    }
    
    /**
     * Tìm kiếm đơn hàng
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
        
        $orders = $query->orderBy('order_date', 'desc')->get();
            
        return response()->json($orders);
    }
    
    /**
     * Thay đổi trạng thái đơn hàng (đã bán/chưa bán)
     */
    public function toggleSoldStatus($id)
    {
        $order = CameraOrder::findOrFail($id);
        $order->is_sold = !$order->is_sold;
        $order->save();
        
        return response()->json([
            'message' => $order->is_sold ? 'Đã đánh dấu là đã bán' : 'Đã đánh dấu là chưa bán',
            'order' => $order
        ]);
    }
} 