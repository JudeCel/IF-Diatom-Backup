"use strict";
var log4js = require('log4js');
var util = require('util');
var errLogger = log4js.getLogger('error');
var ifCommon = require('if-common');
var queueWriter = ifCommon.utils.queueWriter;
var _ = require('lodash');
var loggingHelper = ifCommon.utils.loggingHelper;

module.exports = function (err, req, res, next) {
	errLogger.error(req.headers, req.url, loggingHelper.scrubParams(req.params), loggingHelper.scrubParams(req.body),
		req.query, req.method, err);

	err = _.defaults(err || {}, {
		statusCode: 500,
		message: 'Internal Server Error',
		logLevel: 'ERROR'
	});

	// don't log 401's
	if (err.statusCode === 401
		|| (err.type && err.type === 'not_authorized')) {
		res.send(err.statusCode, err.message);
		return;
	}

	err.message = stringify(err.message);

	var innerError = err.innerError || err;

	var message = err.message;
	if (innerError.message)
		message += ' inner-error message: ' + stringify(innerError.message);

	var logMessage = loggingHelper.messageFromExpressJSReq({
		req: req,
		overrides: {
			priority: err.logLevel,
			exception: util.inspect(innerError.stack || innerError, {depth: 5}),
			message: message
		}
	});

	queueWriter.writeMessage(logMessage);
	res.send(err.statusCode, err.message);
};

function stringify(str) {
	if (_.isString(str)) return str;
	return JSON.stringify(str);
}
