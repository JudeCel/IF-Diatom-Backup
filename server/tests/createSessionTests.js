"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;

test("Connects to the db", commonOperations.dbConnect);

test("Creates session", function (t) {
	var sessionId = 0;
	var brandProjectId = 0;

	t.test("Setup a company and a brand project", function (t) {
		ifTestHelpers.company.createCompany()
			.then(function (info) {
				sessionId = info.sessionId;
				return ifTestHelpers.brandProject.createBrandProject({
					client_company_id: info.id,
					name: "test-brand-project"
				});
			})
			.done(function (info) {
				brandProjectId = info.id;
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

	t.test("Create a session", function(t) {
		var resCb = function (data) {
			t.ok(data, "New session was returned");
			t.end();
		};
		var nextCb = function (err) {
			t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
			t.end();
		};
		run(brandProjectId, resCb, nextCb);
	})

	t.test("Removes session", function (t) {
		testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
	});
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(brandProjectId, resCb, nextCb) {
	var req = expressValidatorStub({
		params: {
			brand_project_id: brandProjectId,
			name: "test-session",
			start_time: '2013-1-1', //ifCommon.utils.dateHelper.formatDateFromDatestamp('1/1/2013'),
			end_time: '2015-1-1', //ifCommon.utils.dateHelper.formatDateFromDatestamp('1/1/2020'),
			status_id: mtypes.statusLookup.active
		}
	});

	var res = { send: resCb };

	var cmd = require('../handlers/createSession.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}