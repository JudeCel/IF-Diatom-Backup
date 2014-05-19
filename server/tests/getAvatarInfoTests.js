"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test("getAvatarInfoTests", function (t) {
    var projectId = 0;
    var sessionId = 0;
    var participantColourLookupId = 0;
    var participantRatingLookupId = 0;
    var participantReplyLookupId = 0;
    var userId = 0;
    var participantId = 0;
    var participantListRecordId = 0;

    t.test("Creates predefined data sets ", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Company, Project, Session created');
                projectId = info.brandProjectId;
                sessionId = info.sessionId;
                return ifTestHelpers.participant.createParticipantColourLookup();
            })
            .then(function (info) {
                t.ok(info, 'Participant Colour Lookup created');
                participantColourLookupId = info.id;
                return ifTestHelpers.participant.createParticipantRatingLookup();
            })
            .then(function (info) {
                t.ok(info, 'Participant Rating Lookup created');
                participantRatingLookupId = info.id;
                return ifTestHelpers.participant.createParticipantReplyLookup();
            })
            .then(function (info) {
                t.ok(info, 'Participant Reply Lookup created');
                participantReplyLookupId = info.id;
                return ifTestHelpers.user.createUser();
            })
            .then(function (info) {
                t.ok(info, 'User created');
                userId = info.id;
                return ifTestHelpers.participant.createParticipant({
                    user_id: userId,
                    brand_project_id: projectId
                });
            })
            .then(function (info) {
                t.ok(info, 'Participant created');
                participantId = info.id;
                return ifTestHelpers.participant.createParticipantListRecord({
                    session_id: sessionId,
                    participant_id: participantId,
                    participant_reply_id: participantReplyLookupId,
                    participant_rating_id: participantRatingLookupId,
                    participant_colour_lookup_id: participantColourLookupId
                });
            })
            .done(function (info) {
                participantListRecordId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Gets Avatar Info", function (t) {
        var params = {
            userId: userId,
            sessionId: sessionId
        };
        var resCb = function (data) {
            t.ok(data, "Avatar Info was returned");
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Gets Avatar Info with values == null", function (t) {
        var params = {
            userId: null,
            sessionId: null
        };
        var resCb = function (data) {
            t.notOk(data, "Brand Project Info should not be returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Gets Avatar Info with wrong values", function (t) {
        var params = {
            userId: "wrong userId value",
            sessionId: "wrong sessionId value"
        };
        var resCb = function (data) {
            t.notOk(data, "Brand Project Info should not be returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
            t.end();
        };
        run(params, resCb, nextCb);
    });

    t.test("Gets Avatar Info with undefined values", function (t) {
        var params = { };
        var resCb = function (data) {
            t.notOk(data, "Brand Project Info should not be returned");
            t.end();
        };
        var nextCb = function (err) {
            t.ok(err, "Validation error: " + err);
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
    var cmd = require('../handlers/getAvatarInfo.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}
