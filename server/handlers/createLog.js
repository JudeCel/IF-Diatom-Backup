"use strict";
var _ = require("lodash");
var createLog = require('if-data').repositories.createLog;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');
var dataHelper = require("../helpers/dataHelper.js");

module.exports.validate = function (req, next) {
    var err = joi.validate(req.params, {
        user_id: joi.types.Number().required(),
        timestamp: joi.types.Number().optional(),
        type: joi.types.String().optional()
    });

    if (err)
        return next(webFaultHelper.getValidationFault(err));

    next();
};

module.exports.run = function (req, resCb, errCb) {
    req.params = _.defaults(_.clone(req.params || {}), {
        timestamp: dataHelper.getTimestamp()
    });

    createLog(req.params)
        .done(function (data) {
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};