"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;

test("Connects to the db", commonOperations.dbConnect);

test('getReportData_ChatHistoryTests.js', function (t) {
    var sessionId = 0;
    var topicId = 0;
    var user1Id = 0;
    var user2Id = 0;
    var event1Id = 0;
    var event2Id = 0;
    var event3Id = 0;
    var event4Id = 0;

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
                t.ok(info, 'Topic created');
                topicId = info.id;
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
                return ifTestHelpers.session.createSessionStaff({
                    user_id: user1Id,
                    session_id: sessionId,
                    type_id: mtypes.userType.globalAdministrator
                });
            })
            .then(function (info) {
                t.ok(info, 'Session Staff - Administrator created');
                return ifTestHelpers.session.createSessionStaff({
                    user_id: user2Id,
                    session_id: sessionId,
                    type_id: mtypes.userType.facilitator
                });
            })
            .then(function (info) {
                t.ok(info, 'Session Staff - Facilitator created');
                return ifTestHelpers.event.createEvent({
                    user_id: user1Id,
                    topic_id: topicId,
                    tag: 16,
                    cmd: 'chat'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 1 created');
                event1Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user1Id,
                    topic_id: topicId,
                    tag: 1,
                    cmd: 'chat'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 2 created');
                event2Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user2Id,
                    topic_id: topicId,
                    tag: 16,
                    cmd: 'chat'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 3 created');
                event3Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user2Id,
                    topic_id: topicId,
                    tag: 1,
                    cmd: 'chat'
                });
            })
            .done(function (info) {
                t.ok(info, 'Event 4 created');
                event4Id = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets all events of the Topic", function (t) {
        var params = {
            topic_id: topicId
        };
        var resCb = function (result) {
            t.ok(result && result.length == 4, "Events were returned");

            t.equal(result[0].id, event1Id, "Event 1 id is correct");
            t.equal(result[0].user_id, user1Id, "Event 1 user_id is correct");
            t.equal(result[0].topic_id, topicId, "Event 1 topicId is correct");

            t.equal(result[1].id, event2Id, "Event 2 id is correct");
            t.equal(result[1].user_id, user1Id, "Event 2 user_id is correct");
            t.equal(result[1].topic_id, topicId, "Event 2 topicId is correct");

            t.equal(result[2].id, event3Id, "Event 3 id is correct");
            t.equal(result[2].user_id, user2Id, "Event 3 user_id is correct");
            t.equal(result[2].topic_id, topicId, "Event 3 topicId is correct");

            t.equal(result[3].id, event4Id, "Event 4 id is correct");
            t.equal(result[3].user_id, user2Id, "Event 4 user_id is correct");
            t.equal(result[3].topic_id, topicId, "Event 4 topicId is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets events of the Topic excluding Facilitator events", function (t) {
        var params = {
            topic_id: topicId,
            sessionStaffTypeToExclude: mtypes.userType.facilitator
        };
        var resCb = function (result) {
            t.ok(result && result.length == 2, "Events were returned");

            t.equal(result[0].id, event1Id, "Event 1 id is correct");
            t.equal(result[0].user_id, user1Id, "Event 1 user_id is correct");
            t.equal(result[0].topic_id, topicId, "Event 1 topicId is correct");

            t.equal(result[1].id, event2Id, "Event 2 id is correct");
            t.equal(result[1].user_id, user1Id, "Event 2 user_id is correct");
            t.equal(result[1].topic_id, topicId, "Event 2 topicId is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets events of the Topic in Stars Only mode", function (t) {
        var params = {
            topic_id: topicId,
            starsOnly: true
        };
        var resCb = function (result) {
            t.ok(result && result.length == 2, "Events were returned");

            t.equal(result[0].id, event2Id, "Event 1 id is correct");
            t.equal(result[0].user_id, user1Id, "Event 1 user_id is correct");
            t.equal(result[0].topic_id, topicId, "Event 1 topicId is correct");

            t.equal(result[1].id, event4Id, "Event 2 id is correct");
            t.equal(result[1].user_id, user2Id, "Event 2 user_id is correct");
            t.equal(result[1].topic_id, topicId, "Event 2 topicId is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets events of the Topic in Stars Only mode excluding Facilitator events", function (t) {
        var params = {
            topic_id: topicId,
            starsOnly: true,
            sessionStaffTypeToExclude: mtypes.userType.facilitator
        };
        var resCb = function (result) {
            t.ok(result && result.length == 1, "Events were returned");

            t.equal(result[0].id, event2Id, "Event 1 id is correct");
            t.equal(result[0].user_id, user1Id, "Event 1 user_id is correct");
            t.equal(result[0].topic_id, topicId, "Event 1 topicId is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
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
    var cmd = require('../handlers/getReportData_ChatHistory.js');
    cmd.validate(req, function (err) {
        if (err) return errCb(err);
        cmd.run(req, res, errCb);
    });
}