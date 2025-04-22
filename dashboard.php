<?php
// dashboard.php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch charts for this user (each row now should include an "importance" column)
$stmt = $db->prepare("SELECT * FROM charts WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$charts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$numCharts = count($charts);

// Determine grid-template-columns based on chart count:
if ($numCharts == 1) {
   $gridCols = "1fr";
} elseif ($numCharts <= 3) {
   $gridCols = "repeat($numCharts, 1fr)";
} elseif ($numCharts == 4) {
   $gridCols = "repeat(2, 1fr)";
} elseif ($numCharts == 5) {
   $gridCols = "repeat(3, 1fr)";
} elseif ($numCharts == 6) {
   $gridCols = "repeat(3, 1fr)";
} else {
   $gridCols = "repeat(auto-fit, minmax(250px, 1fr))";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>jocarsa | blue</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
  <h1><img src="blue.png">jocarsa | blue</h1>
  <p><a href="logout.php">Logout</a></p>

  <!-- 3D Scene Container -->
  <div class="scene">
    <div class="grid-container" style="grid-template-columns: <?php echo $gridCols; ?>;">
      <?php foreach ($charts as $chart): ?>
        <?php
          // Use importance if set (default is 1)
          $importance = isset($chart['importance']) ? (int)$chart['importance'] : 1;
          if ($importance < 1) { $importance = 1; }
          if ($importance > 5) { $importance = 5; }
          // If there's only one chart, span the whole grid
          if ($numCharts == 1) {
            $gridStyle = "grid-column: 1 / -1; grid-row: 1 / -1;";
          } else {
            $gridStyle = "grid-column: span $importance; grid-row: span $importance;";
          }
        ?>
        <div class="chart-container"
             style="<?php echo $gridStyle; ?>"
             id="chart-<?php echo htmlspecialchars($chart['id']); ?>"
             data-chart-type="<?php echo htmlspecialchars($chart['chart_type']); ?>"
             data-data-url="<?php echo htmlspecialchars($chart['data_url']); ?>">
          <h3><?php echo htmlspecialchars($chart['chart_name']); ?></h3>
          <!-- Chart actions overlay (shown on hover) -->
          <div class="chart-actions">
            <a href="edit_chart.php?id=<?php echo $chart['id']; ?>">Edit</a>
            <a href="delete_chart.php?id=<?php echo $chart['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
          </div>
          <!-- The SVG chart will be appended here by JavaScript -->
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Floating Add Button -->
  <a href="add_chart.php" class="floating-add-btn">+</a>

  <!-- Include Chart Library and Custom JS -->
  <script src="js/charts.js"></script>
  <script src="js/codigo.js"></script>
</body>
</html>

