"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('createEventTest', function (t) {
	var sessionId = 0;
    var topicId = 0;
    var userId = 0;

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
		        sessionId = info.sessionId;
                return ifTestHelpers.topic.createTopic({
	                session_id: info.sessionId
                });
            })
            .then(function (info) {
                t.ok(info, 'Topic created');
                topicId = info.id;
                return ifTestHelpers.user.createUser();
            })
            .done(function (info) {
                t.ok(info, 'User created');
                userId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Create Event", function (t) {
        var params = {
            user_id: userId,
            topic_id: topicId,
            tag: 16
        };

        var resCb = function (data) {
            t.ok(data, "Event was created");
            t.equals(data.user_id, params.user_id, "Event user_id is correct");
            t.equals(data.topic_id, params.topic_id, "Event topic_id is correct");
            t.equals(data.tag, params.tag, "Event tag is correct");
            t.end();
        };
        var errCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, errCb);
    });

    t.test("Create Event with values undefined", function (t) {
        var params = {
        };

        var resCb = function (data) {
            t.notOk(data, "Event was created");
            t.end();
        };
        var errCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, errCb);
    });

    t.test("Create Event with values == null", function (t) {
        var params = {
            user_id: null,
            topic_id: null,
            tag: null,
            timestamp: null
        };

        var resCb = function (data) {
            t.notOk(data, "Event was created");
            t.end();
        };

        var errCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, errCb);
    });

    t.test("Create Event with wrong values", function (t) {
        var params = {
            user_id: "wrong user_id value",
            topic_id: "wrong topic_id value",
            tag: "wrong tag value",
            timestamp: "wrong timestamp value"
        };

        var resCb = function (data) {
            t.notOk(data, "Event was created");
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

    var cmd = require('../handlers/createEvent.js');
    cmd.validate(req, function (err) {
        if (err) return errCb(err);
        cmd.run(req, res, errCb);
    });
}