"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Update Topic Tests', function (t) {
    var topicId = 0;
	var sessionId = 0;
    var userId = 0;
    var eventId = 0;
    var newTag = 312;

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
		        sessionId = info.sessionId;
                var newTopic = {
                    session_id: info.sessionId
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
                var newEvent = {
                    topic_id: topicId,
                    user_id: userId
                };
                return ifTestHelpers.event.createEvent(newEvent);
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

    t.test("Update Event", function (t) {
        var params = {
            id: eventId,
            tag: newTag,
            user_id: userId,
            topic_id: topicId,
            thumbs_up: 0
        };

        var resCb = function (data) {
            t.ok(data, "Event was updated");
            t.ok(data.opResult.affectedRows, "At least one row was updated");
            t.equal(data.fields.tag, newTag, "Update is correct");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Event with values undefined", function (t) {
        var params = {
        };

        var resCb = function (data) {
            t.notOk(data, "Event was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Event with values == null", function (t) {
        var params = {
            id: null,
            tag: null
        };

        var resCb = function (data) {
            t.notOk(data, "Event was updated");
            t.end();
        };

        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Event with wrong values", function (t) {
        var params = {
            id: "wrong id value",
            tag: "wrong tag value"
        };

        var resCb = function (data) {
            t.notOk(data, "Event was updated");
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

    var cmd = require('../handlers/updateEvent.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}