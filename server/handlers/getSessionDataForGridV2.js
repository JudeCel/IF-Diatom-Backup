"use strict";

var getSessionDataForGridV2 = require('if-data').repositories.getSessionDataForGridV2;
var webFaultHelper = require('../helpers/webFaultHelper.js');
var joi = require('joi');
var mtypes = require('if-common').mtypes;

module.exports.validate = function (req, res, next) {
    next();
};

module.exports.run = function (req, resCb, errCb) {

    getSessionDataForGridV2(req.params)
        .done(function (data) {
            /*TODO fix status_id !!*/
            for( var i = 0; i < data.length; i++ ) {  
                if(data[i]["status_id"]=='1') data[i]["status_id"] = "Open";
                else data[i]["status_id"] = "Closed";
            }
            resCb.send(data);
        }, function (err) {
            errCb(webFaultHelper.getFault(err));
        });
};
