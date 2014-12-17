"use strict";
var getUser = require('if-data').repositories.getUser;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');
var _ = require('lodash');

module.exports.validate = function (req, res, next) {
    //if (req.locals.userId == undefined)
    //    return next(webFaultHelper.getValidationFault("UserId is missed"));
  var err = joi.validate(req.query, {
      userId: joi.types.Number().required()
  });

  if (err)
    return  next(webFaultHelper.getValidationFault(err.message));
  next();
};

module.exports.run = function (req, resCb, errCb) {

    //{TODO} - need fix it! (problem with $rootScope.userId on frontend)
    if(!parseInt(req.query.userId))  
      _.extend(req.query, {userId: req.locals.userId});
    // getUser(req.locals)
    getUser(req.query)
        .done(function (data) {
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};