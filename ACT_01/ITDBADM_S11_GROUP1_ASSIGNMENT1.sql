/* GROUP 1
	GENOTA, Kean
	GRINO, Gem
    SANTOS, Jerome
*/

-- (1) Big Brother requests that you design and make a database of his housemates.

-- INIT PBB_Collab DB
CREATE DATABASE IF NOT EXISTS pbb_collab;
USE pbb_collab;

-- TABLES
CREATE TABLE housemates(
	id INT NOT NULL,
	firstName VARCHAR(100) NOT NULL,
    lastName  VARCHAR(100) NOT NULL,
	gender VARCHAR(20) NOT NULL,
    age INT NOT NULL,
    hometown VARCHAR(200) NOT NULL,
    dayEntered INT NOT NULL,
    CONSTRAINT housemates_PK PRIMARY KEY (id)
);

CREATE TABLE agency(
	id INT NOT NULL,
    agencyName VARCHAR(50),
    CONSTRAINT agency_PK PRIMARY KEY (id)
)

-- (2)  Delete the first two housemates evicted. (AC and Ashley)

SELECT*FROM housemates;

DELETE FROM housemates
WHERE ID IN (1,2);

-- (3)  Show the agency of the housemate (Star Magic or Sparkle)

SELECT a.id, a.agencyName, h.firstName, h.lastName
FROM agency a
INNER JOIN housemates h ON a.id = h.id;

-- (4) Add a new housemate (Bianca)

INSERT INTO housemates (id, firstName, lastName, gender, age, hometown, dayEntered)
VALUES (16, 'Bianca', 'de Vera', 'Female', 23, 'Taguig', 22)
