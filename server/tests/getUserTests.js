"use strict";
var util = require('util');
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Creates predefined data sets', function (t) {
    var userId = 0;

    t.test('Creates a User', function (t) {
        ifTestHelpers.user.createUser()
            .done(function (info) {
                t.ok(info, 'User created');
                userId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets User", function (t) {
        var params = {
            user_id: userId
        };
        var resCb = function (data) {
            t.ok(data, "User was returned");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets User with undefined userId", function (t) {
        var params = {};
        var resCb = function (data) {
            t.notOk(data, "User should not be returned");
            t.end();
        }

        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Gets User with wrong userId value", function (t) {
        var params = {
            userId: "incorrect valu type"
        };
        var resCb = function (data) {
            t.notOk(data, "User should not be returned");
            t.end();
        }

        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Removes session", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.user.removeUser({userIds: [userId]})).done();
    });
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var req = expressValidatorStub({
        params: params
    });

    var res = { send: resCb };
    var getUser = require('../handlers/getUser.js');
    getUser.validate(req, function (err) {
        if (err) return nextCb(err);
        getUser.run(req, res, nextCb);
    });
}