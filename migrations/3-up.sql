UPDATE `${wpdb_prefix}postmeta`
SET `meta_key` = "eddbk_session_types"
WHERE `meta_key` = "eddbk_session_lengths";

CREATE TABLE `${cqrs/resources/table}`
(
	id bigint AUTO_INCREMENT PRIMARY KEY,
	type varchar(100) NOT NULL,
	name varchar(255) NOT NULL
);

