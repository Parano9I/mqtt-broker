import aedes from 'aedes';
import net from 'net';
import ws from 'websocket-stream';
import * as dotenv from 'dotenv';

dotenv.config();

const server = net.createServer(aedes.handle)

server.listen(process.env.MQTT_PORT, function () {
    console.log('server started and listening on port ');
});