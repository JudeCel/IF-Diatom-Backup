"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Create Log', function (t) {
    var userId = 0;
	var sessionId = 0;

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Session created');
		        sessionId = info.sessionId;
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

    t.test("Create Log", function (t) {
        var params = {
            user_id: userId,
            timestamp: 999
        };

        var resCb = function (data) {
            t.ok(data, "Log was created");
            t.equals(data.user_id, params.user_id, "Log user_id is correct");
            t.equals(data.timestamp, params.timestamp, "Log timestamp is correct");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Create Log should fail if no parameters passed", function (t) {
        var params = {};
        var resCb = function (data) {
            t.notOk(data, "Log was created");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Create Log should fail with improper parameters", function (t) {
        var params = {
	        user_id: null,
	        timestamp: null
        };

        var resCb = function (data) {
            t.notOk(data, "Log was created");
            t.end();
        };

        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Create Event with wrong values", function (t) {
        var params = {
	        user_id: "incorrect value type",
	        timestamp: "incorrect value type"
        };

        var resCb = function (data) {
            t.notOk(data, "Event was created");
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

    var cmd = require('../handlers/createLog.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}