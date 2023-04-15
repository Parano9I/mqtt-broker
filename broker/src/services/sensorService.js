const {Point} = require('@influxdata/influxdb-client');
const influx = require('../influxdb');
const db = require('../db');

const sensorTable = 'sensors';

const isAuth = async (id, token) => {
  const sql = `SELECT *
               FROM ${sensorTable}
               WHERE uuid = '${id}'
                 AND secret = '${token}'`;
  const result = await db.query(sql);

  return result.length > 0;
};

const getTopicId = async (sensorId) => {
  const sql = `SELECT topic_id
               FROM ${sensorTable}
               WHERE uuid = '${sensorId}'`;

  const result = await db.query(sql);

  return result.length ? result[0].topic_id : null;
}

const postData = async (value, sensorId) => {
  const writeApi = await influx.getWriteApi(process.env.INFLUXDB_ORG, process.env.INFLUXDB_BUCKET);

// Create a new point with some data
  const point = new Point('sensors')
    .tag('sensor_id', sensorId)
    .floatField('value', value)
    .timestamp(new Date());

  await writeApi.writePoint(point);

  await writeApi.flush()
    .then(() => {
      console.log('Data written to InfluxDB successfully');
    })
    .catch((error) => {
      console.error('Failed to write data to InfluxDB', error);
    });
}

module.exports = {
  isAuth,
  getTopicId,
  postData
};