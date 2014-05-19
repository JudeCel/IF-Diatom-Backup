CREATE TABLE temp38b44d679151578f001d51b388fd110d
(
uId INT,
cId INT
);

DELIMITER //
CREATE PROCEDURE markSession(sId INT)
BEGIN
		INSERT INTO temp38b44d679151578f001d51b388fd110d
		(
			SELECT pl1.`id` AS participant,
			`participant_colour_lookup`.id AS colour
			FROM `participant_lists` AS pl1
			JOIN `participant_colour_lookup`
			WHERE 
			pl1.`session_id`=sId
			AND
			pl1.`participant_colour_lookup_id` IN 
			(
				SELECT pl2.participant_colour_lookup_id 
				FROM `participant_lists`AS pl2
				WHERE (pl2.`session_id`=sId) AND (pl1.`id`<>pl2.`id`)
			)
			AND
			`participant_colour_lookup`.id NOT IN
				(
					SELECT participant_colour_lookup_id 
					FROM `participant_lists`
					WHERE session_id=sId AND NOT ISNULL(participant_colour_lookup_id)
				)
			LIMIT 0,1 
		);
END//


CREATE PROCEDURE markAllSessions()
BEGIN
	SET @i := 0;
	SET @q = NULL;
	PREPARE look FROM 'SELECT `session_id`
	FROM `participant_lists` AS pl1
	WHERE pl1.`participant_colour_lookup_id` IN 
	(
	SELECT participant_colour_lookup_id 
	FROM `participant_lists`AS pl2
	WHERE (pl1.`session_id`=pl2.`session_id`) AND (pl1.`id`<>pl2.`id`)
	)
	GROUP BY session_id
	LIMIT ?,1 into @q';
	EXECUTE look USING @i;
	WHILE NOT ISNULL(@q) DO
		CALL markSession(@q);
		SET @q:= NULL;
		SET @i:=@i+1;
		EXECUTE look USING @i;
	END WHILE;
	DEALLOCATE PREPARE look;
END//

CREATE PROCEDURE fixSessionColourJSON()
BEGIN
UPDATE sessions
SET colours_used=CONCAT('["',
	(
	SELECT GROUP_CONCAT(`participant_colour_lookup_id`
		SEPARATOR '","')
		FROM `participant_lists`
		WHERE `session_id`=sessions.id
		GROUP BY session_id
	),
'"]');
END//

CREATE PROCEDURE fixAllSessions()
BEGIN
CALL markAllSessions();
UPDATE `participant_lists` JOIN temp38b44d679151578f001d51b388fd110d ON id=uId
SET `participant_colour_lookup_id`=cId;
CALL fixSessionColourJSON();
END//
DELIMITER ;

CALL fixAllSessions();

DROP TABLE temp38b44d679151578f001d51b388fd110d;
DROP PROCEDURE markSession;
DROP PROCEDURE markAllSessions;
DROP PROCEDURE fixSessionColourJSON;
DROP PROCEDURE fixAllSessions;