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

	t.test("Returns topic", function (t) {
		var params = {
			session_id: sessionId
		};
		var resCb = function (data) {
			t.ok(data, "Topic was returned");
			t.equal(data.length, 1, "Found topic");
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
			t.notOk(data.reportTopics, "Report topics should not be returned");
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

	var cmd = require('../handlers/getTopics.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}