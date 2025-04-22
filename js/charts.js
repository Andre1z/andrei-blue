
(function (global) {
  /**
   * Helper to create an SVG element with a given width/height.
   */
  function createSVG(width, height) {
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute("width", width);
    svg.setAttribute("height", height);
    svg.setAttribute("viewBox", `0 0 ${width} ${height}`);
    svg.style.overflow = "visible";
    return svg;
  }

  /**
   * Helper to add a tooltip (via a <title> element) to an SVG element.
   */
  function addTooltip(element, text) {
    const title = document.createElementNS("http://www.w3.org/2000/svg", "title");
    title.textContent = text;
    element.appendChild(title);
  }

  /**
   * Helper to extract numeric value and label from a data item.
   * If the item is an object with a "value" property, returns that value and its label.
   * Otherwise, returns the item as the value with an empty label.
   */
  function extractData(item) {
    if (typeof item === "object" && item !== null && "value" in item) {
      return { value: item.value, label: item.label || "" };
    }
    return { value: item, label: "" };
  }

  /*************************************************************
   * 1) Bar Chart
   *************************************************************/
  function createBarChart(data, options = {}) {
    const width = options.width || 300;
    const height = options.height || 200;
    const barColor = options.barColor || "#fff";
    const hoverColor = options.hoverColor || "#f0f";
    const barSpacing = options.barSpacing || 5;

    const svg = createSVG(width, height);
    // Build an array of numeric values
    const values = data.map(item => extractData(item).value);
    const maxValue = Math.max(...values);
    const barWidth = (width - (data.length - 1) * barSpacing) / data.length;

    data.forEach((item, i) => {
      const { value, label } = extractData(item);
      const barHeight = (value / maxValue) * (height - 20);
      const x = i * (barWidth + barSpacing);
      const y = height - barHeight;

      const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
      rect.setAttribute("x", x);
      rect.setAttribute("y", y);
      rect.setAttribute("width", barWidth);
      rect.setAttribute("height", barHeight);
      rect.setAttribute("fill", barColor);
      rect.style.transition = "0.2s";

      addTooltip(rect, label ? `${label}: ${value}` : `Value: ${value}`);

      rect.addEventListener("mouseover", () => {
        rect.setAttribute("fill", hoverColor);
      });
      rect.addEventListener("mouseout", () => {
        rect.setAttribute("fill", barColor);
      });

      svg.appendChild(rect);
    });

    return svg;
  }

  /*************************************************************
   * 2) Line Chart
   *************************************************************/
  function createLineChart(data, options = {}) {
    const width = options.width || 300;
    const height = options.height || 200;
    const lineColor = options.lineColor || "#fff";
    const pointColor = options.pointColor || "#fff";
    const hoverColor = options.hoverColor || "#f0f";

    const svg = createSVG(width, height);
    const extractedData = data.map(item => extractData(item));
    const values = extractedData.map(item => item.value);
    const maxValue = Math.max(...values);
    const minValue = Math.min(...values);

    const margin = 20;
    const chartHeight = height - margin * 2;
    const chartWidth = width - margin * 2;

    const points = extractedData.map((item, i) => {
      const x = margin + (i / (data.length - 1)) * chartWidth;
      const y = height - margin - ((item.value - minValue) / (maxValue - minValue)) * chartHeight;
      return { x, y, val: item.value, label: item.label };
    });

    // Create line path
    const pathD = points
      .map((p, i) => (i === 0 ? `M${p.x},${p.y}` : `L${p.x},${p.y}`))
      .join(" ");

    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    path.setAttribute("d", pathD);
    path.setAttribute("stroke", lineColor);
    path.setAttribute("fill", "none");
    path.setAttribute("stroke-width", 2);
    svg.appendChild(path);

    // Create circles at each data point
    points.forEach((p) => {
      const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
      circle.setAttribute("cx", p.x);
      circle.setAttribute("cy", p.y);
      circle.setAttribute("r", 4);
      circle.setAttribute("fill", pointColor);
      circle.style.transition = "0.2s";

      addTooltip(circle, p.label ? `${p.label}: ${p.val}` : `Value: ${p.val}`);

      circle.addEventListener("mouseover", () => {
        circle.setAttribute("fill", hoverColor);
      });
      circle.addEventListener("mouseout", () => {
        circle.setAttribute("fill", pointColor);
      });

      svg.appendChild(circle);
    });

    return svg;
  }

  /*************************************************************
   * 3) Pie Chart
   *************************************************************/
  function createArcPath(cx, cy, radius, startAngle, endAngle, innerRadius = 0) {
    const start = (Math.PI / 180) * startAngle;
    const end = (Math.PI / 180) * endAngle;
    const largeArcFlag = endAngle - startAngle <= 180 ? 0 : 1;

    const x1 = cx + radius * Math.cos(start);
    const y1 = cy + radius * Math.sin(start);
    const x2 = cx + radius * Math.cos(end);
    const y2 = cy + radius * Math.sin(end);

    let d = `M ${x1} ${y1} A ${radius} ${radius} 0 ${largeArcFlag} 1 ${x2} ${y2}`;

    if (innerRadius > 0) {
      const x3 = cx + innerRadius * Math.cos(end);
      const y3 = cy + innerRadius * Math.sin(end);
      const x4 = cx + innerRadius * Math.cos(start);
      const y4 = cy + innerRadius * Math.sin(start);
      d += ` L ${x3} ${y3} A ${innerRadius} ${innerRadius} 0 ${largeArcFlag} 0 ${x4} ${y4} Z`;
    } else {
      d += ` L ${cx} ${cy} Z`;
    }

    return d;
  }

  function createPieChart(data, options = {}) {
    const width = options.width || 200;
    const height = options.height || 200;
    const colors = options.colors || ["#4FADCF", "#F8B400", "#F85C50", "#9CFF9C", "#FFD3E8"];

    const svg = createSVG(width, height);
    const cx = width / 2;
    const cy = height / 2;
    const radius = Math.min(width, height) / 2;
    const extractedData = data.map(item => extractData(item));
    const total = extractedData.reduce((acc, item) => acc + item.value, 0);

    let startAngle = 0;

    extractedData.forEach((item, i) => {
      const sliceAngle = (item.value / total) * 360;
      const endAngle = startAngle + sliceAngle;

      const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
      path.setAttribute("d", createArcPath(cx, cy, radius, startAngle, endAngle));
      path.setAttribute("fill", colors[i % colors.length]);
      path.style.transition = "0.2s";

      addTooltip(path, item.label ? `${item.label}: ${item.value}` : `Value: ${item.value}`);

      path.addEventListener("mouseover", () => {
        path.style.opacity = 0.8;
      });
      path.addEventListener("mouseout", () => {
        path.style.opacity = 1;
      });

      svg.appendChild(path);
      startAngle += sliceAngle;
    });

    return svg;
  }

  /*************************************************************
   * 4) Ring (Donut) Chart
   *************************************************************/
  function createRingChart(data, options = {}) {
    const width = options.width || 200;
    const height = options.height || 200;
    const innerRadius = options.innerRadius || 40;
    const colors = options.colors || ["#4FADCF", "#F8B400", "#F85C50", "#9CFF9C", "#FFD3E8"];

    const svg = createSVG(width, height);
    const cx = width / 2;
    const cy = height / 2;
    const outerRadius = Math.min(width, height) / 2;
    const extractedData = data.map(item => extractData(item));
    const total = extractedData.reduce((acc, item) => acc + item.value, 0);

    let startAngle = 0;

    extractedData.forEach((item, i) => {
      const sliceAngle = (item.value / total) * 360;
      const endAngle = startAngle + sliceAngle;

      const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
      path.setAttribute(
        "d",
        createArcPath(cx, cy, outerRadius, startAngle, endAngle, innerRadius)
      );
      path.setAttribute("fill", colors[i % colors.length]);
      path.style.transition = "0.2s";

      addTooltip(path, item.label ? `${item.label}: ${item.value}` : `Value: ${item.value}`);

      path.addEventListener("mouseover", () => {
        path.style.opacity = 0.8;
      });
      path.addEventListener("mouseout", () => {
        path.style.opacity = 1;
      });

      svg.appendChild(path);
      startAngle += sliceAngle;
    });

    return svg;
  }

  /*************************************************************
   * 5) Stacked Bar Chart (Example)
   *************************************************************/
  function createStackedBarChart(dataSets, options = {}) {
    // dataSets: e.g. [ [ {value:10, label:"A"}, ... ], [ ... ] ]
    const width = options.width || 300;
    const height = options.height || 200;
    const colors = options.colors || ["#fff", "#F85C50", "#FFD700", "#00FFB3"];
    const svg = createSVG(width, height);

    // Assume all dataSets have the same length
    const numBars = dataSets[0].length;
    const barSpacing = options.barSpacing || 5;
    const barWidth = (width - (numBars - 1) * barSpacing) / numBars;

    // Calculate total per bar
    const totals = Array.from({ length: numBars }, (_, i) =>
      dataSets.reduce((acc, ds) => {
        const { value } = extractData(ds[i]);
        return acc + value;
      }, 0)
    );
    const maxValue = Math.max(...totals);

    // For each bar, stack the segments
    for (let i = 0; i < numBars; i++) {
      let yOffset = 0;
      const x = i * (barWidth + barSpacing);

      dataSets.forEach((layer, layerIndex) => {
        const { value, label } = extractData(layer[i]);
        const segmentHeight = (value / maxValue) * (height - 20);
        const y = height - segmentHeight - yOffset;

        const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
        rect.setAttribute("x", x);
        rect.setAttribute("y", y);
        rect.setAttribute("width", barWidth);
        rect.setAttribute("height", segmentHeight);
        rect.setAttribute("fill", colors[layerIndex % colors.length]);
        rect.style.transition = "0.2s";

        addTooltip(rect, label ? `${label}: ${value}` : `Value: ${value}`);

        rect.addEventListener("mouseover", () => {
          rect.style.opacity = 0.8;
        });
        rect.addEventListener("mouseout", () => {
          rect.style.opacity = 1;
        });

        svg.appendChild(rect);
        yOffset += segmentHeight;
      });
    }

    return svg;
  }

  /*************************************************************
   * 6) Radial Gauge Chart (Example)
   *************************************************************/
  function createRadialGaugeChart(value, maxValue = 100, options = {}) {
    const width = options.width || 200;
    const height = options.height || 200;
    const color = options.color || "#00FFB3";
    const bgColor = options.bgColor || "rgba(255, 255, 255, 0.2)";
    const svg = createSVG(width, height);

    const cx = width / 2;
    const cy = height / 2;
    const radius = Math.min(width, height) / 2 - 10;
    const startAngle = 135;
    const endAngle = 405;

    // Process gauge value in case it is an object.
    const gaugeData = extractData(value);
    value = gaugeData.value;

    // Draw background arc
    const bgPath = document.createElementNS("http://www.w3.org/2000/svg", "path");
    bgPath.setAttribute(
      "d",
      createArcPath(cx, cy, radius, startAngle, endAngle, radius - 15)
    );
    bgPath.setAttribute("fill", bgColor);
    svg.appendChild(bgPath);

    // Value arc
    const angleRange = endAngle - startAngle;
    const filledAngle = startAngle + (value / maxValue) * angleRange;

    const valuePath = document.createElementNS("http://www.w3.org/2000/svg", "path");
    valuePath.setAttribute(
      "d",
      createArcPath(cx, cy, radius, startAngle, filledAngle, radius - 15)
    );
    valuePath.setAttribute("fill", color);
    svg.appendChild(valuePath);

    addTooltip(valuePath, gaugeData.label ? `${gaugeData.label}: ${gaugeData.value}` : `Value: ${gaugeData.value}`);

    // Add text showing value
    const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
    text.setAttribute("x", cx);
    text.setAttribute("y", cy + 5);
    text.setAttribute("text-anchor", "middle");
    text.setAttribute("fill", "#fff");
    text.setAttribute("font-size", "20");
    text.setAttribute("font-family", "Arial, sans-serif");
    text.textContent = `${value}/${maxValue}`;
    svg.appendChild(text);

    return svg;
  }

  /*************************************************************
   * Expose our chart functions
   *************************************************************/
  global.myCharts = {
    createBarChart,
    createLineChart,
    createPieChart,
    createRingChart,
    createStackedBarChart,
    createRadialGaugeChart,
  };
})(window);

