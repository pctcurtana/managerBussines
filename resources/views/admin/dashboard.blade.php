@extends('layouts.admin')

@section('content')
<style>
    /* Fallback styles nếu Tailwind không load */
    .dashboard-container {
        padding: 1rem;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    @media (min-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    .stats-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
    }
    @media (min-width: 768px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (min-width: 1024px) {
        .form-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    .form-container {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        padding: 1.5rem;
    }
    .table-container {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .loader {
        border: 3px solid #f3f3f3;
        border-radius: 50%;
        border-top: 3px solid #3498db;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 8px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="bg-gray-100 dashboard-container">
    <!-- Main Content -->
    <div class="container mx-auto px-4">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Quản lý bán máy ảnh</h1>
            <p class="text-gray-600 mt-1">Quản lý đơn hàng và doanh thu bán máy ảnh</p>
        </div>
        
        <!-- API Error Message -->
        <div id="api-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 hidden">
            <span class="block sm:inline" id="api-error-message"></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg id="close-error" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 stats-grid">
            <div class="bg-white rounded-lg shadow-md p-6 stats-card border-l-4 border-blue-500">
                <h2 class="text-gray-500 text-sm font-medium uppercase mb-2">Tổng doanh thu</h2>
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-gray-900" id="total-revenue">
                        <span class="loader"></span>Đang tải...
                    </div>
                    <span class="ml-2 text-blue-500 text-sm font-medium">VND</span>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 stats-card border-l-4 border-green-500">
                <h2 class="text-gray-500 text-sm font-medium uppercase mb-2">Doanh thu tháng</h2>
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-gray-900" id="monthly-revenue">
                        <span class="loader"></span>Đang tải...
                    </div>
                    <span class="ml-2 text-green-500 text-sm font-medium">VND</span>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 stats-card border-l-4 border-purple-500">
                <h2 class="text-gray-500 text-sm font-medium uppercase mb-2">Tổng lợi nhuận</h2>
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-gray-900" id="total-profit">
                        <span class="loader"></span>Đang tải...
                    </div>
                    <span class="ml-2 text-purple-500 text-sm font-medium">VND</span>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 stats-card border-l-4 border-yellow-500">
                <h2 class="text-gray-500 text-sm font-medium uppercase mb-2">Tổng đơn hàng</h2>
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-gray-900" id="total-orders">
                        <span class="loader"></span>Đang tải...
                    </div>
                    <span class="ml-2 text-yellow-500 text-sm font-medium">đơn</span>
                </div>
            </div>
        </div>

        <!-- Add New Order Form -->
        <div class="bg-white rounded-lg shadow-md mb-8 form-container">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Thêm đơn hàng mới</h2>
                <form id="order-form" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 form-grid">
                        <div>
                            <label for="camera-name" class="block text-sm font-medium text-gray-700 mb-1">Tên máy ảnh</label>
                            <input type="text" id="camera-name" name="camera_name" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="cost-price" class="block text-sm font-medium text-gray-700 mb-1">Giá thu lại (VND)</label>
                            <input type="number" id="cost-price" name="cost_price" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="selling-price" class="block text-sm font-medium text-gray-700 mb-1">Giá bán đi (VND)</label>
                            <input type="number" id="selling-price" name="selling_price" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="profit" class="block text-sm font-medium text-gray-700 mb-1">Tiền lời (VND)</label>
                            <input type="text" id="profit" name="profit" readonly class="w-full rounded-md bg-gray-100 border-gray-300 shadow-sm px-4 py-2 border">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" id="submit-btn" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Thêm đơn hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden table-container">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Đơn hàng gần đây</h2>
                <div class="flex items-center">
                    <div class="mr-4 flex">
                        <button id="filter-all" class="px-3 py-1 text-sm font-medium rounded-md bg-blue-500 text-white mr-2">Tất cả</button>
                        <button id="filter-sold" class="px-3 py-1 text-sm font-medium rounded-md bg-gray-200 text-gray-700 mr-2">Đã bán</button>
                        <button id="filter-unsold" class="px-3 py-1 text-sm font-medium rounded-md bg-gray-200 text-gray-700">Chưa bán</button>
                    </div>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" id="search-input" class="block w-full pr-10 sm:text-sm border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500" placeholder="Tìm kiếm...">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên máy ảnh</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá thu lại</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá bán đi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiền lời</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="orders-table-body" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <span class="loader"></span> Đang tải dữ liệu...
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="no-orders-message" class="text-center py-8 text-gray-500 hidden">
                    Chưa có đơn hàng nào. Hãy thêm đơn hàng mới!
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // API URLs
        const API_URL = '/api';
        const ORDERS_ENDPOINT = `${API_URL}/camera-orders`;
        const STATS_ENDPOINT = `${API_URL}/stats`;
        const SEARCH_ENDPOINT = `${API_URL}/search`;
        
        // DOM elements
        const costPriceInput = document.getElementById('cost-price');
        const sellingPriceInput = document.getElementById('selling-price');
        const profitInput = document.getElementById('profit');
        const orderForm = document.getElementById('order-form');
        const submitBtn = document.getElementById('submit-btn');
        const searchInput = document.getElementById('search-input');
        const noOrdersMessage = document.getElementById('no-orders-message');
        const apiError = document.getElementById('api-error');
        const apiErrorMessage = document.getElementById('api-error-message');
        const closeError = document.getElementById('close-error');
        
        // Ẩn thông báo lỗi khi click vào nút đóng
        closeError.addEventListener('click', function() {
            apiError.classList.add('hidden');
        });
        
        // Hiển thị lỗi API
        function showApiError(message) {
            apiErrorMessage.textContent = message;
            apiError.classList.remove('hidden');
        }
        
        // Format số tiền
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }
        
        // Format ngày
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'numeric', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('vi-VN', options);
        }
        
        // Calculate prices on input change
        function calculateProfit() {
            const costPrice = parseFloat(costPriceInput.value) || 0;
            const sellingPrice = parseFloat(sellingPriceInput.value) || 0;
            const profit = sellingPrice - costPrice;
            profitInput.value = formatCurrency(profit);
        }
        
        costPriceInput.addEventListener('input', calculateProfit);
        sellingPriceInput.addEventListener('input', calculateProfit);
        
        // Load thống kê
        function loadStats() {
            fetch(STATS_ENDPOINT)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu thống kê');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('total-revenue').textContent = formatCurrency(data.total_revenue);
                    document.getElementById('monthly-revenue').textContent = formatCurrency(data.monthly_revenue);
                    document.getElementById('total-profit').textContent = formatCurrency(data.total_profit);
                    document.getElementById('total-orders').textContent = data.total_orders;
                })
                .catch(error => {
                    console.error('Lỗi khi tải thống kê:', error);
                    showApiError('Không thể tải dữ liệu thống kê. Vui lòng thử lại sau.');
                });
        }
        
        // Load đơn hàng
        function loadOrders() {
            fetch(ORDERS_ENDPOINT)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Không thể tải danh sách đơn hàng');
                    }
                    return response.json();
                })
                .then(orders => {
                    renderOrders(orders);
                })
                .catch(error => {
                    console.error('Lỗi khi tải đơn hàng:', error);
                    showApiError('Không thể tải danh sách đơn hàng. Vui lòng thử lại sau.');
                });
        }
        
        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            if (searchTerm === '') {
                loadOrders();
                return;
            }
            
            fetch(`${SEARCH_ENDPOINT}?search=${searchTerm}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Lỗi khi tìm kiếm');
                    }
                    return response.json();
                })
                .then(orders => {
                    renderOrders(orders);
                })
                .catch(error => {
                    console.error('Lỗi khi tìm kiếm:', error);
                    showApiError('Không thể tìm kiếm đơn hàng. Vui lòng thử lại sau.');
                });
        });
        
        // Filter buttons
        const filterAll = document.getElementById('filter-all');
        const filterSold = document.getElementById('filter-sold');
        const filterUnsold = document.getElementById('filter-unsold');
        
        filterAll.addEventListener('click', function() {
            updateFilterButtons(this);
            loadOrders();
        });
        
        filterSold.addEventListener('click', function() {
            updateFilterButtons(this);
            fetchFilteredOrders('sold');
        });
        
        filterUnsold.addEventListener('click', function() {
            updateFilterButtons(this);
            fetchFilteredOrders('unsold');
        });
        
        function updateFilterButtons(activeButton) {
            // Reset all buttons to default style
            [filterAll, filterSold, filterUnsold].forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            // Set active button style
            activeButton.classList.remove('bg-gray-200', 'text-gray-700');
            activeButton.classList.add('bg-blue-500', 'text-white');
        }
        
        function fetchFilteredOrders(status) {
            fetch(`${SEARCH_ENDPOINT}?status=${status}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Lỗi khi lọc đơn hàng');
                    }
                    return response.json();
                })
                .then(orders => {
                    renderOrders(orders);
                })
                .catch(error => {
                    console.error('Lỗi khi lọc đơn hàng:', error);
                    showApiError('Không thể lọc đơn hàng. Vui lòng thử lại sau.');
                });
        }
        
        // Form submission
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Tắt nút submit và hiển thị trạng thái loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loader"></span> Đang xử lý...';
            
            const formData = new FormData(orderForm);
            
            fetch(ORDERS_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    camera_name: formData.get('camera_name'),
                    cost_price: parseFloat(formData.get('cost_price')),
                    selling_price: parseFloat(formData.get('selling_price')),
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Lỗi khi thêm đơn hàng');
                }
                return response.json();
            })
            .then(order => {
                // Reset form
                orderForm.reset();
                profitInput.value = '';
                
                // Cập nhật UI
                loadOrders();
                loadStats();
                
                // Show success notification
                showNotification('Đã thêm đơn hàng thành công!');
            })
            .catch(error => {
                console.error('Lỗi khi thêm đơn hàng:', error);
                showApiError('Không thể thêm đơn hàng. Vui lòng thử lại sau.');
            })
            .finally(() => {
                // Bật lại nút submit
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg> Thêm đơn hàng';
            });
        });
        
        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg transform transition-transform duration-300 flex items-center';
            notification.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                ${message}
            `;
            
            // Add to DOM
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
        
        // Delete order
        function deleteOrder(id) {
            if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
                fetch(`${ORDERS_ENDPOINT}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Không thể xóa đơn hàng');
                    }
                    return response.json();
                })
                .then(data => {
                    // Cập nhật UI
                    loadOrders();
                    loadStats();
                    showNotification('Đã xóa đơn hàng thành công!');
                })
                .catch(error => {
                    console.error('Lỗi khi xóa đơn hàng:', error);
                    showApiError('Không thể xóa đơn hàng. Vui lòng thử lại sau.');
                });
            }
        }
        
        // Render orders to table
        function renderOrders(orders) {
            const tableBody = document.getElementById('orders-table-body');
            tableBody.innerHTML = '';
            
            if (orders.length === 0) {
                noOrdersMessage.classList.remove('hidden');
                return;
            }
            
            noOrdersMessage.classList.add('hidden');
            
            orders.forEach(order => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 transition-colors';
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${order.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(order.order_date)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${order.camera_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatCurrency(order.cost_price)} VND</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatCurrency(order.selling_price)} VND</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm ${order.profit >= 0 ? 'text-green-600' : 'text-red-600'} font-semibold">${formatCurrency(order.profit)} VND</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex space-x-2">
                            <button onclick="toggleSoldStatus(${order.id}, ${order.is_sold})" class="${order.is_sold ? 'text-green-600' : 'text-gray-500'} hover:text-green-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                ${order.is_sold ? 'Đã bán' : 'Chưa bán'}
                            </button>
                            <button onclick="deleteOrderById(${order.id})" class="text-red-600 hover:text-red-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Xóa
                            </button>
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }
        
        // Make deleteOrderById globally accessible for the onclick handler
        window.deleteOrderById = function(id) {
            deleteOrder(id);
        };
        
        // Toggle sold status
        window.toggleSoldStatus = function(id, currentStatus) {
            fetch(`${ORDERS_ENDPOINT}/${id}/toggle-sold`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không thể cập nhật trạng thái');
                }
                return response.json();
            })
            .then(data => {
                // Cập nhật UI
                loadOrders();
                loadStats();
                showNotification(data.message);
            })
            .catch(error => {
                console.error('Lỗi khi cập nhật trạng thái:', error);
                showApiError('Không thể cập nhật trạng thái đơn hàng. Vui lòng thử lại sau.');
            });
        };
        
        // Initial load
        loadStats();
        loadOrders();
    });
</script>
@endsection 