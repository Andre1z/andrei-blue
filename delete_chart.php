<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$chart_id = $_GET['id'] ?? '';
if (!$chart_id) {
    header("Location: dashboard.php");
    exit;
}

// Verificamos que el gráfico pertenezca al usuario autenticado
$stmt = $db->prepare("SELECT * FROM charts WHERE identificador = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $chart_id,
    ':user_id' => $_SESSION['user_id']
]);
$chart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chart) {
    echo "Gráfico no encontrado o no tienes permiso para eliminarlo.";
    exit;
}

// Eliminamos el gráfico
$stmt = $db->prepare("DELETE FROM charts WHERE identificador = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $chart_id,
    ':user_id' => $_SESSION['user_id']
]);

header("Location: dashboard.php");
exit;
?>