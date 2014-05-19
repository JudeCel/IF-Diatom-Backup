"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test("Gets Brand Project", function (t) {
    var sessionId = 0;

    t.test("Creates Company, Project, Session", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .done(function (info) {
                sessionId = info.sessionId;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets Brand Project Info", function(t) {
	    var params = {
		    sessionId: sessionId
	    };
        var resCb = function (data) {
            t.ok(data, "Brand Project Info was returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Gets Brand Project Info with undefined sessionId", function(t) {
	    var params = {
		    sessionId: null
	    };
        var resCb = function (data) {
            t.notOk(data, "Brand Project Info should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error: " + data);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Gets Brand Project Info with wrong sessionId value", function(t) {
	    var params = {
		    sessionId: "should be a number"
	    };
        var resCb = function (data) {
            t.notOk(data, "Brand Project Info should not be returned");
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

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var req = expressValidatorStub({
        params: params
    });

    var res = { send: resCb };
    var cmd = require('../handlers/getBrandProjectInfo.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}