var _ = require('lodash');
var path = require('path');
var ifCommon = require('if-common');

function HandlerCache() {
	var _handlers = {};

	return {
		setup: function (cb) {
			ifCommon.utils.getFilePathsRecursive({dir: __dirname + '/../handlers', extensionFilter: '.js'}, function (err, files) {
				if (err) return cb(err);
				_.each(files, function (file) {
					_handlers[path.basename(file, path.extname(file))] = require(file);
				});
				cb();
			});
		},
		getHandler: function (handlerName, cb) {
			var handler = _handlers[handlerName];
			if (!handler) return cb(new Error('Unknown request handler [' + handlerName + ']'));
			cb(null, handler);
		}
	};
};
module.exports = new HandlerCache();