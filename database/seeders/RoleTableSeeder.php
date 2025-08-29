<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\RoleService;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = array(
            [
                'name' => 'admin',
            ],
            [
                'name' => 'user',
            ]

        );
        $role_service = new RoleService;
        foreach ($params as $key => $value) {
            $role = $role_service->createRole($value);
            $permissions = Permission::where('name', $role->name);
            $role->givePermissionTo($permissions);
        }
    }
}
