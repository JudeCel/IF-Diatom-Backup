"use strict";
var updateUser = require('if-data').repositories.updateUser;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');
var Q = require('q');

module.exports.validate = function (req, res, next) {
    var err = joi.validate(req.params, {
        id: joi.types.Number().required(),
        avatar_info: joi.types.String().required()
    });

    if (err)
        return next(webFaultHelper.getValidationFault(err.message));

    next();
};

module.exports.run = function (req, resCb, errCb) {
    var fields = {
        id: req.params.id,
        avatar_info: req.params.avatar_info
    };
    updateUser(fields)
        .done(function (opResult) {
            resCb.send({
                opResult: opResult,
                fields: fields
            });
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};