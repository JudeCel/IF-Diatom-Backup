var _ = require('lodash');
var config = require('simpler-config');
//var sess = require('../handlers/session/validateSession');
//var apiValidateKey = require('../handlers/api/auth/apiValidateKey');
var webFaultHelper = require('../helpers/webFaultHelper');
var handlerCache = require('./handlerCache');

function HandlerInterceptor() {
//	function allowInactiveAccount(handlerName) {
//		return function (req, res, next) {
//			sess.registeredUser(req, res, function (err) {
//				if (!err)
//					return handle(handlerName)(req, res, next);
//
//				if (err && !err.data)
//					return next(err);
//				if (!err.data.sessionId || err.data.sessionExpired || err.data.userInactive)
//					return next(err);
//
//				handle(handlerName)(req, res, next);
//			});
//		};
//	}
//
//	function registeredUser(handlerName) {
//		return function (req, res, next) {
//			sess.registeredUser(req, res, function (err) {
//				if (err) return next(err);
//				handle(handlerName)(req, res, next);
//			});
//		};
//	}
//
//	function user(handlerName) {
//		return function (req, res, next) {
//			sess.all(req, res, function (err) {
//				if (err) return next(err);
//				handle(handlerName)(req, res, next);
//			});
//		};
//	}
//
//	function trainer(handlerName) {
//		return function (req, res, next) {
//			sess.trainer(req, res, function (err) {
//				if (err) return next(err);
//				handle(handlerName)(req, res, next);
//			});
//		};
//	}
//
//	function notTrainee(handlerName) {
//		return function (req, res, next) {
//			sess.notTrainee(req, res, function (err) {
//				if (err) return next(err);
//				handle(handlerName)(req, res, next);
//			});
//		};
//	}
//
//	function apiCall(handlerName) {
//		return function (req, res, next) {
//			apiValidateKey(req, res, function (err) {
//				if (err) return next(err);
//				handle(handlerName)(req, res, next);
//			});
//		};
//	}
//
//	function devQaOnly(handlerName) {
//		return function (req, res, next) {
//			if(!~["dev", "qa"].indexOf(config.name.toLowerCase()))
//
//				return next(webFaultHelper.getAuthFault());
//
//			handle(handlerName)(req, res, next);
//		};
//	}

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
		//allowInactiveAccount: allowInactiveAccount,
		//notTrainee: notTrainee,
		//trainer: trainer,
		handle: handle
		//apiCall: apiCall,
		//user: user,
		//registeredUser: registeredUser,
		//devQaOnly: devQaOnly
	};
};
module.exports = new HandlerInterceptor();
