"use strict";
var _ = require('lodash');
var async = require('async');
var config = require('simpler-config');
var ifCommon = require('if-common');
var dateHelper = ifCommon.utils.dateHelper;
var ifData = require('if-data'), db = ifData.db;
var fn = ifCommon.utils.functionHelper;
var webFaultHelper = require('../../helpers/webFaultHelper.js');
var querystring = require('querystring');

module.exports.validate = function (req, res, next) {
	next();
};

module.exports.run = function (req, res, mainCb) {
	var accountId = req.locals.accountId;

	async.parallel({
		accountInfo: fn.wrapWithCb(getAccountInfo, accountId)
	}, function (err, results) {
		if (err) return mainCb(webFaultHelper.getFault(err));
		var ret = _.extend({}, req.locals, results);

		//Adding one to both of these for the end of day if not null
		ret.accountInfo.trialDaysLeft = (ret.accountInfo.trialExpiration ? dateHelper.getDaysBetweenDatesTrial(ret.accountInfo.trialExpiration, Date.now()) : 0);

		res.send(ret);
	});

	function getAccountInfo(accountId, cb) {
		var sql = "SELECT \
			a.pricingType, \
			a.trialExpiration, \
			UNIX_TIMESTAMP(a.cancellationDate) * 1000 cancellationDate,\
			a.ownerEmail \
		FROM account a \
		WHERE a.id = ?";
		db.queryOne(sql, [accountId], cb);
	}
};
