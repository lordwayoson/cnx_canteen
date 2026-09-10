USE canteen_db;

INSERT INTO admin_user (username, password_hash, role) VALUES
('admin', '$2y$12$mgchcbTRlUNNTyOTT1U72eH6KUoNkgg4K1hZlVshDMI0ck1ODxxse', 'admin'),
('kitchen', '$2y$12$ZQUAYZv5q8jS02U9zX.QfefNcnJo64O1FTqmsnUnFyE9HY50xeZkq', 'kitchen')
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = VALUES(role);

INSERT INTO user (userid, username, name, lastname, email, project, cardnumber)
VALUES
(1001, 'jdoe', 'John', 'Doe', 'john.doe@example.com', 'Project Alpha', 'RFID1001'),
(1002, 'aadu', 'Ama', 'Adu', 'ama.adu@example.com', 'Project Beta', 'RFID1002')
ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    name = VALUES(name),
    lastname = VALUES(lastname),
    email = VALUES(email),
    project = VALUES(project),
    cardnumber = VALUES(cardnumber);

INSERT INTO meal_selection (staff_id, project, shift_type, mon, tue, wed, thu, fri, sat, sun, diet_notes, sheet_row_id, week_start_date)
VALUES
(1001, 'Project Alpha', 'Day', 'Jollof Rice', 'Banku', 'Waakye', 'Fufu', 'Fried Rice', 'Kenkey', 'Light Soup', 'Nut-free', 'demo-row-1', '2024-06-03'),
(1002, 'Project Beta', 'Night', 'Kelewele', 'Jollof Rice', 'Rice Balls', 'Yam', 'Ampesi', 'Salad', 'Soup', 'Vegetarian', 'demo-row-2', '2024-06-03')
ON DUPLICATE KEY UPDATE
    project = VALUES(project),
    shift_type = VALUES(shift_type),
    mon = VALUES(mon),
    tue = VALUES(tue),
    wed = VALUES(wed),
    thu = VALUES(thu),
    fri = VALUES(fri),
    sat = VALUES(sat),
    sun = VALUES(sun),
    diet_notes = VALUES(diet_notes),
    week_start_date = VALUES(week_start_date);
