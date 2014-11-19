"use strict";
var getCountries = require("if-data").repositories.getCountries;
var webFaultHelper = require('../helpers/webFaultHelper.js');

module.exports.validate = function (req, res, next) {
    return next();
};

module.exports.run = function (req, resCb, errCb) {
    getCountries ()
        .done(function (data) {
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
}
