"use strict";
var AddUsers = require('if-data').repositories.AddUsers;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');

module.exports.validate = function (req, res, next) {

    var params = {
        name_first: req.param("name_first"),
        name_last: req.param("name_last"),
        gender: req.param("gender"),
        email: req.param("email"),
        address: req.param("address"),
        state:  req.param("state"),
        country_id: req.param("country_id"),
        city: req.param("city"),
        code: req.param("code"),
        company: req.param("company")
    };

    var err = joi.validate(params, {
        name_first: joi.types.String().required(),
        name_last: joi.types.String().required(),
        gender: joi.types.String().required(),
        email: joi.types.String().email().required(),
        address: joi.types.String().optional().allow('').nullOk(),
        phone: joi.types.String().optional().allow('').nullOk(),
        mobile: joi.types.String().optional().allow('').nullOk(),
        state:  joi.types.String().optional().allow('').nullOk(),
        country_id: joi.types.Number().optional().nullOk(),
        city: joi.types.String().optional().allow('').nullOk(),
        code: joi.types.String().optional().allow('').nullOk(),
        company: joi.types.String().optional().allow('').nullOk()
    });
    if (err)
        return next(webFaultHelper.getValidationFault(err.message));

    next();
};

module.exports.run = function (req, resCb, errCb) {
    
    var fields = {
        name_first: req.body.name_first,
        name_last: req.body.name_last,
        gender: req.body.gender,
        email: req.body.email,
        address: req.body.address,
        phone: req.body.phone,
        mobile: req.body.mobile,
        state: req.body.state,
        country_id: req.body.country_id,
        city: req.body.city,
        code: req.body.code,
        company: req.body.company
    };

    resCb.send();

    AddUsers(fields)
        .done(function (data) {
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
 
};