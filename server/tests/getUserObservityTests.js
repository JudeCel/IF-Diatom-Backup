"use strict";
var util = require('util');
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var dataHelper = require("../helpers/dataHelper");

test("Connects to the db", commonOperations.dbConnect);

test('Gets user login info', function (t) {
    var login = "test-user-made-"+dataHelper.getTimestamp().toString();
    var password= "";
    var userId=0;
    var sessionId=0;
    var companyId=0;
    var loginId=0;

    t.test('Setup', function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function(info) {
                t.ok(info, 'Session created');
                t.ok(info.sessionId, 'Session id is valid');
                sessionId = info.sessionId;
                companyId= info.companyId;
                return ifTestHelpers.user.createUserLogin(
                    {username:login}
                );
            })
            .then(function (info) {
                t.ok(info, 'User Login created');
                loginId= info.id;
                password = info.password;
                return  ifTestHelpers.user.createUser(
                    {user_login_id:info.id}
                )
            })
            .then(function (info) {
                t.ok(info, 'User created');
                t.ok(info.id, 'User id is valid');
                userId= info.id;
                return ifTestHelpers.company.createClientUser({
                    user_id: info.id,
                    client_company_id:companyId,
                    type_id:1
                });
            })
            .then(function (info) {
                t.ok(info, 'Client user created');
                return ifTestHelpers.session.createSessionStaff({
                    user_id: info.user_id,
                    type_id:4,
                    session_id:sessionId
                });
            })
            .done(function (info) {
                t.ok(info, 'session staff created');
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Returns user observity data", function (t) {
        var params = {
            login: login,
            password: password
        };
        var resCb = function (data) {
            t.ok(data, "User observity data was returned");
            if(data != undefined)
            {
                t.equal(data.user_id, userId);
                t.equal(data.client_company_id, companyId);
                t.equal(data.ul_id, loginId);
            }
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Should not return user observity data if any parameter is not passed (login)", function (t) {
        var params = {
            login: login
        };
        var resCb = function (data) {
            t.notOk(data, "User login was returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Should not return user observity data if any parameter is not passed (password)", function (t) {
        var params = {
            password: password
        };
        var resCb = function (data) {
            t.notOk(data, "User login was returned");
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
    t.test("Removes user", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.user.removeUser({userIds: [userId]})).done();
    });

});

//test("Removes brand project", commonOperations.removeUser);
test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var req = expressValidatorStub({
        params: params
    });

    var res = { send: resCb };

    var cmd = require('../handlers/getUserObservity.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}