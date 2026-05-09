2026-05-09
- Restored the header to use the original `assets/images/branding/kaptain-one-logo.png` file on disk without converting the image.
- Added global link styling so hyperlinks and button-links do not show browser default underlines across the app.
- Removed decorative active-link underline behavior from the main and mobile navigation.
- Added working Privacy and Terms pages and updated footer links.
- Wired the contact form to save messages in the new `contact_messages` table.
- Updated the default admin seed hash so the documented `admin123` password works on fresh imports.
- Fixed service card links so they work from both the homepage and `/services/index.php`.
- Fixed admin redirects so `/admin` and `/admin/` route safely into the admin area.
- Added a styled "Back to Main Site" button to the admin login screen.
