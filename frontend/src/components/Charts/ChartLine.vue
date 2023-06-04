<template>
  <div>
    <canvas ref="chart" :style="{ height: height + 'px' }"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

export default {
  props: ["data", "height"],
  data() {
    return {
      chart: null,
    };
  },
  mounted() {
    let ctx = this.$refs.chart.getContext("2d");

    this.chart = new Chart(ctx, {
      type: "line",
      data: this.data,
      options: {
        scales: {
          x: {
            ticks: {
              maxTicksLimit: 4,
            },
          },
        },
      },
    });
  },
  computed: {
    datasets() {
      return this.data.datasets;
    },
  },
  watch: {
    datasets: {
      handler(newChartData) {
        // this.chart.data = newChartData;
        this.chart.update();
      },
      deep: true,
    },
  },
  beforeDestroy: function () {
    this.chart.destroy();
  },
};
</script>

<style lang="scss" scoped></style>
