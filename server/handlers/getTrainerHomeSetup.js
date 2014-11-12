"use strict";
var _ = require('lodash');
var async = require('async');
//var mfCommon = require('mf-common');
//var mfData = require('mf-data'), db = mfData.db;
//var webFaultHelper = require('../../../helpers/webFaultHelper.js');
//var fn = mfCommon.utils.functionHelper;
//var ipadCompatibilityHelper = mfCommon.utils.ipadCompatibilityHelper;

module.exports.validate = function (req, res, next) {
	return next();
};

module.exports.run = function (req, res, mainCb) {
	//var accountId = req.locals.accountId;

	async.parallel({
		coursesAndSeries: getCoursesAndSeries
	}, function(err, results) {
		//if(err) return mainCb(webFaultHelper.getFault(err));
		res.header('Access-Control-Allow-Origin', req.headers.origin);
		res.header('Access-Control-Allow-Credentials', 'true');

		res.send(results);
	});

	function getCoursesAndSeries(cb) {
		cb(null, []);
	}

//	function getCoursesAndSeries(cb) {
//		var sql = "SELECT \
//		c.id, \
//		c.name, \
//		UNIX_TIMESTAMP(c.Modified) * 1000 modified, \
//		IFNULL(c.trainerName, u.name) trainerName, \
//		IFNULL(c.trainerEmail, u.email) trainerEmail, \
//		c.thumbUrl, \
//		c.description, \
//		LOWER(s.name) `status`, \
//		LOWER(t.name) `type`, \
//		IF(c.type != 106000400 /*Series*/, c.mobileConversionState, ( \
//				SELECT \
//					GROUP_CONCAT(DISTINCT scs.mobileConversionState SEPARATOR ',') \
//				FROM seriescourse sc \
//				JOIN course scs ON sc.courseId = scs.id \
//				WHERE sc.seriesId = c.id \
//			)) mobileConversionState \
//		FROM course c \
//		JOIN mtype s ON c.status = s.id \
//		JOIN mtype t ON c.type = t.id \
//		JOIN mtype m ON c.mobileconversionstate = m.id \
//		JOIN userrecord u ON c.ownerId = u.id \
//		WHERE c.AccountID = ? \
//		AND c.Deleted IS NULL";
//
//		db.query(sql, [accountId], function (err, results) {
//			if (err) return mainCb(webFaultHelper.getFault(err));
//
//			_.each(results, function (result) {
//				result.ipadCompatible = ipadCompatibilityHelper.isCompatible(result.mobileConversionState);
//				delete result.mobileConversionState;
//			});
//			cb(null, results);
//		});
//	}

//	function getCatalogs(cb) {
//		var sql = "SELECT \
//		c.id, \
//		c.name, \
//		c.description, \
//		c.accessType, \
//		UNIX_TIMESTAMP(c.Modified) * 1000 modified \
//		FROM catalog c \
//		WHERE c.AccountID = ? \
//		AND c.Deleted IS NULL";
//
//		db.query(sql, [accountId], function (err, results) {
//			if (err) return mainCb(webFaultHelper.getFault(err));
//			cb(null, results);
//		});
//	}
};


