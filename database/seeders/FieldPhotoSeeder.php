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

            $photoPath = match ($slug) {
                'badminton' => 'images/courts/badminton.jpg',
                'tenis'     => 'images/courts/tennis.jpg',
                'basket'    => 'images/courts/basketball.jpg',
                default     => 'images/courts/futsal.jpg',
            };

            FieldPhoto::updateOrCreate(
                [
                    'field_id'   => $field->id,
                    'is_primary' => 1,
                ],
                [
                    'foto_url'   => $photoPath,
                ]
            );
        }
    }
}
