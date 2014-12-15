"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var testUtils = ifTestHelpers.utils;
var mtypes = require('if-common').mtypes;
var util = require('util');
var getSessionAndTopics = require('if-data').repositories.getSessionAndTopics;
var updateSession = require('if-data').repositories.updateSession;

test("Connects to the db", commonOperations.dbConnect);

test('copySessionTests', function (t) {
    var accountId = 0;
    var sessionId = 0;
    var sessionCopyId1 = 0;
    var sessionCopyId2 = 0;
    var sessionCopyId3 = 0;
    var session = {};
    var topics = [];

    t.test("Creates predefined data sets", function (t) {
        ifTestHelpers.user.createAccount()
            .then(function (info) {
                t.ok(info, 'AccountCreated');
                accountId = info.id;
                var newSession = {
                    accountId: info.id
                }
                return ifTestHelpers.session.createSession(newSession);
            })
            .then(function (info) {
                t.ok(info, 'Session created');
                session = info;
                var newTopic = {
                    session_id: info.id
                };
                sessionId = info.id;
                return ifTestHelpers.topic.createTopic(newTopic);
            })
            .done(function (info) {
                t.ok(info, 'Topic created');
                topics.push(info);
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Update session with test data", function (t) {

        var updateParams = {
//            query: {sessionId: sessionId},
//            locals: {accountId: accountId},
            body: {
                id: session.id,
                step: 'step1',
                name: 'Updated Session Test',
                start_time: '2014-12-14 21:36:31',
                end_time: '2014-12-24 20:16:31'
            }
        };

        var resCb = function (data) {
            sessionId = data.id;
            updateParams.body.id = sessionId;
            getSessionAndTopics(data.id)
                .done(function (data) {

                    t.ok(data, "Session copy created");
                    t.notEqual(session.name, data.session.name, "Session name ok");
                    t.equals(topics.length, data.topics.length, "Topics count is correct");
                    t.end();
                }, function (err) {
                    t.fail(err);
                    t.end();
                });

            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
            t.end();
        };

        run(updateParams, resCb, nextCb);

    });


    t.test("Remove session", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.session.removeSession({sessionIds: [sessionId, sessionCopyId1, sessionCopyId2, sessionCopyId3]})).done();
    });

    t.test("Remove account", function (t) {
        testUtils.tapExpectFulfillment(t, ifTestHelpers.user.removeAccount({accountIds: [accountId]})).done();
    });
});

test("Can disconnect from DB", commonOperations.dbDisconnect);

function run(params, resCb, nextCb) {
    var req = expressValidatorStub(params);
    var cmd = require('../handlers/updateSession.js');
    var res = { send: resCb };

    cmd.validate(req, res, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}

