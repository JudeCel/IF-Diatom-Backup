var config = require('simpler-config');
var _ = require('lodash');
var util = require('util');
var joi = require('joi');
var async = require('async');
var request = require('request');

var ifCommon = require('if-common');
var mtypes = ifCommon.mtypes;
var validator = ifCommon.utils.validationMethods;
var fn = ifCommon.utils.functionHelper;
var dotNetEncryptionHelper = ifCommon.utils.dotNetEncryptionHelper;
var webFaultHelper = require('../../helpers/webFaultHelper.js');
var createSess = require('../../handlers/createSess.js');
var ifData = require('if-data'), db = ifData.db;
var emailExists = ifData.repositories.emailExists;
var addUsers = ifData.repositories.addUsers;
//var getUserCapacity = ifData.repositories.getUserCapacity;
//var urlHelper = require('../../helpers/urlHelper.js');
//var accountIsActive = require('../../repositories/account/accountIsActive.js');
//var getCourseStatusInfo = require('../../repositories/course/getCourseStatusInfo.js');
//var getCatalogCourseAccessInfo = require('../../repositories/catalog/getCatalogCourseAccessInfo.js');
//var logout = require('../../helpers/logout.js');

var userFields = ['email', 'name_first', 'name_last', 'password'];

module.exports = function (req, res, mainCb) {
	var accountId = res.locals.accountId;
	//var courseId = urlHelper.getCourseId(req);
	//var catalogId = urlHelper.getCatalogId(req);

//	if(catalogId && !res.locals.hasFeature(mtypes.featureEntry.courseCatalog)) {
//		return res.redirect(urlHelper.getCurrentHostUrl(req));
//	}

//	if (!courseId)
//		return logout(res);

	var data = _.extend({
		name_first: '',
		name_last: '',
		email: '',
		password: '',
		ds: '',
		passwordConfirm: ''
	}, req.body, req.query);

	var resData = {
		errors: {},
		errorViewModel: null,
		layoutData: res.locals.layoutData
	};

	var newUser, sessionId;

	// initial load
	if (!_.size(req.body))
		return sendPage();

	async.series({
		validate: validate,
		addUser: addUser,
		createSession: createSession
	}, function (err, results) {
		if (util.isError(err))
			return mainCb(err);

		if (err)
			return sendPage(err);

		res.cookie('sess0', dotNetEncryptionHelper.encryptNumberForUrl(sessionId), null);

		redirectToApp();
	});

	function sendPage(err) {
		if (err)
			resData.errors = err;

		setupPageData(function (pageDataErr) {
			if (pageDataErr)
				resData.errors = _.extend(resData.errors, pageDataErr);
			res.locals(data);
			res.locals(resData);
			res.render('register');
		});
	}

	function setupPageData(cb) {
		cb();
	}

//	function setupPageData(cb) {
//		async.parallel({
//			checkCourseAndAccount: checkCourseAndAccount
//		}, function (err, result) {
//			if (err) return mainCb(err);
//			if (_.size(data.errorViewModel))
//				return cb('general error found')
//
//			cb();
//		});
//	}

	function redirectToApp() {
		var params = {
			req: req,
			sessionId: sessionId
		};
//		if (!courseId)
//			return res.redirect(urlHelper.getTraineeDashboardRedirectUrl(params));

		//params.courseId = courseId;
		return res.redirect(urlHelper.getCourseRedirectUrl(params));
	}

	function validate(cb) {
		if (newUser) return cb();

		var schema = {
			name_first: joi.types.String().required(),
			name_last: joi.types.String().required(),
			email: joi.types.String().nullOk().emptyOk().email().max(254).optional(),
			password: joi.types.String().min(6).max(35).required(),
			passwordConfirm: joi.types.String().required()
		};

		var err = joi.validate(_.pick(data, _.keys(schema)), schema);
		err = err ? webFaultHelper.joiValidationFault(err) : {};

		if (!validator.any(data, "email")) {
			var msg = 'Email required';
			err.email = msg;
		}

		if (data.password.length < 6)
			err.password = 'Passwords minimum of 6 characters';
		if (data.password.length > 35)
			err.password = 'Passwords must be 35 characters or less';

		if (data.password !== data.passwordConfirm)
			err.password = 'Passwords do not match';

		if (_.size(err))
			return cb(err);

		async.parallel({
			emailExists: fn.wrapWithCb(emailExists, {
				email: data.email,
				accountId: accountId
			})
		}, function (err, results) {
			if (err) return cb(err);
			if (_.size(resData.errorViewModel))
				return cb('general error found')

			if (results.emailExists) {
				return cb({email: 'Email already exists'});
			}

			cb();
		})
	}

	function addUser(cb) {
		if (newUser) return cb();

		var userToAdd = _.pick(data, userFields);
		//userToAdd.status = mtypes.userStatus.active;
		//userToAdd.permissions = mtypes.userPermissions.trainee;
		//userToAdd.name = userToAdd.firstName + ' ' + userToAdd.lastName;

		addUsers({
			users: [userToAdd]
		}, function (err, newUsers) {
			if (err) return cb(err);
			newUser = newUsers.shift();
			cb();
		});
	}

	function createSession(cb) {
		if (!newUser) return cb();

		createSess({
			userId: newUser.id
		}, function (err, sessId) {
			if (err) return cb(err);
			sessionId = sessId;
			cb();
		});
	}
};
