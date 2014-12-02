"use strict";

var getUsers = require('if-data').repositories.getUsersList;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');

module.exports.validate = function (req, res, next) {
    next();
};

module.exports.run = function (req, resCb, errCb) {
	getUsers(req.locals.accountId)
        .done(function (data) {
            console.log(data);
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};