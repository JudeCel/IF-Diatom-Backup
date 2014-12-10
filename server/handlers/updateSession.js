"use strict";
var updateSession = require('if-data').repositories.updateSession;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');

module.exports.validate = function (req, res, next) {

    var params = {
        id: req.query.id,
        name: req.query.name,
//        brand_project_id: req.param("brand_project_id"),
        start_time: req.query.start_time,
        end_time: req.query.end_time
//        incentive_details: req.param("incentive_details"),
//        status_id:  req.param("status_id"),
//        active_topic_id: req.param("active_topic_id"),
//        colours_used: req.param("colours_used"),
//        facilitatorId: req.param("facilitatorId")
    };

    var err = joi.validate(params, {
        id: joi.types.Number().required(),
        name: joi.types.String().required(),
//        brand_project_id: joi.types.Number().required(),
        start_time: joi.types.String().required(),
        end_time: joi.types.String().required()
//        incentive_details: joi.types.String().required(),
//        status_id:  joi.types.Number().required(),
//        active_topic_id: joi.types.Number().required(),
//        colours_used: joi.types.String().required(),
//        facilitatorId: joi.types.Number().required()
    });
    if (err)
        return next(webFaultHelper.getValidationFault(err.message));

    next();
};

module.exports.run = function (req, resCb, errCb) {
    console.log(req.param);
    var fields = {
        id: req.query.id,
        name: req.query.name,
//        brand_project_id: req.param("brand_project_id"),
        start_time: req.query.start_time,
        end_time: req.query.end_time
//        incentive_details: req.param("incentive_details"),
//        status_id:  req.param("status_id"),
//        active_topic_id: req.param("active_topic_id"),
//        colours_used: req.param("colours_used"),
//        facilitatorId: req.param("facilitatorId")
    };
    updateSession(fields)
        .done(function (opResult) {
            resCb.send({
                opResult: opResult,
                fields: fields
            });
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};