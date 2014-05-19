"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Gets report topics', function (t) {
	var sessionId = 0;

	t.test("Sets up a session, topic and user", function (t) {
		ifTestHelpers.session.createCompanyProjectSession()
			.then(function (info) {
				t.ok(info, 'Session created');
				t.ok(info.sessionId, 'Session Id is valid');
				sessionId = info.sessionId;
				return ifTestHelpers.topic.createTopic({
					session_id: sessionId
				});
			})
			.done(function (info) {
				t.ok(info, 'Topic created');
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Returns report topics", function (t) {
		var params = {
			session_id: sessionId
		};
		var resCb = function (data) {
			t.ok(data, "Report topics was returned");
			t.equal(data.length, 1, "Found report topics");
			t.end();
		};
		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
			t.end();
		};

		run(params, resCb, nextCb)
	});

	t.test("Should not return report topics if session_id is not provided", function (t) {
		var params = {};
		var resCb = function (data) {
			t.notOk(data, "Report topics should not be returned");
			t.end();
		};
		var nextCb = function (data) {
			t.ok(data, "Validation error should occur: " + data);
			t.end();
		};

		run(params, resCb, nextCb)
	});

	t.test("Removes session", function (t) {
		testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
	});
});

//test("Removes offline transactions", commonOperations.removeOfflineTransactions);     TBD
test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
	var req = expressValidatorStub({
		params: params
	});

	var res = { send: resCb };

	var cmd = require('../handlers/getReportTopics.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}