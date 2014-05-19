"use strict";
var util = require('util');
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var ifCommon = require('if-common');
var mtypes = ifCommon.mtypes;
var testUtils = ifTestHelpers.utils;

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
    var sessionName = "session name";
    var topicName = "topic name";

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
                    name_first: "user-test1"
                })
            })
            .then(function (info) {
                t.ok(info, 'User 1 created');
                user1Id = info.id;
                return ifTestHelpers.user.createUser({
                    name_first: "user-test2"
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
                    cmd: 'chat',
                    event: encodeURI(JSON.stringify({object: {date: new Date(), input: "cmm" }}, null))
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 1 created');
                event1Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user1Id,
                    topic_id: topicId,
                    tag: 1,
                    cmd: 'chat',
                    event: encodeURI(JSON.stringify({object: {date: new Date(), input: "cmm" }}, null))
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 2 created');
                event2Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user2Id,
                    topic_id: topicId,
                    tag: 16,
                    cmd: 'chat',
                    event: encodeURI(JSON.stringify({object: {date: new Date(), input: "cmm" }}, null))
                });
            })
            .then(function (info) {
                t.ok(info, 'Event 3 created');
                event3Id = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: user2Id,
                    topic_id: topicId,
                    tag: 1,
                    cmd: 'chat',
                    event: encodeURI(JSON.stringify({object: {date: new Date(), input: "cmm" }}, null))
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

    t.test("Generates Pdf for Chat Room report", function (t) {
        var params = {
            report: {
                type: mtypes.reportType.chat,
                topic: topicName,
                session: sessionName
            },
            type: "PDF",
            sessionID: sessionId,
            topicID: topicId,
            topic: topicName,
            session: sessionName
        };
        var resCb = function (pdfLinks) {
            t.ok(pdfLinks, "Pdf processing object returned");
            t.ok(pdfLinks.saveAs, "Pdf file system path returned");
            t.ok(pdfLinks.urlPath, "Pdf url path returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Generates Pdf for Chat Room (Stars Only) report", function (t) {
        var params = {
            report: {
                type: mtypes.reportType.chat_stars,
                topic: topicName,
                session: sessionName
            },
            type: "PDF",
            sessionID: sessionId,
            topicID: topicId,
            topic: topicName,
            session: sessionName
        };
        var resCb = function (pdfLinks) {
            t.ok(pdfLinks, "Pdf processing object returned");
            t.ok(pdfLinks.saveAs, "Pdf file system path returned");
            t.ok(pdfLinks.urlPath, "Pdf url path returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Generates Pdf for Whiteboard report", function (t) {
        var params = {
            report: {
                type: mtypes.reportType.whiteboard,
                topic: topicName,
                session: sessionName
            },
            type: "PDF",
            sessionID: sessionId,
            topicID: topicId,
            topic: topicName,
            session: sessionName
        };
        var resCb = function (pdfLinks) {
            t.ok(pdfLinks, "Pdf processing object returned");
            t.ok(pdfLinks.saveAs, "Pdf file system path returned");
            t.ok(pdfLinks.urlPath, "Pdf url path returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Generates Pdf for Vote report", function (t) {
        var params = {
            report: {
                type: mtypes.reportType.vote,
                topic: topicName,
                session: sessionName
            },
            type: "PDF",
            sessionID: sessionId,
            topicID: topicId,
            topic: topicName,
            session: sessionName
        };
        var resCb = function (pdfLinks) {
            t.ok(pdfLinks, "Pdf processing object returned");
            t.ok(pdfLinks.saveAs, "Pdf file system path returned");
            t.ok(pdfLinks.urlPath, "Pdf url path returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Generates Pdf for Statistics report", function (t) {
        var params = {
            report: {
                type: mtypes.reportType.stats,
                topic: topicName,
                session: sessionName
            },
            type: "PDF",
            sessionID: sessionId,
            topicID: topicId
        };
        var resCb = function (pdfLinks) {
            t.ok(pdfLinks, "Pdf processing object returned");
            t.ok(pdfLinks.saveAs, "Pdf file system path returned");
            t.ok(pdfLinks.urlPath, "Pdf url path returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Generates Pdf with wrong parameter values", function (t) {
        var params = {
            report: {
                type: 1
            },
            type: 2,
            sessionID: "wrong sessionID value",
            topicID: "wrong topicID value",
            userID: "wrong userID value"
        };
        var resCb = function (pdfLinks) {
            t.notOk(pdfLinks, "Pdf processing object returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Generates Pdf with parameter values undefined", function (t) {
        var params = { };
        var resCb = function (pdfLinks) {
            t.notOk(pdfLinks, "Pdf processing object returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    require('../handlers/reportHandlers/factory.js')(params, resCb, nextCb);
}