<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\JobCategory;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Resume;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed the root admin
        User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Seed Date to test with
        $jobData = json_decode(file_get_contents(database_path('data/job_data.json')), true);
        $jobApplication = json_decode(file_get_contents(database_path('data/job_applications.json')), true);

        // Create job categories
        foreach ($jobData['jobCategories'] as $category) {
            JobCategory::firstOrCreate([
                'name' => $category,
            ]);
        }

        // Create Companies
        foreach ($jobData['companies'] as $company) {
            // Create company owner
            $companyOwner = User::firstOrCreate([
                'email' => fake()->unique()->safeEmail(),
            ], [
                'name' => fake()->name(),
                'password' => Hash::make('12345678'),
                'role' => 'company-owner',
                'email_verified_at' => now(),
            ]);

            Company::firstOrCreate([
                'name' => $company['name'],
            ], [
                'address' => $company['address'],
                'industry' => $company['industry'],
                'website' => $company['website'],
                'ownerId' => $companyOwner->id,
            ]);
        }
            // Create job vacancies
            foreach ($jobData['jobVacancies'] as $job) {
                // Get the created company id
                $companyId = Company::where('name', $job['company'])->firstOrFail()->id;

                // Get the created job category id
                $jobCategoryId = JobCategory::where('name', $job['category'])->firstOrFail()->id;

                JobVacancy::firstOrCreate([
                    'title' => $job['title'],
                    'companyId' => $companyId,
                ],[
                    'description' => $job['description'],
                    'location' => $job['location'],
                    'type' => $job['type'],
                    'salary' => $job['salary'],
                    'jobCategoryId' => $jobCategoryId,
                ]);
            }

            // Create job Application
            foreach ($jobApplication['jobApplications'] as $application) {
                // Get random job vacancy 
                $jobVacancy = JobVacancy::inRandomOrder()->first();

                // Create applicant (job-seeker)
                $applicant = User::firstOrCreate([
                    'email' => fake()->unique()->safeEmail(),
                ], [
                    'name' => fake()->name(),
                    'password' => Hash::make('12345678'),
                    'role' => 'job-seeker',
                    'email_verified_at' => now(),
                ]);

                // Create resume
                $resume = Resume::create([
                    'userId' => $applicant->id,
                    'filename' => $application['resume']['filename'],
                    'fileUrl' => $application['resume']['fileUrl'],
                    'contactDetails' => $application['resume']['contactDetails'],
                    'summary' => $application['resume']['summary'],
                    'skills' => $application['resume']['skills'],
                    'experience' => $application['resume']['experience'],
                    'education' => $application['resume']['education'],
                ]);
                
                // Create job application
                JobApplication::create([
                    'jobVacancyId' => $jobVacancy->id,
                    'userId' => $applicant->id,
                    'resumeId' => $resume->id,
                    'status' => $application['status'],
                    'aiGeneratedScore' => $application['aiGeneratedScore'],
                    'aiGeneratedFeedback' => $application['aiGeneratedFeedback'],
                ]);
            }
        
    }
}



