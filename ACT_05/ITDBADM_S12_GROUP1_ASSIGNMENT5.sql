-- ---
-- Task 1: Enforce Business Rule
-- Scenario: You are asked to ensure that no new film can be inserted with a rental rate over $10.
-- Task: Create a BEFORE trigger on the film table.
-- If a new film's rental_rate is greater than 10, the trigger should block the insert with an error.
-- Tip: Use the SIGNAL SQLSTATE syntax to raise an error.
-- Expected Behavior: Trying to insert a film with rental_rate = 15 should fail with your custom error message.
-- ---


USE sakila;

DELIMITER $$
CREATE TRIGGER enforce_business_rule
BEFORE INSERT
    ON film
    FOR EACH ROW
BEGIN
	IF NEW.rental_rate > 10 THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Error Insert! Rental Rate should be within $10';
    END IF;
END;

$$ DELIMITER ;

INSERT INTO film (title, language_id, rental_rate)
VALUES ('Too Expensive Film', 1, 10.00);

INSERT INTO film (title, language_id, rental_rate)
VALUES ('Too Expensive Film', 1, 15.00);

-- Task 2: Maintain Audit Log for Price Change
-- Scenario: The company wants to track all price changes for films.
-- Task: Create a new table called film_price_audit with these columns:
-- audit_id (auto-increment primary key)
-- film_id (the film’s ID)
-- old_price
-- new_price
-- change_date (timestamp)
-- Create a trigger on the film table. The trigger should insert a row into film_price_audit only if the rental_rate value was changed.
-- Expected Behavior: E.g. When a film’s rental_rate is updated from 2.99 to 3.99, a new row is inserted into film_price_audit with the old and new values.
-- --

USE sakila;

CREATE TABLE film_price_audit (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    film_id SMALLINT,
    old_price DECIMAL(4,2),
    new_price DECIMAL(4,2),
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DELIMITER $$

CREATE TRIGGER after_update_film_rental_rate
AFTER UPDATE ON film
FOR EACH ROW
BEGIN
    IF OLD.rental_rate != NEW.rental_rate THEN
        INSERT INTO film_price_audit (film_id, old_price, new_price)
        VALUES (NEW.film_id, OLD.rental_rate, NEW.rental_rate);
    END IF;
END$$

DELIMITER ;

UPDATE film
SET rental_rate = 9.99
WHERE film_id = 1;


-- Task 3: Handle Deletes with Archive Table
-- Scenario: The business wants to keep a record of any film that is deleted for auditing purposes.
-- Task:
-- 1.Create a new table called film_archive with appropriate columns to store a deleted film’s details (e.g., film_id, title, description, release_year, etc.).

-- 2.Create a trigger on the film table.

-- 3.The trigger should copy the deleted film’s data into film_archive.

-- Expected Behavior: When a film is deleted from film, its data is preserved in the film_archive table.
-- ---


USE sakila;

CREATE TABLE film_archive (
    film_id SMALLINT UNSIGNED NOT NULL,
    title VARCHAR(128) NOT NULL,
    description TEXT DEFAULT NULL,
    release_year YEAR DEFAULT NULL,
    language_id TINYINT UNSIGNED NOT NULL,
    original_language_id TINYINT UNSIGNED DEFAULT NULL,
    rental_duration TINYINT UNSIGNED NOT NULL DEFAULT 3,
    rental_rate DECIMAL(4,2) NOT NULL DEFAULT 4.99,
    length SMALLINT UNSIGNED DEFAULT NULL,
    replacement_cost DECIMAL(5,2) NOT NULL DEFAULT 19.99,
    rating ENUM('G','PG','PG-13','R','NC-17') DEFAULT 'G',
    special_features SET('Trailers','Commentaries','Deleted Scenes','Behind the Scenes') DEFAULT NULL,
    last_update TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (film_id)
);

DELIMITER $$

CREATE TRIGGER archive_deleted_film
AFTER DELETE ON film
FOR EACH ROW
BEGIN
    INSERT INTO film_archive (
        film_id, 
				title, 
				description, 
				release_year, 
				language_id,
        original_language_id, 
				rental_duration, 
				rental_rate, 
				length,
        replacement_cost, 
				rating, 
				special_features,
				last_update
    ) VALUES (
				OLD.film_id, 
				OLD.title,
				OLD.description,
				OLD.release_year, 
				OLD.language_id,
        OLD.original_language_id, 
				OLD.rental_duration, 
				OLD.rental_rate, 
				OLD.length,
        OLD.replacement_cost, 
				OLD.rating, 
				OLD.special_features, 
				OLD.last_update
    );
END;

$$ DELIMITER ;


INSERT INTO film (
    title, description, release_year, language_id,
    rental_duration, rental_rate, length,
    replacement_cost, rating, special_features
)
VALUES (
    'Trigger Test Movie', 'A film to test delete trigger.', 2025, 1,
    7, 3.99, 100,
    15.99, 'PG-13', 'Trailers,Commentaries'
);


DELETE FROM film WHERE film_id = 1001;


SELECT * FROM sakila.film_archive;
