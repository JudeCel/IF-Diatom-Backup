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

	t.test('Setup', function (t) {
		ifTestHelpers.user.createUser()
			.then(function (info) {
				t.ok(info, 'User created');
				t.ok(info.id, 'User id is valid');
                userId= info.id;
                console.log("userId::",userId);
				return ifTestHelpers.user.createUserLogin({
					user_id: info.id,
                    username:login
				});
			})
			.done(function (info) {
                //login = info.username;
                password = info.password;
				t.ok(info, 'User login created');
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Returns user login info", function (t) {
		var params = {
			login: login,
			password: password
		};
		var resCb = function (data) {
			t.ok(data, "User login was returned");
            if(data != undefined)
			    t.equal(data.id, userId);
			t.end();
		};
		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
			t.end();
		};

		run(params, resCb, nextCb);
	});

    t.test("Returns no user login info on incorrect data", function (t) {
        var params = {
            login: "I'm not a real user, because I'm a GULbP test record",
            password: "I'm not a real password, because I'm a GULbP test password. and, you know, not a hash."
        };
        var resCb = function (data) {
            t.equal(data, null,"empty data");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

	t.test("Should not return user login info if any parameter is not passed", function (t) {
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

	var cmd = require('../handlers/getUserLogin_byPassword.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}