-- Day 2 MVP scope lockdown for Kaptain One.
-- Review and run manually on production. No schema changes.
-- MVP scope: Warsaw only, e-bikes and scooters only, courier audience only.

UPDATE blog_categories
SET
    name = 'Courier Equipment Insights',
    slug = 'courier-equipment-insights',
    description = 'News and practical updates for Warsaw courier equipment rentals'
WHERE slug = 'industry-insights';

UPDATE blog_categories
SET
    name = 'Courier Tips',
    slug = 'courier-tips',
    description = 'Practical advice for Warsaw delivery couriers'
WHERE slug = 'travel-tips';

UPDATE site_settings
SET setting_value = 'Warsaw E-Bike & Scooter Rentals for Couriers'
WHERE setting_key = 'site_tagline';

-- Archive the legacy car/chauffeur seed package without deleting historical data.
UPDATE equipment_packages
SET
    availability_status = 'unavailable',
    is_active = 0,
    updated_at = CURRENT_TIMESTAMP
WHERE slug = 'vehicle-car-leasing-package';

-- Temporary package-backed sample listings.
-- Once the Day 3 asset-owner listings table exists, migrate these rows into that table.
INSERT INTO equipment_packages
(title, slug, category, short_description, description, included_items, weekly_price, monthly_price, deposit_amount, currency, availability_status, requirements, terms, sort_order, is_active)
VALUES
('Warsaw E-bike - Śródmieście', 'warsaw-e-bike-srodmiescie', 'E-bike Rental', 'E-bike rental in Śródmieście for Warsaw courier shifts.', 'A practical e-bike rental option for couriers working central Warsaw delivery zones.', 'E-bike, charger, lock, basic handoff check', 280.00, 1040.00, 400.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrate to listings table after Day 3 asset-owner listing work.', 70, 1),
('Warsaw E-bike - Mokotów', 'warsaw-e-bike-mokotow', 'E-bike Rental', 'E-bike rental in Mokotów for Warsaw courier shifts.', 'A work-ready e-bike rental option for couriers serving Mokotów and nearby delivery areas.', 'E-bike, charger, lock, basic handoff check', 320.00, 1190.00, 500.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrate to listings table after Day 3 asset-owner listing work.', 80, 1),
('Warsaw Scooter - Wola', 'warsaw-scooter-wola', 'Scooter Rental', 'Scooter rental in Wola for Warsaw courier shifts.', 'A scooter rental option for couriers who need efficient movement across Wola and central Warsaw.', 'Scooter, charger if electric, lock, basic handoff check', 380.00, 1410.00, 600.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrate to listings table after Day 3 asset-owner listing work.', 90, 1),
('Warsaw Scooter - Praga-Południe', 'warsaw-scooter-praga-poludnie', 'Scooter Rental', 'Scooter rental in Praga-Południe for Warsaw courier shifts.', 'A scooter rental option for couriers serving Praga-Południe and nearby delivery areas.', 'Scooter, charger if electric, lock, basic handoff check', 350.00, 1300.00, 600.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrate to listings table after Day 3 asset-owner listing work.', 100, 1)
ON DUPLICATE KEY UPDATE
    category = VALUES(category),
    short_description = VALUES(short_description),
    description = VALUES(description),
    included_items = VALUES(included_items),
    weekly_price = VALUES(weekly_price),
    monthly_price = VALUES(monthly_price),
    deposit_amount = VALUES(deposit_amount),
    currency = VALUES(currency),
    availability_status = VALUES(availability_status),
    requirements = VALUES(requirements),
    terms = VALUES(terms),
    sort_order = VALUES(sort_order),
    is_active = VALUES(is_active),
    updated_at = CURRENT_TIMESTAMP;
