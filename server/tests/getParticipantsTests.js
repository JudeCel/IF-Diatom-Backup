"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Gets participants', function (t) {
	var sessionId = 0;
	var brandProjectId = 0;
    var clientCompanyId = 0;

	t.test("Sets up a session, topic and user", function (t) {
		ifTestHelpers.session.createCompanyProjectSession()
			.then(function (info) {
				t.ok(info, 'Session created');
				t.ok(info.sessionId, 'Session Id is valid');
				t.ok(info.brandProjectId, 'Brand Project Id is valid');
				brandProjectId = info.brandProjectId;
				sessionId = info.sessionId;
				return ifTestHelpers.user.createUser();
			})
			.then(function (info) {
				t.ok(info, 'User created');
				t.ok(info.id, 'User Id is valid');
				return ifTestHelpers.participant.createParticipant({
					user_id: info.id,
					brand_project_id: brandProjectId
				});
			})
			.then(function (info) {
				t.ok(info, 'Participant created');
				t.ok(info.id, 'Participant Id is valid');
				return ifTestHelpers.participant.createParticipantListRecord({
					participant_id: info.id,
					session_id: sessionId,
					participant_reply_id: 1
				});
			})
			.done(function (info) {
				t.ok(info, 'Participant is linked with session created');
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Get participants", function (t) {
		var params = {
			session_id: sessionId,
            client_company_id: clientCompanyId
		};
		var resCb = function (data) {
			t.ok(data, "Report topics was returned");
			t.equal(data.length, 1, "Found participant(s)");
			t.end();
		};
		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
			t.end();
		};

		run(params, resCb, nextCb)
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

	var cmd = require('../handlers/getParticipants.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}