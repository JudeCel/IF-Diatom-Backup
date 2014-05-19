"use strict";
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var ifData = require('if-data'), db = ifData.db;
var test = require('tap').test;
var util = require('util');
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifCommon = require('if-common');
var mtypes = ifCommon.mtypes;
var testUtils = ifTestHelpers.utils;
var getResourcesGeneric = require('../handlers/getResourcesGeneric.js');

test("Connects to the db", commonOperations.dbConnect);

test('getResourcesGenericTests', function (t) {
    var sessionId = 0;
    var topic1Id = 0;
    var topic2Id = 0;
    var user1Id = 0;
    var user2Id = 0;
    var resource1Id = 0;
    var resource2Id = 0;
    var resource3Id = 0;

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
                sessionId = info.sessionId;
                return ifTestHelpers.topic.createTopic({
                    session_id: sessionId
                });
            })
            .then(function (info) {
                t.ok(info, 'Topic 1 created');
                topic1Id = info.id;
                return ifTestHelpers.topic.createTopic({
                    session_id: sessionId
                });
            })
            .then(function (info) {
                t.ok(info, 'Topic 2 created');
                topic2Id = info.id;
                return ifTestHelpers.user.createUser();
            })
            .then(function (info) {
                t.ok(info, 'User 1 created');
                user1Id = info.id;
                return ifTestHelpers.user.createUser();
            })
            .then(function (info) {
                t.ok(info, 'User 2 created');
                user2Id = info.id;
                return ifTestHelpers.resource.createResource({
                    type_id: mtypes.resourceType.participant,
                    topic_id: topic1Id,
                    user_id: user1Id
                });
            })
            .then(function (info) {
                t.ok(info, 'Resource 1 created');
                resource1Id = info.id;
                return ifTestHelpers.resource.createResource({
                    type_id: mtypes.resourceType.video,
                    topic_id: topic2Id,
                    user_id: user2Id
                });
            })
            .then(function (info) {
                t.ok(info, 'Resource 2 created');
                resource2Id = info.id;
                return ifTestHelpers.resource.createResource({
                    type_id: mtypes.resourceType.tmp,
                    topic_id: topic1Id,
                    user_id: user2Id
                });
            })
            .done(function (info) {
                t.ok(info, 'Resource 3 created');
                resource3Id = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets Resource 1 by id", function (t) {
        var params = {
            id: resource1Id
        };
        var resCb = function (data) {
            t.ok(data && data.length, "Resources were returned");
            t.equal(data[0].id, resource1Id, "Resource is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        getResourcesGeneric.execute(params, resCb, nextCb)
    });

    t.test("Gets Resource 1 & 3 by topic_id", function (t) {
        var params = {
            topic_id: topic1Id
        };
        var resCb = function (data) {
            t.ok(data && data.length == 2, "Resources were returned");
            t.equal(data[0].id, resource1Id, "Resource is correct");
            t.equal(data[1].id, resource3Id, "Resource is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        getResourcesGeneric.execute(params, resCb, nextCb)
    });

    t.test("Gets Resource 2 & 3 by user_id", function (t) {
        var params = {
            user_id: user2Id
        };
        var resCb = function (data) {
            t.ok(data && data.length == 2, "Resources were returned");
            t.equal(data[0].id, resource2Id, "Resource is correct");
            t.equal(data[1].id, resource3Id, "Resource is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        getResourcesGeneric.execute(params, resCb, nextCb)
    });

    t.test("Gets Resource 1 by topid_id & type_id", function (t) {
        var params = {
            type_id: mtypes.resourceType.participant,
            topic_id: topic1Id
        };
        var resCb = function (data) {
            t.ok(data && data.length == 1, "Resources were returned");
            t.equal(data[0].id, resource1Id, "Resource is correct");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        getResourcesGeneric.execute(params, resCb, nextCb)
    });

    t.test("Gets all Resources", function (t) {
        var params = {
        };
        var resCb = function (data) {
            t.ok(data && data.length, "All Resources were returned");
            t.end();
        };
        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        getResourcesGeneric.execute(params, resCb, nextCb)
    });

    t.test("Gets Resources by wrong values", function (t) {
        var params = {
            id: "wrong id value",
            type_id: "wrong type_id value",
            topic_id: "wrong topic_id value",
            user_id: "wrong user_id value"
        };
        var resCb = function (data) {
            t.notOk(data && data.length, "Resources should not be returned");
            t.end();
        };
        var nextCb = function (data) {
            t.ok(data, "Validation error: " + data);
            t.end();
        };

        getResourcesGeneric.execute(params, resCb, nextCb)
    });

	t.test("Removes session", function (t) {
		testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
	});
});

test("Can disconnect from DB", commonOperations.dbDisconnect);