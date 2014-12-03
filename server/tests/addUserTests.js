"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var ifTestHelpers = require('if-test-helpers');
var ifCommon = require('if-common');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;

test("Connects to the db", commonOperations.dbConnect);

test('addUserTests', function (t) {

    var userId1 = 0;
    var userId2 = 0;

    t.test("Add user", function (t) {
        
        var uuid = ifCommon.utils.uuidHelper.generateUUID();

        var params = {
            body: {
              name_first: "test_name_first",
              name_last: "test_name_last",
              gender: "Male",
              email: uuid+"@test.com"
            },
            locals: {
              accountId: 0
            }
        };

        var resCb = function (data) {
            t.ok(data, "User added");
            userId1 = data.id;
            t.end();
        };
        var nextCb = function (err) {
            t.notok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Add user should fail if no required parameters passed", function (t) {
        var params = {
            body: {
              name_first: null,
              name_last: null,
              gender: null,
              email: null
            },
            locals: {
              accountId: 0
            }
        };
        var resCb = function (data) {
            userId2 = data.id;
            t.notOk(data.id, "User added");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Remove user", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.user.removeUser({userIds: [userId1, userId2]})).done();
    });
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var req = expressValidatorStub( params );
    var cmd = require('../handlers/addUser.js');
    var res = { send: resCb };

    cmd.validate(req, res, function (err) {
        if (err) return nextCb(err);
            cmd.run(req, res, nextCb);
    });
}

