"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var util = require("util");

test("Connects to the db", commonOperations.dbConnect);

test("Create offline transaction", function (t) {
	var sessionId = 0;
	var topicId = 0;
	var userId = 0;

	t.test("Sets up a session", function (t) {
		ifTestHelpers.session.createCompanyProjectSession()
			.then(function (info) {
				t.ok(info, 'Session created');
				sessionId = info.sessionId;
				return ifTestHelpers.user.createUser({
					ifs_admin: true,
					avatar_info: "test-avatar-info"
				});
			})
			.then(function (info) {
				t.ok(info, 'User created');
				userId = info.id;
				return ifTestHelpers.topic.createTopic({
					session_id: sessionId
				});
			})
			.done(function (info) {
				t.ok(info, 'Topic created');
				topicId = info.id;
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Create offline transaction", function(t) {
		var params = {
			user_id: userId,
			session_id: sessionId,
			topic_id: topicId
		}
		var resCb = function () {
			t.end();
		};
		var nextCb = function (err) {
			t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Should not create offline transaction if userId is missed", function(t) {
		var params = {
			session_id: sessionId,
			topic_id: topicId
		}
		var resCb = function (data) {
			t.notOk(data, "New offline transaction was returned");
			t.end();
		};
		var nextCb = function (err) {
			t.ok("Validation error: " + util.inspect(err));
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Should not create offline transaction if sessionId is missed", function(t) {
		var params = {
			user_id: userId,
			topic_id: topicId
		}
		var resCb = function (data) {
			t.notOk(data, "New offline transaction was returned");
			t.end();
		};
		var nextCb = function (err) {
			t.ok("Validation error: " + util.inspect(err));
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Should not create offline transaction if topicId is missed", function(t) {
		var params = {
			user_id: userId,
			session_id: sessionId
		}
		var resCb = function (data) {
			t.notOk(data, "New offline transaction was returned");
			t.end();
		};
		var nextCb = function (err) {
			t.ok("Validation error: " + util.inspect(err));
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
	var cmd = require('../handlers/createOfflineTransactions.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}