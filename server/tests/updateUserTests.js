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

test('updateUserTests', function (t) {
    var sessionId = 0;
    var userId = 0;
    var avatarInfo = "12 3 45 six";

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
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

    t.test("Update User Info", function (t) {
        var params = {
            id: userId,
            avatar_info: avatarInfo
        };
        var resCb = function (data) {
            t.ok(data, "User was updated");
            t.ok(data.opResult.affectedRows, "At least one row was updated");
            t.equal(data.fields.avatar_info, avatarInfo, "Update is correct");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update User Info with wrong values", function (t) {
        var params = {
            id: "wrong userId value",
            avatar_info: 123
        };

        var resCb = function (data) {
            t.notOk(data, "User was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update User Info with undefined values", function (t) {
        var params = {};
        var resCb = function (data) {
            t.notOk(data, "User was updated");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Update User Info with values == null", function (t) {
        var params = {
            id: null,
            avatar_info: null
        };
        var resCb = function (data) {
            t.notOk(data, "User was updated");
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

    var cmd = require('../handlers/updateUser.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}