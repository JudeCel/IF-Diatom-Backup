"use strict";
var ifData = require('if-data')
var getClientCompanyInfo = ifData.repositories.getClientCompanyInfo;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');
var expressValidatorStub = require('../tests/testHelpers/expressValidatorStub.js');

var validate = function (req, next) {
    var err = joi.validate(req.params, {
        companyId: joi.types.Number(), //Code suggests this parameter might not be passed.
        sidx: joi.types.String().with('sord'),
        sord: joi.types.String().with('start'),
        start: joi.types.Number().with('limit'),
        limit: joi.types.Number().with('sidx')
    });
    if (err)
        return next(webFaultHelper.getValidationFault(err.message));

    next();
};
module.exports.validate = validate;

var run = function (req, resCb, errCb) {
    getClientCompanyInfo(req.params)
        .done(function (data) {
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
}
module.exports.run = run;

module.exports.execute = function (params, resCb, nextCb) {
    var req = expressValidatorStub({
        params: params
    });

    var res = { send: resCb };
    validate(req, function (err) {
        if (err) return nextCb(err);
        run(req, res, nextCb);
    });
}