var expressValidatorStub = require('../tests/testHelpers/expressValidatorStub.js');

function createLog(userId, type) {
    var req = expressValidatorStub({
        params: {
            type: type,
            user_id: userId
        }
    });

    var errCb = function (err) {
        // TBD
    };

    var resCb = function (data) {}

    var res = { send: resCb };

    var createLog = require('../handlers/createLog.js');
    createLog.validate(req, function (err) {
        if (err) return errCb(err);
        createLog.run(req, res, errCb);
    });
}

module.exports = createLog;