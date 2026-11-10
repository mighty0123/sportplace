<?php

namespace Tests\Feature\Mitra;

use App\Models\User;
use App\Notifications\ListingModerationStatusUpdated;
use App\Notifications\MitraVerificationStatusUpdated;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function test_notification_structure_and_helper_logic(): void
    {
        $user = new User(['name' => 'Mitra Sport', 'email' => 'mitra@sportplace.com']);

        $notif = new MitraVerificationStatusUpdated('approved');
        $channels = $notif->via($user);
        $data = $notif->toArray($user);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
        $this->assertEquals('mitra_verification', $data['type']);
        $this->assertEquals('approved', $data['status']);
    }

    public function test_listing_moderation_notification_structure(): void
    {
        $user = new User(['name' => 'Mitra Sport', 'email' => 'mitra@sportplace.com']);

        $notif = new ListingModerationStatusUpdated('lapangan', 'Futsal 1', 'aktif');
        $channels = $notif->via($user);
        $data = $notif->toArray($user);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
        $this->assertEquals('listing_moderation', $data['type']);
        $this->assertEquals('lapangan', $data['jenis']);
        $this->assertEquals('aktif', $data['status']);
    }
}
