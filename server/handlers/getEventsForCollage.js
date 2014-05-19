"use strict";
var getEventsForCollage = require('if-data').repositories.getEventsForCollage;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');

module.exports.validate = function (req, resCb) {
    var err = joi.validate(req.params, {
        topicId: joi.types.Number().required()
    });

    if (err)
        return resCb(webFaultHelper.getValidationFault(err.message));

    resCb();
};

module.exports.run = function (req, resCb, errCb) {
    getEventsForCollage(req.params)
        .done(function (event) {
            resCb.send(event);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};