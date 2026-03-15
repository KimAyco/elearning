<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectPriceSeeder extends Seeder
{
    /**
     * Seed subject prices for subjects without a configured price.
     */
    public function run(): void
    {
        Subject::query()
            ->where(function ($query): void {
                $query->whereNull('price_per_subject')
                    ->orWhere('price_per_subject', '<=', 0);
            })
            ->get()
            ->each(function (Subject $subject): void {
                $units = max((float) ($subject->units ?? 0), 1.0);
                $subject->price_per_subject = round($units * 1500, 2);
                $subject->save();
            });
    }
}

