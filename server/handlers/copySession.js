"use strict";
var getSessionAndTopics = require('if-data').repositories.getSessionAndTopics;
var createSession = require('if-data').repositories.createSession;
var createTopic = require('if-data').repositories.createTopic;
var _ = require('lodash');
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

    getSessionAndTopics(req.query)
        .then(function (data) {
            data.session.name = data.session.name +"_Copy";
            data.session.accountId = req.locals.accountId;
            return createSession(data.session);
        })
        .then(function (sessionCopy) {
            _.each(topics, function(topic) {
                topic.session_id = sessionCopy.id;
            });
            return createTopic(topics);
        })
        .done(function (result) {          
            resCb.send();
        }, errCb);  
};