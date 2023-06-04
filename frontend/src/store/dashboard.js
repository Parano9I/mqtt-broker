import { defineStore } from "pinia";

export const useDashboardStore = defineStore("dashboard", {
  state: () => ({
    range: 1,
    sensorsId: [],
  }),
  getters: {
    getSensorsIds(state) {
      return state.sensorsId;
    },
    getRange(state) {
      return state.range;
    },
  },
  actions: {
    setSensors(ids) {
      this.sensorsId = ids;
    },
    removeSensor(id) {
      this.sensorsId = this.sensorsId.filter((sensorId) => sensorId !== id);
    },
    resetSensors() {
      this.sensorsId = [];
    },
    setRange(value) {
      this.range = value;
    },
  },
});
