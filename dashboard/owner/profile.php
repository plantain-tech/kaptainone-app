<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('asset_owner');

$dashboardTitle = 'Owner Profile';
$user = current_user();

$profile = Database::fetch("SELECT * FROM user_profiles WHERE user_id = ?", [$user['id']]);
if (!$profile) {
    Database::insert('user_profiles', [
        'user_id' => $user['id'],
        'full_name' => '',
        'city' => 'Warsaw',
        'country' => 'Poland',
        'preferred_language' => 'English',
    ]);
    $profile = Database::fetch("SELECT * FROM user_profiles WHERE user_id = ?", [$user['id']]) ?? [];
}

$nameParts = preg_split('/\s+/', trim((string) ($profile['full_name'] ?? '')), 2);
$defaults = [
    'first_name' => $nameParts[0] ?? '',
    'last_name' => $nameParts[1] ?? '',
    'phone' => $profile['phone'] ?? '',
    'preferred_language' => $profile['preferred_language'] ?? 'English',
    'preferred_pickup_district' => $profile['preferred_pickup_district'] ?? '',
    'default_pickup_notes' => $profile['default_pickup_notes'] ?? '',
];

$input = $defaults;
$errors = [];
$pickupDistricts = [
    'Bemowo',
    html_entity_decode('Bia&#322;o&#322;&#281;ka', ENT_QUOTES, 'UTF-8'),
    'Bielany',
    html_entity_decode('Mokot&oacute;w', ENT_QUOTES, 'UTF-8'),
    'Ochota',
    html_entity_decode('Praga-Po&#322;udnie', ENT_QUOTES, 'UTF-8'),
    html_entity_decode('Praga-P&oacute;&#322;noc', ENT_QUOTES, 'UTF-8'),
    html_entity_decode('Rembert&oacute;w', ENT_QUOTES, 'UTF-8'),
    html_entity_decode('&#346;r&oacute;dmie&#347;cie', ENT_QUOTES, 'UTF-8'),
    html_entity_decode('Targ&oacute;wek', ENT_QUOTES, 'UTF-8'),
    'Ursus',
    html_entity_decode('Ursyn&oacute;w', ENT_QUOTES, 'UTF-8'),
    'Wawer',
    html_entity_decode('Weso&#322;a', ENT_QUOTES, 'UTF-8'),
    html_entity_decode('Wilan&oacute;w', ENT_QUOTES, 'UTF-8'),
    html_entity_decode('W&#322;ochy', ENT_QUOTES, 'UTF-8'),
    'Wola',
    html_entity_decode('&#379;oliborz', ENT_QUOTES, 'UTF-8')
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = [
        'first_name' => trim((string) ($_POST['first_name'] ?? '')),
        'last_name' => trim((string) ($_POST['last_name'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'preferred_language' => trim((string) ($_POST['preferred_language'] ?? 'English')),
        'preferred_pickup_district' => trim((string) ($_POST['preferred_pickup_district'] ?? '')),
        'default_pickup_notes' => trim((string) ($_POST['default_pickup_notes'] ?? '')),
    ];

    $languages = ['Polish', 'English', 'Ukrainian', 'Russian'];
    $districts = array_merge([''], $pickupDistricts);

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Security check failed. Please try again.';
    }
    if ($input['first_name'] === '') {
        $errors['first_name'] = 'First name is required.';
    }
    if ($input['last_name'] === '') {
        $errors['last_name'] = 'Last name is required.';
    }
    if ($input['phone'] !== '' && !preg_match('/^\+?[0-9\s\-()]{7,24}$/', $input['phone'])) {
        $errors['phone'] = 'Use a valid phone format, for example +48 123 456 789.';
    }
    if (!in_array($input['preferred_language'], $languages, true)) {
        $errors['preferred_language'] = 'Choose a supported language.';
    }
    if (!in_array($input['preferred_pickup_district'], $districts, true)) {
        $errors['preferred_pickup_district'] = 'Choose a Warsaw district or Varies by listing.';
    }
    if (strlen($input['default_pickup_notes']) > 500) {
        $errors['default_pickup_notes'] = 'Pickup notes must be 500 characters or less.';
    }

    if (!$errors) {
        Database::update('user_profiles', [
            'full_name' => trim($input['first_name'] . ' ' . $input['last_name']),
            'phone' => $input['phone'] ?: null,
            'preferred_language' => $input['preferred_language'],
            'preferred_pickup_district' => $input['preferred_pickup_district'] ?: null,
            'default_pickup_notes' => $input['default_pickup_notes'] ?: null,
        ], 'user_id = :user_id', ['user_id' => $user['id']]);

        set_flash('success', 'Your profile was updated.');
        redirect(base_url() . '/dashboard/owner/profile.php');
    }
}

$activeListings = (int) (Database::fetch("SELECT COUNT(*) AS total FROM listings WHERE owner_user_id = ? AND status = 'active'", [$user['id']])['total'] ?? 0);
$completedRentals = (int) (Database::fetch("SELECT COUNT(*) AS total FROM bookings WHERE owner_user_id = ? AND status = 'completed'", [$user['id']])['total'] ?? 0);
$memberSince = !empty($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'Recently';
$verificationLabel = $completedRentals >= 3 ? 'Verified owner' : 'New owner';
$verificationClass = $completedRentals >= 3 ? 'status-active' : 'status-requested';
$languages = ['Polish', 'English', 'Ukrainian', 'Russian'];
require_once __DIR__ . '/_layout.php';
?>

<section class="owner-profile-shell">
    <div class="owner-profile-intro">
        <span class="section-kicker">Owner settings</span>
        <h2>Manage your account details and owner preferences.</h2>
        <?php if (has_role('gig_worker')): ?>
            <p>You also have a courier profile. Manage courier work preferences from <a href="<?= base_url() ?>/dashboard/profile.php">My Profile</a>.</p>
        <?php endif; ?>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-error owner-profile-alert">
            <strong>Please fix the errors below.</strong>
            <?php if (isset($errors['csrf'])): ?><span><?= e($errors['csrf']) ?></span><?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="owner-profile-layout">
        <form method="post" class="owner-profile-card app-form" data-owner-profile-form data-has-errors="<?= $errors ? '1' : '0' ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <p class="form-helper">Fields marked with <span class="required-mark">*</span> are required.</p>

            <div class="form-section full-span">
                <h3>Account Details</h3>
            </div>

            <label>
                First name <span class="required-mark">*</span>
                <input class="form-control <?= isset($errors['first_name']) ? 'has-error' : '' ?>" name="first_name" required value="<?= e($input['first_name']) ?>" placeholder="e.g. Anna">
                <?php if (isset($errors['first_name'])): ?><small class="field-error"><?= e($errors['first_name']) ?></small><?php endif; ?>
            </label>

            <label>
                Last name <span class="required-mark">*</span>
                <input class="form-control <?= isset($errors['last_name']) ? 'has-error' : '' ?>" name="last_name" required value="<?= e($input['last_name']) ?>" placeholder="e.g. Kowalska">
                <?php if (isset($errors['last_name'])): ?><small class="field-error"><?= e($errors['last_name']) ?></small><?php endif; ?>
            </label>

            <label>
                Email
                <input class="form-control" value="<?= e($user['email']) ?>" disabled>
                <small class="field-note">Contact support to change your email.</small>
            </label>

            <label>
                Phone number
                <input class="form-control <?= isset($errors['phone']) ? 'has-error' : '' ?>" name="phone" value="<?= e($input['phone']) ?>" placeholder="+48 123 456 789">
                <?php if (isset($errors['phone'])): ?><small class="field-error"><?= e($errors['phone']) ?></small><?php endif; ?>
            </label>

            <label>
                Preferred language
                <select class="form-control <?= isset($errors['preferred_language']) ? 'has-error' : '' ?>" name="preferred_language">
                    <?php foreach ($languages as $language): ?>
                        <option value="<?= e($language) ?>" <?= $input['preferred_language'] === $language ? 'selected' : '' ?>><?= e($language) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['preferred_language'])): ?><small class="field-error"><?= e($errors['preferred_language']) ?></small><?php endif; ?>
            </label>

            <div class="form-section full-span">
                <h3>Owner Preferences</h3>
            </div>

            <label>
                Preferred pickup district
                <select class="form-control <?= isset($errors['preferred_pickup_district']) ? 'has-error' : '' ?>" name="preferred_pickup_district">
                    <option value="" <?= $input['preferred_pickup_district'] === '' ? 'selected' : '' ?>>Varies by listing</option>
                    <?php foreach ($pickupDistricts as $district): ?>
                        <option value="<?= e($district) ?>" <?= $input['preferred_pickup_district'] === $district ? 'selected' : '' ?>><?= e($district) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['preferred_pickup_district'])): ?><small class="field-error"><?= e($errors['preferred_pickup_district']) ?></small><?php endif; ?>
            </label>

            <label class="full-span">
                Default pickup notes
                <textarea class="form-control <?= isset($errors['default_pickup_notes']) ? 'has-error' : '' ?>" name="default_pickup_notes" maxlength="500" rows="5" placeholder="Add any details couriers should know about pickup location or timing. This will pre-fill for new listings but can be customized per listing." data-owner-notes><?= e($input['default_pickup_notes']) ?></textarea>
                <small class="field-note"><span data-owner-notes-count><?= strlen($input['default_pickup_notes']) ?></span>/500 characters</small>
                <?php if (isset($errors['default_pickup_notes'])): ?><small class="field-error"><?= e($errors['default_pickup_notes']) ?></small><?php endif; ?>
            </label>

            <div class="app-actions full-span">
                <button class="btn btn-primary" type="submit">Save changes</button>
                <a class="btn btn-secondary" href="<?= base_url() ?>/dashboard/owner/index.php">Cancel</a>
            </div>
        </form>

        <aside class="owner-profile-side">
            <section class="owner-profile-card">
                <span class="section-kicker">Owner stats</span>
                <h3>Account standing</h3>
                <div class="owner-stat-list">
                    <div><span>Member since</span><strong><?= e($memberSince) ?></strong></div>
                    <div><span>Active listings</span><strong><?= $activeListings ?></strong></div>
                    <div><span>Completed rentals</span><strong><?= $completedRentals ?></strong></div>
                    <div><span>Verification</span><strong><em class="status-pill <?= e($verificationClass) ?>"><?= e($verificationLabel) ?></em></strong></div>
                </div>
            </section>

            <section class="owner-profile-card">
                <span class="section-kicker">Payouts</span>
                <h3>Payout Settings</h3>
                <p class="text-muted">Manage your bank account and payout preferences on the Payout Settings page.</p>
                <a class="btn btn-primary" href="<?= base_url() ?>/dashboard/owner/payout-settings.php">Manage payouts</a>
            </section>
        </aside>
    </div>
</section>

<script src="<?= asset('js/owner-profile.js') ?>"></script>

<?php require_once __DIR__ . '/_footer.php'; ?>
