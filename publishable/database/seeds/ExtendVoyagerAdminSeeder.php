<?php

use Illuminate\Database\Seeder;

class ExtendVoyagerAdminSeeder extends Seeder
{
    private function seedDataTypes () {
        $items = [
            array (
                'description' => '权限管理',
                'display_name_plural' => '权限',
                'display_name_singular' => '权限',
                'generate_permissions' => 1,
                'icon' => '',
                'model_name' => 'TCG\\Voyager\\Models\\Permission',
                'name' => 'permissions',
                'server_side' => 1,
                'slug' => 'permissions',
            ),
            array (
                'description' => '',
                'display_name_plural' => '策略',
                'display_name_singular' => '策略',
                'generate_permissions' => 1,
                'icon' => '',
                'model_name' => 'TCG\\Voyager\\Models\\Policy',
                'name' => 'policies',
                'server_side' => 1,
                'slug' => 'policies',
            )
        ];
        foreach ($items as $key => $item) {
            \DB::table('data_types')->where('name', $item['name'])->delete();
        }
        \DB::table('data_types')->insert($items);
    }

    /**
     * 填充表格项
     */
    public function seedDataRows ()
    {
        $items = [
            'permissions' => [
                array (
                    'add' => 0,
                    'browse' => 0,
                    'data_type_id' => 15,
                    'delete' => 0,
                    'details' => '',
                    'display_name' => 'Id',
                    'edit' => 0,
                    'field' => 'id',
                    'read' => 0,
                    'required' => 1,
                    'type' => 'PRI',
                ),

                array (
                    'add' => 1,
                    'browse' => 1,
                    'data_type_id' => 15,
                    'delete' => 1,
                    'details' => '',
                    'display_name' => '键值',
                    'edit' => 1,
                    'field' => 'key',
                    'read' => 1,
                    'required' => 1,
                    'type' => 'text',
                ),

                array (
                    'add' => 1,
                    'browse' => 1,
                    'data_type_id' => 15,
                    'delete' => 1,
                    'details' => '',
                    'display_name' => '表格名称',
                    'edit' => 1,
                    'field' => 'table_name',
                    'read' => 1,
                    'required' => 1,
                    'type' => 'text',
                ),

                array (
                    'add' => 1,
                    'browse' => 1,
                    'data_type_id' => 15,
                    'delete' => 1,
                    'details' => '',
                    'display_name' => '描述',
                    'edit' => 1,
                    'field' => 'desc',
                    'read' => 1,
                    'required' => 0,
                    'type' => 'text',
                ),

                array (
                    'add' => 1,
                    'browse' => 1,
                    'data_type_id' => 15,
                    'delete' => 1,
                    'details' => '',
                    'display_name' => '顺序',
                    'edit' => 1,
                    'field' => 'order',
                    'read' => 1,
                    'required' => 1,
                    'type' => 'text',
                ),

                array (
                    'add' => 0,
                    'browse' => 1,
                    'data_type_id' => 15,
                    'delete' => 0,
                    'details' => '',
                    'display_name' => '创建时间',
                    'edit' => 0,
                    'field' => 'created_at',
                    'read' => 1,
                    'required' => 0,
                    'type' => 'timestamp',
                ),

                array (
                    'add' => 0,
                    'browse' => 0,
                    'data_type_id' => 15,
                    'delete' => 0,
                    'details' => '',
                    'display_name' => '修改时间',
                    'edit' => 0,
                    'field' => 'updated_at',
                    'read' => 1,
                    'required' => 0,
                    'type' => 'timestamp',
                ),
            ],
            'policies' => [
                array (
                    'add' => 0,
                    'browse' => 0,
                    'data_type_id' => 16,
                    'delete' => 0,
                    'details' => '',
                    'display_name' => 'Id',
                    'edit' => 0,
                    'field' => 'id',
                    'read' => 0,
                    'required' => 1,
                    'type' => 'PRI',
                ),

                array (
                    'add' => 1,
                    'browse' => 1,
                    'data_type_id' => 16,
                    'delete' => 1,
                    'details' => '',
                    'display_name' => 'Key',
                    'edit' => 1,
                    'field' => 'key',
                    'read' => 1,
                    'required' => 1,
                    'type' => 'text',
                ),

                array (
                    'add' => 1,
                    'browse' => 1,
                    'data_type_id' => 16,
                    'delete' => 1,
                    'details' => '{"default":"0","options":{"0":"永久累计","1":"按月累计","2":"按日累计","3":"按小时累计","4":"数值","5":"周期值","6":"按年累计"}}',
                    'display_name' => '类型',
                    'edit' => 1,
                    'field' => 'type',
                    'read' => 1,
                    'required' => 1,
                    'type' => 'radio_btn',
                ),

                array (
                    'add' => 0,
                    'browse' => 1,
                    'data_type_id' => 16,
                    'delete' => 0,
                    'details' => '',
                    'display_name' => '创建时间',
                    'edit' => 0,
                    'field' => 'created_at',
                    'read' => 1,
                    'required' => 0,
                    'type' => 'timestamp',
                ),

                array (
                    'add' => 0,
                    'browse' => 0,
                    'data_type_id' => 16,
                    'delete' => 1,
                    'details' => '',
                    'display_name' => '更新时间',
                    'edit' => 0,
                    'field' => 'updated_at',
                    'read' => 0,
                    'required' => 0,
                    'type' => 'timestamp',
                ),
            ]
        ];
        foreach ($items as $key => $rows) {
            $dataTypeId = \DB::table('data_types')->where('name', $key)->value('id');
            foreach ($rows as $key => $row) {
                $rows[$key]['data_type_id'] = $dataTypeId;
            }
            \DB::table('data_rows')->insert($rows);
        }
    }


