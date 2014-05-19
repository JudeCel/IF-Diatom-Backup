"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Gets participants', function (t) {
	var companyId = 0;
    var addressId =0;
    var contact1Id=0;
    var contact2Id=0;
    var companyName="TestCo";
    var street="tester st. 3/14";
    var name="Sir Testerton";

	t.test("Sets up a session, topic and user", function (t) {
        ifTestHelpers.company.createAddress(
            {
                street:street,
                suburb:"Testown",
                state:"Untested",
                post_code:"TST-4242"
            })
			.then(function (info) {
				t.ok(info, 'Address created');
				t.ok(info.id, 'Address Id is valid');
                addressId=info.id;
				return ifTestHelpers.company.createCompany(
                    {name:companyName,
                        URL:"t",
                        ABN:"t",
                        number_of_brands:1,
                        self_moderated:"IF",
                        comments:"none",
                        address_id:addressId
                    }
                );
			})
            .then(function (info) {
                t.ok(info, 'Company created');
                t.ok(info.id, 'Company Id is valid');
                companyId = info.id;
                return ifTestHelpers.company.createClientCompanyContacts({
                    client_company_id: info.id,
                    name_first:name,
                    name_last:"Von Test",
                    phone:"555-1234",
                    mobile:"555-9876",
                    contact_type_id:2,
                    email:"e@mail.com"
                });
            })
			.then(function (info) {
                t.ok(info, 'Conact 1 created');
                t.ok(info.id, 'Contact Id is valid');
                contact1Id = info.id;
				return ifTestHelpers.company.createClientCompanyContacts({
					client_company_id: companyId,
                    name_first:"Hihu",
                    name_last:"Flungdung",
                    phone:"4242-2424",
                    mobile:"4242-9999",
                    contact_type_id:2,
                    email:"e@mail.com"
				});
			})
			.done(function (info) {
				t.ok(info, 'Contact 2 created');
                contact2Id=info.id;
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

        t.test("Get company info ordered, filtered", function (t) {
            var params = {
                companyId:companyId,
                sidx:"name_first",
                sord:"asc",
                start:0,
                limit:10
            };
            var resCb = function (data) {
                t.ok(data, "Info was returned");
                if (data!=undefined)
                {
                    t.equals(data.length, 2, "Found The info");
                    if ((data[0]!=undefined)&&(data[1]!=undefined))
                    {
                        t.equals(data[1].name, companyName, "company match");
                        t.equals(data[1].street, street, "address match");
                        t.equals(data[1].name_first, name, "contact match");
                        //if value of name is changed, make sure it's still after H, or change the reference.
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
		testUtils.tapExpectFulfillment(t, ifTestHelpers.company.removeCompany({sessionIds: [companyId]})).done();
	});
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
	var req = expressValidatorStub({
		params: params
	});

	var res = { send: resCb };

	var cmd = require('../handlers/getClientCompanyInfo.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}