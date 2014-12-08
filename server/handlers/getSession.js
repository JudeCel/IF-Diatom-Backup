"use strict";

var getSession = require('if-data').repositories.getSession;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');
var mtypes = require('if-common').mtypes;

module.exports.validate = function (req, res, next) {
    next();
};

module.exports.run = function (req, resCb, errCb) {
	getSession(req.params)
        .done(function (data) {
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};