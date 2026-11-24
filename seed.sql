-- OBSCURA DEMO DATABASE SEEDER
-- For local educational testing only.
-- Demo password for all accounts: ObscuraDemo2026!

START TRANSACTION;

-- 1. Create demo users.
INSERT INTO users (username, email, password_hash, role)
VALUES
('demo_admin', 'admin@obscura.test',
 '$2y$10$NgvR7LUi/gt0nc7AHPewWeK6NHqfIpfdgSozcd3qFswyYZSLCQ996', 'admin'),
('demo_alice', 'alice@obscura.test',
 '$2y$10$NgvR7LUi/gt0nc7AHPewWeK6NHqfIpfdgSozcd3qFswyYZSLCQ996', 'user'),
('demo_bob', 'bob@obscura.test',
 '$2y$10$NgvR7LUi/gt0nc7AHPewWeK6NHqfIpfdgSozcd3qFswyYZSLCQ996', 'user')
ON DUPLICATE KEY UPDATE id = id;

-- 2. Create sample notes without duplicates.
INSERT INTO notes (user_id, title, content)
SELECT u.id, 'Welcome to Obscura',
       'This is a demonstration note for testing the notes application.'
FROM users u
WHERE u.username = 'demo_alice'
AND NOT EXISTS (
    SELECT 1 FROM notes n
    WHERE n.user_id = u.id AND n.title = 'Welcome to Obscura'
);

INSERT INTO notes (user_id, title, content)
SELECT u.id, 'Security Testing',
       'This note can be used for search and CRUD testing.'
FROM users u
WHERE u.username = 'demo_alice'
AND NOT EXISTS (
    SELECT 1 FROM notes n
    WHERE n.user_id = u.id AND n.title = 'Security Testing'
);

INSERT INTO notes (user_id, title, content)
SELECT u.id, 'Bob Private Note',
       'This note belongs to Bob and can be used for access-control testing.'
FROM users u
WHERE u.username = 'demo_bob'
AND NOT EXISTS (
    SELECT 1 FROM notes n
    WHERE n.user_id = u.id AND n.title = 'Bob Private Note'
);

INSERT INTO notes (user_id, title, content)
SELECT u.id, 'Admin Demo Note',
       'This note belongs to the demonstration administrator.'
FROM users u
WHERE u.username = 'demo_admin'
AND NOT EXISTS (
    SELECT 1 FROM notes n
    WHERE n.user_id = u.id AND n.title = 'Admin Demo Note'
);

-- Generate 120 additional demo notes (40 per account). xDDD
-- Re-running the seeder will not create duplicates, hopefully ahahah 

INSERT INTO notes (user_id, title, content)
SELECT
    u.id,
    CONCAT('Demo Note ', LPAD(n.num, 3, '0')),
    CONCAT(
        'Sample content for security testing. ',
        'Owner: ', u.username,
        '. Note number: ', n.num,
        '. Used for search, SQL injection and access-control demonstrations.'
    )
FROM users u
CROSS JOIN (
    SELECT ones.n + tens.n * 10 + 1 AS num
    FROM (
        SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2
        UNION ALL SELECT 3 UNION ALL SELECT 4
        UNION ALL SELECT 5 UNION ALL SELECT 6
        UNION ALL SELECT 7 UNION ALL SELECT 8
        UNION ALL SELECT 9
    ) ones
    CROSS JOIN (
        SELECT 0 AS n UNION ALL SELECT 1
        UNION ALL SELECT 2 UNION ALL SELECT 3
    ) tens
) n
WHERE u.username IN ('demo_admin', 'demo_alice', 'demo_bob')
AND NOT EXISTS (
    SELECT 1
    FROM notes existing
    WHERE existing.user_id = u.id
    AND existing.title = CONCAT('Demo Note ', LPAD(n.num, 3, '0'))
);

COMMIT;
