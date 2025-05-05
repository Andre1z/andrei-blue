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

// Obtener datos del gráfico
$stmt = $db->prepare("SELECT * FROM charts WHERE identificador = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $chart_id,
    ':user_id' => $_SESSION['user_id']
]);
$chart = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$chart) {
    echo "Gráfico no encontrado.";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chart_name = trim($_POST['chart_name'] ?? '');
    $chart_type = trim($_POST['chart_type'] ?? '');
    $data_url = trim($_POST['data_url'] ?? '');
    $importance = isset($_POST['importance']) ? (int)$_POST['importance'] : 1;
    $importance = max(1, min(5, $importance));

    if ($chart_name && $chart_type && $data_url) {
        $stmt = $db->prepare("UPDATE charts 
            SET chart_name = :chart_name, chart_type = :chart_type, data_url = :data_url, importance = :importance 
            WHERE identificador = :id AND user_id = :user_id");
        $stmt->execute([
            ':chart_name' => $chart_name,
            ':chart_type' => $chart_type,
            ':data_url' => $data_url,
            ':importance' => $importance,
            ':id' => $chart_id,
            ':user_id' => $_SESSION['user_id']
        ]);
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar gráfico</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="form-container">
    <h1 class="form-title">Editar gráfico</h1>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="edit_chart.php?id=<?php echo $chart_id; ?>">
      <div class="form-group">
        <label>Nombre del gráfico:</label>
        <input type="text" name="chart_name" value="<?php echo htmlspecialchars($chart['chart_name']); ?>" required>
      </div>
      <div class="form-group">
        <label>Tipo de gráfico:</label>
        <select name="chart_type" required>
          <option value="bar" <?php if ($chart['chart_type'] === 'bar') echo 'selected'; ?>>Barra</option>
          <option value="line" <?php if ($chart['chart_type'] === 'line') echo 'selected'; ?>>Línea</option>
          <option value="pie" <?php if ($chart['chart_type'] === 'pie') echo 'selected'; ?>>Tarta</option>
          <option value="ring" <?php if ($chart['chart_type'] === 'ring') echo 'selected'; ?>>Anillo</option>
          <option value="stacked" <?php if ($chart['chart_type'] === 'stacked') echo 'selected'; ?>>Barras Apiladas</option>
          <option value="gauge" <?php if ($chart['chart_type'] === 'gauge') echo 'selected'; ?>>Radial</option>
        </select>
      </div>
      <div class="form-group">
        <label>URL de datos:</label>
        <input type="text" name="data_url" value="<?php echo htmlspecialchars($chart['data_url']); ?>" required>
      </div>
      <div class="form-group">
        <label>Importancia:</label>
        <select name="importance" required>
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?php echo $i; ?>" <?php if ((int)$chart['importance'] === $i) echo 'selected'; ?>>
              <?php echo $i; ?>
            </option>
          <?php endfor; ?>
        </select>
      </div>
      <button type="submit" class="btn">Guardar cambios</button>
    </form>
    <p class="switch"><a href="dashboard.php">Volver al Dashboard</a></p>
  </div>
</body>
</html>