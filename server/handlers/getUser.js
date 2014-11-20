"use strict";
var getUser = require('if-data').repositories.getUser;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');

module.exports.validate = function (req, res, next) {
  
    var err = joi.validate(req.query, {
        user_id: joi.types.Number().required()
    });
    if (err)
        return next(webFaultHelper.getValidationFault(err.message));

    next();
};

module.exports.run = function (req, resCb, errCb) {
    getUser(req.query)
        .done(function (data) {
            var user = {};
            user.id = data.id;
            user.name_first  =  data.name_first;
            user.name_last  =  data.name_last;
            user.gender = data.gender;
            user.email  = data.email;
            user.address  = data.address;
            user.phone  = data.phone;
            user.mobile  = data.mobile;
            user.state = data.state;
            user.country_id = data.country_id;
            user.city = data.city;
            user.code = data.code;
            user.company = data.company;

            resCb.send(user);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};