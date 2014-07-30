"use strict";
var updateEvent = require('if-data').repositories.updateEvent;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');


module.exports.validate = function (req, resCb) {
    var err = joi.validate(req.params, {
        id: joi.types.Number().required(),
        user_id: joi.types.Number().optional(),
        topic_id: joi.types.Number().required(),
        reply_id: joi.types.Number().optional().nullOk(),
        cmd: joi.types.String().optional().nullOk(),
        tag: joi.types.Number().required(),
        uid: joi.types.String().optional().nullOk(),
        event: joi.types.String().optional(),
        thumbs_up: joi.types.Number().optional().nullOk(),
        timestamp: joi.types.Number().optional(),
        created: joi.types.Object().optional().nullOk(),
        updated: joi.types.Object().optional().nullOk(),
        deleted: joi.types.Object().optional().nullOk()
    });

    if (err)
        return resCb(webFaultHelper.getValidationFault(err.message));

    resCb();
};

module.exports.run = function (req, resCb, errCb) {
    updateEvent(req.params)
        .done(function (opResult) {
            if (resCb)
                resCb.send({
                    opResult: opResult,
                    fields: req.params
                });
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};