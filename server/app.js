var config = require('simpler-config').load(require('./config/config.json'));       // TBD use just require(), no load()
var async = require('async');
var log4js = require('log4js');

setup();

function setup() {
    async.series([
        //handlerCache.setup,
        function (pCb) {
            require('if-data').setup(config.mysql, pCb);
        },
        function (pCb) {
            //log4js.configure(config.logging);
            pCb();
        },
        function (pCb) {
            /*queueWriter.connect({
             queueUri: config.queue.uri,
             queueName: config.queue.workQueueName
             }, pCb); */
            pCb();
        },
        function (pCb) {
            //require('if-communication').setup(config, pCb);
            pCb();
        },
        function (pCb) {
            //mfFileStorage.setup(config, pCb);
            pCb();
        }

    ], function (err) {
        if (err) throw err;
        require('./server').run();
    });
}