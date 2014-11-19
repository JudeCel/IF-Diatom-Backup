"use strict";
var getGallerySessionsPerTopic = require('if-data').repositories.getGallerySessionsPerTopic;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');

module.exports.validate = function (req, res, next) {
	var err =joi.validate(req.params, {
		topic_id: joi.types.Number().required()
	});
	if (err)
		return next(webFaultHelper.getValidationFault(err.message));

	next();
};

module.exports.run = function (req, res, errCb) {
	getGallerySessionsPerTopic(req.params)
        .done(function (data) {
			res.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};