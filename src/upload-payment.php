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
$query = "SELECT * FROM transactions WHERE transaction_code = ? AND user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$transaction_code, $_SESSION['user_id']]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    setFlashMessage('error', 'Pesanan tidak ditemukan');
    header('Location: orders.php');
    exit();
}

if ($transaction['status'] != 'pending') {
    setFlashMessage('error', 'Pesanan ini sudah tidak dapat diupload bukti pembayaran');
    header("Location: order-detail.php?code=$transaction_code");
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    $amount = (float)$_POST['amount'];
    $account_name = trim($_POST['account_name']);
    $account_number = trim($_POST['account_number']);
    
    $errors = [];
    
    if (empty($payment_method)) $errors[] = 'Metode pembayaran harus dipilih';
    if ($amount <= 0) $errors[] = 'Jumlah pembayaran tidak valid';
    if (empty($account_name)) $errors[] = 'Nama rekening harus diisi';
    if (empty($account_number)) $errors[] = 'Nomor rekening/HP harus diisi';
    
    // Handle file upload
    $upload_dir = 'uploads/payments/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['proof_image']['type'], $allowed_types)) {
            $errors[] = 'Format file harus JPG, JPEG, atau PNG';
        }
        
        if ($_FILES['proof_image']['size'] > $max_size) {
            $errors[] = 'Ukuran file maksimal 5MB';
        }
        
        if (empty($errors)) {
            $file_extension = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
            $filename = 'payment_' . $transaction['id'] . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (!move_uploaded_file($_FILES['proof_image']['tmp_name'], $upload_path)) {
                $errors[] = 'Gagal mengupload file';
            }
        }
    } else {
        $errors[] = 'Bukti pembayaran harus diupload';
    }
    
    if (empty($errors)) {
        try {
            // Check if payment already exists
            $query = "SELECT id FROM payments WHERE transaction_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$transaction['id']]);
            
            if ($stmt->rowCount() > 0) {
                // Update existing payment - AUTO APPROVED FOR LEARNING PURPOSE
                $query = "UPDATE payments SET payment_method = ?, amount = ?, account_name = ?, account_number = ?, proof_image = ?, status = 'approved', payment_date = NOW(), created_at = NOW() WHERE transaction_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$payment_method, $amount, $account_name, $account_number, $filename, $transaction['id']]);
            } else {
                // Insert new payment - AUTO APPROVED FOR LEARNING PURPOSE
                $query = "INSERT INTO payments (transaction_id, payment_method, amount, account_name, account_number, proof_image, status, payment_date) VALUES (?, ?, ?, ?, ?, ?, 'approved', NOW())";
                $stmt = $db->prepare($query);
                $stmt->execute([$transaction['id'], $payment_method, $amount, $account_name, $account_number, $filename]);
            }
            
            // Update transaction status to COMPLETED - AUTO PROCESS FOR LEARNING PURPOSE
            $query = "UPDATE transactions SET status = 'completed', updated_at = NOW() WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$transaction['id']]);
            
            logActivity($_SESSION['user_id'], "Payment automatically approved for order {$transaction['transaction_code']} - Learning Mode");
            
            setFlashMessage('success', '🎉 Pembayaran berhasil diverifikasi! Pesanan Anda telah dikonfirmasi dan sedang diproses. Terima kasih!');
            header("Location: order-detail.php?code=$transaction_code");
            exit();
            
        } catch (Exception $e) {
            $errors[] = 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage();
            // For debugging - log the full error
            error_log('Payment upload error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
        }
    }
}

