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
        ];

        foreach ($templates as $data) {
            EmailTemplate::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }
    }
}
