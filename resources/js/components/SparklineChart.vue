<script setup lang="ts">
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  LineElement,
  PointElement,
  LinearScale,
  CategoryScale,
  Filler,
  ChartOptions,
} from 'chart.js';

ChartJS.register(LineElement, PointElement, LinearScale, CategoryScale, Filler);

const props = defineProps<{
  data: number[];
}>();

// Deteksi tren: naik → hijau, turun → merah
const isTrendUp = props.data[props.data.length - 1] >= props.data[0];

const chartData = {
  labels: props.data.map((_, index) => index + 1),
  datasets: [
    {
      data: props.data,
      borderColor: isTrendUp ? '#4ade80' : '#f87171', // green-400 or red-400
      backgroundColor: (context: any) => {
        const ctx = context.chart.ctx;
        const chartArea = context.chart.chartArea;
        if (!chartArea) return 'transparent';

        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
        if (isTrendUp) {
          gradient.addColorStop(0, 'rgba(74, 222, 128, 0.4)'); // green
          gradient.addColorStop(1, 'rgba(74, 222, 128, 0)');
        } else {
          gradient.addColorStop(0, 'rgba(248, 113, 113, 0.4)'); // red
          gradient.addColorStop(1, 'rgba(248, 113, 113, 0)');
        }

        return gradient;
      },
      fill: true,
      borderWidth: 2,
      tension: 0.4,
      pointRadius: 0,
    },
  ],
};

const chartOptions: ChartOptions<'line'> = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    x: { display: false },
    y: { display: false },
  },
  plugins: {
    legend: { display: false },
    tooltip: { enabled: false },
  },
  layout: {
    padding: { left: 10, right: 10, top: 10, bottom: 10 },
  },
};

console.log('Chart data:', props.data);
</script>


<template>
  <div class="h-10 w-16">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>
