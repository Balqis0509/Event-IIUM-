<?php
// ============================================================
// INCLUDE: Flash Message Output — UniEvent IIUM
// ============================================================
if (isset($_SESSION['flash'])) {
    $f = $_SESSION['flash'];
    $type = $f['type'] === 'success' ? 'flash-success' : ($f['type'] === 'info' ? 'flash-info' : 'flash-error');
    $icon = $f['type'] === 'success' ? '✅' : ($f['type'] === 'info' ? 'ℹ️' : '❌');
    echo "<div class='flash {$type}'><span>{$icon}</span><span>" . htmlspecialchars($f['message']) . "</span></div>";
    unset($_SESSION['flash']);
}
?>
