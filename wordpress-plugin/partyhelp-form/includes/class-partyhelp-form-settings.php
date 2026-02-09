<?php
/**
 * Plugin settings and admin UI.
 */

if (! defined('ABSPATH')) {
    exit;
}

class Partyhelp_Form_Settings
{
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
                </table>
                <?php submit_button(); ?>
            </form>

            <hr />

            <h2>Sync Form Config</h2>
            <p>Sync areas, occasion types, guest brackets and budget ranges from get.partyhelp.com.au</p>
            <p>
                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('partyhelp_form_sync', '1'), 'partyhelp_form_sync')); ?>"
                    class="button button-primary">Sync Now</a>
            </p>
            <?php if ($last_sync): ?>
                <p class="description">Last synced: <?php echo esc_html($last_sync); ?></p>
            <?php endif; ?>

            <h3>Cron Job (Hourly Sync)</h3>
            <p>Add this to your system crontab for hourly sync:</p>
            <pre style="background:#f5f5f5;padding:1em;overflow-x:auto;">0 * * * * cd <?php echo esc_html(ABSPATH); ?> && wp cron event run partyhelp_form_cron_sync --due-now 2>/dev/null || php -r "define('ABSPATH','<?php echo esc_html(ABSPATH); ?>'); require 'wp-load.php'; do_action('partyhelp_form_cron_sync');"</pre>
            <p class="description">Or use WP Crontrol / similar to schedule the <code>partyhelp_form_cron_sync</code> hook hourly.</p>

            <h3>Current Config (Synced)</h3>
            <ul>
                <li>Areas: <?php echo count($config['areas'] ?? []); ?></li>
                <li>Occasion types: <?php echo count($config['occasion_types'] ?? []); ?></li>
                <li>Guest brackets: <?php echo count($config['guest_brackets'] ?? []); ?></li>
                <li>Budget ranges: <?php echo count($config['budget_ranges'] ?? []); ?></li>
            </ul>
        </div>
        <?php
    }

    public function get_webhook_url(): string
    {
        return get_option('partyhelp_form_webhook_url', 'https://get.partyhelp.com.au/api/webhook/elementor-lead');
    }
}
