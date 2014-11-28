"use strict";
var deleteSession = require('if-data').repositories.deleteSession;
var deleteSessionTopics = require('if-data').repositories.deleteSessionTopics;

var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');

module.exports.validate = function (req, res, next) {
    
    var err = joi.validate(req.query, {
      sessionId: joi.types.Number().required()
    });

    if (err)
        return  next(webFaultHelper.getValidationFault(err.message));
     next();
};

module.exports.run = function (req, resCb, errCb) {
  
    var session = {};
    var topics = {};

    deleteSession(req.query)
      .then(function (data) {
        session = data;
        return deleteSessionTopics(req.query);
      })
      .done(function (data) {
          resCb.send({
            session: session,
            topics: data
          });
      }, errCb);
};