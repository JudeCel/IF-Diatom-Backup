"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;

test("Connects to the db", commonOperations.dbConnect);

test('Gets resources', function (t) {
	var sessionId = 0;
	var topicId = 0;
	var userId = 0;

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
			.then(function (info) {
				t.ok(info, 'Topic created');
				t.ok(info.id, 'Topic Id is valid');
				topicId = info.id;
				return ifTestHelpers.user.createUser();
			})
			.then(function (info) {
				t.ok(info, 'User created');
				t.ok(info.id, 'User Id is valid');
				userId = info.id;
				return ifTestHelpers.resource.createResource({
					topic_id: topicId,
					user_id: userId
				});
			})
			.done(function (info) {
				t.ok(info, 'Resource created');
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Returns resource", function (t) {
		var params = {
			resource_type: mtypes.resourceType.participant,
			topic_id: topicId,
			user_id: userId
		};
		var resCb = function (data) {
			t.ok(data, "Resources were returned");
			t.equal(data.length, 1, "Found Resources");
			t.end();
		};
		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
			t.end();
		};

		run(params, resCb, nextCb)
	});

	t.test("Should not return resource if topicId is not provided", function (t) {
		var params = {
			resource_type: mtypes.resourceType.participant,
			topic_id: null,
			user_id: userId
		};
		var resCb = function (data) {
			t.notOk(data.resources, "Resources should not be returned");
			t.end();
		};
		var nextCb = function (data) {
			t.ok(data, "Validation error should occur: " + data);
			t.end();
		};

		run(params, resCb, nextCb)
	});

	t.test("Should not return resource if userId is not provided", function (t) {
		var params = {
			resource_type: mtypes.resourceType.participant,
			topic_id: topicId,
			user_id: null
		};
		var resCb = function (data) {
			t.notOk(data.resources, "Resources should not be returned");
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

	var cmd = require('../handlers/getResources.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}