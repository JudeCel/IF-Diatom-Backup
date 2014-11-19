"use strict";
var getGalleryTopics = require('if-data').repositories.getGalleryTopics;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');

module.exports.validate = function (req, res, next) {
	var err = null; /*joi.validate(req.params, {
		session_id: joi.types.Number().required()
	}); */
	if (err)
		return next(webFaultHelper.getValidationFault(err.message));

	next();
};

module.exports.run = function (req, res, errCb) {
	getGalleryTopics(req.params)
        .done(function (data) {
			res.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};