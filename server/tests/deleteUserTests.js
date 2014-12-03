"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var _ = require('lodash');
var getUser = require('if-data').repositories.getUser;

test("Connects to the db", commonOperations.dbConnect);

test('Delete user', function (t) {
  var userId = 0;

  t.test("Creates predefined data sets", function (t) {
    ifTestHelpers.user.addUser({})
        .done(function (info) {
          userId = info.id;
          t.ok(info, 'User Added');
          t.end();
        }, function (err) {
          t.fail(err);
          t.end();
      });
  });

  t.test("Delete user", function (t) {

    var params = {
      query: {userId: userId}
    };

    var resCb = function (data) {
      getUser(params.query)
        .done(function (data) {
          t.notEqual(data.deleted, null, "User deleted ok");
          t.end();
        }, function (err) {
          t.fail(err);
          t.end();
      });
    };

    var nextCb = function (data) {
      t.notOk(data, "No errors should have been thrown, received: " + data);
      t.end();
    }

    run(params, resCb, nextCb);

  });

  t.test("Should not delete when userId is not passed", function (t) {
    var params = {
      query: {userId: null}
    };

    var resCb = function (data) {
      t.notOk(data, "User should not be returned");
      t.end();
    };

    var nextCb = function (data) {
      t.ok(data, "Validation error: " + data);
      t.end();
    };

    run(params, resCb, nextCb);
  });
  
  t.test("Removes users", function (t) {
    testUtils.tapExpectFulfillment(t, ifTestHelpers.user.removeUser({userIds: [userId]})).done();
  });

});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var req = expressValidatorStub( params );
    var cmd = require('../handlers/deleteUser.js');
    var res = { send: resCb };

    cmd.validate(req, res, function (err) {
        if (err) return nextCb(err);
            cmd.run(req, res, nextCb);
    });
}

