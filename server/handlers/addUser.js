"use strict";
var AddUsers = require('if-data').repositories.addUsers;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');
var mtypes = require('if-common').mtypes;

module.exports.validate = function (req, res, next) {

    var err = joi.validate(req.body, {
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
        users: [{
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
        company: req.body.company,
        accountId: req.locals.accountId,
        status: mtypes.userStatus.inactive
    }]};

    AddUsers(fields, function(err, data){
        if(err!==null ) return errCb(webFaultHelper.getFault(err));
        resCb.send({id:data[0].id});
    });
};