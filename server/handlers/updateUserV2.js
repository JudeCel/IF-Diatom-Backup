"use strict";
var updateUser = require('if-data').repositories.updateUser;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');

module.exports.validate = function (req, res, next) {

    var err = joi.validate(req.body, {
        id: joi.types.Number().required(),
        name_first: joi.types.String().required(),
        name_last: joi.types.String().required(),
        gender: joi.types.String().required(),
        email: joi.types.String().email().required(),
        address: joi.types.String().optional().allow(null, ''),
        phone: joi.types.String().optional().allow(null, ''),
        mobile: joi.types.String().optional().allow(null, ''),
        state:  joi.types.String().optional().allow(null, ''),
        country_id: joi.types.Number().optional().nullOk(),
        city: joi.types.String().optional().allow(null, ''),
        code: joi.types.String().optional().allow(null, ''),
        company: joi.types.String().optional().allow(null, '')
    });
    if (err)
        return next(webFaultHelper.getValidationFault(err.message));

    next();
};

module.exports.run = function (req, resCb, errCb) {
    updateUser(req.body)
        .done(function (opResult) {
            resCb.send({
                opResult: opResult,
                fields: req.body
            });
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};