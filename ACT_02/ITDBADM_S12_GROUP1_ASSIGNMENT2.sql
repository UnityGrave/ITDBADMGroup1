-----

-- Task 1: Count Customer Rentals

-- Create a report showing how many times each customer has rented a movie.

-- Write a SQL query to display the ID, first name, and last name of each customer along with the total number of rentals they have made.

-- Sort the results by total_rental in descending order

-- Tables Used: customer,  rental

-- Expected Output Columns: customer_id, first_name , last_name , total_rental

SELECT c.customer_id, c.first_name, c.last_name, COUNT(r.rental_id) AS total_rental
FROM customer c
JOIN rental r ON c.customer_id = r.customer_id
GROUP BY c.customer_id
ORDER BY total_rental DESC;

-- Task 2: Calculate Rental Duration

-- Create a report that will show the rental durations for each customer.

-- Write a SQL query to display the ID, first name, and last name of each customer, along with the number of days between their rental date and return date for each rental.

-- Only include rentals where the return_date is not NULL. (Note: If the return data is NULL, it means the DVD is still with the customer and is not yet returned.)

-- Tables Used: customer, rental

-- Expected Output Columns: customer_id, first_name, last_name, rental_id, rental_duration_days

SELECT c.customer_id, c.first_name, c.last_name,
		r.rental_id, DATEDIFF(r.return_date, r.rental_date) AS rental_duration_days
FROM customer c
JOIN rental r ON c.customer_id = r.customer_id
WHERE r.return_date IS NOT NULL;


---

-- Task 3: Count Rentals per Film

-- Create a report that will show how many times a film has been rented out.

-- Write a SQL query to display the title of each film and the total number of times it has been rented.

-- Sort the results by total_rental in ascending order.

-- Tables Used: film, inventory, rental

-- Expected Output Columns: title, total_rentals


SELECT f.title, COUNT(r.rental_id) AS total_rentals
FROM film f
JOIN inventory i ON f.film_id = i.film_id
JOIN rental r ON r.inventory_id = i.inventory_id
GROUP BY f.title
ORDER BY total_rentals ASC;

---

-- Task 4: Identify High-Activity Customers

-- Write a SQL query to find all customers who have made more than 17 rentals.

-- Display the customer's ID, first name, last name, and the total number of rentals they have made.

-- Return the result ordered by total_rentals in ascending order.

-- Tables Used: customer, rental

-- Expected Output Columns: customer_id, first_name, last_name, total_rental\

SELECT c.customer_id, c.first_name, c.last_name, COUNT(r.rental_id) AS total_rental
FROM customer c
JOIN rental r ON c.customer_id = r.customer_id
GROUP BY c.customer_id
HAVING total_rental > 17
ORDER BY total_rental ASC;
