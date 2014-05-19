"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;

test("Connects to the db", commonOperations.dbConnect);

test('updateResourceTests', function (t) {
	var sessionId = 0;
    var topicId = 0;
    var userId = 0;
    var resourceId = 0;
    var json = {
        title: "test-title",
        question: "test-question",
        style: "test-style"
    };
    var jsonSerialized = encodeURI(JSON.stringify(json, null));

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
                return ifTestHelpers.resource.createResource({
                    topic_id: topicId,
                    user_id: info.id,
                    type_id: mtypes.resourceType.vote
                });
            })
            .done(function (info) {
                t.ok(info, 'Resource created');
                resourceId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Update Resource", function (t) {
        var params = {
            id: resourceId,
            JSON: json
        };

        var resCb = function (data) {
            t.ok(data, "Resource was updated");
            t.ok(data.opResult.affectedRows, "At least one row was updated");
            t.equal(data.fields.JSON, jsonSerialized, "Resource.JSON is correct");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Resource with id undefined", function (t) {
        var params = {
            JSON: json
        };

        var resCb = function (data) {
            t.notOk(data, "Vote was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Resource with id == null", function (t) {
        var params = {
            id: null,
            JSON: json
        };

        var resCb = function (data) {
            t.notOk(data, "Vote was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Resource with wrong id value", function (t) {
        var params = {
            id: "wrong voteId value",
            JSON: json
        };

        var resCb = function (data) {
            t.notOk(data, "Vote was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Resource with JSON undefined", function (t) {
        var params = {
            id: resourceId
        };

        var resCb = function (data) {
            t.notOk(data, "Vote was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Resource with JSON == null", function (t) {
        var params = {
            id: resourceId,
            JSON: null
        };

        var resCb = function (data) {
            t.notOk(data, "Vote was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Resource with wrong JSON value", function (t) {
        var params = {
            id: resourceId,
            JSON: 256
        };
        var resCb = function (data) {
            t.notOk(data, "Vote was updated");
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

    var cmd = require('../handlers/updateResource.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}