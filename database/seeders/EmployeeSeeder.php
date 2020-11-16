<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createAdminEmployee();
        $this->createEmployee();
    }

    private function createAdminEmployee()
    {
        $employee = \App\Models\Employee::factory()->changeFields(
            'محسن', 'مرادی', '+989162582113',
            999555333,'مدیر IT', true)->create();

        $this->command->info('کاربر ادمین اصلی سازمان ایجاد شد.');
    }

    private function createEmployee()
    {
        $user = \App\Models\Employee::factory()->changeFields(
            'محسن', 'بابازاده', '+989162582331',
            777333999, 'معاون')->make();
        $user->save();
        $this->command->info('کاربر سازمان ایجاد شد.');
    }
}
