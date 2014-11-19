module.exports = function (req, res, next) {
	res.setHeader('Cache-Control', 'no-cache, must-revalidate'); // http 1.1
	res.setHeader('Pragma', 'no-cache'); // http 1.0
	res.setHeader('Expires', 'Sat, 20 Jul 1997 05:00:00 GMT');

	next();
};
