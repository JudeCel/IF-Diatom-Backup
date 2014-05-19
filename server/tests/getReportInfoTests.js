var dataHelper = require("../helpers/dataHelper");
var mtypes = require("if-common").mtypes;
var test = require('tap').test;
var chatHistoryReportHandler = require('../handlers/reportHandlers/chatHistoryReport.js');
var statisticsReportHandler = require('../handlers/reportHandlers/statsReport.js');
var voteReportHandler = require('../handlers/reportHandlers/voteReport.js');
var whiteboardReportHandler = require('../handlers/reportHandlers/whiteboardReport.js');

test("Gets Chat History report info", function (t) {
    var brandName = dataHelper.getTimestamp().toString();
    var info = chatHistoryReportHandler.getReportInfo({
        report: {
            brand: brandName,
            type: mtypes.reportType.chat
        }
    });

    t.ok(info, "Report information is available");
    t.equal(info.layout, mtypes.pageOrientation.portrait, "Report page orientation is correct");
    t.equal(info.title, "Chat History - " + brandName, "Report title is correct");
    t.end();
});

test("Gets Chat History (Stars Only) report info", function (t) {
    var brandName = dataHelper.getTimestamp().toString();
    var info = chatHistoryReportHandler.getReportInfo({
        report: {
            brand: brandName,
            type: mtypes.reportType.chat_stars
        }
    });

    t.ok(info, "Report information is available");
    t.equal(info.layout, mtypes.pageOrientation.portrait, "Report page orientation is correct");
    t.equal(info.title, "Chat History (Stars only) - " + brandName, "Report title is correct");
    t.end();
});

test("Gets Statistics report info", function (t) {
    var brandName = dataHelper.getTimestamp().toString();
    var info = statisticsReportHandler.getReportInfo({
        report: {
            brand: brandName
        }
    });

    t.ok(info, "Report information is available");
    t.equal(info.layout, mtypes.pageOrientation.portrait, "Report page orientation is correct");
    t.equal(info.title, "Whiteboard History - " + brandName, "Report title is correct");
    t.end();
});

test("Gets Vote report info", function (t) {
    var brandName = dataHelper.getTimestamp().toString();
    var info = voteReportHandler.getReportInfo({
        report: {
            brand: brandName
        }
    });

    t.ok(info, "Report information is available");
    t.equal(info.layout, mtypes.pageOrientation.portrait, "Report page orientation is correct");
    t.equal(info.title, "Voting - " + brandName, "Report title is correct");
    t.end();
});

test("Gets Vote report info", function (t) {
    var brandName = dataHelper.getTimestamp().toString();
    var info = whiteboardReportHandler.getReportInfo({
        report: {
            brand: brandName
        }
    });

    t.ok(info, "Report information is available");
    t.equal(info.layout, mtypes.pageOrientation.landscape, "Report page orientation is correct");
    t.equal(info.title, "Whiteboard History - " + brandName, "Report title is correct");
    t.end();
});