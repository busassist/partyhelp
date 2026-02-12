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

        $css_url = PARTYHELP_FORM_PLUGIN_URL . 'assets/css/partyhelp-form.css?ver=' . PARTYHELP_FORM_VERSION;
        $link_tag = '<link rel="stylesheet" href="' . esc_url($css_url) . '" id="partyhelp-form-css" />';

        $config = $this->sync->get_config();
        $areas = $this->normalize_areas_for_display($config['areas'] ?? []);
        $occasion_types = $config['occasion_types'] ?? [];
        $guest_brackets = $config['guest_brackets'] ?? [];
        $budget_ranges = $config['budget_ranges'] ?? [];
        $venue_styles = $config['venue_styles'] ?? [];

        $custom_css = partyhelp_form()->settings->get_custom_form_css();

        Partyhelp_Form_Debug::log('form_rendered', [
            'venue_styles_count' => count($venue_styles),
            'custom_css_length' => strlen($custom_css),
            'field_border_radius_px' => partyhelp_form()->settings->get_style_option('field_border_radius_px'),
            'form_bg_color' => partyhelp_form()->settings->get_style_option('form_bg_color'),
        ]);

        ob_start();
        echo $link_tag;
        if ($custom_css !== '') {
            echo "\n<style id=\"partyhelp-form-custom\">\n" . $custom_css . "\n</style>";
        }
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
                        <div class="partyhelp-date-field-wrap" id="ph-date-wrap">
                            <input type="date" id="ph-preferred-date" name="preferred_date" class="partyhelp-input partyhelp-date-input" required />
                        </div>
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
                                <option value="I'm not sure at this stage" selected>I'm not sure at this stage</option>
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
                    <label class="partyhelp-preferred-location-field-group-label">Preferred Locations:</label>
                    <div class="partyhelp-location-grid">
                        <?php foreach ($areas as $area): ?>
                            <div class="partyhelp-location-area-block partyhelp-location-area-<?php echo esc_attr(sanitize_title($area['name'])); ?>">
                                <label class="partyhelp-location-option partyhelp-location-area-option">
                                    <input type="checkbox" name="location[]" value="<?php echo esc_attr('AREA:' . $area['name']); ?>" class="partyhelp-location-checkbox partyhelp-location-area-checkbox" data-area="<?php echo esc_attr($area['name']); ?>" />
                                    <span class="partyhelp-location-area-name"><?php echo esc_html($area['name']); ?></span>
                                </label>
                                <?php if (! empty($area['suburbs'])): ?>
                                    <div class="partyhelp-location-suburbs">
                                        <?php foreach ($area['suburbs'] as $suburb): ?>
                                            <label class="partyhelp-location-option partyhelp-location-suburb-option">
                                                <input type="checkbox" name="location[]" value="<?php echo esc_attr('SUBURB:' . $area['name'] . ':' . $suburb); ?>" class="partyhelp-location-checkbox partyhelp-location-suburb-checkbox" data-area="<?php echo esc_attr($area['name']); ?>" />
                                                <span><?php echo esc_html($suburb); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="partyhelp-location-area-block partyhelp-location-other-block">
                            <label for="ph-other-location" class="partyhelp-other-location-label">Other location</label>
                            <input type="text" id="ph-other-location" name="other_location" class="partyhelp-input partyhelp-other-location-input" placeholder="Other location" />
                        </div>
                    </div>
                    <span class="partyhelp-field-error" data-field="location"></span>
                </div>

                <div class="partyhelp-venue-styles-group">
                    <label class="partyhelp-venue-styles-label">Your preferred style of venue for your party: (choose as many as you like)</label>
                    <?php if (! empty($venue_styles)): ?>
                    <div class="partyhelp-venue-styles-grid">
                        <?php foreach ($venue_styles as $vs): ?>
                        <div class="partyhelp-venue-style-item">
                            <label class="partyhelp-venue-style-option">
                                <input type="checkbox" name="room_styles[]" value="<?php echo esc_attr($vs['key']); ?>" class="partyhelp-venue-style-checkbox" />
                                <?php if (! empty($vs['image_url'])): ?>
                                <span class="partyhelp-venue-style-image-wrap"><img src="<?php echo esc_url($vs['image_url']); ?>" alt="" class="partyhelp-venue-style-image" loading="lazy" /></span>
                                <?php endif; ?>
                                <span class="partyhelp-venue-style-name"><?php echo esc_html($vs['name']); ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="partyhelp-venue-styles-empty">Venue style options (with thumbnails) will appear here after you sync the form config. Go to <a href="<?php echo esc_url(admin_url('options-general.php?page=partyhelp-form')); ?>">Settings → Partyhelp Form</a> and click <strong>Sync Now</strong>. Ensure get.partyhelp.com.au has venue styles (and images) configured in Admin.</p>
                    <?php endif; ?>
                    <span class="partyhelp-field-error" data-field="room_styles"></span>
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

    /**
     * Ensure each area has a suburbs array for suburb checkboxes.
     * If API returns label like "Area Name - Suburb1, Suburb2" but suburbs empty, parse from label.
     */
    private function normalize_areas_for_display(array $areas): array
    {
        $out = [];
        foreach ($areas as $area) {
            $suburbs = $area['suburbs'] ?? [];
            $label = $area['label'] ?? '';
            $name = $area['name'] ?? '';

            if (empty($suburbs) && $label !== '' && strpos($label, ' - ') !== false) {
                $parts = explode(' - ', $label, 2);
                $name = trim($parts[0]);
                $suburb_list = trim($parts[1] ?? '');
                if ($suburb_list !== '') {
                    $suburbs = array_map('trim', explode(',', $suburb_list));
                    $suburbs = array_values(array_filter($suburbs));
                }
            }

            $out[] = array_merge($area, [
                'name' => $name,
                'suburbs' => $suburbs,
            ]);
        }

        return $out;
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
