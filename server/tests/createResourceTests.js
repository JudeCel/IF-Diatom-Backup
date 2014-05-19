"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;
var util = require("util");

test("Connects to the db", commonOperations.dbConnect);

test("Creates resource", function (t) {
	var sessionId = 0;

	t.test("Setup a company, brand project and session", function (t) {
		ifTestHelpers.session.createCompanyProjectSession()
			.done(function (info) {
				t.ok(info, 'Session created');
				sessionId = info.sessionId;
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Create a resource", function(t) {
		var params = {
			type_id: mtypes.resourceType.participant,
			url: "test-url"
		}
		var resCb = function (data) {
			t.ok(data, "New resource was returned");
			t.end();
		};
		var nextCb = function (err) {
			t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Create a resource should fail if parameters aren't passed", function(t) {
		var params = {
			type_id: null,
			url: "test-url"
		}
		var resCb = function (data) {
			t.notOk(data, "New resource was returned");
			t.end();
		};
		var nextCb = function (err) {
			t.ok(err, "Validation error: " + util.inspect(err));
			t.end();
		};
		run(params, resCb, nextCb);
	});

	t.test("Create a resource should fail if parameters aren't proper", function(t) {
		var params = {
			type_id: null,
			url: 123
		}
		var resCb = function (data) {
			t.notOk(data, "New resource was returned");
			t.end();
		};
		var nextCb = function (err) {
			t.ok(err, "Validation error: " + util.inspect(err));
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

	var cmd = require('../handlers/createResource.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}