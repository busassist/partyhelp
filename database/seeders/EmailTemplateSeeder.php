<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'venue_introduction',
                'name' => 'Venue Introduction',
                'subject' => 'Your personalised venue recommendations from Partyhelp',
                'content_slots' => [
                    'header_text' => 'Some venue recommendations tailored for you...',
                    'thank_you_text' => 'Thank you for your party enquiry.',
                    'location_intro' => 'We can see from your enquiry that you expressed interest in venues in the following location:',
                    'recommendations_intro' => 'Please find our best venue recommendations for your party in this location. Please feel free to contact these venues directly.',
                    'closing_text' => 'Please feel free to contact me if you would like some more venue options or further advice. Or, if you want to make a booking, we can also help you finalise the arrangements for your party.',
                    'matching_rooms_label' => 'Matching rooms for your party size:',
                ],
            ],
            [
                'key' => 'form_confirmation',
                'name' => 'Form Confirmation',
                'subject' => 'Your tailored list of party venues is on the way!',
                'content_slots' => [
                    'header_text' => 'Your tailored list of party venues is on the way!',
                    'thank_you_intro' => 'Thank you for leaving your party details with our website {{websiteUrl}}. We will email you a tailored list of venues soon.',
                    'what_happens_now_text' => '<ul><li>One of our customer service team members is analysing your party requirements and matching them to the best party venues for you</li><li>You will then receive an email with the tailored list of party venues</li></ul>',
                    'what_to_do_text' => '<ul><li>Go through the list of venues and their function packages and create a short list</li><li>Speak with the functions managers that we introduce you to in our email and identify the venues that you want to visit</li><li>Visit with the venues and book your perfect party venue!</li></ul>',
                    'contact_intro' => 'If you require any information, please contact me at',
                    'ps_message' => "Don't forget to let us know which venue you have booked so we can put you in the draw to win a $159 Gold Class experience. And if you book a Partyhelp venue, you will also receive a $50 drink card!",
                ],
            ],
            [
                'key' => 'no_few_responses_prompt',
                'name' => 'No/Few Responses Prompt',
                'subject' => "Still looking for your perfect venue? We're on it",
                'content_slots' => [
                    'header_text' => "Still looking for your perfect venue? We're on it",
                    'intro_text' => "We're still matching your enquiry to the best venues. You may receive more venue introductions soon.",
                    'reassurance_text' => "If you haven't heard from many venues yet, don't worry – we've sent your details to venues that match your requirements and they have 72 hours to respond.",
                    'cta_text' => 'Need help or more options? Reply to this email or contact us.',
                    'closing_text' => 'We look forward to helping you find your perfect party venue.',
                ],
            ],
            [
                'key' => 'shortlist_check',
                'name' => 'Shortlist Check',
                'subject' => "How's your shortlist going?",
                'content_slots' => [
                    'header_text' => "How's your shortlist going?",
                    'intro_text' => 'You should have received introductions from some of our partner venues by now.',
                    'tips_text' => '<ul><li>Compare the venues and create a shortlist of your favourites</li><li>Contact the venues directly to ask questions or arrange a visit</li><li>Book your preferred venue and let us know so we can put you in the draw for a Gold Class experience!</li></ul>',
                    'cta_text' => 'Need more venue options? Get in touch.',
                    'closing_text' => 'Happy planning!',
                ],
            ],
            [
                'key' => 'additional_services_lead_expiry',
                'name' => 'Additional Services / Lead Expiry',
                'subject' => "Your lead window has closed – here's what's next",
                'content_slots' => [
                    'header_text' => "Your lead window has closed – here's what's next",
                    'expiry_intro_text' => 'The 72-hour window for venues to purchase your lead has now closed.',
                    'additional_services_text' => 'If you would like more venue options or help finalising your booking, we can still assist. Reply to this email or visit our website.',
                    'cta_text' => 'Contact us for more venue options or booking support.',
                    'closing_text' => 'Thank you for using Partyhelp.',
                ],
            ],
            [
                'key' => 'lead_opportunity',
                'name' => 'Lead Opportunity',
                'subject' => 'New {{occasion}} Lead - {{suburb}} - {{guestCount}} guests - ${{price}}',
                'content_slots' => [
                    'intro_text' => 'A new lead matches your venue. Purchase it to receive full customer details and the function pack.',
                    'cta_button_label' => 'Purchase This Lead - ${{price}}',
                    'footer_balance_text' => 'Your credit balance: ${{creditBalance}}',
                    'topup_link_text' => 'Top up credits',
                ],
            ],
            [
                'key' => 'lead_opportunity_discount',
                'name' => 'Lead Opportunity (discount tier)',
                'subject' => 'New {{occasion}} Lead - {{suburb}} - {{discountPercent}}% off - ${{price}}',
                'content_slots' => [
                    'intro_text' => 'This lead is still available – now at {{discountPercent}}% off.',
                    'discount_intro_text' => 'This lead is now {{discountPercent}}% off. Purchase to receive full customer details.',
                    'cta_button_label' => 'Purchase This Lead - ${{price}}',
                    'footer_balance_text' => 'Your credit balance: ${{creditBalance}}',
                    'topup_link_text' => 'Top up credits',
                ],
            ],
            [
                'key' => 'lead_no_longer_available',
                'name' => 'Lead No Longer Available',
                'subject' => 'Lead no longer available - {{suburb}}',
                'content_slots' => [
                    'header_text' => 'Lead no longer available',
                    'body_text' => 'This lead has been fulfilled or has expired. You can view other available leads in your dashboard.',
                    'cta_text' => 'View available leads',
                ],
            ],
            [
                'key' => 'function_pack',
                'name' => 'Function Pack',
                'subject' => 'Your function pack is ready to download',
                'content_slots' => [
                    'header_text' => 'Your function pack is ready to download',
                    'intro_text' => 'You have purchased a lead. Download the function pack below to access the full customer details and materials.',
                    'download_button_label' => 'Download function pack',
                    'closing_text' => 'The download link will expire after 30 days. Contact us if you need assistance.',
                ],
            ],
            [
                'key' => 'failed_topup_notification',
                'name' => 'Failed Top-Up Notification',
                'subject' => 'Your Partyhelp credit top-up could not be processed',
                'content_slots' => [
                    'header_text' => 'Credit top-up unsuccessful',
                    'intro_text' => 'We tried to top up your credit balance but the payment could not be processed. Please update your payment method to continue receiving lead opportunities.',
                    'cta_button_label' => 'Update payment method',
                    'closing_text' => 'Your lead opportunities may be paused until your payment method is updated.',
                ],
            ],
            [
                'key' => 'invoice_receipt',
                'name' => 'Invoice / Receipt',
                'subject' => 'Your Partyhelp {{documentType}} #{{invoiceNumber}}',
                'content_slots' => [
                    'header_text' => 'Your payment confirmation',
                    'intro_text' => 'Thank you for your payment. Details are below.',
                    'view_statement_label' => 'View invoice / receipt',
                    'closing_text' => 'If you have any questions, please contact us.',
                ],
            ],
            [
                'key' => 'venue_set_password',
                'name' => 'Venue set password (new venue)',
                'subject' => 'Set your Partyhelp venue portal password',
                'content_slots' => [
                    'intro_text' => 'Your venue {{venueName}} has been set up on Partyhelp. Click the button below to set your password and sign in to the venue portal.',
                    'cta_button_label' => 'Set password',
                    'expiry_note' => 'This link will expire after 60 minutes.',
                ],
            ],
            [
                'key' => 'venue_registration_approved',
                'name' => 'Venue registration approved',
                'subject' => 'Your Partyhelp venue registration has been approved',
                'content_slots' => [
                    'header_text' => 'Your registration has been approved',
                    'intro_text' => 'Your venue registration has been approved. You may now sign in to the Partyhelp venue portal to begin receiving leads.',
                    'cta_button_label' => 'Sign in to venue portal',
                ],
            ],
            [
                'key' => 'new_venue_for_approval',
                'name' => 'New Venue For Approval',
                'subject' => 'New venue pending approval: {{venueName}}',
                'content_slots' => [
                    'header_text' => 'New venue pending approval',
                    'intro_text' => 'A new venue has registered and is awaiting approval. Review the details and approve or reject below.',
                    'review_button_label' => 'Review vendor',
                    'approve_button_label' => 'Approve',
                    'reject_button_label' => 'Reject',
                ],
            ],
            [
                'key' => 'low_match_alert',
                'name' => 'Low-Match Alert',
                'subject' => 'Low-match alert: {{matchCount}} venues for lead in {{suburb}}',
                'content_slots' => [
                    'header_text' => 'Low-match alert',
                    'intro_text' => 'Fewer than 10 venues matched this lead. You may want to review matching criteria or contact the customer.',
                    'view_lead_label' => 'View lead in dashboard',
                ],
            ],
        ];

        foreach ($templates as $data) {
            EmailTemplate::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }
    }
}
