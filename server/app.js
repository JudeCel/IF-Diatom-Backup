var config = require('simpler-config').load(require('./config/config.json'));
var async = require('async');
var handlerCache = require('./helpers/handlerCache.js');

setup();

function setup() {
    async.series([
	    handlerCache.setup,
        function (pCb) {
            require('if-data').setup(config.mysql, pCb);
        }
    ], function (err) {
        if (err) throw err;
        require('./server').run();
    });
}