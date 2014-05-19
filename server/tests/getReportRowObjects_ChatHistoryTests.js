var dataHelper = require("../helpers/dataHelper");
var mtypes = require("if-common").mtypes;
var test = require('tap').test;
var commonOperations = require('./testHelpers/commonOperations.js');
var ifTestHelpers = require('if-test-helpers');
var chatHistoryReportHandler = require('../handlers/reportHandlers/chatHistoryReport.js');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('getReportRowObjects_ChatHistoryTests.js', function (t) {
    var sessionId = 0;
    var topicId = 0;
    var user1Id = 0;
    var user2Id = 0;
    var event1Id = 0;
    var event2Id = 0;
    var event3Id = 0;
    var event4Id = 0;
    var userName1 = "test-name_first1_" + dataHelper.getTimestamp().toString();
    var userName2 = "test-name_first2_" + dataHelper.getTimestamp().toString();

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
                return ifTestHelpers.user.createUser({
                    name_first: userName1
                })
            })
            .then(function (info) {
                t.ok(info, 'User 1 created');
                user1Id = info.id;
                return ifTestHelpers.user.createUser({
                    name_first: userName2
                });
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
            topicID: topicId,
            includeFacilitator: true,
            report: {
                type: mtypes.reportType.chat
            }
        };
        var resCb = function (result) {
            t.ok(result, "Events are returned");
            t.equal(result.length, 4, "Event count is correct");

            t.equal(result[0].isFirst, true, "Event[0].isFirst is correct");
            t.equal(result[0].isLast, false, "Event[0].isLast is correct");
            t.equal(result[0].isTagged, false, "Event[0].isTagged is correct");
            t.equal(result[0].user_id, user1Id, "Event[0].user_id is correct");
            t.equal(result[0].name, userName1, "Event[0].name is correct");

            t.equal(result[1].isFirst, false, "Event[1].isFirst is correct");
            t.equal(result[1].isLast, false, "Event[1].isLast is correct");
            t.equal(result[1].isTagged, true, "Event[1].isTagged is correct");
            t.equal(result[1].user_id, user1Id, "Event[1].user_id is correct");
            t.equal(result[1].name, userName1, "Event[1].name is correct");

            t.equal(result[2].isFirst, false, "Event[2].isFirst is correct");
            t.equal(result[2].isLast, false, "Event[2].isLast is correct");
            t.equal(result[2].isTagged, false, "Event[2].isTagged is correct");
            t.equal(result[2].user_id, user2Id, "Event[2].user_id is correct");
            t.equal(result[2].name, userName2, "Event[2].name is correct");

            t.equal(result[3].isFirst, false, "Event[3].isFirst is correct");
            t.equal(result[3].isLast, true, "Event[3].isLast is correct");
            t.equal(result[3].isTagged, true, "Event[3].isTagged is correct");
            t.equal(result[3].user_id, user2Id, "Event[3].user_id is correct");
            t.equal(result[3].name, userName2, "Event[3].name is correct");

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
            topicID: topicId,
            report: {
                type: mtypes.reportType.chat
            }
        };
        var resCb = function (result) {
            t.ok(result, "Events are returned");
            t.equal(result.length, 2, "Event count is correct");

            t.equal(result[0].isFirst, true, "Event[0].isFirst is correct");
            t.equal(result[0].isLast, false, "Event[0].isLast is correct");
            t.equal(result[0].isTagged, false, "Event[0].isTagged is correct");
            t.equal(result[0].user_id, user1Id, "Event[0].user_id is correct");
            t.equal(result[0].name, userName1, "Event[0].name is correct");

            t.equal(result[1].isFirst, false, "Event[1].isFirst is correct");
            t.equal(result[1].isLast, true, "Event[1].isLast is correct");
            t.equal(result[1].isTagged, true, "Event[1].isTagged is correct");
            t.equal(result[1].user_id, user1Id, "Event[1].user_id is correct");
            t.equal(result[1].name, userName1, "Event[1].name is correct");

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
            topicID: topicId,
            includeFacilitator: true,
            report: {
                type: mtypes.reportType.chat_stars
            }
        };
        var resCb = function (result) {
            t.ok(result, "Events are returned");
            t.equal(result.length, 2, "Event count is correct");

            t.equal(result[0].isFirst, true, "Event[0].isFirst is correct");
            t.equal(result[0].isLast, false, "Event[0].isLast is correct");
            t.equal(result[0].isTagged, true, "Event[0].isTagged is correct");
            t.equal(result[0].user_id, user1Id, "Event[0].user_id is correct");
            t.equal(result[0].name, userName1, "Event[0].name is correct");

            t.equal(result[1].isFirst, false, "Event[1].isFirst is correct");
            t.equal(result[1].isLast, true, "Event[1].isLast is correct");
            t.equal(result[1].isTagged, true, "Event[1].isTagged is correct");
            t.equal(result[1].user_id, user2Id, "Event[1].user_id is correct");
            t.equal(result[1].name, userName2, "Event[1].name is correct");

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
            topicID: topicId,
            includeFacilitator: false,
            report: {
                type: mtypes.reportType.chat_stars
            }
        };

        var resCb = function (result) {
            t.ok(result && result.length == 1, "Events were returned");

            t.equal(result[0].isFirst, true, "Event[0].isFirst is correct");
            t.equal(result[0].isLast, true, "Event[0].isLast is correct");
            t.equal(result[0].isTagged, true, "Event[0].isTagged is correct");
            t.equal(result[0].user_id, user1Id, "Event[0].user_id is correct");
            t.equal(result[0].name, userName1, "Event[0].name is correct");

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

function run(params, resCb, nextCb) {
    var getReportDataCb = function (data) {
        resCb(chatHistoryReportHandler.getReportRowObjects(data, nextCb));
    }
    chatHistoryReportHandler.getReportData(params, getReportDataCb, nextCb);
}