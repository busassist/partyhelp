<?php
/**
 * Syncs form config (areas, occasion types, etc.) from get.partyhelp.com.au API.
 */

if (! defined('ABSPATH')) {
    exit;
}

class Partyhelp_Form_Sync
{
    private const OPTION_KEY = 'partyhelp_form_config';

    private const OPTION_LAST_SYNC = 'partyhelp_form_last_sync';

    public function get_config(): array
    {
        $stored = get_option(self::OPTION_KEY, null);

        if (is_array($stored) && ! empty($stored)) {
            return $stored;
        }

        $this->sync_from_api();

        return get_option(self::OPTION_KEY, $this->get_default_config());
    }

    public function sync_from_api(): array
    {
        $api_url = $this->get_api_base_url() . '/config';

        $response = wp_remote_get($api_url, [
            'timeout' => 15,
            'headers' => ['Accept' => 'application/json'],
        ]);

        if (is_wp_error($response)) {
            return get_option(self::OPTION_KEY, $this->get_default_config());
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (! is_array($data)) {
            return get_option(self::OPTION_KEY, $this->get_default_config());
        }

        $config = [
            'areas' => $data['areas'] ?? [],
            'occasion_types' => $data['occasion_types'] ?? [],
            'guest_brackets' => $data['guest_brackets'] ?? [],
            'budget_ranges' => $data['budget_ranges'] ?? [],
            'venue_styles' => $data['venue_styles'] ?? [],
        ];

        // If main config did not include venue styles (e.g. older API), fetch from dedicated endpoint
        if (empty($config['venue_styles'])) {
            $config['venue_styles'] = $this->fetch_venue_styles();
        }

        update_option(self::OPTION_KEY, $config);
        update_option(self::OPTION_LAST_SYNC, current_time('mysql'));

        Partyhelp_Form_Debug::log('sync_completed', [
            'venue_styles_count' => count($config['venue_styles']),
            'areas_count' => count($config['areas']),
        ]);

        return $config;
    }

    public function get_last_sync(): ?string
    {
        return get_option(self::OPTION_LAST_SYNC);
    }

    public function get_api_base_url(): string
    {
        $url = get_option('partyhelp_form_api_url', 'https://get.partyhelp.com.au/api/partyhelp-form');
        $url = rtrim($url, '/');

        return $url;
    }

    private function get_default_config(): array
    {
        return [
            'areas' => [
                ['id' => 1, 'name' => 'CBD', 'label' => 'CBD - Southbank, Docklands, CBD', 'suburbs' => ['Southbank', 'Docklands', 'Melbourne']],
                ['id' => 2, 'name' => 'INNER SOUTH', 'label' => 'INNER SOUTH - South Melbourne, Port Melbourne, Albert Park, Middle Park', 'suburbs' => ['South Melbourne', 'Port Melbourne', 'Albert Park', 'Middle Park']],
                ['id' => 3, 'name' => 'INNER SOUTH EAST', 'label' => 'INNER SOUTH EAST - South Yarra, St Kilda, Prahran, Windsor', 'suburbs' => ['South Yarra', 'St Kilda', 'Prahran', 'Windsor']],
                ['id' => 4, 'name' => 'INNER EAST', 'label' => 'INNER EAST - Richmond, Hawthorn, Kew, Abbotsford', 'suburbs' => ['Richmond', 'Hawthorn', 'Kew', 'Abbotsford']],
                ['id' => 5, 'name' => 'INNER NORTH', 'label' => 'INNER NORTH - Carlton, Collingwood, Fitzroy, Brunswick', 'suburbs' => ['Carlton', 'Collingwood', 'Fitzroy', 'Brunswick']],
                ['id' => 6, 'name' => 'INNER WEST', 'label' => 'INNER WEST - Flemington, Footscray, Yarraville', 'suburbs' => ['Flemington', 'Footscray', 'Yarraville']],
            ],
            'occasion_types' => [
                ['key' => '21st_birthday', 'label' => '21st Birthday'],
                ['key' => '30th_birthday', 'label' => '30th Birthday'],
                ['key' => '50th_birthday', 'label' => '50th Birthday'],
                ['key' => 'engagement_party', 'label' => 'Engagement Party'],
                ['key' => 'wedding_reception', 'label' => 'Wedding Reception'],
                ['key' => 'corporate_function', 'label' => 'Corporate Function'],
                ['key' => 'christmas_party', 'label' => 'Christmas Party'],
                ['key' => 'other', 'label' => 'Other'],
            ],
            'guest_brackets' => [
                ['value' => '10-29', 'label' => '10-29', 'guest_min' => 10, 'guest_max' => 29],
                ['value' => '30-60', 'label' => '30-60', 'guest_min' => 30, 'guest_max' => 60],
                ['value' => '61-100', 'label' => '61-100', 'guest_min' => 61, 'guest_max' => 100],
                ['value' => '100+', 'label' => '100+', 'guest_min' => 101, 'guest_max' => 500],
            ],
            'budget_ranges' => [],
            'venue_styles' => [],
        ];
    }

    /**
     * Fetch venue styles from the dedicated API endpoint.
     * Used when the main /config response does not include venue_styles.
     */
    private function fetch_venue_styles(): array
    {
        $response = wp_remote_get($this->get_api_base_url() . '/venue-styles', [
            'timeout' => 10,
            'headers' => ['Accept' => 'application/json'],
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (! is_array($data) || empty($data['venue_styles'])) {
            return [];
        }

        return $data['venue_styles'];
    }
}
