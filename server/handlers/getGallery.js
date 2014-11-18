"use strict";
var getGallery = require('if-data').repositories.getGallery;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');

module.exports.validate = function (req, resCb) {
	var err = null; /*joi.validate(req.params, {
		session_id: joi.types.Number().required()
	}); */
	if (err)
		return resCb(webFaultHelper.getValidationFault(err.message));

    resCb();
};

module.exports.run = function (req, res, errCb) {
	getGallery(req.params)
        .done(function (data) {
			// TEMPORARY!
			res.header('Access-Control-Allow-Origin', req.headers.origin);
			res.header('Access-Control-Allow-Credentials', 'true');

			data = [
				{
					id: 100,
					name: "image pic",
					type: 103000100
				},
				{
					id: 101,
					name: "video super",
					type: 103000300
				},
				{
					id: 102,
					name: "audio file",
					type: 103000400
				}
			];
			res.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};