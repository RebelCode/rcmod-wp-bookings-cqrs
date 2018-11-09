UPDATE `${wpdb_prefix}postmeta`
SET `meta_key` = "eddbk_session_lengths"
WHERE `meta_key` = "eddbk_session_types";

-- Delete the resources table
DROP TABLE IF EXISTS `${cqrs/resources/table}`;

-- Delete the session-resources relationship table
DROP TABLE IF EXISTS booking_resources;

-- Delete the session-resources relationship table
DROP TABLE IF EXISTS session_resources;
