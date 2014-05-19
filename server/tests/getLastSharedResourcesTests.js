"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Creates predefined data sets', function (t) {
    var sessionId = 0;
    var topicId = 0;
    var userId = 0;
    var event1Id = 0;
    var event2Id = 0;

    t.test("Creates Company, Project, Session", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
                sessionId = info.sessionId;
                var topic = {
                    session_id: sessionId
                };
                return ifTestHelpers.topic.createTopic(topic);
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
                    user_id: userId,
                    tag: 8, //NOT interested IN images
                    cmd: 'shareresource'
                };
                return ifTestHelpers.event.createEvent(newEvent);
            })
            .then(function (info) {
                t.ok(info, 'Event 1 created');
                event1Id = info.id;
                var newEvent = {
                    topic_id: topicId,
                    user_id: userId,
                    tag: 8, //NOT interested IN images
                    cmd: 'shareresource'
                };
                return ifTestHelpers.event.createEvent(newEvent);
            })
            .then(function (info) {
                t.ok(info, 'Event 2 created');
                event2Id = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets Last Shared Resources", function (t) {
	    var params = {
		    topicId: topicId
		};
        var resCb = function (data) {
            t.ok(data && data.length, "Last Shared Resources were returned");
            t.end();
        }

        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets Last Shared Resources with undefined topicId", function (t) {
	    var params = {
		    topicId: null
	    };
        var resCb = function (data) {
            t.notOk(data && data.length, "Last Shared Resources should not be returned");
            t.end();
        }

        var nextCb = function (data) {
            t.ok(data, "Validation error: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets Last Shared Resources with wrong topicId value", function (t) {
	    var params = {
		    topicId: "wrong topicId format"
	    };
        var resCb = function (data) {
            t.notOk(data && data.length, "Last Shared Resources should not be returned");
            t.end();
        }

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

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var req = expressValidatorStub({
        params: params
    });

    var res = { send: resCb };
    var getUser = require('../handlers/getLastSharedResources.js');
    getUser.validate(req, function (err) {
        if (err) return nextCb(err);
        getUser.run(req, res, nextCb);
    });
}