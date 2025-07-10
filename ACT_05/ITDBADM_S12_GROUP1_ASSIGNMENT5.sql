-- ---
-- Task 1: Enforce Business Rule
-- Scenario: You are asked to ensure that no new film can be inserted with a rental rate over $10.
-- Task: Create a BEFORE trigger on the film table.
-- If a new film's rental_rate is greater than 10, the trigger should block the insert with an error.
-- Tip: Use the SIGNAL SQLSTATE syntax to raise an error.
-- Expected Behavior: Trying to insert a film with rental_rate = 15 should fail with your custom error message.
-- ---


-- use sakila

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

-- Task 3: Handle Deletes with Archive Table
-- Scenario: The business wants to keep a record of any film that is deleted for auditing purposes.
-- Task:
-- 1.Create a new table called film_archive with appropriate columns to store a deleted film’s details (e.g., film_id, title, description, release_year, etc.).

-- 2.Create a trigger on the film table.

-- 3.The trigger should copy the deleted film’s data into film_archive.

-- Expected Behavior: When a film is deleted from film, its data is preserved in the film_archive table.
-- ---
