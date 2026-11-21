<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\FieldPhoto;
use Illuminate\Database\Seeder;

class FieldPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $fields = Field::with('sport')->get();

        foreach ($fields as $field) {
            $slug = strtolower($field->sport?->slug ?? 'futsal');

            $photos = match ($slug) {
                'badminton' => [
                    ['url' => 'images/courts/badminton.jpg', 'is_primary' => 1],
                    ['url' => 'images/fields/arena-sport-center.jpg', 'is_primary' => 0],
                    ['url' => 'images/fields/green-court-arena.jpg', 'is_primary' => 0],
                    ['url' => 'images/courts/badminton.jpg', 'is_primary' => 0],
                ],
                'tenis' => [
                    ['url' => 'images/courts/tennis.jpg', 'is_primary' => 1],
                    ['url' => 'images/fields/sunrise-tennis-club.jpg', 'is_primary' => 0],
                    ['url' => 'images/fields/arena-sport-center.jpg', 'is_primary' => 0],
                    ['url' => 'images/courts/tennis.jpg', 'is_primary' => 0],
                ],
                'basket' => [
                    ['url' => 'images/courts/basketball.jpg', 'is_primary' => 1],
                    ['url' => 'images/fields/bali-basket-hub.jpg', 'is_primary' => 0],
                    ['url' => 'images/fields/arena-sport-center.jpg', 'is_primary' => 0],
                    ['url' => 'images/courts/basketball.jpg', 'is_primary' => 0],
                ],
                default => [
                    ['url' => 'images/courts/futsal.jpg', 'is_primary' => 1],
                    ['url' => 'images/fields/green-court-arena.jpg', 'is_primary' => 0],
                    ['url' => 'images/fields/arena-sport-center.jpg', 'is_primary' => 0],
                    ['url' => 'images/courts/futsal.jpg', 'is_primary' => 0],
                ],
            };

            // Hapus foto lama untuk field ini agar tidak duplikat
            FieldPhoto::where('field_id', $field->id)->delete();

            foreach ($photos as $p) {
                FieldPhoto::create([
                    'field_id'   => $field->id,
                    'foto_url'   => $p['url'],
                    'is_primary' => $p['is_primary'],
                ]);
            }
        }
    }
}

