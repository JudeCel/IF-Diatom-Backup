"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Get event', function (t) {
    var userId = 0;
    var eventId = 0;
    var sessionId = 0;
    var topicId = 0;
    var uid = "-23i+dsidj23D)SKD)Okdlspa";

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
                    uid: uid
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

    t.test("Gets Event", function (t) {
        var params = {
            uid: uid
        };

        var resCb = function (data) {
            t.ok(data, "Event was returned");
            t.equals(data.uid, uid, "Event uid is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets Event with values == null", function (t) {
        var params = {
            uid: null
        };
        var resCb = function (data) {
            t.notOk(data, "Event should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets Event with values undefined", function (t) {
        var params = {
        };
        var resCb = function (data) {
            t.notOk(data, "Event should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets Event with wrong values", function (t) {
        var params = {
            uid: 1024
        };
        var resCb = function (data) {
            t.notOk(data, "Event should not be returned");
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

    var cmd = require('../handlers/getEventByUid.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}