const {InfluxDB} = require('@influxdata/influxdb-client');

const influx = new InfluxDB({
  url: `http://${process.env.INFLUXDB_HOST}:8086`,
  token: process.env.INFLUXDB_TOKEN
})

module.exports = influx;