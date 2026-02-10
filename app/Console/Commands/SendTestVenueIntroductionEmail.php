<?php

namespace App\Console\Commands;

use App\Mail\VenueIntroductionEmail;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestVenueIntroductionEmail extends Command
{
    protected $signature = 'email:test-venue-introduction
                            {--to= : Recipient email address}
                            {--name=PipTest : Customer first name}
                            {--location= : Location (e.g. "INNER NORTH - Carlton, Collingwood, Fitzroy, Brunswick")}';

    protected $description = 'Send a test Venue Introduction email with sample data';

    public function handle(): int
    {
        $to = $this->option('to') ?? config('mail.from.address');
        $name = $this->option('name');
        $location = $this->option('location') ?? 'INNER NORTH - Carlton, Collingwood, Fitzroy, Brunswick';

        $venues = $this->getSampleVenues();
        $viewInBrowserUrl = config('app.url') . '/emails/venue-intro/preview?token=sample';
        $unsubscribeUrl = config('app.url') . '/unsubscribe?token=sample';

        $mailable = new VenueIntroductionEmail(
            customerName: $name,
            location: $location,
            venues: $venues,
            viewInBrowserUrl: $viewInBrowserUrl,
            unsubscribeUrl: $unsubscribeUrl,
        );

        Mail::to($to)->send($mailable);

        $this->info("Test Venue Introduction email sent to: {$to}");

        return self::SUCCESS;
    }

    private function getSampleVenues(): array
    {
        $imageUrls = $this->getMediaImageUrls();
        $placeholder = 'https://via.placeholder.com/600x450/1e0f3d/9ca3af?text=Room+Photo';
        $nextImage = fn () => $imageUrls->isEmpty() ? $placeholder : $imageUrls->shift();

        return [
            [
                'venue_name' => 'The Queensberry Hotel',
                'venue_area' => 'Carlton/CBD',
                'contact_name' => 'Anne',
                'contact_phone' => '9347 2648',
                'email' => 'party@queensberryhotel.com.au',
                'website' => 'www.queensberryhotel.com.au',
                'room_hire' => '$0',
                'minimum_spend' => '$1,500',
                'rooms' => [
                    [
                        'room_name' => 'Corporate Room',
                        'description' => 'A versatile space equipped with premium AV facilities, ideal for professional networking and celebratory dinners.',
                        'image_url' => $nextImage(),
                        'capacity_min' => 20,
                        'capacity_max' => 80,
                    ],
                    [
                        'room_name' => 'Private Dining Room',
                        'description' => 'Intimate space perfect for smaller groups with dedicated service.',
                        'image_url' => $nextImage(),
                        'capacity_min' => 10,
                        'capacity_max' => 30,
                    ],
                ],
            ],
            [
                'venue_name' => 'Le Bon Ton',
                'venue_area' => 'Collingwood',
                'contact_name' => 'Functions Coordinator',
                'contact_phone' => '(03) 9416 4341',
                'email' => 'bookings@lebonton.com.au',
                'website' => 'www.lebonton.com.au',
                'room_hire' => 'Yes, please speak with the functions coordinator',
                'minimum_spend' => 'Yes, please speak with the functions coordinator',
                'rooms' => [
                    [
                        'room_name' => 'Main Function Space',
                        'description' => 'Atmospheric New Orleans-inspired space with high ceilings and vintage charm.',
                        'image_url' => $nextImage(),
                        'capacity_min' => 30,
                        'capacity_max' => 120,
                    ],
                ],
            ],
            [
                'venue_name' => 'The Brunswick Mess Hall',
                'venue_area' => 'Brunswick',
                'contact_name' => 'Functions Coordinator',
                'contact_phone' => '(03) 9388 0297',
                'email' => 'info@thebrunswickmesshall.com.au',
                'website' => 'www.thebrunswickmesshall.com.au',
                'room_hire' => '$0',
                'minimum_spend' => '$1,500',
                'rooms' => [
                    [
                        'room_name' => 'The Hall',
                        'description' => 'Spacious industrial-style venue with exposed brick and great natural light.',
                        'image_url' => $nextImage(),
                        'capacity_min' => 40,
                        'capacity_max' => 150,
                    ],
                ],
            ],
        ];
    }

    private function getMediaImageUrls(): \Illuminate\Support\Collection
    {
        $media = Media::orderByDesc('created_at')
            ->take(20)
            ->get();

        return $media->map(fn (Media $m) => url('/media/' . $m->file_path));
    }
}
