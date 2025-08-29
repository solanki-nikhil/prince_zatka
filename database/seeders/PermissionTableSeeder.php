<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\PermissionService;


class PermissionTableSeeder extends Seeder
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
        $permission_service = new PermissionService;
        foreach ($params as $key => $value) {
            $permission_service->createPermission($value);
        }
    }
}
