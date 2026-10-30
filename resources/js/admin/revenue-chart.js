import Chart from "chart.js/auto";

// Warna disalin dari theme.css (hex, supaya tidak bergantung pada CSS variable).
const C = {
    line: "#0b604f",
    fill: "67, 229, 197",
    grid: "#e1e3d9",
    tick: "#5a6c5b",
    surface: "#fcfbf5",
};

const compact = new Intl.NumberFormat("id-ID", {
    notation: "compact",
    maximumFractionDigits: 1,
});
const full = new Intl.NumberFormat("id-ID");
const RANGE_LABEL = { "7d": "7 hari", "30d": "30 hari", "6m": "6 bulan" };

document.addEventListener("alpine:init", () => {
    window.Alpine.data("revenueChart", (data) => {
        let chart = null; // di luar state Alpine agar tidak dibungkus Proxy

        return {
            metric: "nilai",
            range: "7d",

            get series() {
                return data.ranges[this.range];
            },
            get values() {
                return this.series[this.metric];
            },
            get total() {
                return this.values.reduce((a, b) => a + b, 0);
            },
            get totalText() {
                return this.metric === "nilai"
                    ? "Rp " + compact.format(this.total)
                    : full.format(this.total) + " transaksi";
            },
            get totalLabel() {
                return "Total " + RANGE_LABEL[this.range];
            },
            get partialText() {
                return this.range === "6m"
                    ? "Bulan ini, belum selesai"
                    : "Hari ini, hingga " + data.asOf + " (belum selesai)";
            },
            get summary() {
                const what =
                    this.metric === "nilai"
                        ? "nilai transaksi"
                        : "jumlah transaksi";
                return `Grafik garis ${what} ${RANGE_LABEL[this.range]} terakhir. ${this.totalLabel}: ${this.totalText}.`;
            },

            init() {
                chart = new Chart(this.$refs.canvas, this.config());
                this.$watch("metric", () => this.refresh());
                this.$watch("range", () => this.refresh());
            },
            destroy() {
                chart?.destroy();
            },

            refresh() {
                chart.data.labels = [...this.series.labels];
                chart.data.datasets[0].data = [...this.values];
                chart.update();
            },

            config() {
                const isLast = (ctx) =>
                    ctx.dataIndex === ctx.chart.data.labels.length - 1;
                const reduce = window.matchMedia(
                    "(prefers-reduced-motion: reduce)",
                ).matches;

                return {
                    type: "line",
                    data: {
                        labels: [...this.series.labels],
                        datasets: [
                            {
                                data: [...this.values],
                                borderColor: C.line,
                                borderWidth: 2.5,
                                tension: 0.3,
                                fill: true,
                                backgroundColor: (c) => {
                                    const { ctx, chartArea } = c.chart;
                                    if (!chartArea) return null;
                                    const g = ctx.createLinearGradient(
                                        0,
                                        chartArea.top,
                                        0,
                                        chartArea.bottom,
                                    );
                                    g.addColorStop(0, `rgba(${C.fill}, .32)`);
                                    g.addColorStop(1, `rgba(${C.fill}, 0)`);
                                    return g;
                                },
                                pointRadius: (ctx) =>
                                    isLast(ctx)
                                        ? 5
                                        : ctx.chart.data.labels.length > 14
                                          ? 0
                                          : 3,
                                pointHoverRadius: 6,
                                pointBorderWidth: 2,
                                pointBorderColor: C.line,
                                pointBackgroundColor: (ctx) =>
                                    isLast(ctx) ? C.surface : C.line,
                                // segmen terakhir (periode berjalan) putus-putus
                                segment: {
                                    borderDash: (c) =>
                                        c.p1DataIndex ===
                                        c.chart.data.labels.length - 1
                                            ? [6, 5]
                                            : undefined,
                                },
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: reduce ? false : { duration: 600 },
                        interaction: { mode: "index", intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                displayColors: false,
                                callbacks: {
                                    label: (ctx) =>
                                        this.metric === "nilai"
                                            ? "Rp " + full.format(ctx.parsed.y)
                                            : full.format(ctx.parsed.y) +
                                              " transaksi",
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: {
                                    color: C.tick,
                                    maxRotation: 0,
                                    autoSkip: false,
                                    callback(value, index, ticks) {
                                        const last = ticks.length - 1;
                                        const step = last > 14 ? 5 : 1;
                                        return (last - index) % step === 0
                                            ? this.getLabelForValue(value)
                                            : "";
                                    },
                                },
                            },
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: C.grid },
                                ticks: {
                                    color: C.tick,
                                    maxTicksLimit: 5,
                                    precision: 0,
                                    callback: (v) =>
                                        this.metric === "nilai"
                                            ? compact.format(v)
                                            : v,
                                },
                            },
                        },
                    },
                };
            },
        };
    });
});