$page_title = 'Upload Bukti Pembayaran';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold font-heading">Upload Bukti Pembayaran</h1>
        <p class="text-gray-600 mt-2">Pesanan #<?php echo htmlspecialchars($transaction['transaction_code']); ?></p>
        
        <!-- Learning Mode Indicator -->
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-graduation-cap text-blue-500 text-xl mr-3"></i>
                <div>
                    <h3 class="text-blue-800 font-semibold">Mode Pembelajaran Aktif</h3>
                    <p class="text-blue-600 text-sm mt-1">
                        Untuk tujuan pembelajaran, setiap upload gambar akan otomatis memproses pembayaran sebagai berhasil.
                        Tidak perlu transfer uang sungguhan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upload Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Informasi Pembayaran</h2>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran *</label>
                        <select name="payment_method" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">Pilih Metode Pembayaran</option>
                            <?php if ($transaction['payment_method'] == 'bank_transfer'): ?>
                                <option value="bca">BCA</option>
                                <option value="bni">BNI</option>
                                <option value="bri">BRI</option>
                                <option value="mandiri">Mandiri</option>
                            <?php elseif ($transaction['payment_method'] == 'e_wallet'): ?>
                                <option value="ovo">OVO</option>
                                <option value="gopay">GoPay</option>
                                <option value="dana">DANA</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Transfer *</label>
                        <input type="number" name="amount" value="<?php echo $transaction['total_amount']; ?>" 
                               step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <p class="text-sm text-gray-500 mt-1">
                            Total yang harus dibayar: <span class="font-semibold text-pink-500"><?php echo formatPrice($transaction['total_amount']); ?></span>
                        </p>
                    </div>

                    <!-- Account Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Rekening/Akun *</label>
                        <input type="text" name="account_name" required
                               placeholder="Nama sesuai rekening/akun yang digunakan"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>

                    <!-- Account Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening/HP *</label>
                        <input type="text" name="account_number" required
                               placeholder="Nomor rekening atau nomor HP"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>

                    <!-- Proof Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Transfer *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" name="proof_image" id="proof_image" accept="image/*" required
                                   class="hidden" onchange="previewImage(this)">
                            <label for="proof_image" class="cursor-pointer">
                                <div id="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-600 mb-2">Klik untuk upload bukti transfer</p>
                                    <p class="text-sm text-gray-500">Format: JPG, JPEG, PNG (Maks. 5MB)</p>
                                </div>
                                <div id="image-preview" class="hidden">
                                    <img id="preview-img" src="" alt="Preview" class="max-w-xs mx-auto rounded-lg shadow-md">
                                    <p class="text-sm text-gray-600 mt-2">Klik untuk mengganti gambar</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex space-x-4">
                        <button type="submit" class="flex-1 btn-primary py-3 rounded-lg text-white">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Bukti Pembayaran
                        </button>
                        
                        <a href="order-detail.php?code=<?php echo $transaction_code; ?>" 
                           class="flex-1 border border-gray-300 py-3 rounded-lg text-center hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h2 class="text-xl font-semibold mb-4">Informasi Transfer</h2>
                
                <?php if ($transaction['payment_method'] == 'bank_transfer'): ?>
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-blue-600 mb-2">Bank BCA</h3>
                            <p class="text-sm">No. Rekening: <span class="font-mono">1234567890</span></p>
                            <p class="text-sm">Atas Nama: <span class="font-semibold">Merona Shop</span></p>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-orange-600 mb-2">Bank BNI</h3>
                            <p class="text-sm">No. Rekening: <span class="font-mono">0987654321</span></p>
                            <p class="text-sm">Atas Nama: <span class="font-semibold">Merona Shop</span></p>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-blue-800 mb-2">Bank BRI</h3>
                            <p class="text-sm">No. Rekening: <span class="font-mono">5678901234</span></p>
                            <p class="text-sm">Atas Nama: <span class="font-semibold">Merona Shop</span></p>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-yellow-600 mb-2">Bank Mandiri</h3>
                            <p class="text-sm">No. Rekening: <span class="font-mono">4321098765</span></p>
                            <p class="text-sm">Atas Nama: <span class="font-semibold">Merona Shop</span></p>
                        </div>
                    </div>
                <?php elseif ($transaction['payment_method'] == 'e_wallet'): ?>
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-purple-600 mb-2">OVO</h3>
                            <p class="text-sm">Nomor: <span class="font-mono">081234567890</span></p>
                            <p class="text-sm">Atas Nama: <span class="font-semibold">Merona Shop</span></p>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-green-600 mb-2">GoPay</h3>
                            <p class="text-sm">Nomor: <span class="font-mono">081234567890</span></p>
                            <p class="text-sm">Atas Nama: <span class="font-semibold">Merona Shop</span></p>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-blue-500 mb-2">DANA</h3>
                            <p class="text-sm">Nomor: <span class="font-mono">081234567890</span></p>
                            <p class="text-sm">Atas Nama: <span class="font-semibold">Merona Shop</span></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="mt-6 p-4 bg-pink-50 rounded-lg">
                    <h3 class="font-semibold text-pink-800 mb-2">Total Transfer</h3>
                    <p class="text-2xl font-bold text-pink-600">
                        <?php echo formatPrice($transaction['total_amount']); ?>
                    </p>
                </div>
                
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <h3 class="font-semibold text-yellow-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Penting!
                    </h3>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Transfer sesuai nominal yang tertera</li>
                        <li>• Upload bukti transfer yang jelas</li>
                        <li>• Verifikasi maksimal 1x24 jam</li>
                        <li>• Hubungi CS jika ada kendala</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('preview-img').src = e.target.result;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>