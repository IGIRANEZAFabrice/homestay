// ── Chart.js defaults ──
Chart.defaults.color = "#666C82";
Chart.defaults.font.family = "DM Sans, sans-serif";
Chart.defaults.font.size = 12;

const AMBER = "#E8A844",
  TEAL = "#2EB8A0",
  PURPLE = "#8B5CF6",
  ROSE = "#F43F5E";
const SURFACE3 = "#222738",
  SURFACE4 = "#2C3347",
  BORDER = "rgba(255,255,255,0.06)";

// ── Sparklines ──
function sparkline(id, data, color) {
  const ctx = document.getElementById(id)?.getContext("2d");
  if (!ctx) return;
  new Chart(ctx, {
    type: "line",
    data: {
      labels: data.map((_, i) => i),
      datasets: [
        {
          data,
          borderColor: color,
          borderWidth: 2,
          fill: true,
          backgroundColor: color + "18",
          tension: 0.4,
          pointRadius: 0,
        },
      ],
    },
    options: {
      plugins: {
        legend: { display: false },
        tooltip: { enabled: false },
      },
      scales: { x: { display: false }, y: { display: false } },
      animation: { duration: 1200 },
      responsive: true,
      maintainAspectRatio: false,
    },
  });
}
sparkline(
  "spark0",
  [480, 520, 610, 590, 740, 810, 780, 920, 880, 970, 1050, 1180],
  AMBER,
);
sparkline(
  "spark1",
  [1200, 1350, 1500, 1400, 1700, 1900, 1800, 2100, 2050, 2300, 2400, 2600],
  TEAL,
);
sparkline("spark2", [2, 3, 4, 3, 5, 4, 6, 5, 7, 6, 8, 7], PURPLE);
sparkline(
  "spark3",
  [200, 210, 190, 220, 230, 240, 215, 250, 245, 260, 255, 270],
  ROSE,
);

// ── Visitors line chart ──
const visitorsCtx = document.getElementById("visitorsChart")?.getContext("2d");
const visitorsData = {
  "7d": {
    labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    visitors: [310, 425, 380, 490, 540, 620, 480],
    views: [820, 1100, 980, 1280, 1420, 1650, 1240],
  },
  "30d": {
    labels: Array.from({ length: 30 }, (_, i) => `Apr ${i + 1}`),
    visitors: [
      180, 210, 190, 240, 280, 310, 290, 350, 330, 400, 420, 380, 450, 430, 470,
      500, 520, 490, 540, 560, 530, 580, 610, 590, 640, 620, 660, 700, 680, 720,
    ],
    views: [
      480, 560, 510, 640, 750, 820, 770, 940, 880, 1080, 1120, 1010, 1200, 1150,
      1260, 1340, 1390, 1310, 1440, 1500, 1420, 1560, 1640, 1580, 1720, 1660,
      1770, 1880, 1820, 1950,
    ],
  },
  "12m": {
    labels: [
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
      "Jan",
      "Feb",
      "Mar",
      "Apr",
    ],
    visitors: [
      3200, 3800, 4500, 5200, 4800, 4200, 3600, 3100, 4100, 4600, 5100, 5800,
    ],
    views: [
      8400, 9800, 11800, 13600, 12500, 11000, 9400, 8100, 10700, 12000, 13300,
      15200,
    ],
  },
};

