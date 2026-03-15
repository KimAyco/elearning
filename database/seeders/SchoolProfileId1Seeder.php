<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolProfile;
use Illuminate\Database\Seeder;

class SchoolProfileId1Seeder extends Seeder
{
    /**
     * Seed rich public-page content for school id 1.
     */
    public function run(): void
    {
        $school = School::query()->find(1);

        if (! $school) {
            return;
        }

        // Optional: refine the short description / location line
        if (! $school->short_description) {
            $school->short_description = 'Panabo City, Davao del Norte • Public higher-education institution';
            $school->save();
        }

        SchoolProfile::query()->updateOrCreate(
            ['school_id' => $school->id],
            [
                'intro' => 'Davao del Norte State College is a student-centered public institution in Panabo City, advancing sustainable development, marine and environmental sciences, and community-responsive programs for Davao del Norte and beyond.',
                'tag_primary' => 'Public higher-education institution',
                'tag_neutral' => 'Coastal & community-focused campus',
                'tag_accent' => 'Scholarships & extension programs',
                'fact1_label' => 'Established',
                'fact1_value' => '1995',
                'fact1_caption' => 'Decades of service to Davao del Norte and nearby provinces',
                'fact2_label' => 'Approx. students',
                'fact2_value' => '8,000+',
                'fact2_caption' => 'Undergraduate and graduate learners across disciplines',
                'fact3_label' => 'Programs offered',
                'fact3_value' => '60+',
                'fact3_caption' => 'From fisheries and marine sciences to education, IT, and management',
                'campus_title' => 'Campus life & student support',
                'campus_bullet1' => 'Modern classrooms, laboratories, and learning spaces overlooking Davao Gulf.',
                'campus_bullet2' => 'Active student organizations, cultural groups, and sports teams that build leadership and community.',
                'campus_bullet3' => 'Scholarships, grants, and financial aid for deserving and low-income students.',
                'campus_bullet4' => 'Career, OJT, and extension programs connecting students with LGUs, industries, and partner communities.',
            ],
        );
    }
}

