<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $adminRole =Role::findByName('Admin','api');
        $admin->assignRole($adminRole);

        $instructor = User::firstOrCreate(
            ['email' => 'instructor1@test.com'],
            [
                'name' => 'Instructor User',
                'password' => Hash::make('password'),
            ]
        );
        $instructorRole =Role::findByName('Instructor','api');
        $instructor->assignRole($instructorRole);

        $student = User::firstOrCreate(
            ['email' => 'student1@test.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password'),
            ]
        );
        $studentRole =Role::findByName('Student','api');
        $student->assignRole($studentRole);
    }
}
