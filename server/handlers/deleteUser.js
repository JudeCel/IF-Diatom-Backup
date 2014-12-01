"use strict";
//var deleteUser = require('if-data').repositories.deleteUser;

var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');

module.exports.validate = function (req, res, next) {
    
    var err = joi.validate(req.query, {
      userId: joi.types.Number().required()
    });

    if (err)
        return  next(webFaultHelper.getValidationFault(err.message));
     next();
};

module.exports.run = function (req, resCb, errCb) {
  resCb.send();
  /*
    deleteUser(req.query)
      .then(function (data) {
        return deleteSessionTopics(req.query);
      })
      .done(function (data) {
          resCb.send();
      }, errCb);
*/
};