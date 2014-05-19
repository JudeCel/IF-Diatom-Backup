"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var ifData = require('if-data');
var test = require('tap').test;
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifCommon = require('if-common');
var mtypes = ifCommon.mtypes;
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('getSessionStaffUserIdsTests', function (t) {
    var sessionId = 0;
    var user1Id = 0;
    var user2Id = 0;
    var sessionStaff1Id = 0;
    var sessionStaff2Id = 0;

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
                sessionId = info.sessionId;
                return ifTestHelpers.user.createUser();
            }).then(function (info) {
                t.ok(info, 'User 1 created');
                user1Id = info.id;
                return ifTestHelpers.user.createUser();
            }).then(function (info) {
                t.ok(info, 'User 2 created');
                user2Id = info.id;
                return ifTestHelpers.session.createSessionStaff({
                    session_id: sessionId,
                    user_id: user1Id,
                    type_id: mtypes.userType.facilitator
                });
            }).then(function (info) {
                t.ok(info, 'Session Staff 1 created');
                sessionStaff1Id = info.id;
                return ifTestHelpers.session.createSessionStaff({
                    session_id: sessionId,
                    user_id: user2Id,
                    type_id: mtypes.userType.observer
                });
            }).done(function (info) {
                t.ok(info, 'Session Staff 2 created');
                sessionStaff2Id = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets Session Staff 1 by type_id & session_id", function (t) {
        var params = {
            type_id: mtypes.userType.facilitator,
            session_id: sessionId
        };
        var resCb = function (data) {
            t.ok(data && data.length == 1, "Session Staff was returned");
            t.equal(data[0].id, sessionStaff1Id, "Result is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets Session Staff 2 by type_id & session_id", function (t) {
        var params = {
            type_id: mtypes.userType.observer,
            session_id: sessionId
        };
        var resCb = function (data) {
            t.ok(data && data.length == 1, "Session Staff was returned");
            t.equal(data[0].id, sessionStaff2Id, "Result is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets all Session Staff", function (t) {
        var params = { };
        var resCb = function (data) {
            t.ok(data && data.length, "Session Staff was returned");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
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

    var cmd = require('../handlers/getSessionStaffUserIds.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}