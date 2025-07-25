<?php
require_once 'includes/functions.php';
requireLogin();

$transaction_code = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($transaction_code)) {
    header('Location: orders.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get transaction details
$query = "SELECT t.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
          FROM transactions t 
          JOIN users u ON t.user_id = u.id 
          WHERE t.transaction_code = ? AND t.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$transaction_code, $_SESSION['user_id']]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    setFlashMessage('error', 'Invoice tidak ditemukan');
    header('Location: orders.php');
    exit();
}

// Only allow invoice download for completed orders
if ($transaction['status'] != 'completed') {
    setFlashMessage('error', 'Invoice hanya tersedia untuk pesanan yang sudah selesai');
    header("Location: order-detail.php?code=$transaction_code");
    exit();
}

// Get transaction items
$query = "SELECT ti.*, p.name as product_name 
          FROM transaction_items ti 
          JOIN products p ON ti.product_id = p.id 
          WHERE ti.transaction_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$transaction['id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get payment info
$query = "SELECT * FROM payments WHERE transaction_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$transaction['id']]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?php echo htmlspecialchars($transaction['transaction_code']); ?> - Merona Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
            background: white;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 3px solid #e91e63;
            padding-bottom: 20px;
        }
        
        .company-info h1 {
            color: #e91e63;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-info p {
            color: #666;
            font-size: 14px;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .detail-section h3 {
            color: #e91e63;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .detail-section p {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th,
        .items-table td {
            text-align: left;
            padding: 12px 8px;
            border-bottom: 1px solid #eee;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }
        
        .items-table td {
            font-size: 14px;
        }
        
        .text-right {
            text-align: right !important;
        }
        
        .summary-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 8px 12px;
            border: none;
            font-size: 14px;
        }
        
        .summary-table .total-row {
            border-top: 2px solid #e91e63;
            font-weight: bold;
            font-size: 16px;
            color: #e91e63;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .learning-badge {
            background-color: #e3f2fd;
            color: #1565c0;
            margin-left: 10px;
        }
        
        .footer-note {
            margin-top: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        .print-controls {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #e91e63;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #c2185b;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        @media print {
            .print-controls {
                display: none !important;
            }
            
            body {
                background: white !important;
            }
            
            .invoice-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 20px !important;
            }
        }
        
        /* Styles for saving as image */
        .save-as-image .invoice-container {
            border: 1px solid #ddd;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <h3 style="margin-bottom: 15px;">📄 Invoice Actions</h3>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Invoice
        </button>
        <button onclick="saveAsImage()" class="btn btn-secondary">
            <i class="fas fa-download"></i> Save as JPG
        </button>
        <a href="order-detail.php?code=<?php echo $transaction_code; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Order
        </a>
    </div>

    <div class="invoice-container" id="invoice">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1><i class="fas fa-shopping-bag" style="margin-right: 8px;"></i>Merona</h1>
                <p>Fashion Online Store</p>
                <p>Jl. Fashion Street No. 123, Jakarta</p>
                <p>Phone: +62 812-3456-7890</p>
                <p>Email: info@merona.com</p>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p><strong><?php echo htmlspecialchars($transaction['transaction_code']); ?></strong></p>
                <p><?php echo date('d M Y', strtotime($transaction['created_at'])); ?></p>
                <div style="margin-top: 10px;">
                    <span class="status-badge status-completed">Completed</span>
                    <span class="status-badge learning-badge"><i class="fas fa-graduation-cap"></i> Learning Mode</span>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="detail-section">
                <h3>Bill To:</h3>
                <p><strong><?php echo htmlspecialchars($transaction['user_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($transaction['user_email']); ?></p>
                <?php if ($transaction['user_phone']): ?>
                    <p><?php echo htmlspecialchars($transaction['user_phone']); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="detail-section">
                <h3>Ship To:</h3>
                <p><strong><?php echo htmlspecialchars($transaction['shipping_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($transaction['shipping_phone']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($transaction['shipping_address'])); ?></p>
                <p><?php echo htmlspecialchars($transaction['shipping_city']); ?>, <?php echo htmlspecialchars($transaction['shipping_postal_code']); ?></p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td class="text-right"><?php echo $item['quantity']; ?></td>
                    <td class="text-right"><?php echo formatPrice($item['price']); ?></td>
                    <td class="text-right"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Summary -->
        <table class="summary-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right"><?php echo formatPrice($transaction['total_amount']); ?></td>
            </tr>
            <tr>
                <td>Shipping:</td>
                <td class="text-right" style="color: green;">FREE</td>
            </tr>
            <tr class="total-row">
                <td>Total:</td>
                <td class="text-right"><?php echo formatPrice($transaction['total_amount']); ?></td>
            </tr>
        </table>

        <!-- Payment Info -->
        <?php if ($payment): ?>
        <div style="margin-top: 30px;">
            <h3 style="color: #e91e63; margin-bottom: 10px;">Payment Information</h3>
            <p><strong>Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></p>
            <p><strong>Amount:</strong> <?php echo formatPrice($payment['amount']); ?></p>
            <p><strong>Date:</strong> <?php echo date('d M Y, H:i', strtotime($payment['payment_date'])); ?></p>
            <p><strong>Status:</strong> <span style="color: green; font-weight: bold;">APPROVED (Learning Mode)</span></p>
        </div>
        <?php endif; ?>

        <!-- Footer Note -->
        <div class="footer-note">
            <p><strong>🎓 LEARNING MODE - EDUCATIONAL PURPOSE ONLY</strong></p>
            <p>This is a simulated invoice for educational purposes. No real transaction occurred.</p>
            <p>Generated on <?php echo date('d M Y, H:i'); ?> | Invoice #<?php echo htmlspecialchars($transaction['transaction_code']); ?></p>
            <p>Thank you for using Merona Fashion Learning Platform!</p>
        </div>
    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- html2canvas library for saving as image -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <script>
        function saveAsImage() {
            const element = document.getElementById('invoice');
            const printControls = document.querySelector('.print-controls');
            
            // Hide print controls
            printControls.style.display = 'none';
            
            // Add border for better image appearance
            document.body.classList.add('save-as-image');
            
            html2canvas(element, {
                scale: 2, // Higher quality
                useCORS: true,
                backgroundColor: '#ffffff',
                width: element.offsetWidth,
                height: element.offsetHeight,
            }).then(function(canvas) {
                // Create download link
                const link = document.createElement('a');
                link.download = 'Invoice_<?php echo $transaction["transaction_code"]; ?>.jpg';
                
                // Convert to JPG
                link.href = canvas.toDataURL('image/jpeg', 0.9);
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Restore controls and styling
                printControls.style.display = 'block';
                document.body.classList.remove('save-as-image');
                
                // Show success message
                alert('✅ Invoice berhasil disimpan sebagai JPG!');
            }).catch(function(error) {
                console.error('Error saving invoice:', error);
                alert('❌ Gagal menyimpan invoice. Silakan coba lagi.');
                
                // Restore controls
                printControls.style.display = 'block';
                document.body.classList.remove('save-as-image');
            });
        }
        
        // Auto-focus for better printing
        window.onload = function() {
            document.title = 'Invoice_<?php echo $transaction["transaction_code"]; ?>';
        };
    </script>
</body>
</html>
