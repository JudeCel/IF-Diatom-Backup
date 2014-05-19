"use strict";
var util = require('util');
var async = require('async');
var _ = require('lodash');
var ifCommon = require('if-common');
//var mockQueueWriter = ifCommon.testHelpers.mockWorkQueueWriter;
var ifData = require('if-data'), db = ifData.db;
//var helper = ifData.testHelpers.integrationTestHelper;
var test = require('tap').test;
var config = require('simpler-config').load(require('../../config/config.json'));
var mockery = require('mockery');
var ifTestHelpers = require('if-test-helpers');

module.exports.dbConnect = function(t) {
	/*config.connectionLimit = 2;
	config.amtToPrefill = 2;
	config.debugging = {
		hilo: false,
		ping: false,
		connectionEnd: false,
		connectionError: false,
		poolPerf: false,
		queryPerf: false,
		queryPerfSlowQueryThresholdSec: 10,
		queryResult: false
	};*/
	ifData.setup(config.mysql, function(err, res) {
		if(err) throw err;
		t.end();
	});
};

module.exports.dbDisconnect = function (t) {
	ifData.db.disconnect(function(err) {
		if(err) throw err;
		t.end();
	});
};

module.exports.tearDownMocks = function (t) {
	mockery.disable();
	mockery.deregisterAll();
	t.end();
};

module.exports.useMockQueueWriter = function(t) {
	//ifCommon.utils.queueWriter = mockQueueWriter;
	mockery.enable();
	mockery.registerMock('if-common', ifCommon);
	mockery.warnOnUnregistered(false);
	t.end();
};