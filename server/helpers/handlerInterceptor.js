var _ = require('lodash');
var config = require('simpler-config');
var webFaultHelper = require('../helpers/webFaultHelper');
var handlerCache = require('./handlerCache');
var sess = require('../handlers/session/validateSession');

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

	function accountManager(handlerName) {
		return function (req, res, next) {
			sess.accountManager(req, res, function (err) {
				if (err) return next(err);
				handle(handlerName)(req, res, next);
			});
		};
	}

	return {
		handle: handle,
		accountManager: accountManager
	};
};
module.exports = new HandlerInterceptor();
