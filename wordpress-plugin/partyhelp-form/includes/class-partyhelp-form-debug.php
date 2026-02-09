<?php
/**
 * Debug logger: buffers messages and sends to get.partyhelp.com.au when debug mode is on.
 */

if (! defined('ABSPATH')) {
    exit;
}

class Partyhelp_Form_Debug
{
    private static array $entries = [];

    public static function log(string $message, array $context = []): void
    {
        if (! get_option('partyhelp_form_debug_enabled', false)) {
            return;
        }
        self::$entries[] = [
            'ts' => gmdate('c'),
            'message' => $message,
            'context' => $context,
        ];
    }

    /**
     * Send buffered entries to the debug API and clear the buffer.
     */
    public static function flush(): void
    {
        if (! get_option('partyhelp_form_debug_enabled', false) || empty(self::$entries)) {
            return;
        }

        $url = get_option('partyhelp_form_debug_api_url', 'https://get.partyhelp.com.au/api/partyhelp-form/debug-log');
        $url = rtrim($url, '/');

        $payload = [
            'site_url' => home_url(),
            'plugin_version' => defined('PARTYHELP_FORM_VERSION') ? PARTYHELP_FORM_VERSION : '',
            'entries' => self::$entries,
        ];

        wp_remote_post($url, [
            'timeout' => 10,
            'blocking' => false,
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => wp_json_encode($payload),
        ]);

        self::$entries = [];
    }
}
