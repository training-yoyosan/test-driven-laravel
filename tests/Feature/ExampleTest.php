<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function user_can_view_a_concert_listing()
    {
        // Arrange
        // Create a concert
        $concert = Concert::create([
            'title' => 'Red Cord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250, // stored in cents
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.',
        ]);

        // Act
        // View the concert listing
        $this->visit('/concerts/' . $concert->id);

        // Assert
        // See the concert details
        $this->see('Red Cord')
            ->see('with Animosity and Lethargy')
            ->see('December 13, 2016')
            ->see('8:00pm')
            ->see('32.50')
            ->see('The Mosh Pit')
            ->see('123 Example Lane')
            ->see('Laraville, ON 17916')
            ->see('For tickets, call (555) 555-5555.');
    }
}