let visChart;
function buildVisitorsChart(range) {
  if (!visitorsCtx) return;
  const d = visitorsData[range];
  if (visChart) visChart.destroy();
  visChart = new Chart(visitorsCtx, {
    type: "line",
    data: {
      labels: d.labels,
      datasets: [
        {
          label: "Visitors",
          data: d.visitors,
          borderColor: AMBER,
          borderWidth: 2.5,
          backgroundColor: "rgba(232,168,68,0.08)",
          fill: true,
          tension: 0.4,
          pointRadius: 0,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: AMBER,
        },
        {
          label: "Page Views",
          data: d.views,
          borderColor: TEAL,
          borderWidth: 2,
          backgroundColor: "rgba(46,184,160,0.05)",
          fill: true,
          tension: 0.4,
          pointRadius: 0,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: TEAL,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: "index", intersect: false },
      plugins: {
        legend: {
          display: true,
          position: "top",
          labels: {
            boxWidth: 10,
            boxHeight: 2,
            usePointStyle: true,
            pointStyle: "line",
            padding: 16,
            color: "#A8AEBF",
          },
        },
        tooltip: {
          backgroundColor: "#1A1E2A",
          borderColor: "rgba(255,255,255,0.08)",
          borderWidth: 1,
          titleColor: "#F0F2F8",
          bodyColor: "#A8AEBF",
          padding: 12,
          cornerRadius: 10,
        },
      },
      scales: {
        x: {
          grid: { color: "rgba(255,255,255,0.04)" },
          ticks: { maxRotation: 0, maxTicksLimit: 6 },
        },
        y: {
          grid: { color: "rgba(255,255,255,0.04)" },
          beginAtZero: true,
          ticks: { callback: (v) => (v >= 1000 ? v / 1000 + "K" : v) },
        },
      },
    },
  });
}
buildVisitorsChart("30d");

// Tab listeners
document.querySelectorAll('[data-chart="visitors"]').forEach((btn) => {
  btn.addEventListener("click", function () {
    document
      .querySelectorAll('[data-chart="visitors"]')
      .forEach((b) => b.classList.remove("active"));
    this.classList.add("active");
    buildVisitorsChart(this.dataset.range);
  });
});

// ── Monthly bar chart ──
const monthlyCtx = document.getElementById("monthlyChart")?.getContext("2d");
if (monthlyCtx) {
  new Chart(monthlyCtx, {
    type: "bar",
    data: {
      labels: [
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
        "Jan",
        "Feb",
        "Mar",
        "Apr",
      ],
      datasets: [
        {
          label: "Visitors",
          data: [
            3200, 3800, 4500, 5200, 4800, 4200, 3600, 3100, 4100, 4600, 5100,
            5800,
          ],
          backgroundColor: (ctx) => {
            const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 240);
            g.addColorStop(0, "rgba(232,168,68,0.85)");
            g.addColorStop(1, "rgba(232,168,68,0.15)");
            return g;
          },
          borderRadius: 8,
          borderSkipped: false,
          maxBarThickness: 32,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: "#1A1E2A",
          borderColor: "rgba(255,255,255,0.08)",
          borderWidth: 1,
          titleColor: "#F0F2F8",
          bodyColor: "#A8AEBF",
          padding: 12,
          cornerRadius: 10,
          callbacks: {
            label: (ctx) => ` ${ctx.parsed.y.toLocaleString()} visitors`,
          },
        },
      },
      scales: {
        x: { grid: { display: false }, ticks: {} },
        y: {
          grid: { color: "rgba(255,255,255,0.04)" },
          beginAtZero: true,
          ticks: { callback: (v) => (v >= 1000 ? v / 1000 + "K" : v) },
        },
      },
    },
  });
}

// ── Source doughnut ──
const sourceCtx = document.getElementById("sourceChart")?.getContext("2d");
if (sourceCtx) {
  new Chart(sourceCtx, {
    type: "doughnut",
    data: {
      labels: ["Google", "Direct", "Social", "Referral", "Other"],
      datasets: [
        {
          data: [5204, 3842, 2105, 1047, 649],
          backgroundColor: [AMBER, TEAL, PURPLE, ROSE, "#475569"],
          borderWidth: 0,
          hoverOffset: 8,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "72%",
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: "#1A1E2A",
          borderColor: "rgba(255,255,255,0.08)",
          borderWidth: 1,
          cornerRadius: 10,
          padding: 10,
        },
      },
    },
  });
}

