
-- Table 1: category (Parent Table)
CREATE TABLE category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 2: reporter (Parent Table)
CREATE TABLE reporter (
    reporter_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 3: news (Child Table with Foreign Keys and Index on title)
CREATE TABLE news (
    news_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    reporter_id INT NOT NULL,
    published_on DATE NOT NULL,
    INDEX idx_news_title (title),
    FOREIGN KEY (category_id) REFERENCES category(category_id),
    FOREIGN KEY (reporter_id) REFERENCES reporter(reporter_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==================================================
-- PART 4 — INSERT DATA
-- ==================================================

-- Insert categories (multi-row INSERT)
INSERT INTO category (category_id, category_name) VALUES
(1, 'National'),
(2, 'Sports'),
(3, 'Technology');

-- Insert reporters (multi-row INSERT)
INSERT INTO reporter (reporter_id, full_name) VALUES
(1, 'Amina Karim'),
(2, 'Rafiq Islam');

-- Insert four news records using foreign keys (multi-row INSERT)
INSERT INTO news (news_id, title, description, category_id, reporter_id, published_on) VALUES
(1, 'City Flood Alert', 'Emergency teams and municipal authorities have issued a flood alert for low-lying riverside areas following continuous heavy rainfall.', 1, 1, '2026-09-01'),
(2, 'U-17 Final',       'The regional under-17 football championship reaches its climax this weekend as top youth teams face off for the title.',       2, 2, '2026-09-02'),
(3, 'New Campus Lab',   'The university inaugurated a state-of-the-art computer science laboratory equipped with modern robotics testing kits.',          3, 1, '2026-09-03'),
(4, 'Campus Fair',      'Students and local innovators gathered at the annual university grounds to showcase technological inventions and community projects.', 3, 2, '2026-09-04');


-- ==================================================
-- PART 5 — RETRIEVE NEWS (INITIAL JOIN QUERY)
-- ==================================================
SELECT 
    n.title,
    n.description,
    c.category_name,
    r.full_name AS reporter_name,
    n.published_on
FROM news n
INNER JOIN category c ON n.category_id = c.category_id
INNER JOIN reporter r ON n.reporter_id = r.reporter_id;


-- ==================================================
-- PART 6 — SEARCH
-- ==================================================
-- Direct concatenation of user/visitor input into SQL statements can allow SQL Injection (SQLi) attacks.
-- If user-supplied text like "' OR '1'='1" is directly joined into an SQL string, the query's structure
-- is compromised, potentially exposing, modifying, or deleting database data.
-- To prevent SQL injection, parameterized queries / prepared-statement approaches must be used so that
-- search input is strictly treated as data rather than executable SQL syntax.

SET @search_term = '%Flood%';

PREPARE search_stmt FROM
    'SELECT n.title, n.description, c.category_name, r.full_name AS reporter_name, n.published_on
     FROM news n
     INNER JOIN category c ON n.category_id = c.category_id
     INNER JOIN reporter r ON n.reporter_id = r.reporter_id
     WHERE n.title LIKE ?';

EXECUTE search_stmt USING @search_term;
DEALLOCATE PREPARE search_stmt;


-- ==================================================
-- PART 7 — UPDATE
-- ==================================================
UPDATE news
SET title = 'Flood Watch'
WHERE title = 'City Flood Alert';


-- ==================================================
-- PART 8 — DELETE
-- ==================================================
DELETE FROM news
WHERE title = 'Campus Fair';


-- ==================================================
-- PART 9 — FINAL RETRIEVAL (JOIN QUERY AFTER UPDATE & DELETE)
-- ==================================================
SELECT 
    n.title,
    n.description,
    c.category_name,
    r.full_name AS reporter_name,
    n.published_on
FROM news n
INNER JOIN category c ON n.category_id = c.category_id
INNER JOIN reporter r ON n.reporter_id = r.reporter_id;
