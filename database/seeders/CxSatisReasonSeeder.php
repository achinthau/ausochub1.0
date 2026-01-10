<?php

namespace Database\Seeders;

use App\Models\CxSatisReason;
use Illuminate\Database\Seeder;

class CxSatisReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reasons = [
            // Satisfaction Reasons
            ["reason" => "TIMELY COMPLETED JOB", "type" => "satisfaction"],
            ["reason" => "GREAT ATTITUDE/SERVICE", "type" => "satisfaction"],
            ["reason" => "INSTRUCTIONS FOR USE/ EXPLAIN THE CAUSE OF DEFECT", "type" => "satisfaction"],
            ["reason" => "COMPETENT ON WORK", "type" => "satisfaction"],
            ["reason" => "ATTENDED TO THE JOB WITHIN PROMISED TIME", "type" => "satisfaction"],
            ["reason" => "PROMOTERS(8-10)", "type" => "satisfaction"],

            // Dissatisfaction Reasons
            ["reason" => "FAKE REPAIR - INCORRECT CUSTOMER", "type" => "dissatisfaction"],
            ["reason" => "STOCK REPAIR", "type" => "dissatisfaction"],
            ["reason" => "PRODUCT COLLECTION/ DELIVERY PENDING", "type" => "dissatisfaction"],
            ["reason" => "CUSTOMER UNREACHABLE", "type" => "dissatisfaction"],
            ["reason" => "FAKE REPAIR – INCORRECT PRODUCT", "type" => "dissatisfaction"],
            ["reason" => "INVALID CONTACT NUMBER", "type" => "dissatisfaction"],
            ["reason" => "PENDING JOB", "type" => "dissatisfaction"],
            ["reason" => "OTHER", "type" => "dissatisfaction"],
            ["reason" => "CONTACT NUMBER BELONGS TO DEALER", "type" => "dissatisfaction"],

            // Cancel Reasons
            ["reason" => "PROBLEM STILL OCCURRING", "type" => "cancel"],
            ["reason" => "EXCHANGE CONFIRMED BUT NOT RECEIVED", "type" => "cancel"],
            ["reason" => "SERVICE NOT PROFESSIONAL", "type" => "cancel"],
            ["reason" => "EXCHANGE ISSUE - RETAIL SHOWROOM", "type" => "cancel"],
            ["reason" => "EXCHANGE ISSUE - SERVICE CENTRE", "type" => "cancel"],
            ["reason" => "REPAIR DELAYED", "type" => "cancel"],
            ["reason" => "INAPPROPRIATE ATTIRE", "type" => "cancel"],
            ["reason" => "INAPPROPRIATE LANGUAGE/ BEHAVIOUR", "type" => "cancel"],
            ["reason" => "LACK OF COMPETENCY ON WORK", "type" => "cancel"],
            ["reason" => "PROMOTERS(8-10)", "type" => "cancel"],
            ["reason" => "PASSIVES(7)", "type" => "cancel"],
            ["reason" => "DETRACTORS(1-6)", "type" => "cancel"],
            ["reason" => "PRODUCT PERFORMANCE LOWER THAN EXPECTED", "type" => "cancel"],
            ["reason" => "LOWER CUSTOMER SERVICE AT THE SHOWROOM", "type" => "cancel"],
        ];

        // Insert each reason into the database
        foreach ($reasons as $data) {
            CxSatisReason::create([
                'reasons' => $data['reason'],
                'type' => $data['type']
            ]);
        }
    }
}
