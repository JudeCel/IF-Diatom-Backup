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
    var user1Id= 0,user2Id=0;
    var user1Name="Twofer", user2Name="Fourman";

	t.test("Sets up a session, topic and user", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'BPS created');
                t.ok(info.sessionId, 'session ID valid');
                companyId=info.companyId;
                brandPId=info.brandProjectId;
                sessionId=info.sessionId;
                return  ifTestHelpers.user.createUser({name_first:user1Name})
            })
            .then(function (info) {
                t.ok(info, 'user 1 created');
                t.ok(info.id, 'user 1 id is valid');
                user1Id=info.id;
                return ifTestHelpers.session.createSessionStaff({
                    session_id:sessionId,
                    type_id:2,
                    user_id:info.id
                });
            })
            .then(function (info) {
                t.ok(info, 'staff 1 created');
                return  ifTestHelpers.company.createClientUser({
                    user_id:user1Id,
                    client_company_id:companyId
                })
            })
            .then(function (info) {
                t.ok(info, 'client-user 1 created');
                return  ifTestHelpers.user.createUser({name_first:user2Name})
            })
            .then(function (info) {
                t.ok(info, 'user 2 created');
                t.ok(info.id, 'user 2 id is valid');
                user2Id=info.id;
                return ifTestHelpers.session.createSessionStaff({
                    session_id:sessionId,
                    type_id:4,
                    user_id:info.id
                });
            })
            .then(function (info) {
                t.ok(info, 'staff 2 created');
                return  ifTestHelpers.company.createClientUser({
                    user_id:user1Id,
                    client_company_id:companyId
                })
            })
			.done(function (info) {
				t.ok(info, 'client-user 2 created');
				t.end();
			}, function (err) {
				t.fail(err);
				t.end();
			});
	});

        t.test("Get session user, correct type", function (t) {
            var params = {
                sidx:"name",
                sord:"asc",
                start:0,
                limit:10,
                userId:user1Id,
                type:2
            };
            var resCb = function (data) {
                t.ok(data, "Info was returned");
                if (data!=undefined)
                {
                    t.equals(data.length, 1, "Found The User");
                    if ((data[0]!=undefined)&&(data[1]!=undefined))
                    {
                        t.equals(data[0].name_first, user1Name, "Found The User");
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

    t.test("Get session user, correct type, with companyId", function (t) {
        var params = {
            sidx:"name",
            sord:"asc",
            start:0,
            limit:10,
            userId:user2Id,
            type:4,
            companyId:companyId
        };
        var resCb = function (data) {
            t.ok(data, "Info was returned");
            if (data!=undefined)
            {
                t.equals(data.length, 1, "Found The User");
                if ((data[0]!=undefined)&&(data[1]!=undefined))
                {
                    t.equals(data[0].name_first, user2Name, "Found The User");
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

    t.test("Should not return users of wrong type", function (t) {
        var params = {
            sidx:"name",
            sord:"asc",
            start:0,
            limit:10,
            userId:user1Id,
            type:4};
        var resCb = function (data) {
            t.ok(data, "Something (empty or not) was returned");
            if (data!=undefined)
            {
                t.equals(data.length, 0, "User List Empty");
            }
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

    t.test("Removes client contact (1)", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.user.removeUser({userIds: [user1Id]})).done();
    });

    t.test("Removes client contact (2)", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.user.removeUser({userIds: [user2Id]})).done();
    });
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
	var req = expressValidatorStub({
		params: params
	});

	var res = { send: resCb };

	var cmd = require('../handlers/getSessionDataForGridByUser.js');
	cmd.validate(req, function (err) {
		if (err) return nextCb(err);
		cmd.run(req, res, nextCb);
	});
}