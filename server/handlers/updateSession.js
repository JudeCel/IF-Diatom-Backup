"use strict";
var updateSession = require('if-data').repositories.updateSession;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');

module.exports.validate = function (req, res, next) {
    var step = req.body.step;
    var params = {
        id: req.body.id,
        name: req.body.name,
        brand_project_id: req.body.brand_project_id,
        start_time: req.body.start_time,
        end_time: req.body.end_time,
        incentive_details: req.body.incentive_details,
//        status_id:  req.param("status_id"),
        active_topic_id: req.body.active_topic_id,
        colours_used: req.body.colours_used,
        facilitatorId: req.body.facilitatorId
    };

    var err;

    if (step === 'step1') {
        err = joi.validate(params, {
            id: joi.types.Number().required(),
            name: joi.types.String().required(),
            brand_project_id: joi.types.Number().optional(),
            start_time: joi.types.String().required(),
            end_time: joi.types.String().required(),
            colours_used: joi.types.String().optional()
//            status_id:  joi.types.Number().optional()
        });
    } else if (step === 'step2') {
        err = joi.validate(params, {
            id: joi.types.Number().required(),
            facilitatorId: joi.types.Number().required()
        });
    }

    if (err)
        return next(webFaultHelper.getValidationFault(err.message));

    next();
};

module.exports.run = function (req, resCb, errCb) {
    console.log(req.param);
    var fields = {
        id: req.body.id,
        name: req.body.name,
        brand_project_id: req.body.brand_project_id,
        start_time: req.body.start_time,
        end_time: req.body.end_time,
        incentive_details: req.body.incentive_details,
//        status_id:  req.body.status_id,
        active_topic_id: req.body.active_topic_id,
        colours_used: req.body.colours_used,
        facilitatorId: req.body.facilitatorId
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