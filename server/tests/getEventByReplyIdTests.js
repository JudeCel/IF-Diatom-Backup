"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Get event by replyId', function (t) {
    var userId = 0;
    var sessionId = 0;
    var topicId = 0;
	var voteId = 0;

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
			        reply_id: 999,
			        cmd: 'vote'
		        });
	        })
	        .then(function (info) {
		        t.ok(info, 'Event created');
		        return ifTestHelpers.vote.createVote({
			        event_id: info.id
		        });
	        })
            .done(function (info) {
		        t.ok(info, 'Vote created');
                voteId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Get Event by replyId", function (t) {
        var params = {
            reply_id: 999
        };

        var resCb = function (data) {
            t.ok(data.length, "Event was returned");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Get Event with values == null", function (t) {
        var params = {
	        reply_id: null
        };
        var resCb = function (data) {
            t.notOk(data.length, "Event should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Get Event with values undefined", function (t) {
        var params = {};
        var resCb = function (data) {
            t.notOk(data.length, "Event should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Get Event with wrong values", function (t) {
        var params = {
	        reply_id: -10
        };
        var resCb = function (data) {
            t.notOk(data.length, "Event should not be returned");
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

    var cmd = require('../handlers/getEventByReplyId.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}