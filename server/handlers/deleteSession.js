"use strict";
var deleteSession = require('if-data').repositories.deleteSession;
var deleteSessionTopics = require('if-data').repositories.deleteSessionTopics;

var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');

module.exports.validate = function (req, res, next) {
    
    var params = { sessionId: req.param("sessionId") };

    var err = joi.validate(params, {
        sessionId: joi.types.Number().required()
    });

    if (err)
        return  next(webFaultHelper.getValidationFault(err.message));
     next();
};

module.exports.run = function (req, resCb, errCb) {
    deleteSession(req.query)
      .then(deleteSessionTopics(req.query))
      .done(function (resObj) {
          console.log(resObj);
          resCb.send();
      }, errCb);
};