"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Gets participants', function (t) {
	var companyId = 0;
    var brandPId=0;
    var sessionId=0;

	t.test("Sets up a session, topic and user", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
			.done(function (info) {
				t.ok(info, 'Contact 2 created');
                companyId=info.companyId;
                brandPId=info.brandProjectId;
                sessionId=info.id;
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

        t.test("Get brand project by company, filtered", function (t) {
            var params = {
                companyId:companyId,
                sidx:"name",
                sord:"asc",
                start:0,
                limit:10
            };
            var resCb = function (data) {
                t.ok(data, "Info was returned");
                if (data!=undefined)
                {
                    t.equals(data.length, 1, "Found The info");
                    if ((data[0]!=undefined)&&(data[1]!=undefined))
                    {
                        t.equals(data[0].brand_project_id, brandPId, "project match");
                        t.equals(data[0].client_company_id, companyId, "company match");

                    }


                }
                t.end();
            };
            var nextCb = function (data) {
                t.notOk(data, "No errors should have been thrown, received: " + data);
                t.end();
            };
        run(params, resCb, nextCb)
    });


    t.test("Get brand project by company, unfiltered", function (t) {
        var params = {
            sidx:"name",
            sord:"asc",
            start:0,
            limit:10
        };
        var resCb = function (data) {
            t.ok(data, "Info was returned");
            if (data!=undefined)
            {
                t.ok((data.length>=1), "Found The info");
            }
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };
        run(params, resCb, nextCb)
    });

    t.test("Should not return info if some of the ordering data is not provided (sord)", function (t) {
        var params = {sord:"sword"};
        var resCb = function (data) {
            t.notOk(data, "Info should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error should occur: " + data);
            t.end();
        };

        run(params, resCb, nextCb)
    });


    t.test("Should not return info if some of the ordering data is not provided (start,limit)", function (t) {
        var params = {start:0, limit:42};
        var resCb = function (data) {
            t.notOk(data, "Info should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error should occur: " + data);
            t.end();
        };

        run(params, resCb, nextCb)
    });

    t.test("Should not return info if some of the ordering data is not provided (no sord)", function (t) {
        var params = {sidx:"name_first",
            start:0,
            limit:10};
        var resCb = function (data) {
            t.notOk(data, "Info should not be returned");
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
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
	var req = expressValidatorStub({
		params: params
	});

	var res = { send: resCb };

	var cmd = require('../handlers/getBrandProject.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}