<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Occasion Types
    |--------------------------------------------------------------------------
    */
    'occasion_types' => [
        '21st_birthday' => '21st Birthday',
        '30th_birthday' => '30th Birthday',
        '40th_birthday' => '40th Birthday',
        '50th_birthday' => '50th Birthday',
        '60th_birthday' => '60th Birthday',
        'other_birthday' => 'Other Birthday',
        'engagement_party' => 'Engagement Party',
        'wedding_reception' => 'Wedding Reception',
        'corporate_function' => 'Corporate Function',
        'christmas_party' => 'Christmas Party',
        'farewell_party' => 'Farewell Party',
        'baby_shower' => 'Baby Shower',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Room Styles
    |--------------------------------------------------------------------------
    */
    'room_styles' => [
        'bar' => 'Bar',
        'function_room' => 'Function Room',
        'pub' => 'Pub',
        'club' => 'Club',
        'semi_outdoor' => 'Semi-Outdoor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Venue Portal Limits (PRD 8.3)
    |--------------------------------------------------------------------------
    */
    'max_rooms_per_venue' => (int) env('PARTYHELP_MAX_ROOMS_PER_VENUE', 6),
    'max_photos_per_room' => (int) env('PARTYHELP_MAX_PHOTOS_PER_ROOM', 4),
    'max_images_per_venue' => (int) env('PARTYHELP_MAX_IMAGES_PER_VENUE', 10),

    /*
    |--------------------------------------------------------------------------
    | Lead Settings
    |--------------------------------------------------------------------------
    */
    'lead' => [
        'max_matches' => 30,
        'fulfilment_threshold' => 3,
        'expiry_hours' => 72,
    ],

    /*
    |--------------------------------------------------------------------------
    | Credit Settings
    |--------------------------------------------------------------------------
    */
    'credits' => [
        'minimum_balance' => 100.00,
        'default_topup_threshold' => 75.00,
        'default_topup_amount' => 50.00,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Notification Email
    |--------------------------------------------------------------------------
    */
    'admin_email' => env('PARTYHELP_ADMIN_EMAIL', 'admin@partyhelp.com.au'),
    'admin_phone' => env('PARTYHELP_ADMIN_PHONE'),

    /*
    |--------------------------------------------------------------------------
    | Twilio WhatsApp – Lead opportunity interactive template
    |--------------------------------------------------------------------------
    | Content Template SID for the "Accept / Ignore" quick-reply message sent
    | when notifying venues about a new lead. Create in Twilio Console > Content
    | Template Builder (twilio/quick-reply, body + two buttons with id {{1}} and {{2}}).
    */
    'twilio_lead_opportunity_content_sid' => env('TWILIO_LEAD_OPPORTUNITY_CONTENT_SID'),

    /*
    |--------------------------------------------------------------------------
    | Public website URL (customer-facing marketing site, used in emails)
    |--------------------------------------------------------------------------
    */
    'public_website_url' => env('PARTYHELP_PUBLIC_WEBSITE_URL', 'https://partyhelp.com.au'),

    /*
    |--------------------------------------------------------------------------
    | Test Mode (for lead injection etc.)
    |--------------------------------------------------------------------------
    */
    'test_mode' => env('APP_ENV', 'production') !== 'production',
    'test_user_email' => env('PARTYHELP_TEST_USER_EMAIL', 'venue@partyhelp.com.au'),

    /*
    |--------------------------------------------------------------------------
    | Media Upload Settings
    |--------------------------------------------------------------------------
    */
    'media' => [
        'max_width' => (int) env('MEDIA_MAX_WIDTH', 1920),
        'output_format' => env('MEDIA_OUTPUT_FORMAT', 'jpeg'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Venue Introduction Email
    |--------------------------------------------------------------------------
    */
    'venue_intro_email' => [
        'footer_tagline' => env('VENUE_INTRO_FOOTER_TAGLINE', "We've helped over 50,000 people find their perfect venue. Let us help you make your next event unforgettable."),
        'support_email' => env('VENUE_INTRO_SUPPORT_EMAIL', 'venues@partyhelp.com.au'),
        'sign_off_name' => env('VENUE_INTRO_SIGN_OFF_NAME', 'Johnny'),
        'sign_off_title' => env('VENUE_INTRO_SIGN_OFF_TITLE', 'Manager, Party Venues'),
        'business_address' => env('VENUE_INTRO_BUSINESS_ADDRESS', 'Business Assist, 195 Little Collins Street, Melbourne VIC 3000, Australia'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Confirmation Email
    |--------------------------------------------------------------------------
    */
    'form_confirmation_email' => [
        'support_email' => env('FORM_CONFIRMATION_SUPPORT_EMAIL', 'venues@partyhelp.com.au'),
        'sign_off_name' => env('FORM_CONFIRMATION_SIGN_OFF_NAME', 'Johnny'),
        'sign_off_title' => env('FORM_CONFIRMATION_SIGN_OFF_TITLE', 'Manager, Party Venues'),
        'business_address' => env('FORM_CONFIRMATION_BUSINESS_ADDRESS', 'Party Help, 195 Little Collins Street, Melbourne Victoria 3000, Australia'),
        'ps_message' => env('FORM_CONFIRMATION_PS_MESSAGE', "Don't forget to let us know which venue you have booked so we can put you in the draw to win a \$159 Gold Class experience. And if you book a Partyhelp venue, you will also receive a \$50 drink card!"),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Template Editable Slots
    |--------------------------------------------------------------------------
    | Maps template key => [slot_key => human_label]. Used for WYSIWYG editing.
    */
    'email_template_slots' => [
        'venue_introduction' => [
            'header_text' => 'Header text',
            'thank_you_text' => 'Thank you message',
            'location_intro' => 'Location intro',
            'recommendations_intro' => 'Recommendations intro',
            'closing_text' => 'Closing message',
            'matching_rooms_label' => 'Matching rooms label',
        ],
        'form_confirmation' => [
            'header_text' => 'Header text',
            'thank_you_intro' => 'Thank you intro',
            'what_happens_now_text' => 'What happens now',
            'what_to_do_text' => 'What do I need to do',
            'contact_intro' => 'Contact intro',
            'ps_message' => 'PS message',
        ],
        'no_few_responses_prompt' => [
            'header_text' => 'Header text',
            'intro_text' => 'Intro text',
            'reassurance_text' => 'Reassurance text',
            'cta_text' => 'CTA text',
            'closing_text' => 'Closing message',
        ],
        'shortlist_check' => [
            'header_text' => 'Header text',
            'intro_text' => 'Intro text',
            'tips_text' => 'Tips (HTML list ok)',
            'cta_text' => 'CTA text',
            'closing_text' => 'Closing message',
        ],
        'additional_services_lead_expiry' => [
            'header_text' => 'Header text',
            'expiry_intro_text' => 'Expiry intro',
            'additional_services_text' => 'Additional services',
            'cta_text' => 'CTA text',
            'closing_text' => 'Closing message',
        ],
        'lead_opportunity' => [
            'intro_text' => 'Intro text',
            'cta_button_label' => 'CTA button label',
            'footer_balance_text' => 'Footer balance text',
            'topup_link_text' => 'Top-up link text',
        ],
        'lead_opportunity_discount' => [
            'intro_text' => 'Intro text (use {{discountPercent}} for the tier %)',
            'discount_intro_text' => 'Discount intro (use {{discountPercent}})',
            'cta_button_label' => 'CTA button label',
            'footer_balance_text' => 'Footer balance text',
            'topup_link_text' => 'Top-up link text',
        ],
        'lead_no_longer_available' => [
            'header_text' => 'Header text',
            'body_text' => 'Body text',
            'cta_text' => 'CTA text',
        ],
        'function_pack' => [
            'header_text' => 'Header text',
            'intro_text' => 'Intro text',
            'download_button_label' => 'Download button label',
            'closing_text' => 'Closing message',
        ],
        'failed_topup_notification' => [
            'header_text' => 'Header text',
            'intro_text' => 'Intro text',
            'cta_button_label' => 'CTA button label',
            'closing_text' => 'Closing message',
        ],
        'invoice_receipt' => [
            'header_text' => 'Header text',
            'intro_text' => 'Intro text',
            'view_statement_label' => 'View statement label',
            'closing_text' => 'Closing message',
        ],
        'venue_set_password' => [
            'intro_text' => 'Intro text (use {{venueName}})',
            'cta_button_label' => 'CTA button label',
            'expiry_note' => 'Expiry note',
        ],
        'venue_registration_approved' => [
            'header_text' => 'Header text',
            'intro_text' => 'Intro text',
            'cta_button_label' => 'CTA button label',
        ],
        'new_venue_for_approval' => [
            'header_text' => 'Header text',
            'intro_text' => 'Intro text',
            'review_button_label' => 'Review button label',
            'approve_button_label' => 'Approve button label',
            'reject_button_label' => 'Reject button label',
        ],
        'low_match_alert' => [
            'header_text' => 'Header text',
            'intro_text' => 'Intro text',
            'view_lead_label' => 'View lead label',
        ],
    ],
];
