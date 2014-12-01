"use strict";
var util = require('util');
var _ = require('lodash');
var config = require('simpler-config');
var ifData = require('if-data'), db = ifData.db;
var ifCommon = require('if-common');
var encryptor = ifCommon.utils.dotNetEncryptionHelper;
var mtypes = ifCommon.mtypes;
var webFaultHelper = require('../../helpers/webFaultHelper.js');
var ifAuth = require('if-auth');

module.exports.all = function (req, res, mainCb) {
	return getSession({sessTypes: [mtypes.sessType.standard, mtypes.sessType.sME, mtypes.sessType.anonymous]}, req, res, mainCb);
};

module.exports.registeredUser = function (req, res, mainCb) {
	return getSession({}, req, res, mainCb);
};

module.exports.accountManager = function (req, res, mainCb) {
	return getSession({userType: [
		mtypes.userRole.accountManager
	]}, req, res, mainCb);
};

module.exports.trainer = function (req, res, mainCb) {
	return getSession({userType: mtypes.userType.regular}, req, res, mainCb);
};

function getSessionFromHeader(req) {
	var sessionHeaderValue = req.headers['x-insiderfocus-sessionid'] || req.headers['x-if-sess'];
	var sessId = 0;

	if(encryptor.isNewEncryption(sessionHeaderValue)) {
		sessId = encryptor.decryptNumberFromUrl(sessionHeaderValue);
	} else {
		try {
			var decryptedSessId = encryptor.decryptFromUrlOld(sessionHeaderValue);
			if(decryptedSessId)
				sessId = parseInt(decryptedSessId, 10);
		}
		catch (e) {
			sessId = 0
		}
	}

	return sessId;
}

function getSession(options, req, res, mainCb) {
	delete req.session;
	options = options || {};
	var userType = options.userType;
	var sessTypes = options.sessTypes;

	var sessionId = getSessionFromHeader(req);
	if(!sessionId)
		return mainCb(webFaultHelper.getAuthFault());

	ifAuth.validateSession({
		sessionId: sessionId,
		userType: userType,
		sessionInactivityExpirationMinutes: config.sessionInactivityExpirationMinutes,
		sessTypes: sessTypes
	}, function (err, sessResult) {
		if (util.isError(err))
			return mainCb(webFaultHelper.getFault(err));

		if (err == 'session_expired')
			return mainCb(webFaultHelper.getDetailedAuthFault({sessionExpired: true, sessionId: sessionId}));
		if (err == 'user_inactive')
			return mainCb(webFaultHelper.getDetailedAuthFault({userInactive: true}));

		// for inactive accounts, set the session to allow us to prompt the account-holders to pay us
		req.locals = sessResult;

		if (err == 'account_inactive')
			return mainCb(webFaultHelper.getDetailedAuthFault({accountInactive: true}));

		if (!sessResult || !sessResult.sessionId)
			return mainCb(webFaultHelper.getAuthFault());

		mainCb();
	});
}
