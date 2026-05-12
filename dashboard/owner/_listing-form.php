<?php
$listing = $listing ?? [];
$errors = $errors ?? [];
$values = array_merge([
    'asset_type' => 'ebike',
    'title' => '',
    'brand' => '',
    'model' => '',
    'condition_grade' => 'good',
    'weekly_price_pln' => '',
    'monthly_price_pln' => '',
    'deposit_pln' => '',
    'location_district' => 'Śródmieście',
    'description' => '',
    'included_items' => '',
    'status' => 'draft',
], $listing, $_POST);
$isEdit = !empty($listing['id']);
$existingPhotoCount = isset($photos) ? count($photos) : 0;
?>

<form method="post" enctype="multipart/form-data" class="app-form listing-form" data-listing-form data-has-errors="<?= $errors ? '1' : '0' ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <p class="listing-form__hint">Fields marked with <span class="required-marker">*</span> are required.</p>

    <section class="listing-form__section">
        <h3>Asset basics</h3>
        <div class="form-group">
            <label>Asset type <span class="required-marker">*</span></label>
            <div class="choice-grid">
                <label class="choice-card">
                    <input type="radio" name="asset_type" value="ebike" <?= ($values['asset_type'] ?? '') === 'ebike' ? 'checked' : '' ?>>
                    <span>E-bike</span>
                </label>
                <label class="choice-card">
                    <input type="radio" name="asset_type" value="scooter" <?= ($values['asset_type'] ?? '') === 'scooter' ? 'checked' : '' ?>>
                    <span>Scooter</span>
                </label>
            </div>
            <?php if (!empty($errors['asset_type'])): ?><small class="form-error"><?= e($errors['asset_type']) ?></small><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="title">Listing title <span class="required-marker">*</span></label>
            <input class="form-control" id="title" name="title" value="<?= e($values['title'] ?? '') ?>" maxlength="150" placeholder="e.g. Romet Wagant 2 - Mokotów, ready for delivery" required>
            <?php if (!empty($errors['title'])): ?><small class="form-error"><?= e($errors['title']) ?></small><?php endif; ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="brand">Brand</label>
                <input class="form-control" id="brand" name="brand" value="<?= e($values['brand'] ?? '') ?>" maxlength="80" placeholder="e.g. Romet, Kross, Xiaomi">
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <input class="form-control" id="model" name="model" value="<?= e($values['model'] ?? '') ?>" maxlength="80" placeholder="e.g. Wagant 2, Hexagon, Mi Pro 2">
            </div>
        </div>

        <div class="form-group">
            <label for="condition_grade">Condition <span class="required-marker">*</span></label>
            <select class="form-control" id="condition_grade" name="condition_grade" required>
                <?php foreach (['new' => 'New', 'excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($values['condition_grade'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['condition_grade'])): ?><small class="form-error"><?= e($errors['condition_grade']) ?></small><?php endif; ?>
        </div>
    </section>

    <section class="listing-form__section">
        <h3>Pricing</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="weekly_price_pln">Weekly price (PLN) <span class="required-marker">*</span></label>
                <input class="form-control" id="weekly_price_pln" name="weekly_price_pln" type="number" min="50" max="1500" step="0.01" value="<?= e($values['weekly_price_pln'] ?? '') ?>" placeholder="295" required>
                <?php if (!empty($errors['weekly_price_pln'])): ?><small class="form-error"><?= e($errors['weekly_price_pln']) ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="monthly_price_pln">Monthly price (optional)</label>
                <input class="form-control" id="monthly_price_pln" name="monthly_price_pln" type="number" min="0" step="0.01" value="<?= e($values['monthly_price_pln'] ?? '') ?>" placeholder="leave empty to auto-calculate (~3.7x weekly)">
            </div>
            <div class="form-group">
                <label for="deposit_pln">Deposit (PLN) <span class="required-marker">*</span></label>
                <input class="form-control" id="deposit_pln" name="deposit_pln" type="number" min="0" max="5000" step="0.01" value="<?= e($values['deposit_pln'] ?? '') ?>" placeholder="500" required>
                <?php if (!empty($errors['deposit_pln'])): ?><small class="form-error"><?= e($errors['deposit_pln']) ?></small><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="listing-form__section">
        <h3>Location</h3>
        <div class="form-group">
            <label for="location_district">Warsaw district <span class="required-marker">*</span></label>
            <select class="form-control" id="location_district" name="location_district" required>
                <?php foreach (warsaw_districts() as $district): ?>
                    <option value="<?= e($district) ?>" <?= ($values['location_district'] ?? '') === $district ? 'selected' : '' ?>><?= e($district) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['location_district'])): ?><small class="form-error"><?= e($errors['location_district']) ?></small><?php endif; ?>
        </div>
    </section>

    <section class="listing-form__section">
        <h3>Details</h3>
        <div class="form-group">
            <label for="description">Description <span class="required-marker">*</span></label>
            <textarea class="form-control" id="description" name="description" maxlength="2000" placeholder="Tell couriers about the e-bike or scooter, condition, typical range, where to pick it up, and anything else they should know." required><?= e($values['description'] ?? '') ?></textarea>
            <small><span id="description-count"><?= strlen((string) ($values['description'] ?? '')) ?></span>/2000 characters</small>
            <?php if (!empty($errors['description'])): ?><small class="form-error"><?= e($errors['description']) ?></small><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="included_items">Included items</label>
            <input class="form-control" id="included_items" name="included_items" value="<?= e($values['included_items'] ?? '') ?>" maxlength="500" placeholder="Lock, charger, helmet, phone mount">
            <?php if (!empty($errors['included_items'])): ?><small class="form-error"><?= e($errors['included_items']) ?></small><?php endif; ?>
        </div>
    </section>

    <?php if ($isEdit): ?>
        <section class="listing-form__section">
            <h3>Status</h3>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                    <?php foreach (['draft', 'active', 'paused', 'archived'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= ($values['status'] ?? '') === $status ? 'selected' : '' ?>><?= e(listing_status_label($status)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </section>
    <?php endif; ?>

    <section class="listing-form__section">
        <h3>Photos</h3>
        <?php if (!empty($photos)): ?>
            <div class="form-group">
                <label>Existing photos</label>
                <div class="package-grid">
                    <?php foreach ($photos as $photo): ?>
                        <div class="package-card">
                            <img src="<?= e(base_url() . $photo['file_path']) ?>" alt="Listing photo" style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px;">
                            <button class="btn btn-secondary" type="submit" name="delete_photo_id" value="<?= (int) $photo['id'] ?>" onclick="return confirm('Delete this photo?')">Delete photo</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="photos">Photos <?= $isEdit ? '(add more)' : '' ?></label>
            <input class="form-control" id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple data-photo-input data-existing-count="<?= (int) $existingPhotoCount ?>">
            <small>Photos are required when publishing a new listing, optional for drafts. Up to 6 photos total. JPEG, PNG, or WebP. 5MB each.</small>
            <small class="form-error" data-photo-error hidden></small>
            <div class="listing-photo-preview-grid" data-photo-preview></div>
            <?php if (!empty($errors['photos'])): ?><small class="form-error"><?= e($errors['photos']) ?></small><?php endif; ?>
        </div>
    </section>

    <div class="app-actions listing-form__actions">
        <?php if ($isEdit): ?>
            <button class="btn btn-primary" type="submit" name="save_listing" value="1" data-primary-submit>Save changes</button>
        <?php else: ?>
            <button class="btn btn-primary" type="submit" name="status_action" value="active" data-primary-submit>Publish</button>
            <button class="btn btn-secondary" type="submit" name="status_action" value="draft">Save as draft</button>
        <?php endif; ?>
        <a href="<?= base_url() ?>/dashboard/owner/listings.php" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="listing-submit-overlay" data-submit-overlay hidden>
        <div class="listing-submit-overlay__box">
            <span class="listing-spinner" aria-hidden="true"></span>
            <strong>Uploading photos and saving listing...</strong>
        </div>
    </div>
</form>

<script src="<?= asset('js/listing-form.js') ?>"></script>
