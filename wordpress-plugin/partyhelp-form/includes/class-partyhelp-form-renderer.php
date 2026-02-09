<?php
/**
 * Renders the party details form.
 */

if (! defined('ABSPATH')) {
    exit;
}

class Partyhelp_Form_Renderer
{
    private Partyhelp_Form_Sync $sync;

    public function __construct(Partyhelp_Form_Sync $sync)
    {
        $this->sync = $sync;
    }

    public function render_form(): string
    {
        $this->maybe_enqueue_assets();

        $config = $this->sync->get_config();
        $areas = $config['areas'] ?? [];
        $occasion_types = $config['occasion_types'] ?? [];
        $guest_brackets = $config['guest_brackets'] ?? [];
        $budget_ranges = $config['budget_ranges'] ?? [];

        ob_start();
        ?>
        <div class="partyhelp-form-wrapper">
            <form id="partyhelp-form" class="partyhelp-form" method="post" novalidate>
                <p class="partyhelp-form-heading">Please complete your party details below and we will email you some venue recommendations very soon!</p>
                <p class="partyhelp-form-instruction">Please try and provide us with as much information as possible about your party so that we can recommend the most suitable party venues.</p>
                <p class="partyhelp-form-note">Please note that Partyhelp is only for people aged 18 years and over. Partyhelp do NOT cater for underage functions.</p>
                <p class="partyhelp-form-help"><a href="<?php echo esc_url(home_url('/my-party-details-help/')); ?>">My Party Details Help Page</a></p>

                <div class="partyhelp-personal-info-group">
                    <div class="partyhelp-form-row partyhelp-form-row-2">
                        <div class="partyhelp-first-name-field">
                            <label for="ph-first-name">First Name <span class="required">*</span></label>
                            <input type="text" id="ph-first-name" name="first_name" class="partyhelp-input" placeholder="First Name" required minlength="2" maxlength="100" />
                            <span class="partyhelp-field-error" data-field="first_name"></span>
                        </div>
                        <div class="partyhelp-last-name-field">
                            <label for="ph-last-name">Last Name <span class="required">*</span></label>
                            <input type="text" id="ph-last-name" name="last_name" class="partyhelp-input" placeholder="Last Name" required minlength="2" maxlength="100" />
                            <span class="partyhelp-field-error" data-field="last_name"></span>
                        </div>
                    </div>
                    <div class="partyhelp-form-row partyhelp-form-row-2">
                        <div class="partyhelp-email-field">
                            <label for="ph-email">Email <span class="required">*</span></label>
                            <input type="email" id="ph-email" name="email" class="partyhelp-input" placeholder="Email" required />
                            <span class="partyhelp-field-error" data-field="email"></span>
                        </div>
                        <div class="partyhelp-phone-field">
                            <label for="ph-phone">Phone <span class="required">*</span></label>
                            <input type="tel" id="ph-phone" name="phone" class="partyhelp-input" placeholder="Phone" required minlength="8" maxlength="20" />
                            <span class="partyhelp-field-error" data-field="phone"></span>
                        </div>
                    </div>
                </div>

                <div class="partyhelp-party-details-group">
                    <div class="partyhelp-occasion-type-field-group">
                        <label for="ph-occasion-type">Select the type of occasion <span class="required">*</span></label>
                        <select id="ph-occasion-type" name="occasion_type" class="partyhelp-select" required>
                            <option value="">Select occasion</option>
                            <?php foreach ($occasion_types as $ot): ?>
                                <option value="<?php echo esc_attr($ot['key']); ?>"><?php echo esc_html($ot['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="partyhelp-field-error" data-field="occasion_type"></span>
                    </div>
                    <div class="partyhelp-preferred-date-field">
                        <label for="ph-preferred-date">Preferred Date <span class="required">*</span></label>
                        <input type="date" id="ph-preferred-date" name="preferred_date" class="partyhelp-input" required />
                        <span class="partyhelp-field-error" data-field="preferred_date"></span>
                    </div>
                    <div class="partyhelp-num-guests-field-group">
                        <label for="ph-guest-count">Number of Guests <span class="required">*</span></label>
                        <select id="ph-guest-count" name="guest_count" class="partyhelp-select" required>
                            <option value="">Select guests</option>
                            <?php foreach ($guest_brackets as $gb): ?>
                                <option value="<?php echo esc_attr($gb['value']); ?>"><?php echo esc_html($gb['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="partyhelp-field-error" data-field="guest_count"></span>
                    </div>
                    <div class="partyhelp-estimated-budget-field">
                        <label for="ph-budget">Estimated Budget</label>
                        <?php if (! empty($budget_ranges)): ?>
                            <select id="ph-budget" name="budget_range" class="partyhelp-select">
                                <option value="">Select budget</option>
                                <?php foreach ($budget_ranges as $br): ?>
                                    <option value="<?php echo esc_attr($br['value']); ?>"><?php echo esc_html($br['label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" id="ph-budget" name="budget_range" class="partyhelp-input" placeholder="Estimated Budget" />
                        <?php endif; ?>
                    </div>
                </div>

                <div class="partyhelp-location-group">
                    <label class="partyhelp-preferred-location-field-group-label">Select preferred location</label>
                    <div class="partyhelp-location-checkboxes">
                        <?php foreach ($areas as $area): ?>
                            <label class="partyhelp-location-option">
                                <input type="checkbox" name="location[]" value="<?php echo esc_attr($area['label']); ?>" class="partyhelp-location-checkbox partyhelp-location-<?php echo esc_attr(sanitize_title($area['name'])); ?>" />
                                <span><?php echo esc_html($area['label']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <span class="partyhelp-field-error" data-field="location"></span>
                </div>

                <div class="partyhelp-other-details-group">
                    <label for="ph-other-details">Other details about the party:</label>
                    <textarea id="ph-other-details" name="special_requirements" class="partyhelp-textarea" rows="4" placeholder="Tell us more about your party..." maxlength="500"></textarea>
                    <span class="partyhelp-field-error" data-field="special_requirements"></span>
                </div>

                <div class="partyhelp-form-submit-wrap">
                    <button type="submit" class="partyhelp-submit-button" id="partyhelp-submit-btn">Send me venues</button>
                    <p class="partyhelp-form-message" id="partyhelp-form-message"></p>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function is_form_page(): bool
    {
        if (is_singular()) {
            $post = get_queried_object();
            return $post && has_shortcode($post->post_content, 'partyhelp-form');
        }

        return false;
    }

    private function maybe_enqueue_assets(): void
    {
        static $enqueued = false;
        if ($enqueued) {
            return;
        }
        $enqueued = true;

        wp_enqueue_style(
            'partyhelp-form',
            PARTYHELP_FORM_PLUGIN_URL . 'assets/css/partyhelp-form.css',
            [],
            PARTYHELP_FORM_VERSION
        );

        wp_enqueue_script(
            'partyhelp-form',
            PARTYHELP_FORM_PLUGIN_URL . 'assets/js/partyhelp-form.js',
            ['jquery'],
            PARTYHELP_FORM_VERSION,
            true
        );

        wp_localize_script('partyhelp-form', 'partyhelpForm', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('partyhelp_form_submit'),
            'webhookUrl' => partyhelp_form()->settings->get_webhook_url(),
        ]);
    }
}
