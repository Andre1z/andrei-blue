<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener los gráficos del usuario ordenados por importancia
$stmt = $db->prepare("SELECT * FROM charts WHERE user_id = :user_id ORDER BY importance DESC");
$stmt->execute([':user_id' => $user_id]);
$charts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Charts</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
  <div class="scene">
    <div class="grid-container">
      <?php foreach ($charts as $chart): ?>
        <div class="chart-box">
          <h3 class="chart-title"><?php echo htmlspecialchars($chart['chart_name']); ?></h3>
          <p>Tipo: <?php echo htmlspecialchars($chart['chart_type']); ?></p>
          <p>Importancia: <?php echo htmlspecialchars($chart['importance']); ?></p>
          <a href="edit_chart.php?id=<?php echo $chart['identificador']; ?>">Editar</a> |
          <a href="delete_chart.php?id=<?php echo $chart['identificador']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este gráfico?');">Eliminar</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="logout-link">
    <a href="logout.php">Cerrar sesión</a>
  </div>
</body>
</html>