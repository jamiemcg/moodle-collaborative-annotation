#Creates the table mdl_annotation_image in the database bitnami_moodle
#Use to store annotations for images

using bitnami_moodle;

CREATE table mdl_annotation_image
(
	id int NOT NULL AUTO_INCREMENT,
	userid int NOT NULL,
	username varchar,
	annotation text,
	shapes varchar NOT NULL,
	url varchar NOT NULL,
	timecreated int NOT NULL,
	tags varchar
);