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
var companyId=0;
var logoURL="I'm a bigger test URL";

    t.test('Setup', function (t) {
        ifTestHelpers.company.createCompany(
            {
                client_company_logo_thumbnail_url:"I'm a test URL",
                client_company_logo_url:logoURL
            }
            )
            .done(function (info) {
                companyId=info.id;
                t.ok(info, 'Company created');
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Returns Company Logo", function (t) {
        var params = {
            id: companyId
        };
        var resCb = function (data) {
            t.ok(data, "Company Logo was returned");
            if(data != undefined)
            {
                t.ok(data.client_company_logo_thumbnail_url);
                t.equal(data.client_company_logo_url, logoURL);
            }
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb);
    });

    t.test("Should not return Logo if any parameter is not passed)", function (t) {
        var params = {};
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

    });

//test("Removes brand project", commonOperations.removeUser);
test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var req = expressValidatorStub({
        params: params
    });

    var res = { send: resCb };

    var cmd = require('../handlers/getClientCompanyLogo.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}