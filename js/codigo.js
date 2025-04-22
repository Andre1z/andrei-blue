/********************************************************
 * Dynamic Chart Rendering for CRUD Charts
 ********************************************************/
document.addEventListener("DOMContentLoaded", function() {
  const chartContainers = document.querySelectorAll(".chart-container");
  
  chartContainers.forEach((container) => {
    const chartType = container.getAttribute("data-chart-type");
    const dataUrl = container.getAttribute("data-data-url");
    
    // If a data URL is provided, fetch the data. Otherwise, use dummy data.
    if (dataUrl) {
      fetch(dataUrl)
        .then(response => response.json())
        .then(fetchedData => {
          // If the fetched JSON is an object with a "data" key, use that.
          let chartData = fetchedData;
          if (fetchedData && fetchedData.data && Array.isArray(fetchedData.data)) {
            chartData = fetchedData.data;
          }
          renderChart(container, chartType, chartData);
        })
        .catch(err => {
          console.error("Error fetching chart data from " + dataUrl + ": ", err);
          // Fallback to dummy data if fetching fails
          renderChart(container, chartType, getDummyData(chartType));
        });
    } else {
      renderChart(container, chartType, getDummyData(chartType));
    }
  });
  
  // Render the chart based on its type and data.
  function renderChart(container, chartType, data) {
    let svg;
    switch(chartType) {
      case "bar":
        svg = myCharts.createBarChart(data, {
          width: 250,
          height: 150,
          barColor: "#ffffff",
          hoverColor: "#FFD700"
        });
        break;
      case "line":
        svg = myCharts.createLineChart(data, {
          width: 250,
          height: 150,
          lineColor: "#00FFB3",
          pointColor: "#ffffff",
          hoverColor: "#FFD700"
        });
        break;
      case "pie":
        svg = myCharts.createPieChart(data, {
          width: 200,
          height: 200,
          colors: ["#00bfff", "#f8b400", "#f85c50", "#9cff9c"]
        });
        break;
      case "ring":
        svg = myCharts.createRingChart(data, {
          width: 200,
          height: 200,
          innerRadius: 50,
          colors: ["#0bd", "#f8b400", "#f85c50", "#9cff9c"]
        });
        break;
      case "stacked":
        svg = myCharts.createStackedBarChart(data, {
          width: 250,
          height: 150,
          colors: ["#f85c50", "#ffd700", "#00ffb3"],
          barSpacing: 5
        });
        break;
      case "gauge":
        svg = myCharts.createRadialGaugeChart(data, 100, {
          width: 200,
          height: 200,
          color: "#ffd700",
          bgColor: "rgba(255,255,255,0.2)"
        });
        break;
      default:
        console.error("Unsupported chart type: " + chartType);
        return;
    }
    if (svg) {
      container.appendChild(svg);
    }
  }
  
  // Provide dummy data with labels for testing.
  function getDummyData(chartType) {
    switch(chartType) {
      case "bar":
      case "line":
        return [
          { label: "A", value: 10 },
          { label: "B", value: 20 },
          { label: "C", value: 30 },
          { label: "D", value: 40 },
          { label: "E", value: 50 }
        ];
      case "pie":
      case "ring":
        return [
          { label: "A", value: 10 },
          { label: "B", value: 20 },
          { label: "C", value: 30 },
          { label: "D", value: 40 }
        ];
      case "stacked":
        return [
          [
            { label: "A", value: 5 },
            { label: "B", value: 15 },
            { label: "C", value: 25 }
          ],
          [
            { label: "A", value: 10 },
            { label: "B", value: 20 },
            { label: "C", value: 30 }
          ],
          [
            { label: "A", value: 8 },
            { label: "B", value: 18 },
            { label: "C", value: 28 }
          ]
        ];
      case "gauge":
        return { label: "Gauge", value: Math.floor(Math.random() * 100) };
      default:
        return [];
    }
  }
});

