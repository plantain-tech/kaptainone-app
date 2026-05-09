# OpenClaw Instructions for Kaptain One

This is a PHP/MySQL web application hosted on Hostinger shared hosting.

## Project Location

C:\Users\Raider GE76\.openclaw\workspace\kaptain-one

## Hosting Structure

The project root is designed to be uploaded directly into Hostinger public_html.

Main public pages are in the root:
- index.php
- about.php
- contact.php
- solutions.php

Main folders:
- admin/ = admin dashboard and management pages
- assets/ = CSS, JavaScript, images, icons
- blog/ = blog pages or blog system
- config/ = configuration files
- includes/ = shared PHP components like header, footer, navigation, database connection
- partners/ = partner-related pages
- services/ = service-related pages
- backups/ = backup copies before major changes

## Rules

1. Do not create a second project folder.
2. Do not create a public/ folder unless specifically asked.
3. Do not delete existing files without permission.
4. Do not overwrite database credentials.
5. Do not expose passwords, API keys, Hostinger login info, or database credentials.
6. Keep the website compatible with Hostinger shared hosting.
7. Use PHP, MySQL, HTML, CSS, and JavaScript.
8. Keep the design responsive for mobile, tablet, laptop, and desktop.
9. Preserve the current brand style unless asked to redesign.
10. Before major changes, create a backup copy in backups/.
11. After every update, write notes in deploy-notes.md.
12. After every task, report:
   - files changed
   - what changed
   - how to test locally
   - how to deploy to Hostinger
   - whether database changes are needed