"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Gets offline transactions', function (t) {
	var sessionId = 0;
	var topicId = 0;
	var replyUserId = 0;

	t.test("Sets up a session with offline transaction and then retrieve it", function (t) {
		ifTestHelpers.session.createCompanyProjectSession()
			.then(function (info) {
				t.ok(info, 'Session created');
				sessionId = info.sessionId;
				return;
			})
			.then(function () {
				return ifTestHelpers.user.createUser();
			})
			.then(function (info) {
				return ifTestHelpers.offlineTransactions.createOfflineTransaction({
					user_id: info.id,
					session_id: sessionId,
					topic_id: 111   // TBD
				});
			})
			.done(function (info) {
				topicId = info.topic_id;
				replyUserId = info.reply_user_id;
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Returns offline transactions", function (t) {
		var params = {
			session_id: sessionId,
			reply_user_id: replyUserId
		};
		var resCb = function (data) {
			t.ok(data, "Offline transaction was returned");
			t.equal(data.length, 1, "Found offline transactions");
			t.end();
		};
		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
			t.end();
		};

		run(params, resCb, nextCb)
	});

	t.test("Should not return offline transactions if reply_user_id is not provided", function (t) {
		var params = {
			session_id: sessionId
		};
		var resCb = function (data) {
			t.notOk(data, "Offline transaction should not be returned");
			t.end();
		};
		var nextCb = function (data) {
			t.ok(data, "Validation error should occur: " + data);
			t.end();
		};

		run(params, resCb, nextCb)
	});

	t.test("Should not return offline transactions if session_id is not provided", function (t) {
		var params = {
			reply_user_id: replyUserId
		};
		var resCb = function (data) {
			t.notOk(data, "Offline transaction should not be returned");
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

	var cmd = require('../handlers/getOfflineTransactions.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}