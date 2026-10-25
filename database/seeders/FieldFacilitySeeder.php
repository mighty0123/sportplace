<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldFacilitySeeder extends Seeder
{
    public function run(): void
    {
        $fields = DB::table('fields')->get();
        $facilities = DB::table('master_facilities')->take(2)->get();

        if ($fields->isEmpty() || $facilities->isEmpty()) {
            return;
        }

        foreach ($fields as $field) {
            foreach ($facilities as $facility) {
                DB::table('field_facilities')->updateOrInsert(
                    [
                        'field_id' => $field->id,
                        'facilities_id' => $facility->id,
                    ],
                    []
                );
            }
        }
    }
}
