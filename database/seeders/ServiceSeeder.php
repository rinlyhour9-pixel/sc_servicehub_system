<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        ServiceCategory::query()->orderBy('name')->each(function (ServiceCategory $category): void {
            Service::updateOrCreate(
                ['name' => $category->name],
                [
                    'description' => $category->description,
                    'duration_minutes' => 60,
                    'base_price' => null,
                    'is_active' => true,
                ],
            );
        });
    }
}
