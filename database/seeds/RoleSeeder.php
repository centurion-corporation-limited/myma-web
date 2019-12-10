<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Kodeine\Acl\Models\Eloquent\Role::create(['name' => 'Admin', 'slug' => 'admin']);
        \Kodeine\Acl\Models\Eloquent\Role::create(['name' => 'Influencer', 'slug' => 'influencer']);
        \Kodeine\Acl\Models\Eloquent\Role::create(['name' => 'Brand Manager', 'slug' => 'brand_manager']);
    }
}
