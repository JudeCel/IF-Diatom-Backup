"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

var updateTmpTitle = require("../handlers/updateTmpTitle.js");
var saveResourceToDb = require("../socketHelper/saveResourceToDb.js");

var dataHelper = require("../helpers/dataHelper.js");

test("Connects to the db", commonOperations.dbConnect);

test('uploadResourceTests.js', function (t) {
    var sessionId = 0;
    var userId = 0;
    var topicId = 0;
    var initialFileName = "title-test" + dataHelper.getTimestamp() + ".tmp";
    var tempFileName = dataHelper.clearFileNameExtraSymbols(initialFileName);

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Session created');
                sessionId = info.sessionId;
                return ifTestHelpers.user.createUser();
            })
            .then(function (info) {
                userId = info.id;
                return ifTestHelpers.topic.createTopic({
                    session_id: sessionId
                });
            })
            .done(function (info) {
                topicId = info.id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("\"Uploads\" a resource successfully", function (t) {
        var params = {};
        params.updateTmpTitleParams = {
            user_id: userId,
            topic_id: topicId,
            URL: "url",
            JSON: {
                title: initialFileName,
                text: initialFileName
            }
        };

        params.saveResourceToDbParams = {
            name: dataHelper.getResourceFileName(tempFileName),
            matchName: tempFileName,
            type: "image"
        };

        var resCb = function (resultUserId, json) {
            t.equal(resultUserId, userId, "userId is correct");
            t.end();
        };

        var nextCb = function (data) {
            t.notOk(data, "No errors should have been thrown, received: " + data);
            t.end();
        };

        run(params, resCb, nextCb)
    });

    t.test("Removes session", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId]})).done();
    });
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var nextC = function () {
        saveResourceToDb(params.saveResourceToDbParams, resCb, nextCb);
    };

    updateTmpTitle.execute(params.updateTmpTitleParams, nextC, nextCb);
}