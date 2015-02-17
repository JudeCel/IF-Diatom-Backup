"use strict";
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');
var directoryHelper = require('../helpers/directoryHelper');
var dataHelper = require('../helpers/dataHelper');
var updateSession = require('if-data').repositories.updateSession;
var config = require('simpler-config');
var formidable = require('formidable');
var util = require('util');
var fs = require('fs');
var imagemagick = require('imagemagick-native');
var io = require('../sockets').io;

module.exports.validate = function (req, res, next) {

    var params = {
        "content-type": req.headers["content-type"],
        "content-length": req.headers["content-length"],
        "session-id": req.headers["session-id"]
    };

    var err;

    err = joi.validate(params, {
        "content-type": joi.types.String().regex(new RegExp(/(multipart\/form-data)/ig)).required(),
        "content-length": joi.types.Number().max(config.images.maxSize * 1024 * 1024).required(),
        "session-id": joi.types.String().required()
    });

    if (err) {
        return next(webFaultHelper.getValidationFault(err.message));
    }

    next();
};

module.exports.run = function (req, res, next) {

    var fileHelper = (function () {
        var dataHelper = require('../helpers/dataHelper');
        var config = require('simpler-config');
        var uploadPath = config.paths.uploadsPath + req.locals.accountId;
        var tmpPath = config.paths.temporaryPath;

        function file(filename) {
            return dataHelper.splitExtensionHelper(filename);
        }

        function fileName(filename) {
            return file(filename).name;
        }

        function fileExt(filename) {
            return file(filename).ext;
        }

        function clearName(filename) {
            return dataHelper.clearFileNameExtraSymbols(fileName(filename));
        }

        function fileUploadPath(filename) {
            return uploadPath + "/" + clearName(filename) + fileExt(filename);
        }

        function tmpFile(form) {
            return tmpPath + "/" + dataHelper.clearFileNameExtraSymbols(fileName(form.openedFiles[0].name)) + config.images.thumbAppendix + fileExt(form.openedFiles[0].name);
        }

        function outputFile(form) {
            return uploadPath + "/" + dataHelper.clearFileNameExtraSymbols(fileName(form.openedFiles[0].name)) + config.images.thumbAppendix + fileExt(form.openedFiles[0].name);
        }

        function srcPath(form) {
            return form.openedFiles[0].path;
        }

        return {
            uploadPath: uploadPath,
            tmpPath: tmpPath,
            file: file,
            fileName: fileName,
            fileExt: fileExt,
            clearName: clearName,
            fileUploadPath: fileUploadPath,
            tmpFile: tmpFile,
            outputFile: outputFile,
            srcPath: srcPath
        }

    })();

    function Form() {
        this.init = function () {

            var form = new formidable.IncomingForm({uploadDir: fileHelper.uploadPath, keepExtensions: false});

            form.on('fileBegin', function (name, file) {
                io().sockets.emit('file:uploading', {msg: 'Uploading file…'});

                file.path = fileHelper.fileUploadPath(file.name);
                updateSession({
                    id: req.headers["session-id"],
                    sessionLogoName: fileHelper.clearName(file.name),
                    sessionLogoExt: fileHelper.fileExt(file.name)
                })
                    .done(function (opResult) {
                        io().sockets.emit('session:updated', opResult);
                    }, function (err) {
                        next(webFaultHelper.getFault(err));
                    });
            });

            form.on('end', function () {
                io().sockets.emit('file:converting', {msg: 'File uploaded, start resizing…'});

                var readStream = fs.createReadStream(fileHelper.srcPath(form));
                var writeStream = fs.createWriteStream(fileHelper.tmpFile(form));

                writeStream
                    .on('finish', function(){
                        fs.rename(fileHelper.tmpFile(form), fileHelper.outputFile(form), function(err) {
                            if (err) throw err;
                            io().sockets.emit('file:ready',{msg: 'File ready!', filePath: fileHelper.outputFile(form)});
                        })
                    });

                readStream
                    .pipe(imagemagick.streams.convert(config.images.resizeOptions))
                    .pipe(writeStream);

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

    directoryHelper.directoryCheck(fileHelper.uploadPath, form_instance.init);
};
