"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Get chats', function (t) {
	var userId = 0;
	var eventId = 0;
	var sessionId = 0;
	var topicId = 0;

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
			.then(function (info) {
				t.ok(info, 'Topic created');
				topicId = info.id;
				return ifTestHelpers.event.createEvent({
					user_id: userId,
					topic_id: topicId,
					cmd: "chat"
				});
			})
			.done(function (info) {
				eventId = info.id;
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Returns chat", function (t) {
		var params = {
			topic_id: topicId
		}
		var resCb = function (data) {
			t.ok(data, "Chat was returned");
			t.end();
		};
		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Should not return a chat if topicId is not passed", function (t) {
		var params = {
			topic_id: null
		};
		var resCb = function (data) {
			t.notOk(data.chats, "Chat should not be returned");
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

//test("Removes offline transactions", commonOperations.removeOfflineTransactions);     TBD
test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
	var req = expressValidatorStub({
		params: params
	});

	var res = { send: resCb };

	var cmd = require('../handlers/getChats.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}