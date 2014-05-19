"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Gets participants', function (t) {
	var userId = 0;
    var sessionId=0;
    var userName="TestUser Name";

    t.test("Sets up a session, topic and user", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'BPS created');
                t.ok(info.sessionId, 'session ID valid');
                sessionId=info.sessionId;
                return  ifTestHelpers.user.createUser({name_first:userName})
            })
            .then(function (info) {
                t.ok(info, 'user  created');
                t.ok(info.id, 'user id is valid');
                userId=info.id;
                return ifTestHelpers.session.createSessionStaff({
                    session_id:sessionId,
                    type_id:2,
                    user_id:info.id
                });
            })
            .done(function (info) {
                t.ok(info, 'staff created');
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

        t.test("Get session mod", function (t) {
            var params = {sessionId:sessionId};
            var resCb = function (data) {
                t.ok(data, "Info was returned");
                t.end();
            };
            var nextCb = function (data) {
                t.notOk(data, "No errors should have been thrown, received: " + data);
                t.end();
            };
        run(params, resCb, nextCb)
    });



    t.test("Should not return Mod if some  data is not provided", function (t) {
        var params = {};
        var resCb = function (data) {
            t.notOk(data, "Mod should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error should occur: " + data);
            t.end();
        };

        run(params, resCb, nextCb)
    });


	t.test("Removes client company and contact", function (t) {
		testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
	});

    t.test("Removes client company and contact", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.user.removeUser({userIds: [userId]})).done();
    });


});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
	var req = expressValidatorStub({
		params: params
	});

	var res = { send: resCb };

	var cmd = require('../handlers/getSessionMod.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}