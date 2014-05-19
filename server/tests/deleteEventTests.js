"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var dataHelper = require("../helpers/dataHelper");

test("Connects to the db", commonOperations.dbConnect);

test('Delete events', function (t) {
    var userId = 0;
    var sessionId = 0;
    var topicId = 0;
    var event1Id = 0;
    var event2Uid = dataHelper.getTimestamp().toString();
    var event2Id = 0;

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
                    topic_id: topicId
                });
            })
            .then(function (info) {
                t.ok(info, 'Topic created');
                event1Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: userId,
                    topic_id: topicId,
                    uid: event2Uid
                });
            })
            .done(function (info) {
                event2Id = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Delete event 1", function (t) {
        var params = {
            event_id: event1Id
        };
        var resCb = function (opResult) {
            t.ok(opResult, "Result returned");
            t.equal(opResult.affectedRows, 1, "Row affected");
            t.equal(opResult.changedRows, 1, "Row changed");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + data);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Delete event 2", function (t) {
        var params = {
            uid: event2Uid
        };
        var resCb = function (opResult) {
            t.ok(opResult, "Result returned");
            t.equal(opResult.affectedRows, 1, "Row affected");
            t.equal(opResult.changedRows, 1, "Row changed");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Should not delete the events topicId is not passed", function (t) {
        var params = {};
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

    var cmd = require('../handlers/deleteEvent.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}