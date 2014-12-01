"use strict";

var getUsers = require('if-data').repositories.getUsers;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');
var mtypes = require('if-common').mtypes;

module.exports.validate = function (req, res, next) {
    next();
};

module.exports.run = function (req, resCb, errCb) {
	getUsers(req.locals.accountId)
        .done(function (data) {
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};