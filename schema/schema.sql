# schema for rover proejct...

CREATE database `rover`;

use `rover`;

#the first thing we want to do is check our data and place data into categories and see what we can improve

# we are given the 9 fields below -> i am assuming text is the review of the sit...
#rating  sitter_image  end_date  text  owner_image   dogs  sitter  owner   start_date


# 3 categories for now
#sitters
# attributes - overall_rating, sitter_image, sitter

#owners
# attributes - owner_image, owner

#owner_pets

#pet_types (dogs for now)

#sitter_appointments - rating, start_date, end_date, text
#sitter_appointments_pets
#sitter_appointments_pets (sitter_appointment_id, owner_pet_id)
#so let's start by creating some tables...

CREATE TABLE `sitters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  `rating` double unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `owners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  `rating` double unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# adding pet types for future designation
CREATE TABLE `pet_types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `pet_types` VALUES (1,'Dog'),(2,'Cat');

CREATE TABLE `owner_pets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `pet_type_id` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitter_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pet_count` int(11) DEFAULT NULL,
  `review` text,
  `rating` double unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `appointment_pets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `owner_pet_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# now, we want to put the data in. I will create a temp database to store the data we have recovered, and then parse...
# we can load in the names, assuming distinct names and image pair from old data...

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating` double unsigned NOT NULL DEFAULT '0',
  `sitter_image` varchar(100) NOT NULL,
  `end_date` date NOT NULL,
  `review` text,
  `owner_image` varchar(100) NOT NULL,
  `dogs` varchar(255) NOT NULL,
  `sitter_name` varchar(100) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# load in our original data but ignore first row of col names
load data local infile 'reviews.csv' into table  reviews fields terminated by ','
enclosed by '"'
lines terminated by '\n' IGNORE 1 LINES
(rating, sitter_image, end_date, review, owner_image, dogs, sitter_name, owner_name, start_date);

# spot check some rows...rows look fine.

#next, do some quick queries on the data to see what we are dealing
#mysql> select count(DISTINCT owner_name) FROM reviews;
#+----------------------------+
#| count(DISTINCT owner_name) |
#+----------------------------+
#|                        186 |
#+----------------------------+
#1 row in set (0.00 sec)

#mysql> select count(DISTINCT owner_image) FROM reviews;
#+-----------------------------+
#| count(DISTINCT owner_image) |
#+-----------------------------+
#|                         189 |
#+-----------------------------+
#1 row in set (0.00 sec)

#mysql> select count(DISTINCT sitter_image) FROM reviews;
#+------------------------------+
#| count(DISTINCT sitter_image) |
#+------------------------------+
#|                          100 |
#+------------------------------+
#1 row in set (0.00 sec)

#mysql> select count(DISTINCT dogs) FROM reviews;
#+----------------------+
#| count(DISTINCT dogs) |
#+----------------------+
#|                  189 |
#+----------------------+
#1 row in set (0.00 sec)
#
#mysql> select count(DISTINCT sitter_name) FROM reviews;
#+-----------------------------+
#| count(DISTINCT sitter_name) |
#+-----------------------------+
#|                         100 |
#+-----------------------------+
#1 row in set (0.00 sec)

# we will be safe here, and assume a different (image, name) implies a different user...
INSERT INTO sitters (name, image, created) SELECT sitter_name, sitter_image, now() FROM reviews GROUP BY sitter_name,sitter_image;

INSERT INTO owners (name, image, created) SELECT owner_name, owner_image, now() FROM reviews GROUP BY owner_image,owner_name;

INSERT INTO appointments
  (sitter_id, owner_id, start_date, end_date, pet_count, review, rating, created)
SELECT 
  sitters.id,
  owners.id,
  reviews.start_date,
  reviews.end_date, 
  LENGTH(`dogs`) - LENGTH(REPLACE(`dogs`, '|', ''))+1,
  reviews.review,
  reviews.rating,
  now()
FROM reviews 
INNER JOIN sitters ON reviews.sitter_name = sitters.name AND reviews.sitter_image = sitters.image
INNER JOIN owners ON reviews.owner_name = owners.name AND reviews.owner_image = owners.image;

# now , we need to insert the pets - pets are distinct if owner and owner_image are the same.
# we can find the max pet count and thats 3...

#below neatly splices first pet, second pet and third pet
SELECT
   SUBSTRING_INDEX(SUBSTRING_INDEX(dogs, '|', 1), '|', -1) AS first_pet,
   If(length(dogs) - length(replace(dogs, '|', ''))>0,  
       SUBSTRING_INDEX(SUBSTRING_INDEX(dogs, '|', 2), '|', -1) ,NULL) 
           as second_pet,
   If(length(dogs) - length(replace(dogs, '|', ''))=2,  
       SUBSTRING_INDEX(SUBSTRING_INDEX(dogs, '|', 3), '|', -1) ,NULL) 
           as third_pet,   dogs
FROM reviews;


INSERT INTO owner_pets (name, pet_type_id, owner_id, created)
SELECT
  SUBSTRING_INDEX(SUBSTRING_INDEX(dogs, '|', 1), '|', -1),
  1,
  owners.id,
  now()
FROM reviews
  INNER JOIN owners ON reviews.owner_name = owners.name AND reviews.owner_image = owners.image
GROUP BY owners.id;



INSERT INTO owner_pets (name, pet_type_id, owner_id, created)
SELECT
   If(length(dogs) - length(replace(dogs, '|', ''))>0,  
       SUBSTRING_INDEX(SUBSTRING_INDEX(dogs, '|', 2), '|', -1) ,NULL) as name,
  1,
  owners.id,
  now()
FROM reviews
  INNER JOIN owners ON reviews.owner_name = owners.name AND reviews.owner_image = owners.image
WHERE  If(length(dogs) - length(replace(dogs, '|', ''))>0,  
       SUBSTRING_INDEX(SUBSTRING_INDEX(dogs, '|', 2), '|', -1) ,NULL) IS NOT NULL
GROUP BY owners.id;


INSERT INTO owner_pets (name, pet_type_id, owner_id, created)
SELECT
   If(length(dogs) - length(replace(dogs, '|', ''))=2,  
       SUBSTRING_INDEX(SUBSTRING_INDEX(dogs, '|', 3), '|', -1) ,NULL) 
           as name,
  1,
  owners.id,
  now()
FROM reviews
  INNER JOIN owners ON reviews.owner_name = owners.name AND reviews.owner_image = owners.image
WHERE    If(length(dogs) - length(replace(dogs, '|', ''))=2,  
       SUBSTRING_INDEX(SUBSTRING_INDEX(dogs, '|', 3), '|', -1) ,NULL) IS NOT NULL
GROUP BY owners.id;

# now, update the rankings...
#http://localhost/rover/sitters/set_rank

# view search page
#http://localhost/rover/sitters/index

