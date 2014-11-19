"use strict";
var _ = require('lodash');
var express = require('express');
//var config = require('simpler-config');        TBD use this
var config = require('simpler-config').load(require('./config/config.json'));       // TBD use just require(), no load()
var log4js = require('log4js');
var socketHelper = require('./socketHelper.js');
var fs = require('fs');
var url = require('url');
var handlerInterceptor = require('./helpers/handlerInterceptor.js');
//var layoutDataLoader = require('./helpers/layoutDataLoader');

var stdLogger = log4js.getLogger('info');
stdLogger.setLevel('INFO');

var server;
module.exports = {
    run: function () {
	    var handle = handlerInterceptor.handle;

        var app = express();

	    app.use(require('./helpers/headers/poweredByHeader.js'));
	    app.use(require('./helpers/headers/noCacheHeaders.js'));
	    app.use(require('./helpers/headers/corsResponse.js'));

	    app.set('view engine', 'ejs');
	    app.set('views', __dirname + '/web');
	    app.use(express.bodyParser());
	    app.use(require('./helpers/sessionLoader'));

        server = app.listen(config.port);
        var io = require('./sockets.js').listen(server);

        io.configure(function () {
            io.set('log level', 0);		//	0(error), 1(warn), 2(info), 3(debug)
        });

        socketHelper.dbHelper.dbHandleDisconnect();

        var format_Content = function (content) {
            content = content.replace(/\[__SERVER__\]/g, config.paths.urlPath + config.paths.serverPath);
            content = content.replace(/\[__CONFIG__\]/g, config.paths.urlPath + config.paths.configPath);
            content = content.replace(/\[__ADMIN__\]/g, config.paths.urlPath + config.paths.adminPath);
            content = content.replace(/\[__CHAT_ROOM__\]/g, config.paths.urlPath + config.paths.chatRoomPath);
            content = content.replace(/\[__MODE__\]/g, config.mode);
            content = content.replace(/\[__PORT__\]/g, config.port);
            content = content.replace(/\[__CONFIG_PORT__\]/g, config.port);
            content = content.replace(/\[__DOMAIN__\]/g, config.domain);
            return content;
        }


        //PHP2Node
        app.get("/getuserloginRS", function (req, res) {
            /*            console.log("this is req:");
             console.log(req);*/
            var params = {
                login: req.param("login"),
                password:req.param("password")
            };

            require("./handlers/getUserLogin_byPassword.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getUserAdminity", function (req, res) {
            var params = {
                login: req.param("login"),
                password:req.param("password")
            };
            require("./handlers/getUserAdminity.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getUserObservity", function (req, res) {
            var params = {
                login: req.param("login"),
                password:req.param("password")
            };
            require("./handlers/getUserObservity.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getUserModity", function (req, res) {
            var params = {
                login: req.param("login"),
                password:req.param("password")
            };
            require("./handlers/getUserModity.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getUserParticipity", function (req, res) {
            var params = {
                login: req.param("login"),
                password:req.param("password")
            };
            require("./handlers/getUserParticipity.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getClientCompanyLogo", function (req, res) {
            var params = {
                id: req.param("id")
            };
            require("./handlers/getClientCompanyLogo.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getClientCompanyInfo", function (req, res) {
            var params = {
                companyId: req.param("companyId"),
                sidx: req.param("sidx"),
                sord: req.param("sord"),
                start: req.param("start"),
                limit: req.param("limit")
            };
            require("./handlers/getClientCompanyInfo.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getBrandProject", function (req, res) {
            var params = {
                companyId: req.param("companyId"),
                sidx: req.param("sidx"),
                sord: req.param("sord"),
                start: req.param("start"),
                limit: req.param("limit")
            };
            require("./handlers/getBrandProject.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getBrandProjectBySession", function (req, res) {
            var params = {
                companyId: req.param("companyId"),
                sidx: req.param("sidx"),
                sord: req.param("sord"),
                start: req.param("start"),
                limit: req.param("limit")
            };
            require("./handlers/getBrandProjectBySession.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getSessionDataForGrid", function (req, res) {
            var params = {companyId: req.param("companyId"),
                            limit: req.param("limit")};
            require("./handlers/getSessionDataForGrid.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getSessionDataForGridByUser", function (req, res) {
            var params = {
                type: req.param("type"),
                userId: req.param("userId"),
                companyId: req.param("companyId"),
                sidx: req.param("sidx"),
                sord: req.param("sord"),
                start: req.param("start"),
                limit: req.param("limit")
            };
            require("./handlers/getSessionDataForGridByUser.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get("/getSessionMod", function (req, res) {
            var params = {companyId: req.param("companyId")};
            require("./handlers/getSessionMod.js").execute(params, function (data) {
                res.send(data);
            }, function (err) {
                throw err;
            });
        });

        app.get('/', function (req, res) {
            fs.readFile(__dirname + '/html/topic.html', 'utf8', function (err, html) {
                res.send(format_Content(html));
            });
        });

        app.get('/admin', function (req, res) {
            fs.readFile(__dirname + '/html/admin.html', 'utf8', function (err, html) {
                res.send(format_Content(html));
            });
        });

        app.get('/help', function (req, res) {
            fs.readFile(__dirname + '/html/help.html', 'utf8', function (err, html) {
                res.send(format_Content(html));
            });
        });

        app.get('/helpadmin', function (req, res) {
            fs.readFile(__dirname + '/html/helpAdmin.html', 'utf8', function (err, html) {
                res.send(format_Content(html));
            });
        });

        app.use(express.favicon(__dirname + '/html/favicon.ico', { maxAge: 2592000000 }));
	    //app.use(require('./helpers/sessionLoader'));        //TBD!!

        function uploadResourceCallback(user_id, json) {
            var foundUser = _.find(io.sockets.clients(), function (client) {
                return client.user_id === user_id;
            });

            if (foundUser) {
                foundUser.emit("fileuploadcomplete", json);
            }
        }

	    function mapRoutes(verb, routes) {
		    if (_.isArray(routes))
			    routes = [routes];

		    var handlers = _.rest(arguments, 2);

		    _.each(routes, function (route) {
			    var applyArgs = [route].concat(handlers);
			    app[verb].apply(app, applyArgs);
		    });
	    }

        app.post('/uploadimage', function (req, res) {
            socketHelper.uploadResource({
                req: req,
                res: res,
                width: 950,
                height: 460,
                type: 'image',
                resCb: uploadResourceCallback
            });
        });

        app.post('/uploadcollage', function (req, res) {
            socketHelper.uploadResource({
                req: req,
                res: res,
                width: 250,
                height: 250,
                type: 'collage',
                resCb: uploadResourceCallback
            });
        });

        app.post('/uploadaudio', function (req, res) {
            socketHelper.uploadResource({
                req: req,
                res: res,
                type: 'audio',
                resCb: uploadResourceCallback
            });
        });

	    function routes() {
		    mapRoutes('all', ['/register', '/registration.aspx'], require('./pages/register'));
	    }

	    app.get('/insiderfocus-api/gallery', handle('getGallery'));
	    app.get('/insiderfocus-api/gallerySessionsPerTopic', handle('getGallerySessionsPerTopic'));
	    app.get('/insiderfocus-api/galleryTopics', handle('getGalleryTopics'));
	    app.get('/insiderfocus-api/session', handle('getSessionInfo'));

        app.get('/insiderfocus-api/userProfile', handle('getUser'));
        app.post('/insiderfocus-api/userProfile', handle('updateUserV2'));
        app.get('/insiderfocus-api/countryLookup', handle('getCountries'));

	    routes();
        //console.log('Listening for HTTP requests on port ' + app.get('port'));
    },

    close: function () {
        server.close();
    }
};