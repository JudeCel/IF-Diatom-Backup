"use strict";
var updateUser = require('if-data').repositories.updateUser;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');
var Q = require('q');

module.exports.validate = function (req, res, next) {
    console.log(req.body);
    var err = joi.validate(req.body, {
        id: joi.types.Number().required(),
        name_first: joi.types.String().required(),
        name_last: joi.types.String().required(),
        gender: joi.types.String().required(),
        email: joi.types.String().email().required(),
        address: joi.types.String().optional().nullOk(),
        state:  joi.types.String().optional().nullOk(),
        country_id: joi.types.Number().optional(),
        city: joi.types.String().optional().nullOk(),
        code: joi.types.String().optional().nullOk(),
        company: joi.types.String().optional().nullOk()
    });

    if (err)
        return next(webFaultHelper.getValidationFault(err.message));

    next();
};

module.exports.run = function (req, resCb, errCb) {
    
    var fields = {
        id: req.body.id,
        name_first: req.body.name_first,
        name_last: req.body.name_last,
        gender: req.body.gender,
        email: req.body.email,
        state: req.body.state,
        country_id: req.body.country_id,
        city: req.body.city,
        code: req.body.code,
        company: req.body.company
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