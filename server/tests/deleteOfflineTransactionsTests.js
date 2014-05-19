"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test("Delete offline transaction", function (t) {
    var sessionId = 0;
    var topicId = 0;
    var replyUserId = 0;
    var userId = 0;

    t.test("Sets up a session with offline transaction and then remove it", function (t) {
        ifTestHelpers.session.createCompanyProjectSession()
            .then(function (info) {
                t.ok(info, 'Session created');
                sessionId = info.sessionId;
                return;
            })
            .then(function () {
                return ifTestHelpers.user.createUser({
                    ifs_admin: true,
                    avatar_info: "test-avatar-info"
                });
            })
            .then(function (info) {
                userId = info.id;
                return ifTestHelpers.topic.createTopic({
                    session_id: sessionId
                });
            })
            .then(function (info) {
                return ifTestHelpers.offlineTransactions.createOfflineTransaction({
                    user_id: userId,
                    session_id: sessionId,
                    topic_id: info.id
                });
            })
            .done(function (info) {
                topicId = info.topic_id;
                replyUserId = info.reply_user_id;
                t.end();
            }, function (err) {
                t.fail(err);
                t.end();
            });
    });

    t.test("Deletes offline transaction", function (t) {
        var params = {
            topic_id: topicId,
            reply_user_id: replyUserId
        };
        var resCb = function () {
            t.end();
        };
        var nextCb = function (err) {
            t.notOk(err, "No errors should have been thrown, received: " + util.inspect(err));
            t.end();
        };
        run(params, resCb, nextCb);
    })

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
    var cmd = require('../handlers/deleteOfflineTransactions.js');
    cmd.validate(req, function (err) {
        if (err) return nextCb(err);
        cmd.run(req, res, nextCb);
    });
}