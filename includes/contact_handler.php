<?php
/**
 * Kaptain One - Contact Form Handler
 */

require_once __DIR__ . '/db.php';

class ContactHandler {
    public static function process(array $data): array {
        $errors = self::validate($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $id = Database::insert('contact_messages', [
                'full_name' => trim($data['name']),
                'email' => trim($data['email']),
                'subject' => trim($data['subject'] ?? 'General Inquiry'),
                'message' => trim($data['message']),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]);

            return ['success' => true, 'id' => $id];
        } catch (Exception $e) {
            error_log('Contact form error: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['Unable to save your message right now. Please email us directly.']];
        }
    }

    private static function validate(array $data): array {
        $errors = [];

        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Please enter your full name';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        if (empty($data['message']) || strlen(trim($data['message'])) < 5) {
            $errors['message'] = 'Please enter a message';
        }

        return $errors;
    }
}
