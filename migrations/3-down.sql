UPDATE `${wpdb_prefix}postmeta`
SET `meta_key` = "eddbk_session_lengths"
WHERE `meta_key` = "eddbk_session_types";

-- Delete the resources table
DROP TABLE IF EXISTS `${cqrs/resources/table}`;

-- Delete the session-resources relationship table
DROP TABLE IF EXISTS booking_resources;

-- Delete the session-resources relationship table
DROP TABLE IF EXISTS session_resources;

-- Re-add the resource_id column to the bookings table
ALTER table `${cqrs/bookings/table}` ADD `resource_id` int NOT NULL;
-- Set all resource IDs in the bookings table to be equal to service IDs
UPDATE `${cqrs/bookings/table}` SET `resource_id` = `service_id`;

-- Re-add the resource_id column to the bookings table
ALTER table `${cqrs/sessions/table}` ADD `resource_id` int NOT NULL;
