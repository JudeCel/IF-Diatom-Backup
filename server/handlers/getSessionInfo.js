"use strict";
var _ = require('lodash');
var async = require('async');
var config = require('simpler-config');
//var mfCommon = require('mf-common');
//var dateHelper = mfCommon.utils.dateHelper;
//var mfData = require('mf-data'), db = mfData.db;
//var fn = mfCommon.utils.functionHelper;
//var goodDataHelper = mfCommon.utils.goodDataHelper;
//var webFaultHelper = require('../../helpers/webFaultHelper.js');
//var getUserCustomFields = mfData.repositories.getUserCustomFields;
//var encryptor = mfCommon.utils.dotNetEncryptionHelper;
//var querystring = require('querystring');

module.exports.validate = function (req, res, next) {
	next();
};

module.exports.run = function (req, res, mainCb) {
	//var accountId = req.locals.accountId;
	//var allowedGroups = req.locals.allowedGroups
	//var groupRestrictions = (allowedGroups && allowedGroups.length > 0);

	async.parallel({
		accountInfo: getAccountInfo
	}, function (err, results) {

		res.header('Access-Control-Allow-Origin', req.headers.origin);
		res.header('Access-Control-Allow-Credentials', 'true');

		//if (err) return mainCb(webFaultHelper.getFault(err));
		//var ret = _.extend({}, req.locals, results);

		//ret.goodDataEnabled = false;
		//ret.useGroupRestrictions = groupRestrictions;

		//Adding one to both of these for the end of day if not null
		//ret.accountInfo.trialDaysLeft = (ret.accountInfo.trialExpiration ? dateHelper.getDaysBetweenDatesTrial(ret.accountInfo.trialExpiration, Date.now()) : 0);

		//ret.accountInfo.advancedTierDaysLeft = -1;
		//if(ret.accountInfo.hideAdvancedTierTrial == null && ret.accountInfo.tierId == 500002)
		//	ret.accountInfo.advancedTierDaysLeft = (ret.accountInfo.advancedTierTrialExpiration ? dateHelper.getDaysBetweenDatesAdvancedTrial(ret.accountInfo.advancedTierTrialExpiration, Date.now()) : 0);


//		//Send them thru sess in case the domains are different
//		ret.viewAsTraineeUrl = "http://" + ret.traineeDomain + "/sess?" + querystring.stringify({
//			sess: encryptor.encryptNumberForUrl(ret.sessionId),
//			redirectUrl: "http://" + ret.traineeDomain + "/trainee/"
//		});
		res.send(results);
	});

	function getAccountInfo(cb) {
		cb(null, {});
//		var sql = "SELECT \
//			a.maxTeamMembers, \
//			a.pricingMaxTrainees, \
//			IF(IFNULL(a.YammerEnabled, 0) = 0, 0, 1) yammerEnabled, \
//			a.pricingType, \
//			a.trialExpiration, \
//			a.advancedTierTrialExpiration, \
//			a.loginMode, \
//			a.name as companyName, \
//			a.tierId as tierId, \
//			a.LoginMode, \
//			UNIX_TIMESTAMP(a.cancellationDate) * 1000 cancellationDate,\
//			a.ownerEmail \
//		FROM account a \
//		WHERE a.id = ?";
//		db.queryOne(sql, [accountId], cb);
	}
};
