<?php
/**
 * Kaptain One - Form Handler
 * Processes partner and demo form submissions
 */

require_once __DIR__ . '/db.php';

class FormHandler {
    
    public static function processPartnerForm(array $data): array {
        $errors = self::validatePartnerForm($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $id = Database::insert('partner_inquiries', [
                'full_name' => $data['full_name'],
                'company_name' => $data['company_name'] ?? '',
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'business_type' => $data['business_type'] ?? '',
                'city' => $data['city'] ?? '',
                'country' => $data['country'] ?? '',
                'website' => $data['website'] ?? '',
                'message' => $data['message'] ?? '',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            
            return ['success' => true, 'id' => $id];
        } catch (Exception $e) {
            error_log('Partner form error: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public static function processDemoForm(array $data): array {
        $errors = self::validateDemoForm($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $id = Database::insert('demo_requests', [
                'full_name' => $data['full_name'],
                'company' => $data['company'] ?? '',
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'company_size' => $data['company_size'] ?? '',
                'service_interest' => $data['service_interest'] ?? '',
                'contact_method' => $data['contact_method'] ?? 'email',
                'message' => $data['message'] ?? '',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            
            return ['success' => true, 'id' => $id];
        } catch (Exception $e) {
            error_log('Demo form error: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    private static function validatePartnerForm(array $data): array {
        $errors = [];
        
        if (empty($data['full_name']) || strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Please enter your full name';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
        
        if (empty($data['business_type'])) {
            $errors['business_type'] = 'Please select a business type';
        }
        
        // Honeypot check
        if (!empty($data['website_url'])) {
            $errors['spam'] = 'Spam detected';
        }
        
        return $errors;
    }
    
    private static function validateDemoForm(array $data): array {
        $errors = [];
        
        if (empty($data['full_name']) || strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Please enter your full name';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
        
        if (empty($data['service_interest'])) {
            $errors['service_interest'] = 'Please select a service interest';
        }
        
        // Honeypot check
        if (!empty($data['website_url'])) {
            $errors['spam'] = 'Spam detected';
        }
        
        return $errors;
    }
}
