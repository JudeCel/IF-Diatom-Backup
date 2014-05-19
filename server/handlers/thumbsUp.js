"use strict";
var _ = require('lodash');
var ifData = require('if-data'), db = ifData.db;
var joi = require('joi');
var webFaultHelper = require('../helpers/webFaultHelper.js');
var getEvent = ifData.repositories.getEvent;
var getUserVotes = ifData.repositories.getUserVotes;
var getVotes = ifData.repositories.getVotes;
var createVote = ifData.repositories.createVote;
var updateVote = ifData.repositories.updateVote;
var createUserVotes =  ifData.repositories.createUserVotes;

module.exports.validate = function (req, next) {
    var err = joi.validate(req.params, {
        event_id: joi.types.Number().required()
    });

    if (err) return next(webFaultHelper.getValidationFault(err.message));

    next();
};

module.exports.run = function (req, res, mainCb) {
	var topicId = 0;

	getEvent(req.params.event_id)
		.then(function (event) {
			if (!event) return;
			topicId = event.topic_id;
			getUserVotes(event.user_id, event.topic_id, req.params.event_id)
				.then(function (userVotes) {
					if (userVotes && userVotes.length > 0) return;
					return getVotes(req.params.event_id);
				})
				.then (function (votes) {
					if (!votes || votes.length == 0) {
						createVote({
							event_id: req.params.event_id,
							count: 1
						})
					}
					else {
						updateVote({
							id: votes[0].id,
							count: votes[0].count + 1
						});
					}
				})
				.then(function(){
					return getVotes({
						event_id: req.params.event_id
					})
				})
				.then (function(vote) {
					if (vote) {
						createUserVotes({
							vote_id: vote.id,
							user_id: req.params.user_id,
							topic_id: topicId,
							event_id: req.params.event_id
						})
						return vote.count;
					}
					return 0;
				})
				.done(function (votesCount) {
					res.send(votesCount);
				}, mainCb);
		})
		.done(function (votesCount) {
			res.send();
		}, mainCb);
};