var _ = require('lodash');
var webFaultHelper = require('../helpers/webFaultHelper.js');
var fs = require('fs');


module.exports.directoryCheck = function(path, cb) {
    fs.exists(path, function (exists) {

        if (!exists) {
            fs.mkdir(path, '755', function (err) { // TODO: Chmod rules are ok for this directory? (mir4a at 11:00, 12/12/14)
                if (err) {
                    return cb(webFaultHelper.getFault(err));
                }
            });
        }
        return cb();
    });
};