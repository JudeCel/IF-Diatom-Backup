"use strict";
var dataHelper = require("../helpers/dataHelper");
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;

var whiteboardReportHandler = require('../handlers/reportHandlers/whiteboardReport.js');

test("Connects to the db", commonOperations.dbConnect);

test('getReportRowObjects_WhiteboardTests.js', function (t) {
    var sessionId = 0;
    var topicId = 0;
    var user1Id = 0;
    var user2Id = 0;
    var event1Id = 0;
    var event2Id = 0;
    var event3Id = 0;

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
                    tag: 0,
                    cmd: 'deleteall'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 1 created');
                event1Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user1Id,
                    topic_id: topicId,
                    tag: 0,
                    cmd: 'object'
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 2 created');
                event2Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user2Id,
                    topic_id: topicId,
                    tag: 16,
                    cmd: 'shareresource'
                });
            })
            .done(function (info) {
                t.ok(info, 'Event 3 created');
                event3Id = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets all events of the Topic", function (t) {
        var params = {
            topicID: topicId
        };
        var resCb = function (result) {
            t.ok(result, "Events are returned");
            t.equal(result.length, 3, "Event count is correct");

            t.equal(result[0].isFirst, true, "result[0].isFirst is correct");
            t.equal(result[0].isLast, false, "result[0].isLast is correct");
            t.equal(result[0].user_id, user1Id, "result[0].user_id is correct");
            t.equal(result[0].name, userName1, "result[0].name is correct");

            t.equal(result[1].isFirst, false, "result[1].isFirst is correct");
            t.equal(result[1].isLast, false, "result[1].isLast is correct");
            t.equal(result[1].user_id, user1Id, "result[1].user_id is correct");
            t.equal(result[1].name, userName1, "result[1].name is correct");

            t.equal(result[2].isFirst, false, "result[2].isFirst is correct");
            t.equal(result[2].isLast, true, "result[2].isLast is correct");
            t.equal(result[2].user_id, user2Id, "result[2].user_id is correct");
            t.equal(result[2].name, userName1, "result[2].name is correct");

            t.end();
        };
        var errCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, errCb);
    });

    t.test("Removes session", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
    });
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var getReportDataCb = function (data) {
        resCb(whiteboardReportHandler.getReportRowObjects(data, nextCb));
    }
    whiteboardReportHandler.getReportData(params, getReportDataCb, nextCb);
}