"use strict";
var getGallerySessionsPerTopic = require('if-data').repositories.getGallerySessionsPerTopic;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');

module.exports.validate = function (req, resCb) {
	var err =joi.validate(req.params, {
		topic_id: joi.types.Number().required()
	});
	if (err)
		return resCb(webFaultHelper.getValidationFault(err.message));

    resCb();
};

module.exports.run = function (req, res, errCb) {
	getGallerySessionsPerTopic(req.params)
        .done(function (data) {
			res.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};