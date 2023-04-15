const aedes = require('aedes')();
const broker = require('net').createServer(aedes.handle);
const userService = require('./src/services/userService');
const sensorService = require('./src/services/sensorService');
const topicService = require('./src/services/topicService');
const influx = require('./src/influxdb');

// aedes.authenticate = async function (client, id, token, callback) {
//   if (await sensorService.isAuth(id, token)) {
//     callback(null, true);
//   }
//
//   const error = new Error('Auth error');
//   error.returnCode = 4;
//   callback(error, false);
// }

aedes.authorizePublish = async function (client, packet, callback) {
  const sensorId = client.id;
  const topic = packet.topic;

  const topicId = await sensorService.getTopicId(sensorId);

  if (!topicId) {
    const message = 'Sensor ID not found.';
    console.log(message);

    const error = new Error(message);
    return callback(error, false);
  }
  const rightTopic = await topicService.getFullTopicPath(topicId);

  if (rightTopic !== topic) {
    const message = 'Topic mismatch.';
    console.log(message);

    const error = new Error(message);
    return callback(error, false);
  }

  return callback(null);
}

broker.listen(process.env.MQTT_PORT, function () {
  console.log(`server started and listening on port ${process.env.MQTT_PORT}`);
});

aedes.on('subscribe', function (subscriptions, client) {
  console.log('MQTT client \x1b[32m' + (client ? client.id : client) +
    '\x1b[0m subscribed to topics: ' + subscriptions.map(s => s.topic).join('\n'), 'from broker', aedes.id)
})

aedes.on('client', function (client) {
  console.log('Client Connected: \x1b[33m' + (client ? client.id : client) + '\x1b[0m', 'to broker', aedes.id)
});
aedes.on('clientDisconnect', function (client) {
  console.log('Client Disconnected: \x1b[31m' + (client ? client.id : client) + '\x1b[0m', 'to broker', aedes.id)
});

aedes.on('publish', async function (packet, client) {
  const payload = packet.payload.toString();

  if (client) {
    const data = JSON.parse(packet.payload.toString());
    const sensorId = client.id;
    const value = data.value;

    await sensorService.postData(value, sensorId);
  }
});