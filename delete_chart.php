<?php
// delete_chart.php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$chart_id = $_GET['id'] ?? '';
if ($chart_id) {
    $stmt = $db->prepare("DELETE FROM charts WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id' => $chart_id,
        ':user_id' => $_SESSION['user_id']
    ]);
}
header("Location: dashboard.php");
exit;
?>

