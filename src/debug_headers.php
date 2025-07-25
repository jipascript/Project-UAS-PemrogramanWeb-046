<?php
// Debug script to find BOM or output issues causing "headers already sent"

echo "<h2>🔍 Debugging Headers Issue</h2>";

// Check if headers were already sent
if (headers_sent($file, $line)) {
    echo "<div style='color: red;'>";
    echo "❌ Headers already sent!<br>";
    echo "File: $file<br>";
    echo "Line: $line<br>";
    echo "</div>";
} else {
    echo "<div style='color: green;'>✅ Headers not sent yet - this is good!</div>";
}

echo "<hr>";

// Check files for BOM
$files_to_check = [
    'includes/functions.php',
    'includes/header.php',
    'config/database.php',
    'checkout.php'
];

echo "<h3>🔍 Checking files for BOM (Byte Order Mark):</h3>";

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $bom = substr($content, 0, 3);
        
        echo "<div>";
        echo "<strong>$file:</strong> ";
        
        if ($bom === "\xEF\xBB\xBF") {
            echo "<span style='color: red;'>❌ Has BOM - This causes headers already sent!</span>";
        } else {
            echo "<span style='color: green;'>✅ No BOM</span>";
        }
        
        // Check for whitespace before <?php
        if (preg_match('/^\s+<\?php/', $content)) {
            echo " <span style='color: red;'>❌ Has whitespace before &lt;?php</span>";
        }
        
        // Check for content after closing ?>
        if (preg_match('/\?>\s*\r?\n(.+)$/s', $content, $matches)) {
            echo " <span style='color: red;'>❌ Has content after closing ?&gt;</span>";
        }
        
        echo "</div>";
    } else {
        echo "<div><strong>$file:</strong> <span style='color: orange;'>File not found</span></div>";
    }
}

echo "<hr>";

echo "<h3>🔧 Quick Fixes:</h3>";
echo "<ol>";
echo "<li><strong>If any file has BOM:</strong> Save the file with UTF-8 without BOM encoding</li>";
echo "<li><strong>If whitespace before &lt;?php:</strong> Remove any spaces, tabs, or newlines before &lt;?php</li>";
echo "<li><strong>If content after ?&gt;:</strong> Remove closing ?&gt; tags or content after them</li>";
echo "<li><strong>Alternative:</strong> Use output buffering (already implemented in checkout.php)</li>";
echo "</ol>";

echo "<hr>";

echo "<h3>🧪 Test Output Buffering:</h3>";
ob_start();
echo "This content is buffered";
$buffer_content = ob_get_contents();
ob_end_clean();

if ($buffer_content === "This content is buffered") {
    echo "<span style='color: green;'>✅ Output buffering works correctly</span>";
} else {
    echo "<span style='color: red;'>❌ Output buffering not working</span>";
}

echo "<hr>";
echo "<p><a href='checkout.php'>Test Checkout Again</a></p>";
echo "<p><a href='fix_checkout.php'>Run Database Fix First</a></p>";
?>
