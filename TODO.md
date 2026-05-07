# Task: Fix Spatie Permission RoleDoesNotExist error in seeding

## Steps:
- [ ] 1. Update DatabaseSeeder.php: Swap RoleSeeder before UserSeeder
- [ ] 2. Update UserSeeder.php: Change 'Admin' to 'admin' 
- [ ] 3. Test: Run `php artisan migrate:fresh --seed`
