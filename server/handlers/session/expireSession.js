"use strict";
var _ = require('lodash');
var ifData = require('if-data'), db = ifData.db;
var expireSession = require('if-auth').expireSession;

module.exports.validate = function (req, res, next) {
	if (req.headers['x-if-sess'] == undefined)
		return next(new Error('No sessionId passed in headers'));
	next();
};

module.exports.run = function (req, res, resCb) {
	var sessIdEncrypted = req.headers['x-if-sess'];
	expireSession(sessIdEncrypted)
		.done(function (opResult) {
			res.send(opResult);
		}, resCb);
};