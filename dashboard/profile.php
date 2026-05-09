<?php
$dashboardTitle = 'My Profile';
require_once __DIR__ . '/_layout.php';

$profile = Database::fetch("SELECT * FROM user_profiles WHERE user_id = ?", [$user['id']]) ?? [];
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security check failed. Please try again.';
    } else {
        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'preferred_language' => trim($_POST['preferred_language'] ?? ''),
            'work_type' => trim($_POST['work_type'] ?? ''),
            'preferred_platforms' => implode(', ', $_POST['preferred_platforms'] ?? []),
            'driver_license_status' => trim($_POST['driver_license_status'] ?? ''),
            'national_id' => trim($_POST['national_id'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'emergency_contact' => trim($_POST['emergency_contact'] ?? '')
        ];

        if ($data['full_name'] === '') $errors[] = 'Full name is required.';

        if (!$errors) {
            Database::update('user_profiles', $data, 'user_id = :user_id', ['user_id' => $user['id']]);
            $profile = Database::fetch("SELECT * FROM user_profiles WHERE user_id = ?", [$user['id']]) ?? [];
            $success = true;
        }
    }
}

$platforms = ['Uber', 'Uber Eats', 'Bolt', 'Bolt Food', 'Glovo', 'Wolt', 'Stuart', 'Pyszne.pl', 'Other'];
$selectedPlatforms = array_map('trim', explode(',', $profile['preferred_platforms'] ?? ''));
?>

<section class="dash-panel">
    <?php if ($success): ?><div class="alert alert-success">Profile updated.</div><?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
    <form method="post" class="app-form two-col">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Full Name<input class="form-control" name="full_name" required value="<?= e($profile['full_name'] ?? '') ?>"></label>
        <label>Email<input class="form-control" value="<?= e($user['email']) ?>" disabled></label>
        <label>Phone<input class="form-control" name="phone" value="<?= e($profile['phone'] ?? '') ?>"></label>
        <label>City<input class="form-control" name="city" value="<?= e($profile['city'] ?? 'Warsaw') ?>"></label>
        <label>Country<input class="form-control" name="country" value="<?= e($profile['country'] ?? 'Poland') ?>"></label>
        <label>Preferred Language<input class="form-control" name="preferred_language" value="<?= e($profile['preferred_language'] ?? 'English') ?>"></label>
        <label>Work Type
            <select class="form-control" name="work_type">
                <?php foreach (['food delivery','parcel delivery','rideshare driver','private chauffeur','tourist support','local courier'] as $type): ?>
                    <option value="<?= e($type) ?>" <?= ($profile['work_type'] ?? '') === $type ? 'selected' : '' ?>><?= e(ucwords($type)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Driver License Status
            <select class="form-control" name="driver_license_status">
                <?php foreach (['not applicable','no license','valid license','in progress'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= ($profile['driver_license_status'] ?? '') === $status ? 'selected' : '' ?>><?= e(ucwords($status)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>ID / PESEL<input class="form-control" name="national_id" value="<?= e($profile['national_id'] ?? '') ?>"></label>
        <label>Emergency Contact<input class="form-control" name="emergency_contact" value="<?= e($profile['emergency_contact'] ?? '') ?>"></label>
        <label class="full-span">Address<input class="form-control" name="address" value="<?= e($profile['address'] ?? '') ?>"></label>
        <div class="full-span checkbox-grid">
            <?php foreach ($platforms as $platform): ?>
                <label><input type="checkbox" name="preferred_platforms[]" value="<?= e($platform) ?>" <?= in_array($platform, $selectedPlatforms, true) ? 'checked' : '' ?>> <?= e($platform) ?></label>
            <?php endforeach; ?>
        </div>
        <button class="btn btn-primary btn-large" type="submit">Save Profile</button>
    </form>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
