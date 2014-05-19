"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;

test("Connects to the db", commonOperations.dbConnect);

test('getReportData_StatisticsTests.js', function (t) {
    var sessionId = 0;
    var topic1Id = 0;
    var topic2Id = 0;
    var user1Id = 0;
    var user2Id = 0;

    t.test('Creates predefined data sets', function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
                sessionId = info.sessionId;
                return ifTestHelpers.topic.createTopic({
                    session_id: sessionId
                });
            })
            .then(function (info) {
                t.ok(info, 'Topic 1  created');
                topic1Id = info.id;
                return ifTestHelpers.topic.createTopic({
                    session_id: sessionId
                });
            })
            .then(function (info) {
                t.ok(info, 'Topic2 created');
                topic2Id = info.id;
                return ifTestHelpers.user.createUser()
            })
            .then(function (info) {
                t.ok(info, 'User 1 created');
                user1Id = info.id;
                return ifTestHelpers.user.createUser();
            })
            .then(function (info) {
                t.ok(info, 'User 2 created');
                user2Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user1Id,
                    topic_id: topic1Id,
                    tag: 16,
                    cmd: 'chat'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 1 created');
                return ifTestHelpers.event.createEvent({
                    user_id: user1Id,
                    topic_id: topic1Id,
                    tag: 16,
                    cmd: 'chat'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 2 created');
                return ifTestHelpers.event.createEvent({
                    user_id: user2Id,
                    topic_id: topic1Id,
                    tag: 16,
                    cmd: 'chat'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 3 created');
                return ifTestHelpers.event.createEvent({
                    user_id: user1Id,
                    topic_id: topic2Id,
                    tag: 16,
                    cmd: 'chat'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 4 created');
                return ifTestHelpers.event.createEvent({
                    user_id: user2Id,
                    topic_id: topic2Id,
                    tag: 16,
                    cmd: 'chat'
                });
            })
            .done(function (info) {
                t.ok(info, 'Event 5 created');
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets statistics of the Session", function (t) {
        var params = {
            session_id: sessionId
        };
        var resCb = function (result) {
            t.ok(result && result.length == 4, "Statistics was returned");

            t.equal(result[0].user_id, user1Id, "Statistics 1 user_id is correct");
            t.equal(result[0].count_user_id, 2, "Statistics 1 count_user_id is correct");
            t.equal(result[0].topic_id, topic1Id, "Statistics 1 topic_id is correct");

            t.equal(result[1].user_id, user2Id, "Statistics 2 user_id is correct");
            t.equal(result[1].count_user_id, 1, "Statistics 2 count_user_id is correct");
            t.equal(result[1].topic_id, topic1Id, "Statistics 2 topic_id is correct");

            t.equal(result[2].user_id, user1Id, "Statistics 3 user_id is correct");
            t.equal(result[2].count_user_id, 1, "Statistics 3 count_user_id is correct");
            t.equal(result[2].topic_id, topic2Id, "Statistics 3 topic_id is correct");

            t.equal(result[3].user_id, user2Id, "Statistics 3 user_id is correct");
            t.equal(result[3].count_user_id, 1, "Statistics 3 count_user_id is correct");
            t.equal(result[3].topic_id, topic2Id, "Statistics 3 topic_id is correct");
            t.end();
        };
        var errCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };

        run(params, resCb, errCb);
    });

    t.test("Gets statistics of the Session with parameters == null", function (t) {
        var params = {
            session_id: null
        };
        var resCb = function (result) {
            t.notOk(result, "Statistics was returned");
            t.end();
        };
        var errCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };

        run(params, resCb, errCb);
    });

    t.test("Gets statistics of the Session with wrong parameter values", function (t) {
        var params = {
            session_id: "wrong session_id value"
        };
        var resCb = function (result) {
            t.notOk(result, "Statistics was returned");
            t.end();
        };
        var errCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };

        run(params, resCb, errCb);
    });

    t.test("Gets statistics of the Session with parameters undefined", function (t) {
        var params = {
        };
        var resCb = function (result) {
            t.notOk(result, "Statistics was returned");
            t.end();
        };
        var errCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };

        run(params, resCb, errCb);
    });

	t.test("Removes session", function (t) {
		testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
	});
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, errCb) {
    var req = expressValidatorStub({
        params: params
    });

    var res = { send: resCb };
    var cmd = require('../handlers/getReportData_Statistics.js');
    cmd.validate(req, function (err) {
        if (err) return errCb(err);
        cmd.run(req, res, errCb);
    });
}