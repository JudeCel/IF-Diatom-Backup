"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;

test("Connects to the db", commonOperations.dbConnect);

test('updateResourcesTests', function (t) {
	var sessionId = 0;
    var resourceId = 0;
	var topicId = 0;

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Session created');
		        sessionId = info.sessionId;
                return ifTestHelpers.topic.createTopic({
	                session_id: info.sessionId
                });
            })
	        .then(function (info) {
		        t.ok(info, 'Topic created');
		        topicId = info.id;
		        return ifTestHelpers.user.createUser();
	        })
	        .then(function (info) {
		        t.ok(info, 'User created');
		        return ifTestHelpers.resource.createResource({
			        topic_id: topicId,
			        user_id: info.id,
			        type_id: mtypes.resourceType.participant
		        });
	        })
            .done(function (info) {
                t.ok(info, 'Resource created');
		        t.ok(info.id, 'Resource Id is valid');
		        resourceId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Update resources", function (t) {
	    var params = {
		    id: resourceId,
			type_id: mtypes.resourceType.facilitator,
		    url: "test-resource-url"
	    };
        var resCb = function (data) {
            t.ok(data, "Resource was updated");
            t.ok(data.opResult.affectedRows, "At least one row was updated");
	        t.equal(data.fields.type_id, params.type_id, "Resource type is updated");
	        t.equal(data.fields.url, params.url, "Resource url is updated");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + err);
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

    var cmd = require('../handlers/updateResources.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}