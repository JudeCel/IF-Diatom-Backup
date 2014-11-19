var _ = require('lodash');
var config = require('simpler-config');
var webFaultHelper = require('../helpers/webFaultHelper');
var handlerCache = require('./handlerCache');

function HandlerInterceptor() {
	function handle(handlerName) {
		return function (req, res, next) {
			handlerCache.getHandler(handlerName, function (err, handler) {
				if (err) return next(err);
				handler.validate(req, res, function (err) {
					if (err) return next(err);
					handler.run(req, res, next);
				});
			});
		};
	}

	return {
		handle: handle
	};
};
module.exports = new HandlerInterceptor();
