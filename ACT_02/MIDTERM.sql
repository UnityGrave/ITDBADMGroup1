
-- SQL TASK 1: MAKING A DATABASE
-- Task 1-1: Create and Use a Schema
-- a. CREATE and USE a schema named PBB_Collab.
-- b. CREATE A TABLE named housemates that stores basic information about the PBB Collab housemates. Use the structure below:

CREATE SCHEMA `pbb_collab`;

USE `pbb_collab`;

CREATE TABLE `pbb_collab`.`housemates` (
  `housemate_id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(45) NULL,
  `last_name` VARCHAR(45) NULL,
  `birth_date` DATE NULL,
  `gender` ENUM('M', 'F', 'O') NULL,
  `evicted_status` TINYINT NULL,
  PRIMARY KEY (`housemate_id`));
  
-- Task 1-2: Insert Housemates
-- a. Write SQL statements to insert the following 3 housemates into the housemates table:

USE `pbb_collab`;

INSERT INTO housemates (first_name, last_name, birth_date, gender, evicted_status)
VALUES ('Shuvee', 'Etrata', '2001-06-04', 'F', TRUE), ('Klarisee', 'de Guzman', '1991-09-06', 'F', TRUE), ('AZ', 'Martinez', '2003-03-02', 'F', FALSE);

--  Task 1-3: String Manipulation with Names
-- Write an SQL query that returns a list of housemates with the following output:
-- a. A new column called full_name that combines their first_name and last_name
-- b. The full_name should be displayed in all lowercase letters

USE `pbb_collab`;

SELECT 
	housemates
FROM
	first_name


  