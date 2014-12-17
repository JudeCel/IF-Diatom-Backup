"use strict";
var AddUsers = require('if-data').repositories.addUsers;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');
var mtypes = require('if-common').mtypes;
var _ = require('lodash');

module.exports.validate = function (req, res, next) {

    var err = joi.validate(req.body, {
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
    
    _.extend(req.body, {accountId: req.locals.accountId, status: mtypes.userStatus.inactive});
    
    AddUsers({users: [req.body]}, function(err, data){
        if(err!==null ) return errCb(webFaultHelper.getFault(err));
        resCb.send({id:data[0].id});
    });
};