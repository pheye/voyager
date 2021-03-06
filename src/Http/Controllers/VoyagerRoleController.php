<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\Policy;
use TCG\Voyager\Models\Permission;
use Artisan;
use Log;

class VoyagerRoleController extends VoyagerBreadController
{
    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        Voyager::canOrFail('edit_roles');

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        $data->permissions()->sync($request->input('permissions', []));

        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => "Successfully Updated {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }

    // POST BRE(A)D
    public function store(Request $request)
    {
        Voyager::canOrFail('add_roles');

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $data = new $dataType->model_name();
        $this->insertUpdateData($request, $slug, $dataType->addRows, $data);

        $data->permissions()->sync($request->input('permissions', []));

        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => "Successfully Added New {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }

    /**
     * 显示权限和策略平面视图
     * 权限和策略正常情况不应该被删除
     * @return Response
     */
    public function showPermissionMap()
    {
        Voyager::canOrFail('edit_roles');

        $roles = Role::where('id', '>', 2)->with('policies')->get();
        $groupedPermissions = Permission::all()->groupBy('table_name');
        $groupedPolicies = Policy::all()->groupBy('key');
        foreach ($roles as $role) {
            $role->groupedPolicies = $role->generateGroupedPolicies();
        }
        return view('voyager::permission_map', ['roles' => $roles, 'groupedPermissions' => $groupedPermissions, 'groupedPolicies' => $groupedPolicies, 'types' => Policy::TYPE_DESC]);
    }

    /**
     * 保存权限和策略平面视图
     * @return Response
     */
    public function storePermissionMap(Request $request)
    {
        Voyager::canOrFail('edit_roles');

        /* $groupedPermissions = Permission::all()->groupBy('table_name'); */
        $groupedPolicies = Policy::all()->groupBy('key');

        $roles = Role::where('id', '>', 2)->with('policies')->get();
        // 权限与策略分开操作
        foreach ($roles as $role) {
            if (!$request->has($role->name))
                continue;
            // 角色与权限之间的关系
            $oldPermissions = $role->permissions->groupBy('key');
            $newPermissions = new Collection($request[$role->name]['permission']);

            // 不要重建permission_role，这样对数据库操作太频繁
            // new有,old没有，添加
            // new没有,old有，删除
            // new有,old有，不变
            foreach ($newPermissions as $key => $id) {
                if (!$oldPermissions->has($key))
                    $role->permissions()->attach($id);
            }
            foreach ($oldPermissions as $key => $permission) {
                if (!$newPermissions->has($key)) {
                    $role->permissions()->detach($permission[0]->id);
                }
            }
            $role->save();
        }

        foreach ($roles as $role) {
            if (!$request->has($role->name))
                continue;
            $oldPolicies = new Collection($role->generateGroupedPolicies());
            $newPolicies = new Collection($request[$role->name]['policy']);

            // 不要重建policy_role，这样对数据库操作太频繁
            // new有,old没有，添加
            // new没有,old有，删除
            // new有,old有，但是数值不一致，更新
            // new有,old有，并且数值一致，忽略
            foreach ($newPolicies as $key => $policy) {
                if (!$oldPolicies->has($key)) {
                    $role->policies()->attach($groupedPolicies[$key][0]->id, ['value' => $policy['value']]);
                    continue;
                }
                if ($oldPolicies[$key][1] != $policy['value']) {
                    $role->policies()->updateExistingPivot($groupedPolicies[$key][0]->id, ['value' => $policy['value']]);
                }
            }
            foreach ($oldPolicies as $key => $policy) {
                if (!$newPolicies->has($key)) {
                    $role->policies()->detach($groupedPolicies[$key][0]->id);
                }
            }
            $role->save();
        }

        foreach ($roles as $role) {
            $role->policies = $role->policies()->get();
            $role->generateCache();
        }
        return redirect()->back();
    }

    public function generatePermissionCache()
    {
        Voyager::canOrFail('edit_roles');
        Artisan::call('bba:check-usage', [
            '--all' => true,
            '--fix' => true,
        ]);
        Log::info("`bba:check-usage --all --fix` command is executed in admin board");
        /* Log::info(Artisan::output()); */
        return redirect()->back();
    }

}
