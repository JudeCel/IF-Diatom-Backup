"use strict";
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');
var directoryHelper = require('../helpers/directoryHelper');
var config = require('simpler-config');
var formidable = require('formidable');
var util = require('util');
var fs = require('fs');



module.exports.validate = function (req, res, next) {
    var err = false;
// FIXME: Resolve validation for image upload (mir4a at 00:03, 12/10/14)
    if (err) {
        return resCb(webFaultHelper.getValidationFault(err.message));
    }

    next();
};

module.exports.run = function (req, res, next) {

    var uploadPath = config.paths.uploadsPath + req.locals.accountId;

    function Form() {
        this.init = function() {

            var form = new formidable.IncomingForm({uploadDir: uploadPath, keepExtensions: true});

            form.on('fileBegin', function (name, file) {
                file.path = form.uploadDir + "/" + file.name;
            });

            form.parse(req, function (err, fields, files) {
                res.writeHead(200, {'content-type': 'text/plain'});
                res.write('received upload:\n\n');
                res.end(util.inspect({fields: fields, files: files}));
            });
        };
        return this;
    }

    var form_instance = new Form();

    directoryHelper.directoryCheck(uploadPath, form_instance.init);
};
