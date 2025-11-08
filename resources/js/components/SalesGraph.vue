<template>
  <div class="bg-white shadow rounded p-4 h-80">
    <h3 class="text-lg font-semibold text-purple-700 mb-3">Sales (Last 7 Days)</h3>
    <line-chart :chart-data="formattedChartData" :chart-options="chartOptions" />
  </div>
</template>

<script>
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

export default {
  name: 'SalesGraph',
  components: { LineChart: Line },
  props: {
    salesData: {
      type: Array,
      default: () => [],
      // Expecting format: [{ date: '2025-11-01', total: 1000 }, ...]
    },
  },
  computed: {
    formattedChartData() {
      // Extract labels (days) and totals
      const labels = this.salesData.map(s => {
        const date = new Date(s.date);
        return date.toLocaleDateString('en-US', { weekday: 'short' }); // 'Mon', 'Tue'...
      });

      const data = this.salesData.map(s => s.total);

      return {
        labels,
        datasets: [
          {
            label: 'Sales',
            data,
            borderColor: '#7c3aed', // Tailwind purple-700
            backgroundColor: 'rgba(124, 58, 237, 0.2)',
            fill: true,
            tension: 0.4,
          },
        ],
      };
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: { color: '#6b21a8' }, // purple-800
          },
        },
        scales: {
          x: { ticks: { color: '#4b5563' } },
          y: { ticks: { color: '#4b5563' } },
        },
      };
    },
  },
};
</script>

<style scoped>
/* Ensure chart fills container */
div {
  height: 100%;
}
</style>