// ── Device doughnut ──
const deviceCtx = document.getElementById("deviceChart")?.getContext("2d");
if (deviceCtx) {
  new Chart(deviceCtx, {
    type: "doughnut",
    data: {
      labels: ["Mobile", "Desktop", "Tablet"],
      datasets: [
        {
          data: [58, 34, 8],
          backgroundColor: [AMBER, TEAL, PURPLE],
          borderWidth: 0,
          hoverOffset: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "68%",
      plugins: {
        legend: {
          display: true,
          position: "bottom",
          labels: {
            boxWidth: 10,
            boxHeight: 10,
            usePointStyle: true,
            pointStyle: "rectRounded",
            padding: 14,
            color: "#A8AEBF",
          },
        },
        tooltip: {
          backgroundColor: "#1A1E2A",
          borderWidth: 1,
          borderColor: "rgba(255,255,255,0.08)",
          cornerRadius: 10,
          padding: 10,
          callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.raw}%` },
        },
      },
    },
  });
}

// ── WhatsApp conversions bar ──
const waCtx = document.getElementById("waChart")?.getContext("2d");
if (waCtx) {
  new Chart(waCtx, {
    type: "bar",
    data: {
      labels: ["Home", "Rooms", "Activities", "About", "Car", "Blog"],
      datasets: [
        {
          data: [284, 197, 143, 88, 64, 42],
          backgroundColor: "rgba(37,211,102,0.7)",
          borderRadius: 5,
          maxBarThickness: 24,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: "y",
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: "#1A1E2A",
          borderWidth: 1,
          borderColor: "rgba(255,255,255,0.08)",
          cornerRadius: 8,
          padding: 8,
        },
      },
      scales: {
        x: {
          grid: { color: "rgba(255,255,255,0.04)" },
          ticks: {},
          beginAtZero: true,
        },
        y: { grid: { display: false } },
      },
    },
  });
}

// ── Animated KPI counters ──
function animateCount(el, target, suffix) {
  let start = 0,
    duration = 1600,
    startTime = null;
  function step(timestamp) {
    if (!startTime) startTime = timestamp;
    const progress = Math.min((timestamp - startTime) / duration, 1);
    const ease = 1 - Math.pow(1 - progress, 3);
    const value = Math.floor(ease * target);
    el.textContent = value.toLocaleString() + (suffix || "");
    if (progress < 1) requestAnimationFrame(step);
  }
  requestAnimationFrame(step);
}

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.dataset.target);
        const suffix = el.dataset.suffix || "";
        const textTarget = el.dataset.targetText;
        if (textTarget) {
          setTimeout(() => {
            el.textContent = textTarget;
          }, 800);
        } else if (!isNaN(target)) animateCount(el, target, suffix);
        observer.unobserve(el);
      }
    });
  },
  { threshold: 0.3 },
);

document
  .querySelectorAll("[data-target], [data-target-text]")
  .forEach((el) => observer.observe(el));

// ── Sidebar toggle ──
const sidebar = document.getElementById("dashSidebar");
const layout = document.getElementById("dashLayout");
const overlay = document.getElementById("sidebarOverlay");
const toggle = document.getElementById("sidebarToggle");
const close = document.getElementById("sidebarClose");

toggle?.addEventListener("click", () => {
  if (window.innerWidth <= 900) {
    sidebar.classList.toggle("mobile-open");
    overlay.classList.toggle("visible");
  } else {
    sidebar.classList.toggle("collapsed");
    layout.classList.toggle("sidebar-collapsed");
  }
});
overlay?.addEventListener("click", () => {
  sidebar.classList.remove("mobile-open");
  overlay.classList.remove("visible");
});
close?.addEventListener("click", () => {
  sidebar.classList.remove("mobile-open");
  overlay.classList.remove("visible");
});

// ── Avatar dropdown ──
const avatarBtn = document.getElementById("avatarBtn");
avatarBtn?.addEventListener("click", (e) => {
  e.stopPropagation();
  avatarBtn.classList.toggle("open");
});
document.addEventListener("click", () => avatarBtn?.classList.remove("open"));

// ── Live visitor counter ──
function randomNear(n, spread) {
  return n + Math.floor((Math.random() - 0.5) * 2 * spread);
}
const liveEl = document.getElementById("liveCount");
setInterval(() => {
  if (liveEl) liveEl.textContent = randomNear(4, 3);
}, 7000);

// ── Monthly tab (visitors/views) ──
const monthlyTabs =
  document.querySelectorAll("#monthlyChart").length > 0
    ? document.querySelectorAll(".panel__header .tab-pill:not([data-chart])")
    : [];
// simple active toggle only (chart rebuild can be added if needed)
monthlyTabs.forEach((btn) =>
  btn.addEventListener("click", function () {
    this.closest(".tab-pills")
      ?.querySelectorAll(".tab-pill")
      .forEach((b) => b.classList.remove("active"));
    this.classList.add("active");
  }),
);
