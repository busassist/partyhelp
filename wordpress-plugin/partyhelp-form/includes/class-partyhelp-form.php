<?php
/**
 * Main plugin class.
 */

if (! defined('ABSPATH')) {
    exit;
}

class Partyhelp_Form
{
    private static ?self $instance = null;

    public Partyhelp_Form_Sync $sync;

    public Partyhelp_Form_Settings $settings;

    public Partyhelp_Form_Renderer $renderer;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->sync = new Partyhelp_Form_Sync;
        $this->settings = new Partyhelp_Form_Settings;
        $this->renderer = new Partyhelp_Form_Renderer($this->sync);
    }

    public function init(): void
    {
        add_shortcode('partyhelp-form', [$this->renderer, 'render_form']);
        add_action('admin_menu', [$this->settings, 'add_settings_page']);
        add_action('admin_init', [$this->settings, 'register_settings']);
        add_action('wp_ajax_partyhelp_form_submit', [$this, 'handle_form_submit']);
        add_action('wp_ajax_nopriv_partyhelp_form_submit', [$this, 'handle_form_submit']);
        add_action('partyhelp_form_cron_sync', [$this->sync, 'sync_from_api']);
        add_filter('cron_schedules', [$this, 'add_cron_schedule']);
        add_action('update_option_partyhelp_form_sync_frequency_minutes', [$this, 'reschedule_sync_cron'], 10, 3);
        add_action('shutdown', [Partyhelp_Form_Debug::class, 'flush']);
    }

    /** Add custom interval for config sync (frequency in minutes). */
    public function add_cron_schedule(array $schedules): array
    {
        $minutes = (int) get_option('partyhelp_form_sync_frequency_minutes', 60);
        $minutes = max(1, min(1440, $minutes));
        $schedules['partyhelp_form_sync'] = [
            'interval' => $minutes * 60,
            'display'  => sprintf(/* translators: %d = minutes */ __('Every %d minutes', 'partyhelp-form'), $minutes),
        ];

        return $schedules;
    }

    /** Reschedule sync cron when frequency option changes. */
    public function reschedule_sync_cron($old_value, $value, $option): void
    {
        wp_clear_scheduled_hook('partyhelp_form_cron_sync');
        if (! wp_next_scheduled('partyhelp_form_cron_sync')) {
            wp_schedule_event(time(), 'partyhelp_form_sync', 'partyhelp_form_cron_sync');
        }
    }

    public function handle_form_submit(): void
    {
        check_ajax_referer('partyhelp_form_submit', 'nonce');

        $locations = isset($_POST['location']) && is_array($_POST['location'])
            ? array_map('sanitize_text_field', array_filter($_POST['location']))
            : [];
        $other_location = sanitize_text_field($_POST['other_location'] ?? '');

        if (! empty($other_location)) {
            $suburb_value = $other_location;
            $locations = array_merge($locations, [$other_location]);
        } elseif (! empty($locations)) {
            $suburb_value = $locations[0];
        } else {
            $suburb_value = '';
        }

        $room_styles = isset($_POST['room_styles']) && is_array($_POST['room_styles'])
            ? array_map('sanitize_text_field', array_filter($_POST['room_styles']))
            : [];

        $raw = [
            'First_Name' => sanitize_text_field($_POST['first_name'] ?? ''),
            'Last_Name' => sanitize_text_field($_POST['last_name'] ?? ''),
            'Email' => sanitize_email($_POST['email'] ?? ''),
            'Phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'Select_the_type_of_occasion' => sanitize_text_field($_POST['occasion_type'] ?? ''),
            'Preferred_Date' => sanitize_text_field($_POST['preferred_date'] ?? ''),
            'Number_of_Guests' => sanitize_text_field($_POST['guest_count'] ?? ''),
            'Select_preferred_location' => $suburb_value,
            'location' => $locations,
            'Estimated_Budget' => sanitize_text_field($_POST['budget_range'] ?? ''),
            'Other_details_about_the_party:' => sanitize_textarea_field($_POST['special_requirements'] ?? ''),
            'room_styles' => $room_styles,
        ];

        $response = wp_remote_post($this->settings->get_webhook_url(), [
            'timeout' => 15,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode($raw),
        ]);

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Unable to submit. Please try again.']);
        }

        if ($code >= 200 && $code < 300 && ! empty($data['success'])) {
            $payload = ['message' => $data['message'] ?? "We've got your details and will send venue details asap."];
            $redirect = $this->settings->get_redirect_url();
            if ($redirect !== '') {
                $payload['redirect_url'] = $redirect;
            }
            wp_send_json_success($payload);
        }

        $errors = $data['errors'] ?? [];
        $message = $data['message'] ?? 'Something went wrong. Please try again.';
        wp_send_json_error(['message' => $message, 'errors' => $errors]);
    }
}
