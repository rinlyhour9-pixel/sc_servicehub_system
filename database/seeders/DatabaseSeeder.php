<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DailyOperation;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Technician;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Steav Preysor',
            'phone' => '012 000 001',
            'password' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $tech1 = Technician::updateOrCreate([
            'email' => 'jordan@example.com',
        ], [
            'name' => 'សុខ សុវណ្ណ',
            'phone' => '០១២ ៣៤៥ ៦៧៨',
            'password' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $tech2 = Technician::updateOrCreate([
            'email' => 'sam@example.com',
        ], [
            'name' => 'ទូច សារី',
            'phone' => '០១៧ ៨៩០ ១០១',
            'password' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $additionalTechs = [
            ['name' => 'សេង ពេញ', 'email' => 'seng.pieng@example.com', 'phone' => '012 111 222'],
            ['name' => 'ម៉ាលី សុភា', 'email' => 'maly.sopha@example.com', 'phone' => '017 222 333'],
            ['name' => 'ចន ប៊ុនធឿន', 'email' => 'chan.buntheun@example.com', 'phone' => '016 333 444'],
            ['name' => 'ពៅ វណ្ណា', 'email' => 'pov.vanna@example.com', 'phone' => '098 444 555'],
            ['name' => 'រ័ត្ន មុន្នី', 'email' => 'rath.monny@example.com', 'phone' => '070 555 666'],
            ['name' => 'សុជាតិ លាង', 'email' => 'sochate.leang@example.com', 'phone' => '075 666 777'],
        ];

        $techs = [$tech1, $tech2];
        foreach ($additionalTechs as $t) {
            $techs[] = Technician::updateOrCreate([
                'email' => $t['email'],
            ], [
                'name' => $t['name'],
                'phone' => $t['phone'],
                'is_active' => true,
            ]);
        }

        $categories = collect([
            ['name' => 'Electrician', 'description' => 'Electrical repairs and installations'],
            ['name' => 'Plumber', 'description' => 'Pipes, leaks, and plumbing fixes'],
            ['name' => 'AC Repair', 'description' => 'Air conditioning and cooling issues'],
            ['name' => 'TV Repair', 'description' => 'Television and display repairs'],
            ['name' => 'Painter', 'description' => 'Interior and exterior painting'],
            ['name' => 'Home Cleaning', 'description' => 'General home cleaning services'],
            ['name' => 'Cooking Range', 'description' => 'Cooking range and stove repair'],
            ['name' => 'Washing Machine', 'description' => 'Laundry appliance service and repair'],
            ['name' => 'Fridge Repair', 'description' => 'Refrigerator and freezer repair'],
        ])->map(fn ($c) => ServiceCategory::updateOrCreate(['name' => $c['name']], $c));

        $customers = collect([
            ['name' => 'ហាងសាខា', 'email' => 'contact@acmeretail.test', 'phone' => '012 100 123', 'address' => 'Street 12, Phnom Penh', 'password' => Hash::make('123456')],
            ['name' => 'លីណា សុវណ្ណ', 'email' => 'lena.ortiz@test.com', 'phone' => '017 444 999', 'address' => 'Street 48, Phnom Penh'],
            ['name' => 'ហាងអាហារ', 'email' => 'info@downtowndiner.test', 'phone' => '016 100 333', 'address' => 'Street 9, Phnom Penh'],
            ['name' => 'ម៉ាលី ធាវី', 'email' => 'marcus.webb@test.com', 'phone' => '098 555 100', 'address' => 'Street 210, Phnom Penh'],
        ])->map(fn ($c) => Customer::updateOrCreate(['email' => $c['email']], $c));

        $samples = [
            ['title' => 'Kitchen sink leaking under cabinet', 'status' => ServiceRequest::STATUS_PENDING, 'priority' => ServiceRequest::PRIORITY_HIGH, 'category' => 0, 'customer' => 0],
            ['title' => 'No power to upstairs outlets', 'status' => ServiceRequest::STATUS_ASSIGNED, 'priority' => ServiceRequest::PRIORITY_URGENT, 'category' => 1, 'customer' => 1, 'tech' => $tech1],
            ['title' => 'AC unit not cooling', 'status' => ServiceRequest::STATUS_IN_PROGRESS, 'priority' => ServiceRequest::PRIORITY_HIGH, 'category' => 2, 'customer' => 2, 'tech' => $tech2],
            ['title' => 'Walk-in freezer compressor noise', 'status' => ServiceRequest::STATUS_COMPLETED, 'priority' => ServiceRequest::PRIORITY_MEDIUM, 'category' => 3, 'customer' => 2, 'tech' => $tech1],
            ['title' => 'Routine building inspection', 'status' => ServiceRequest::STATUS_PENDING, 'priority' => ServiceRequest::PRIORITY_LOW, 'category' => 4, 'customer' => 3],
        ];

        foreach ($samples as $s) {
            $sr = ServiceRequest::create([
                'customer_id' => $customers[$s['customer']]->id,
                'service_category_id' => $categories[$s['category']]->id,
                'assigned_technician_id' => $s['tech']->id ?? null,
                'created_by' => $admin->id,
                'title' => $s['title'],
                'description' => 'Sample seeded ticket for demonstration purposes.',
                'service_address' => $customers[$s['customer']]->address,
                'status' => $s['status'],
                'priority' => $s['priority'],
                'scheduled_at' => now()->addDays(rand(0, 5)),
                'completed_at' => $s['status'] === ServiceRequest::STATUS_COMPLETED ? now()->subDay() : null,
            ]);

            $sr->notes()->create([
                'user_id' => $admin->id,
                'body' => 'Ticket created from initial customer call.',
            ]);
        }

        // Assign 2-3 random service categories to each technician
        $categoryIds = $categories->pluck('id');
        foreach ($techs as $tech) {
            $pick = $categoryIds->random(rand(2,3))->toArray();
            $tech->serviceCategories()->sync($pick);
        }

        // Seed a week of daily cash operations with income/expense history.
        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = now()->subDays($daysAgo);
            $isToday = $daysAgo === 0;

            $day = DailyOperation::updateOrCreate([
                'business_date' => $date->toDateString(),
            ], [
                'opened_by' => $admin->id,
                'opened_at' => $date->copy()->setTime(8, 0),
                'opening_cash' => 100,
                'opening_notes' => 'Opening float for the day.',
            ]);

            $entries = [
                ['type' => 'sale', 'payment_method' => 'cash', 'amount' => rand(40, 120), 'description' => 'Walk-in service payment'],
                ['type' => 'sale', 'payment_method' => 'bank_qr', 'amount' => rand(30, 90), 'description' => 'Invoice payment via QR'],
                ['type' => 'receivable', 'payment_method' => 'cash', 'amount' => rand(20, 60), 'description' => 'Partial payment collected'],
                ['type' => 'delivery_payment', 'payment_method' => 'cash', 'amount' => rand(10, 30), 'description' => 'Delivery fee collected'],
                ['type' => 'expense', 'payment_method' => 'cash', 'amount' => rand(10, 40), 'description' => 'Fuel and supplies'],
            ];

            foreach ($entries as $entry) {
                $transaction = WalletTransaction::create($entry + [
                    'daily_operation_id' => $day->id,
                    'created_by' => $admin->id,
                ]);
                $transaction->forceFill([
                    'created_at' => $date->copy()->setTime(rand(9, 18), rand(0, 59)),
                ])->save();
            }

            if (! $isToday) {
                $income = $day->walletTransactions()->whereIn('type', ['sale', 'receivable', 'delivery_payment'])->sum('amount');
                $expense = $day->walletTransactions()->where('type', 'expense')->sum('amount');
                $expectedCash = $day->opening_cash + $income - $expense;

                $day->update([
                    'closed_by' => $admin->id,
                    'closed_at' => $date->copy()->setTime(20, 0),
                    'closing_notes' => 'Auto-closed seeded day.',
                    'expected_cash' => $expectedCash,
                    'actual_cash' => $expectedCash,
                    'cash_difference' => 0,
                ]);
            }
        }
    }
}
