<?php
/**
 * Plugin settings and admin UI.
 */

if (! defined('ABSPATH')) {
    exit;
}

class Partyhelp_Form_Settings
{
    private const STYLE_DEFAULTS = [
        'form_bg_color' => '#2c0f4a',
        'text_font_family' => "'Instrument Sans', ui-sans-serif, system-ui, sans-serif",
        'heading_font_family' => "'Instrument Sans', ui-sans-serif, system-ui, sans-serif",
        'field_border_radius_px' => 8,
        'field_border_color' => '#4a3a6a',
        'label_color' => '#ffffff',
        'text_color' => '#e1e1e1',
    ];

    public function add_settings_page(): void
    {
        add_options_page(
            'Partyhelp Form',
            'Partyhelp Form',
            'manage_options',
            'partyhelp-form',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings(): void
    {
        register_setting('partyhelp_form', 'partyhelp_form_api_url', [
            'type' => 'string',
            'default' => 'https://get.partyhelp.com.au/api/partyhelp-form',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        register_setting('partyhelp_form', 'partyhelp_form_webhook_url', [
            'type' => 'string',
            'default' => 'https://get.partyhelp.com.au/api/webhook/elementor-lead',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        register_setting('partyhelp_form', 'partyhelp_form_sync_frequency_minutes', [
            'type' => 'integer',
            'default' => 60,
            'sanitize_callback' => function ($value) {
                $v = absint($value);
                return $v >= 1 && $v <= 1440 ? $v : 60;
            },
        ]);

        register_setting('partyhelp_form', 'partyhelp_form_debug_enabled', [
            'type' => 'boolean',
            'default' => false,
            'sanitize_callback' => function ($value) {
                return (bool) $value;
            },
        ]);

        register_setting('partyhelp_form', 'partyhelp_form_debug_api_url', [
            'type' => 'string',
            'default' => 'https://get.partyhelp.com.au/api/partyhelp-form/debug-log',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        foreach (array_keys(self::STYLE_DEFAULTS) as $key) {
            $option_name = 'partyhelp_form_style_' . $key;
            $default = self::STYLE_DEFAULTS[$key];
            if (is_int($default)) {
                register_setting('partyhelp_form_styles', $option_name, [
                    'type' => 'integer',
                    'default' => $default,
                    'sanitize_callback' => function ($value) use ($default) {
                        $v = absint($value);
                        return $v >= 0 && $v <= 999 ? $v : $default;
                    },
                ]);
            } else {
                register_setting('partyhelp_form_styles', $option_name, [
                    'type' => 'string',
                    'default' => $default,
                    'sanitize_callback' => function ($value) use ($key, $default) {
                        return $this->sanitize_style_value($key, $value, $default);
                    },
                ]);
            }
        }

        if (isset($_GET['partyhelp_form_sync']) && check_admin_referer('partyhelp_form_sync')) {
            partyhelp_form()->sync->sync_from_api();
            wp_redirect(add_query_arg(['settings-updated' => '1', 'synced' => '1'], wp_get_referer() ?: admin_url('options-general.php?page=partyhelp-form')));
            exit;
        }
    }

    public function render_settings_page(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $last_sync = partyhelp_form()->sync->get_last_sync();
        $config = partyhelp_form()->sync->get_config();
        ?>
        <div class="wrap">
            <h1>Partyhelp Form Settings</h1>
            <?php if (defined('PARTYHELP_FORM_VERSION')): ?>
            <p class="description">Plugin version: <strong><?php echo esc_html(PARTYHELP_FORM_VERSION); ?></strong>. If you don’t see the <strong>Debug</strong> section below, update to the latest plugin from the repo.</p>
            <?php endif; ?>

            <?php if (isset($_GET['synced'])): ?>
                <div class="notice notice-success"><p>Form config synced successfully.</p></div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields('partyhelp_form'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="partyhelp_form_api_url">API Base URL</label></th>
                        <td>
                            <input type="url" id="partyhelp_form_api_url" name="partyhelp_form_api_url"
                                value="<?php echo esc_attr(get_option('partyhelp_form_api_url', 'https://get.partyhelp.com.au/api/partyhelp-form')); ?>"
                                class="regular-text" />
                            <p class="description">Base URL for form config (areas, occasion types, etc.)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_webhook_url">Webhook URL</label></th>
                        <td>
                            <input type="url" id="partyhelp_form_webhook_url" name="partyhelp_form_webhook_url"
                                value="<?php echo esc_attr(get_option('partyhelp_form_webhook_url', 'https://get.partyhelp.com.au/api/webhook/elementor-lead')); ?>"
                                class="regular-text" />
                            <p class="description">Form submissions are sent to this URL</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_sync_frequency_minutes">Sync frequency (minutes)</label></th>
                        <td>
                            <input type="number" id="partyhelp_form_sync_frequency_minutes" name="partyhelp_form_sync_frequency_minutes"
                                value="<?php echo esc_attr(get_option('partyhelp_form_sync_frequency_minutes', 60)); ?>"
                                min="1" max="1440" step="1" class="small-text" />
                            <p class="description">How often to sync form config (areas, occasion types, venue styles, etc.) from the API. Default: 60 (hourly). Save to apply.</p>
                        </td>
                    </tr>
                </table>

                <h3 style="margin-top:1.5em;">Debug</h3>
                <p class="description" style="margin-bottom:0.5em;">Send plugin debug messages to get.partyhelp.com.au to troubleshoot issues remotely.</p>
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable debug mode</th>
                        <td>
                            <input type="hidden" name="partyhelp_form_debug_enabled" value="0" />
                            <label for="partyhelp_form_debug_enabled">
                                <input type="checkbox" id="partyhelp_form_debug_enabled" name="partyhelp_form_debug_enabled" value="1"
                                    <?php checked(get_option('partyhelp_form_debug_enabled', false)); ?> />
                                Enable debug mode
                            </label>
                            <p class="description">When enabled, the plugin sends debug messages (e.g. form render, style options, sync results) to the Debug API URL below.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_debug_api_url">Debug API URL</label></th>
                        <td>
                            <input type="url" id="partyhelp_form_debug_api_url" name="partyhelp_form_debug_api_url"
                                value="<?php echo esc_attr(get_option('partyhelp_form_debug_api_url', 'https://get.partyhelp.com.au/api/partyhelp-form/debug-log')); ?>"
                                class="large-text" />
                            <p class="description">Endpoint that receives debug payloads (default: get.partyhelp.com.au). View logs in Laravel <code>storage/logs/laravel.log</code>.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <h2>Form appearance</h2>
            <p>Customise colours and typography for the form. Leave blank to use defaults.</p>
            <form method="post" action="options.php">
                <?php settings_fields('partyhelp_form_styles'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="partyhelp_form_style_form_bg_color">Form background colour</label></th>
                        <td>
                            <input type="text" id="partyhelp_form_style_form_bg_color" name="partyhelp_form_style_form_bg_color"
                                value="<?php echo esc_attr($this->get_style_option('form_bg_color')); ?>"
                                class="small-text" placeholder="<?php echo esc_attr(self::STYLE_DEFAULTS['form_bg_color']); ?>" />
                            <p class="description">e.g. #2c0f4a. Default: <?php echo esc_html(self::STYLE_DEFAULTS['form_bg_color']); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_style_label_color">Heading &amp; field label colour</label></th>
                        <td>
                            <input type="text" id="partyhelp_form_style_label_color" name="partyhelp_form_style_label_color"
                                value="<?php echo esc_attr($this->get_style_option('label_color')); ?>"
                                class="small-text" placeholder="<?php echo esc_attr(self::STYLE_DEFAULTS['label_color']); ?>" />
                            <p class="description">Default: white (#ffffff)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_style_text_color">Text colour (body &amp; options)</label></th>
                        <td>
                            <input type="text" id="partyhelp_form_style_text_color" name="partyhelp_form_style_text_color"
                                value="<?php echo esc_attr($this->get_style_option('text_color')); ?>"
                                class="small-text" placeholder="<?php echo esc_attr(self::STYLE_DEFAULTS['text_color']); ?>" />
                            <p class="description">Body text and checkbox option text. Default: #e1e1e1</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_style_text_font_family">Text font family</label></th>
                        <td>
                            <input type="text" id="partyhelp_form_style_text_font_family" name="partyhelp_form_style_text_font_family"
                                value="<?php echo esc_attr($this->get_style_option('text_font_family')); ?>"
                                class="large-text" placeholder="<?php echo esc_attr(self::STYLE_DEFAULTS['text_font_family']); ?>" />
                            <p class="description">e.g. 'Instrument Sans', ui-sans-serif, system-ui, sans-serif</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_style_heading_font_family">Heading font family</label></th>
                        <td>
                            <input type="text" id="partyhelp_form_style_heading_font_family" name="partyhelp_form_style_heading_font_family"
                                value="<?php echo esc_attr($this->get_style_option('heading_font_family')); ?>"
                                class="large-text" placeholder="<?php echo esc_attr(self::STYLE_DEFAULTS['heading_font_family']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_style_field_border_radius_px">Field border radius (px)</label></th>
                        <td>
                            <input type="number" id="partyhelp_form_style_field_border_radius_px" name="partyhelp_form_style_field_border_radius_px"
                                value="<?php echo esc_attr($this->get_style_option('field_border_radius_px')); ?>"
                                min="0" step="1" class="small-text" />
                            <p class="description">Default: 8. No maximum.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partyhelp_form_style_field_border_color">Field border colour</label></th>
                        <td>
                            <input type="text" id="partyhelp_form_style_field_border_color" name="partyhelp_form_style_field_border_color"
                                value="<?php echo esc_attr($this->get_style_option('field_border_color')); ?>"
                                class="small-text" placeholder="<?php echo esc_attr(self::STYLE_DEFAULTS['field_border_color']); ?>" />
                            <p class="description">Default: #4a3a6a</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save appearance'); ?>
            </form>

            <hr />

            <h2>Sync Form Config</h2>
            <p>Sync areas, occasion types, guest brackets, budget ranges and venue styles from get.partyhelp.com.au</p>
            <p>
                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('partyhelp_form_sync', '1'), 'partyhelp_form_sync')); ?>"
                    class="button button-primary">Sync Now</a>
            </p>
            <?php if ($last_sync): ?>
                <p class="description">Last synced: <?php echo esc_html($last_sync); ?></p>
            <?php endif; ?>

            <h3>Cron Job (Scheduled Sync)</h3>
            <p>Sync runs on the interval set above (default: every 60 minutes). Add this to your system crontab so WordPress cron runs:</p>
            <pre style="background:#f5f5f5;padding:1em;overflow-x:auto;">*/5 * * * * cd <?php echo esc_html(ABSPATH); ?> && wp cron event run --due-now 2>/dev/null || php -r "define('ABSPATH','<?php echo esc_html(ABSPATH); ?>'); require 'wp-load.php'; wp_cron();"</pre>
            <p class="description">Or use WP Crontrol to schedule the <code>partyhelp_form_cron_sync</code> hook with your chosen interval.</p>

            <h3>Current Config (Synced)</h3>
            <ul>
                <li>Areas: <?php echo count($config['areas'] ?? []); ?></li>
                <li>Occasion types: <?php echo count($config['occasion_types'] ?? []); ?></li>
                <li>Guest brackets: <?php echo count($config['guest_brackets'] ?? []); ?></li>
                <li>Budget ranges: <?php echo count($config['budget_ranges'] ?? []); ?></li>
                <li>Venue styles: <?php echo count($config['venue_styles'] ?? []); ?></li>
            </ul>
            <?php if (empty($config['venue_styles'])): ?>
            <p class="description">If venue styles is 0, the form will show a message instead of style options. Click <strong>Sync Now</strong> after ensuring get.partyhelp.com.au has the venue_styles table migrated and seeded, and venue styles (with images) set in Admin → Venue Styles.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function get_webhook_url(): string
    {
        return get_option('partyhelp_form_webhook_url', 'https://get.partyhelp.com.au/api/webhook/elementor-lead');
    }

    /**
     * Get a style option value with default.
     * @return string|int
     */
    public function get_style_option(string $key)
    {
        $default = self::STYLE_DEFAULTS[$key] ?? '';
        $value = get_option('partyhelp_form_style_' . $key, $default);

        return $value !== '' && $value !== null ? $value : $default;
    }

    /**
     * Return CSS to override form variables from saved style options.
     * Output inside a <style> block when rendering the form.
     */
    public function get_custom_form_css(): string
    {
        $form_bg = $this->sanitize_hex_or_empty($this->get_style_option('form_bg_color'));
        $label_color = $this->sanitize_hex_or_empty($this->get_style_option('label_color'));
        $text_color = $this->sanitize_hex_or_empty($this->get_style_option('text_color'));
        $border_color = $this->sanitize_hex_or_empty($this->get_style_option('field_border_color'));
        $radius = (int) $this->get_style_option('field_border_radius_px');
        $radius = $radius >= 0 && $radius <= 999 ? $radius : 8;
        $text_font = $this->get_style_option('text_font_family');
        $heading_font = $this->get_style_option('heading_font_family');

        $form_bg = $form_bg !== '' ? $form_bg : self::STYLE_DEFAULTS['form_bg_color'];
        $label_color = $label_color !== '' ? $label_color : self::STYLE_DEFAULTS['label_color'];
        $text_color = $text_color !== '' ? $text_color : self::STYLE_DEFAULTS['text_color'];
        $border_color = $border_color !== '' ? $border_color : self::STYLE_DEFAULTS['field_border_color'];
        $text_font = is_string($text_font) && $text_font !== '' ? $text_font : self::STYLE_DEFAULTS['text_font_family'];
        $heading_font = is_string($heading_font) && $heading_font !== '' ? $heading_font : self::STYLE_DEFAULTS['heading_font_family'];

        $lines = [
            '.partyhelp-form-wrapper {',
        ];
        $lines[] = '  --ph-bg-form: ' . $form_bg . ';';
        $lines[] = '  --ph-text: ' . $label_color . ';';
        $lines[] = '  --ph-text-muted: ' . $text_color . ';';
        $lines[] = '  --ph-border: ' . $border_color . ';';
        $lines[] = '  --ph-radius-sm: ' . $radius . 'px;';
        $lines[] = '  font-family: ' . $text_font . ';';
        $lines[] = '}';
        $lines[] = '.partyhelp-form-wrapper .partyhelp-form-heading,';
        $lines[] = '.partyhelp-form-wrapper label,';
        $lines[] = '.partyhelp-form-wrapper .partyhelp-location-area-name,';
        $lines[] = '.partyhelp-form-wrapper .partyhelp-venue-style-name {';
        $lines[] = '  font-family: ' . $heading_font . ';';
        $lines[] = '}';

        return implode("\n", $lines);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string|int $default
     * @return string|int
     */
    private function sanitize_style_value(string $key, $value, $default)
    {
        if (is_int($default)) {
            $v = absint($value);
            return $v >= 0 && $v <= 999 ? $v : $default;
        }
        if (in_array($key, ['form_bg_color', 'label_color', 'text_color', 'field_border_color'], true)) {
            $hex = $this->sanitize_hex_or_empty($value);
            return $hex !== '' ? $hex : $default;
        }
        if (in_array($key, ['text_font_family', 'heading_font_family'], true)) {
            $s = is_string($value) ? preg_replace('/[^\p{L}\p{N}\s,\'\"\-]/u', '', $value) : '';
            $s = substr(trim($s), 0, 200);
            return $s !== '' ? $s : $default;
        }
        return is_string($value) ? sanitize_text_field($value) : $default;
    }

    private function sanitize_hex_or_empty(mixed $value): string
    {
        $s = is_string($value) ? trim($value) : '';
        if ($s === '') {
            return '';
        }
        if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $s)) {
            return $s;
        }
        return '';
    }
}
