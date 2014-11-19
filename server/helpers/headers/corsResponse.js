module.exports = function (req, res, next) {
	if (!req.headers.hasOwnProperty('origin'))
		return next();

	res.setHeader('Access-Control-Allow-Origin', req.headers.origin);
	res.setHeader('Access-Control-Allow-Credentials', 'true');

	if (req.method !== 'OPTIONS') return next();

	// respond to CORS pre-flight request
	res.setHeader('Access-Control-Allow-Headers', req.headers['access-control-request-headers']);
	res.setHeader('Access-Control-Allow-Methods', req.headers['access-control-request-method']);
	res.setHeader('Access-Control-Max-Age', '1728000');
	res.send(200);
};
