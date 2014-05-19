"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test("getEventsAsCollage", function (t) {
    var projectId = 0;
    var sessionId = 0;
    var topicId = 0;
    var userId = 0;
    var eventId = 0;

    t.test("Creates predefined data sets ", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
                projectId = info.brandProjectId;
                sessionId = info.sessionId;
                var newTopic = {
                    session_id: sessionId
                };
                return ifTestHelpers.topic.createTopic(newTopic);
            })
            .then(function (info) {
                t.ok(info, 'Topic created');
                topicId = info.id;
                return ifTestHelpers.user.createUser();
            })
            .then(function (info) {
                t.ok(info, 'User created');
                userId = info.id;
                return ifTestHelpers.event.createEvent({
                    user_id: userId,
                    topic_id: topicId,
                    cmd: 'collage'
                });
            })
            .done(function (info) {
                t.ok(info, 'Event created');
                eventId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets Events", function (t) {
        var params = {
            topicId: topicId
        };
        var resCb = function (data) {
            t.ok(data && data.length, "Events were returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Gets Events with values == null", function (t) {
        var params = {
            topicId: null
        };
        var resCb = function (data) {
            t.notOk(data && data.length, "Events should not be returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Gets Events with wrong values", function (t) {
        var params = {
            topicId: "wrong topicId value"
        };
        var resCb = function (data) {
            t.notOk(data && data.length, "Events should not be returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Gets Events with undefined values", function (t) {
        var params = { };
        var resCb = function (data) {
            t.notOk(data && data.length, "Events should not be returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
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
    var req = expressValidatorStub({
        params: params
    });

    var res = { send: resCb };
    var cmd = require('../handlers/getEventsForCollage.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}
