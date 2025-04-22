<?php
// add_chart.php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
        $stmt = $db->prepare("INSERT INTO charts (user_id, chart_name, chart_type, data_url, importance) VALUES (:user_id, :chart_name, :chart_type, :data_url, :importance)");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':chart_name' => $chart_name,
            ':chart_type' => $chart_type,
            ':data_url' => $data_url,
            ':importance' => $importance
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
  <title>Add New Chart</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="form-container">
    <h1 class="form-title">Add New Chart</h1>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="add_chart.php">
      <div class="form-group">
        <label>Chart Name:</label>
        <input type="text" name="chart_name" placeholder="Enter chart name" required>
      </div>
      <div class="form-group">
        <label>Chart Type:</label>
        <select name="chart_type" required>
          <option value="bar">Bar Chart</option>
          <option value="line">Line Chart</option>
          <option value="pie">Pie Chart</option>
          <option value="ring">Ring Chart</option>
          <option value="stacked">Stacked Bar Chart</option>
          <option value="gauge">Radial Gauge Chart</option>
        </select>
      </div>
      <div class="form-group">
        <label>Data URL:</label>
        <input type="text" name="data_url" placeholder="Enter data source URL" required>
      </div>
      <div class="form-group">
        <label>Importance:</label>
        <select name="importance" required>
          <option value="1">1 (Small)</option>
          <option value="2">2 (Medium)</option>
          <option value="3">3 (Large)</option>
          <option value="4">4 (Extra Large)</option>
          <option value="5">5 (Max)</option>
        </select>
      </div>
      <button type="submit" class="btn">Add Chart</button>
    </form>
    <p class="switch"><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</body>
</html>