    /**
     * 填充权限
     */
    public function seedPermissions ()
    {
        $items = [
            'permissions' => [
                array (
                    'desc' => '浏览权限',
                    'key' => 'browse_permissions',
                    'order' => 0,
                    'table_name' => 'permissions',
                    'type' => 0,
                ),

                array (
                    'desc' => '读取权限',
                    'key' => 'read_permissions',
                    'order' => 0,
                    'table_name' => 'permissions',
                    'type' => 0,
                ),

                array (
                    'desc' => '写入权限',
                    'key' => 'edit_permissions',
                    'order' => 0,
                    'table_name' => 'permissions',
                    'type' => 0,
                ),

                array (
                    'desc' => '新增权限',
                    'key' => 'add_permissions',
                    'order' => 0,
                    'table_name' => 'permissions',
                    'type' => 0,
                ),

                array (
                    'desc' => '删除权限',
                    'key' => 'delete_permissions',
                    'order' => 0,
                    'table_name' => 'permissions',
                    'type' => 0,
                ),
            ],
			'policies' => [
				array (
					'desc' => NULL,
					'key' => 'browse_policies',
					'order' => 0,
					'table_name' => 'policies',
					'type' => 0,
				),

				array (
					'desc' => NULL,
					'key' => 'read_policies',
					'order' => 0,
					'table_name' => 'policies',
					'type' => 0,
				),

				array (
					'desc' => NULL,
					'key' => 'edit_policies',
					'order' => 0,
					'table_name' => 'policies',
					'type' => 0,
				),

				array (
					'desc' => NULL,
					'key' => 'add_policies',
					'order' => 0,
					'table_name' => 'policies',
					'type' => 0,
				),

				array (
					'desc' => NULL,
					'key' => 'delete_policies',
					'order' => 0,
					'table_name' => 'policies',
					'type' => 0,
				),
			]
        ];
        $role = \DB::table('roles')->where('name', 'admin')->first();
        foreach ($items as $key => $rows) {
            \DB::table('permissions')->where('table_name', $key)->delete();
            \DB::table('permissions')->insert($rows);
            $rows = \DB::table('permissions')->where('table_name', $key)->get();
            foreach ($rows as $key => $row) {
                \DB::table('permission_role')->insert(['role_id' => $role->id, 'permission_id' => $row->id]);
            }
        }
    }

    /**
     * 填充菜单项
     */
    public function seedMenus ()
    {
        $items = [ 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-bulb',
                'menu_id' => 1,
                'order' => 5,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => 'voyager.permissions.index',
                'target' => '_self',
                'title' => '权限管理',
                'url' => '',
            ),
            
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-key',
                'menu_id' => 1,
                'order' => 5,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => 'voyager.policies.index',
                'target' => '_self',
                'title' => '策略管理',
                'url' => '',
            ),
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-gift',
                'menu_id' => 1,
                'order' => 5,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => 'voyager.permission_map',
                'target' => '_self',
                'title' => '权限视图',
                'url' => '',
            )
        ];
        foreach ($items as $key => $row) {
            \DB::table('menu_items')->where('route', $row['route'])->delete();
        }
        \DB::table('menu_items')->insert($items);
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedDataTypes();
        $this->seedDataRows();
        $this->seedPermissions();
        $this->seedMenus();
    }
}
