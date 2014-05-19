"use strict";
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var ifData = require('if-data'), db = ifData.db;
var test = require('tap').test;
var util = require('util');
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Gets session preferences', function (t) {
	var sessionId = 0;

	t.test("Sets up a session with session preference", function (t) {
		ifTestHelpers.session.createCompanyProjectSession()
			.then(function (info) {
				t.ok(info, 'Session created');
				sessionId = info.sessionId;
				t.ok(sessionId, 'Session id is valid');
				return ifTestHelpers.session.createSessionPreferences({
					session_id: sessionId
				});
			})
			.done(function (info) {
				t.ok(info.id, 'Session preferences created');
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Returns session preferences", function (t) {
		var params = {
			session_id: sessionId
		};
		var resCb = function (data) {
			t.ok(data, "Session preferences were returned");
			t.end();
		};
		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
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

	var cmd = require('../handlers/getSessionPreferences.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}