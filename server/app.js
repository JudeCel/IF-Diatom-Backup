var config = require('simpler-config').load(require('./config/config.json'));
var async = require('async');

setup();

function setup() {
    async.series([
        function (pCb) {
            require('if-data').setup(config.mysql, pCb);
        }
    ], function (err) {
        if (err) throw err;
        require('./server').run();
    });
}