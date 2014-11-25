"use strict";
var getSession = require('if-data').repositories.getSession;
var createSession = require('if-data').repositories.createSession;
var getTopics = require('if-data').repositories.getTopics;
var createTopic = require('if-data').repositories.createTopic;

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
    getSession(req.query)
        .then(function (session) {
            session.name = session.name +"_Copy";
            return createSession(session);
        })
        .then(function (sessionCopy) {
            return getTopics({session_id:req.params.sessionId})
        })
        .then(function (topics) {
            console.log(topics);
            //async.each(topics, function (topic, next) {
            //    topic.session_id =  sessionCopy.id;  
            //    createTopic(topic, next);
            //}, callback);
        })  
        .done(function (data) {
           // console.log(data);
            resCb.send();
        }, errCb);
};