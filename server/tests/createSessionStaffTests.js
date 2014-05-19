"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;
var util = require("util");

test("Connects to the db", commonOperations.dbConnect);

test("Creates session staff", function (t) {
	var sessionId = 0;
	var userId = 0;

	t.test("Setup a company, brand project and session", function (t) {
		ifTestHelpers.session.createCompanyProjectSession()
			.then(function (info) {
				t.ok(info, 'Session created');
				sessionId = info.sessionId;
				return ifTestHelpers.user.createUser();
			})
			.done(function (info) {
				userId = info.id;
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Create a session staff", function(t) {
		var params = {
			user_id: userId,
			session_id: sessionId,
			type_id: mtypes.userType.globalAdministrator,
			active: 1
		};
		var resCb = function (data) {
			t.ok(data, "New session staff was returned");
			t.end();
		};
		var nextCb = function (err) {
			t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Should not create session staff if userId isn't passed", function(t) {
		var params = {
			user_id: null,
			session_id: sessionId,
			type_id: mtypes.userType.globalAdministrator,
			active: 1
		};
		var resCb = function (data) {
			t.notOk(data, "New session staff should not be returned");
			t.end();
		};
		var nextCb = function (err) {
			t.ok(err, "Validation error: " + util.inspect(err));
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Should not create session staff if sessionId isn't passed", function(t) {
		var params = {
			user_id: userId,
			session_id: null,
			type_id: mtypes.userType.globalAdministrator,
			active: 1
		};
		var resCb = function (data) {
			t.notOk(data, "New session staff should not be returned");
			t.end();
		};
		var nextCb = function (err) {
			t.ok(err, "Validation error: " + util.inspect(err));
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Removes session", function (t) {
		testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
	});
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
	var req = expressValidatorStub({
		params: params
	});

	var res = { send: resCb };

	var cmd = require('../handlers/createSessionStaff.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}