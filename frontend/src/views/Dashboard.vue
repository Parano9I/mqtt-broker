<template>
  <div>
    <a-row :gutter="24" type="flex">
      <a-col :span="24" class="mb-24">
        <a-card
          :bordered="false"
          class="header-solid h-full"
          style="padding: 4px"
          :bodyStyle="{ padding: 0 }"
        >
          <a-select
            style="width: 120px"
            :default-value="dashboardState.getRange"
            @select="onSelectRange"
          >
            <a-select-option value="1">1 hour</a-select-option>
            <a-select-option value="24">1 day</a-select-option>
            <a-select-option value="72">3 days</a-select-option>
            <a-select-option value="168">1 week</a-select-option>
            <a-select-option value="720">1 month</a-select-option>
          </a-select>
          <chart-line
            v-if="isShowChart"
            height="300"
            :data="chartMetricData"
            :key="chartMetricData.datasets.length"
          />
          <a-table
            :columns="columns"
            :data-source="sensors"
            :row-selection="rowSelection"
            :pagination="true"
            row-key="id"
          >
            <template slot="status" slot-scope="status">
              <p :style="{ color: status === 'offline' ? 'red' : 'green' }">
                {{ status }}
              </p>
            </template>
          </a-table>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import { sensorsAPI } from "@/services/http/api/sensors";
import ChartLine from "@/components/Charts/ChartLine.vue";
import { useDashboardStore } from "@/store/dashboard";

export default {
  components: { ChartLine },
  data() {
    return {
      isShowChart: false,
      dashboardState: useDashboardStore(),
      chartMetricData: {
        labels: [],
        datasets: [],
      },
      rowSelection: {
        selectedRowKeys: [],
        onSelect: (record, selected, selectedRows) => {
          this.dashboardState.setSensors(selectedRows.map((row) => row.id));
          this.getSensorsMetrics();
        },
        onSelectAll: (selected, selectedRows, changeRows) => {
          this.dashboardState.setSensors(selectedRows.map((row) => row.id));
          this.getSensorsMetrics();
        },
      },
      sensors: [],
      columns: [
        {
          title: "Id",
          dataIndex: "id",
        },
        {
          title: "Name",
          dataIndex: "name",
        },
        {
          title: "Path",
          dataIndex: "path",
        },
        {
          title: "Status",
          dataIndex: "status",
          scopedSlots: { customRender: "status" },
        },
      ],
    };
  },
  mounted() {
    this.rowSelection.selectedRowKeys = this.dashboardState.getSensorsIds;

    this.getSensorsMetrics();
    this.getSensors();
  },
  methods: {
    onSelectRange(value) {
      this.dashboardState.setRange(value);
      this.getSensorsMetrics();
    },
    getSensorsMetrics() {
      this.chartMetricData = {
        labels: [],
        datasets: [],
      };

      this.dashboardState.getSensorsIds.forEach((sensorId) => {
        this.getSensorMetrics(sensorId);
      });
    },
    async getSensors() {
      const [error, response] = await sensorsAPI.getAll();

      if (error) {
        console.log(error);
      } else {
        this.sensors = response.data.map((sensor) => ({
          id: sensor.id,
          ...sensor.attributes,
        }));
      }
    },
    async getSensorMetrics(sensorId) {
      const params = {
        range: this.dashboardState.getRange,
      };

      const [error, response] = await sensorsAPI.getMetrics(sensorId, params);

      if (error) {
        console.log(error);
      } else {
        this.prepareDataset(response.data);
        this.isShowChart = true;
      }
    },
    prepareDataset(sensorMetrics) {
      this.chartMetricData.labels = [
        ...[
          ...this.chartMetricData.labels,
          // ...sensorMetrics.metrics.map((metric) => Date.parse(metric.datetime)),
          ...sensorMetrics.metrics.map((metric) => metric.datetime),
        ].sort((a, b) => {
          return new Date(a) - new Date(b);
        }),
      ];

      this.chartMetricData.datasets.push({
        label: sensorMetrics.sensor.attributes.name,
        borderColor: this.generateHexColor(),
        data: sensorMetrics.metrics.map((metric) => ({
          y: metric.value,
          // x: Date.parse(metric.datetime),
          x: metric.datetime,
        })),
        // xAxisID: "x",
      });
    },
    generateHexColor() {
      const color = Math.floor(Math.random() * 16777215).toString(16);

      return `#${color}`;
    },
  },
  computed: {
    selectedSensors() {
      return this.dashboardState.getSensorsIds;
    },
  },
  watch: {
    selectedSensors: {
      handler() {
        this.rowSelection.selectedRowKeys = this.dashboardState.getSensorsIds;
      },
    },
  },
};
</script>

<style lang="scss"></style>
