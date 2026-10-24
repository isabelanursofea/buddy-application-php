# Buddy Application: A Web-Based Social Networking System

A streamlined, beginner-friendly web platform for managing a small, closed social circle, built for **CSC574 Dynamic Web Application Development** (Group CDCS2304A, Faculty of Computer and Mathematical Sciences, UiTM).

## Problem Statement
Mainstream social networks are bloated and poorly suited to small, closed environments such as campuses or departments. The challenges identified:

- **Lack of a unified space:** no simple, central portal to organise a local social circle.
- **Hidden connections:** no intuitive way to explore mutual friends or shared contacts.
- **Weak administrative control:** admins lack tools to verify, monitor and manage registrations.
- **No demographic insights:** no easy visual statistics about the makeup of a user's circle.

## Objectives
1. Build a secure web app (HTML, CSS, JavaScript, PHP, MySQL) where users can sign up, log in and customise their profiles.
2. Provide an admin control panel for managing user records, plus dynamic demographic reports using CanvasJS charts.
3. Deliver a responsive, user-friendly interface using clean HTML and CSS.

## Features

### User
- Sign up, sign in and sign out
- Profile page with editable details and password update
- Manage friend records: add, view, edit, delete and search (photo, gender, phone, email, address)
- Reports: gender and state distribution charts (CanvasJS) and a downloadable PDF report (FPDF)

### Admin
- Admin dashboard
- Manage users: view, search, add, edit and delete accounts
- Approve pending sign-ups
- Users listed by role (Admin / User)

## Constraints
- Closed system: no external APIs or outside messaging
- Runs on a local machine only (XAMPP); no public hosting or cloud backup
- Data stored in a local MySQL database

## Tech Stack
HTML5, CSS3, JavaScript, PHP, MySQL, CanvasJS, FPDF

## Database
Two tables, `users` and `friends`, linked by `user_id` (one-to-many). Deleting a user cascades to their friend records. An admin is a user whose `role` is `admin`. The schema is in `database/buddy_db.sql`.

## How to Run
1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy this folder into `C:\xampp\htdocs\`.
3. Open phpMyAdmin, create a database named `buddy_db`, and import `database/buddy_db.sql`.
4. Check the connection settings in `database/dbconn.php`.
5. Open `http://localhost/<folder-name>/` in your browser.

## Team (Group CDCS2304A)
| Member | Role |
|---|---|
| Nur Alyssa Hani binti Riduan | Authentication & Admin Developer |
| Nicole Marsha binti Lucius | User Module & Front-End Developer |
| Isabela Nursofea binti Noor Azman | Database Designer |
| Eyva Greaty Geoffrey | System Analyst |
| Amira Adani binti Sabarudin | Technical Writer & UI Documentation |
