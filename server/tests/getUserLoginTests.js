"use strict";
var util = require('util');
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Gets user login info', function (t) {
	var sessionId = 0;
	var userId = 0;

	t.test('Setup', function (t) {
		ifTestHelpers.session.createCompanyProjectSession()
			.then(function(info) {
				t.ok(info, 'Session created');
				t.ok(info.sessionId, 'Session id is valid');
				sessionId = info.sessionId;
				return ifTestHelpers.user.createUser()
			})
			.then(function (info) {
				t.ok(info, 'User created');
				t.ok(info.id, 'User id is valid');
				userId = info.id;
				return ifTestHelpers.user.createUserLogin({
					user_id: info.id
				});
			})
			.then(function (info) {
				t.ok(info, 'User login created');
				return ifTestHelpers.session.createSessionStaff({
					user_id: userId,
					session_id: sessionId
				});
			})
			.done(function (info) {
				t.ok(info, 'Session staff created');
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Returns user login info", function (t) {
		var params = {
			user_id: userId,
			session_id: sessionId
		};
		var resCb = function (data) {
			t.ok(data, "User login was returned");
			t.equal(data.id, userId);
			t.end();
		};
		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
			t.end();
		};

		run(params, resCb, nextCb);
	});

	t.test("Should not return user login info if any parameter is not passed", function (t) {
		var params = {
			user_id: userId
		};
		var resCb = function (data) {
			t.notOk(data, "User login was returned");
			t.end();
		};
		var nextCb = function (data) {
			t.ok(data, "Validation error: " + data);
			t.end();
		};

		run(params, resCb, nextCb);
	});

	t.test("Removes session", function (t) {
		testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
	});
});

//test("Removes brand project", commonOperations.removeUser);
test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
	var req = expressValidatorStub({
		params: params
	});

	var res = { send: resCb };

	var cmd = require('../handlers/getUserLogin.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}