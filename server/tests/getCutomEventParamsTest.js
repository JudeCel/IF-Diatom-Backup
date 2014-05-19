/*
 Renamed from getUpdateEventsParamsTest.js
 */
"use strict";
var _ = require('lodash');
var commonOperations = require('./testHelpers/commonOperations.js');
var ifData = require('if-data'), db = ifData.db;
var test = require('tap').test;
var util = require('util');
var ifTestHelpers = require('if-test-helpers');
var expressValidatorStub = require('./testHelpers/expressValidatorStub.js');
var testUtils = ifTestHelpers.utils;
var getCustomEventParams = require("../socketHelper/getCustomEventParams.js");

test('Gets params of command [shareresource] & event w/ type image', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "shareresource",
        event: JSON.stringify({"id": 84, "target": "whiteboard", "type": "image", "content": "http://thirdwavemediaptyltd.hosting24.com.au/ifs-test/IFS/uploads/Sunflower.gif", "actualSize": {"width": 48, "height": 48}, "updateEvent": true})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 0, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [shareresource] & event w/ type vote', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "shareresource",
        event: JSON.stringify({"id": 91, "target": "console", "type": "vote", "content": "%7B%22title%22:%22Flags?%22,%22question%22:%22Do%20you%20like%20flags?%22,%22style%22:%22YesNoUnsure%22%7D", "updateEvent": true})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 1, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [shareresource] & event w/ type audio', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "shareresource",
        event: JSON.stringify({"id": 94, "target": "console", "type": "audio", "content": "http://thirdwavemediaptyltd.hosting24.com.au/ifs-test/IFS/uploads/Test.mp3", "updateEvent": true})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 2, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [shareresource] & event w/ type video', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "shareresource",
        event: JSON.stringify({"id": 85, "target": "console", "type": "video", "content": "<iframe width=\"420\" height=\"315\" src=\"http://www.youtube.com/embed/KyW_waaWFio\" frameborder=\"0\" allowfullscreen></iframe>", "updateEvent": true})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 4, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [shareresource] & event w/ type video', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "shareresource",
        event: JSON.stringify({"type": "pictureboard", "content": "false"})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 32, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [shareresource] & event w/ type video', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "shareresource",
        event: JSON.stringify({"type": "null", "content": "null", "id": -1, "updateEvent": true})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 65536, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [image]', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "image",
        event: JSON.stringify({"some_property": "some_value"})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 0, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [vote]', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "vote",
        event: JSON.stringify({"id": 1})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 1, 'result.tag is valid');
    t.equal(result.replyId, 1, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [audio]', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "audio",
        event: JSON.stringify({"some_property": "some_value"})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 2, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [video]', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "video",
        event: JSON.stringify({"some_property": "some_value"})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 4, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.end();
});

test('Gets params of command [chat]', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "chat",
        event: JSON.stringify({"name": "some_name", object: { mode: { messageId: 1}}})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 8, 'result.tag is valid');
    t.equal(result.replyId, 1, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.ok(result.responseObject, 'responseObject is returned');
    t.end();
});

test('Gets params of command [object]', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "object",
        event: JSON.stringify({"name": "some_name", object: { mode: { messageId: 1}}})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 16, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.ok(result.responseObject, 'responseObject is returned');
    t.end();
});

test('Gets params of command [pictureboard]', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "pictureboard",
        event: JSON.stringify({"name": "some_name", object: { mode: { messageId: 1}}})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 32, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.ok(result.responseObject, 'responseObject is returned');
    t.end();
});

test('Gets params of command [null]', function (t) {
    var params = {
        topicId: 1,
        userId: 2,
        command: "null",
        event: JSON.stringify({"name": "some_name", object: { mode: { messageId: 1}}})
    };

    var result = getCustomEventParams(params);
    t.ok(result, 'result returned');
    t.ok(result.isValid, 'result is valid');
    t.equal(result.tag, 65536, 'result.tag is valid');
    t.equal(result.replyId, undefined, 'result.replyId is valid');
    t.equal(result.event, encodeURI(params.event), 'result.event is valid');
    t.ok(result.responseObject, 'responseObject is returned');
    t.end();
});