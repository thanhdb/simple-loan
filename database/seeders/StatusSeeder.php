<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statusArr = $this->getStatuses();
        foreach ($statusArr as $status) {
            Status::updateOrCreate([
                'slug' => $status['slug'],
                'name' => $status['name']
            ]);
        }
    }

    /**
     * getStatuses
     *
     * @return array
     */
    private function getStatuses()
    {
        return [
            [
                'slug' => 'pending',
                'name' => 'Pending'
            ],
            [
                'slug' => 'approved',
                'name' => 'Approved'
            ],
            [
                'slug' => 'paid',
                'name' => 'Paid'
            ]
        ];
    }
}
