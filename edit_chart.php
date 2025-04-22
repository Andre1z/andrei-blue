<?php
// edit_chart.php
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

// Fetch chart data (which now includes the importance field)
$stmt = $db->prepare("SELECT * FROM charts WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $chart_id,
    ':user_id' => $_SESSION['user_id']
]);
$chart = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$chart) {
    echo "Chart not found.";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chart_name = trim($_POST['chart_name'] ?? '');
    $chart_type = trim($_POST['chart_type'] ?? '');
    $data_url = trim($_POST['data_url'] ?? '');
    $importance = isset($_POST['importance']) ? (int)$_POST['importance'] : 1;
    if ($importance < 1) { $importance = 1; }
    if ($importance > 5) { $importance = 5; }

    if ($chart_name && $chart_type && $data_url) {
        $stmt = $db->prepare("UPDATE charts SET chart_name = :chart_name, chart_type = :chart_type, data_url = :data_url, importance = :importance WHERE id = :id AND user_id = :user_id");
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
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Chart</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="form-container">
    <h1 class="form-title">Edit Chart</h1>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="edit_chart.php?id=<?php echo $chart_id; ?>">
      <div class="form-group">
        <label>Chart Name:</label>
        <input type="text" name="chart_name" value="<?php echo htmlspecialchars($chart['chart_name']); ?>" required>
      </div>
      <div class="form-group">
        <label>Chart Type:</label>
        <select name="chart_type" required>
          <option value="bar" <?php if ($chart['chart_type'] === 'bar') echo 'selected'; ?>>Bar Chart</option>
          <option value="line" <?php if ($chart['chart_type'] === 'line') echo 'selected'; ?>>Line Chart</option>
          <option value="pie" <?php if ($chart['chart_type'] === 'pie') echo 'selected'; ?>>Pie Chart</option>
          <option value="ring" <?php if ($chart['chart_type'] === 'ring') echo 'selected'; ?>>Ring Chart</option>
          <option value="stacked" <?php if ($chart['chart_type'] === 'stacked') echo 'selected'; ?>>Stacked Bar Chart</option>
          <option value="gauge" <?php if ($chart['chart_type'] === 'gauge') echo 'selected'; ?>>Radial Gauge Chart</option>
        </select>
      </div>
      <div class="form-group">
        <label>Data URL:</label>
        <input type="text" name="data_url" value="<?php echo htmlspecialchars($chart['data_url']); ?>" required>
      </div>
      <div class="form-group">
        <label>Importance:</label>
        <select name="importance" required>
          <option value="1" <?php if ((int)$chart['importance'] === 1) echo 'selected'; ?>>1 (Small)</option>
          <option value="2" <?php if ((int)$chart['importance'] === 2) echo 'selected'; ?>>2 (Medium)</option>
          <option value="3" <?php if ((int)$chart['importance'] === 3) echo 'selected'; ?>>3 (Large)</option>
          <option value="4" <?php if ((int)$chart['importance'] === 4) echo 'selected'; ?>>4 (Extra Large)</option>
          <option value="5" <?php if ((int)$chart['importance'] === 5) echo 'selected'; ?>>5 (Max)</option>
        </select>
      </div>
      <button type="submit" class="btn">Update Chart</button>
    </form>
    <p class="switch"><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</body>
</html>

