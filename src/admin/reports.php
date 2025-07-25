<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get sales data
$query = "SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_sales
    FROM transactions 
    WHERE status != 'cancelled' 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get top products
$query = "SELECT 
    p.name,
    SUM(ti.quantity) as total_sold,
    SUM(ti.quantity * ti.price) as revenue
    FROM transaction_items ti
    JOIN products p ON ti.product_id = p.id
    JOIN transactions t ON ti.transaction_id = t.id
    WHERE t.status != 'cancelled'
    AND t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Laporan';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold font-heading">Laporan</h1>
            <p class="text-gray-600 mt-2">Analisis penjualan dan performa toko</p>
        </div>
        <div class="flex space-x-4">
            <form method="GET" action="export-report.php" class="flex space-x-4">
                <input type="date" name="start_date" value="<?php echo date('Y-m-01'); ?>" 
                       class="border border-gray-300 rounded-lg px-4 py-2">
                <input type="date" name="end_date" value="<?php echo date('Y-m-d'); ?>" 
                       class="border border-gray-300 rounded-lg px-4 py-2">
                <div class="relative">
                    <select name="format" class="border border-gray-300 rounded-lg px-4 py-2 pr-8">
                        <option value="csv">Export CSV</option>
                        <option value="excel">Export Excel</option>
                    </select>
                </div>
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Penjualan</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php echo formatPrice(array_sum(array_column($sales_data, 'total_sales'))); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Pesanan</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php echo array_sum(array_column($sales_data, 'total_orders')); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-calculator text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Rata-rata Pesanan</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php 
                        $total_orders = array_sum(array_column($sales_data, 'total_orders'));
                        $total_sales = array_sum(array_column($sales_data, 'total_sales'));
                        echo formatPrice($total_orders > 0 ? $total_sales / $total_orders : 0);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Produk Terjual</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php echo array_sum(array_column($top_products, 'total_sold')); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">Penjualan Harian</h2>
            </div>
            
            <div class="space-y-4">
                <?php foreach (array_slice($sales_data, 0, 7) as $data): ?>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-semibold text-sm"><?php echo date('d M Y', strtotime($data['date'])); ?></p>
                        <p class="text-gray-600 text-sm"><?php echo $data['total_orders']; ?> pesanan</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold"><?php echo formatPrice($data['total_sales']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">Produk Terlaris</h2>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($top_products as $index => $product): ?>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-pink-600 font-semibold text-sm"><?php echo $index + 1; ?></span>
                        </div>
                        <div>
                            <p class="font-semibold text-sm"><?php echo htmlspecialchars($product['name']); ?></p>
                            <p class="text-gray-600 text-sm"><?php echo $product['total_sold']; ?> terjual</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold"><?php echo formatPrice($product['revenue']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
