<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use App\Models\TicketSubCategory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=CategorySeeder
     * @return void
     */
    public function run()
    {
        TicketCategory::query()->truncate();
        TicketSubCategory::query()->truncate();

        $cat1 = TicketCategory::create([
            'title' => 'Complaint',
            'created_at' => Carbon::now(),
        ]);

        TicketSubCategory::create(
            [
                'title' => 'Product',
                'ticket_category_id' => $cat1->id,
                'tags' => ['Philiqs', 'Blender', 'Refrigerator', 'Oven']
            ]
        );

        TicketSubCategory::create(
            [
                'title' => 'Service',
                'ticket_category_id' => $cat1->id,
                'tags' => ['Customer Care', 'Technical', 'Financial', 'Product Service']
            ]
        );

        TicketSubCategory::create(
            [
                'title' => 'Sales',
                'ticket_category_id' => $cat1->id,
                'tags' => ['Pre Sales', 'After Sale']
            ]
        );

        $cat2 = TicketCategory::create([
            'title' => 'Inquiry',
            'created_at' => Carbon::now(),
        ]);


        TicketSubCategory::create(
            [
                'title' => 'Product',
                'ticket_category_id' => $cat2->id,
                'tags' => ['Philiqs', 'Blender', 'Refrigerator', 'Oven']
            ]
        );

        TicketSubCategory::create(
            [
                'title' => 'Service',
                'ticket_category_id' => $cat2->id,
                'tags' => ['Customer Care', 'Technical', 'Financial', 'Product Service']
            ]
        );

        TicketSubCategory::create(
            [
                'title' => 'Sales',
                'ticket_category_id' => $cat2->id,
                'tags' => ['Pre Sales', 'After Sale']
            ]
        ); 

        


        $cat3 = TicketCategory::create([
            'title' => 'Order',
            'created_at' => Carbon::now(),
        ]);

        TicketSubCategory::create(
            [
                'title' => 'Delivery',
                'ticket_category_id' => $cat3->id,
                'tags' => ['Cash','Visa','Master','Amex']
            ]
        );

        TicketSubCategory::create(
            [
                'title' => 'Outlet Pickup',
                'ticket_category_id' => $cat3->id,
                'tags' => ['Cash','Visa','Master','Amex']
            ]
        );
    }
}
