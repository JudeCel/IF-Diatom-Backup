"use strict";
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var ifData = require('if-data'), db = ifData.db;
var test = require('tap').test;
var util = require('util');
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifCommon = require('if-common');
var mtypes = ifCommon.mtypes;
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('getVoteTests', function (t) {
	var sessionId = 0;
    var topicId = 0;
    var userId = 0;
    var resourceId = 0;

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

    t.test("Get Resource", function (t) {
	    var params = {
		    id: resourceId
	    };
        var resCb = function (data) {
            t.ok(data, "Vote was returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Get Resource with voteId undefined", function (t) {
	    var params = {};
        var resCb = function (data) {
            t.notOk(data, "Vote was returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Get Resource with wrong voteId value", function (t) {
	    var params = {
		    id: "wrong value type"
	    };
        var resCb = function (data) {
            t.notOk(data, "Vote was returned");
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

    var cmd = require('../handlers/getResource.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}