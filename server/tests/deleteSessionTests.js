"use strict";
var commonOperations = require('./testHelpers/commonOperations.js');
var test = require('tap').test;
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var ifTestHelpers = require('if-test-helpers');
var testUtils = ifTestHelpers.utils;

test("Connects to the db", commonOperations.dbConnect);

test('Delete session', function (t) {
  var accountId = 0;
  var sessionId = 0;
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

	t.test("Delete session", function (t) {

    var params = {
      query: {sessionId: sessionId}
    };

		var resCb = function (data) {
			t.ok(data, "Result returned");
      t.equal(data.session.affectedRows, 1, "Session Row affected");
      t.equal(data.session.changedRows, 1, "Session Row changed");
      t.equal(data.topics.affectedRows, 1, "Topic Row affected");
      t.equal(data.topics.changedRows, 1, "Topic Row changed");      
			t.end();
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

