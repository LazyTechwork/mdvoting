var request = require('request'),
    io = require('socket.io')(6001),
    Redis = require('ioredis'),
    redis = new Redis();

console.log("Server started");

redis.on('pmessage', function (pattern, channel, message) {
    message = JSON.parse(message);
    io
        .to(channel + ':' + message.event)
        .emit(channel + ':' + message.event, message.data.message);
    console.log(pattern, channel, message);
});
