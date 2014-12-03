"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;
var _ = require('lodash');
var getSessionAndTopics = require('if-data').repositories.getSessionAndTopics;

test("Connects to the db", commonOperations.dbConnect);

test('Delete session', function (t) {
  var accountId = 0;
  var sessionId = 0;

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
	        t.end();
	      }, function (err) {
	        t.fail(err);
	        t.end();
	    });
 	});

	t.test("Delete session", function (t) {

    var params = {
      query: {sessionId: sessionId}
    };

		var resCb = function (data) {
      getSessionAndTopics(params.query)
        .done(function (data) {
          t.notEqual(data.session.deleted, null, "Session deleted ok");
          t.equal(data.topics.length, 0, "Topics deleted ok");
          t.end();
        }, function (err) {
          t.fail(err);
          t.end();
      });
		};

		var nextCb = function (data) {
			t.notOk(data, "No errors should have been thrown, received: " + data);
			t.end();
		}

		run(params, resCb, nextCb);

	});

	t.test("Should not delete when sessionId is not passed", function (t) {
    var params = {
      query: {sessionId: null}
    };

		var resCb = function (data) {
			t.notOk(data, "Session should not be returned");
			t.end();
		};

		var nextCb = function (data) {
			t.ok(data, "Validation error: " + data);
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
    var req = expressValidatorStub( params );
    var cmd = require('../handlers/deleteSession.js');
    var res = { send: resCb };

    cmd.validate(req, res, function (err) {
        if (err) return nextCb(err);
            cmd.run(req, res, nextCb);
    });
}

