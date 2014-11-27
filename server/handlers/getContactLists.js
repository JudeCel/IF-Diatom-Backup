"use strict";
var _ = require('lodash');
var async = require('async');
var ifData = require('if-data'), db = ifData.db;
var ifCommon = require('if-common');
var mtypes = ifCommon.mtypes;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var arrayHelper = ifCommon.utils.arrayHelper;

module.exports.validate = function (req, res, next) {
	next();
};

module.exports.run = function (req, res, mainCb) {
	var allowedContactLists = req.locals.allowedContactLists;
	var contactListRestrictions = (allowedContactLists && allowedContactLists.length > 0);

	async.parallel({
		users: getUsers,
		contactLists: getContactLists
	}, function (err, results) {
		if (err) return mainCb(webFaultHelper.getFault(err));
		res.send(results);
	});

	function getContactLists(cb) {
		var query = "SELECT \
			cl.ID, \
			cl.Name, \
			GROUP_CONCAT(u.id SEPARATOR ',') userIds \
		FROM contactlist cl \
		LEFT JOIN contactlistuser clu ON cl.ID = clu.ContactListID \
			AND clu.Deleted IS NULL \
		LEFT JOIN users u ON clu.userId = u.ID \
			AND u.Deleted IS NULL \
		WHERE cl.AccountID = ? \
		AND cl.Deleted IS NULL";

		if (contactListRestrictions)
			query = query + " AND cl.ID IN (?)";

		query = query + " GROUP BY cl.ID \
		ORDER BY cl.Name ASC;";

		db.query(query, [req.locals.accountId, allowedContactLists], function (err, res) {
			if (err) return cb(err);
			_.each(res, function(contactList) {
				contactList.userIds = contactList.userIds ? arrayHelper.strArrayToIntArray(contactList.userIds.split(',')) : [];
			});
			cb(null, res);
		});
	}

	function getUsers(cb) {
		var query = "SELECT \
			DISTINCT u.ID, \
			u.name_first, \
			u.name_last, \
			CONCAT(u.name_last,', ',u.name_first) Name, \
			u.Email \
		FROM users u"

		if (contactListRestrictions)
			query = query + " JOIN contactlistuser clu ON clu.UserID = u.ID \
				AND ugu.Deleted IS NULL";

		query = query + " WHERE u.AccountID = ? \
		AND u.Deleted IS NULL";

		if (contactListRestrictions)
			query = query + " AND clu.ContactListID IN (?)"

		db.query(query, [req.locals.accountId, allowedContactLists], function (err, results) {
			if (err) return cb(err);

			cb(null, _.map(results, function (result) {
				return result;
			}));
		});
	}
};
