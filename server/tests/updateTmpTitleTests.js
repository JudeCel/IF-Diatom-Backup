"use strict";
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var ifData = require('if-data'), db = ifData.db;
var test = require('tap').test;
var util = require('util');
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('updateTmpTitleTests', function (t) {
    var content = {
        title: "title",
        text: new Date()
    };
    var json = encodeURI(JSON.stringify(content, null));
    var userId = 0;
    var sessionId = 0;
    var topicId = 0;

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
                sessionId = info.sessionId;
                return ifTestHelpers.user.createUser();
            })
            .then(function (info) {
                t.ok(info, 'User created');
                userId = info.id;
                return ifTestHelpers.topic.createTopic({
                    session_id: sessionId
                });
            })
            .done(function (info) {
                t.ok(info, 'Topic created');
                topicId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Update Tmp Title", function (t) {
        var params = {
            user_id: userId,
            topic_id: topicId,
            URL: "url",
            JSON: content
        };

        var resCb = function (data) {
            t.equal(data.JSON, json, "Tmp Title was updated and is correct");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Tmp Title with wrong values", function (t) {
        var params = {
            user_id: "wrong userId value",
            topic_id: "wrong topic_id value",
            URL: 8,
            JSON: 2
        };

        var resCb = function (data) {
            t.notOk(data, "Tmp Title was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Tmp Title with undefined values", function (t) {
        var params = {
        };
        var resCb = function (data) {
            t.notOk(data, "Tmp Title was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update Tmp Title with values == null", function (t) {
        var params = {
            user_id: null,
            topic_id: null,
            URL: null,
            JSON: null
        };

        var resCb = function (data) {
            t.notOk(data, "Tmp Title was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "No errors should have been thrown, received: " + err);
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

    var cmd = require('../handlers/updateTmpTitle.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}

module.exports = run;