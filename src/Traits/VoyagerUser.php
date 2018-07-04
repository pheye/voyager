<?php

namespace TCG\Voyager\Traits;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\Policy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache; 
use TCG\Voyager\Models\Permission;
use Carbon\Carbon;
use Log;

/**
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 */
trait VoyagerUser
{
    public function role()
    {
        return $this->belongsTo(Voyager::modelClass('Role'));
    }

    /**
     * Check if User has a Role(s) associated.
     *
     * @param string|array $name The role to check.
     *
     * @return bool
     */
    public function hasRole($name)
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        return in_array($this->role->name, (is_array($name) ? $name : [$name]));
    }

    public function setRole($name)
    {
        $role = Voyager::model('Role')->where('name', '=', $name)->first();

        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    public function hasPermission($name)
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        return in_array($name, $this->role->permissions->pluck('key')->toArray());
    }

    public function hasPermissionOrFail($name)
    {
        if (!$this->hasPermission($name)) {
            throw new UnauthorizedHttpException(null);
        }

        return true;
    }

    public function hasPermissionOrAbort($name, $statusCode = 403)
    {
        if (!$this->hasPermission($name)) {
            return abort($statusCode);
        }

        return true;
    }

    public function policies()               
    {                                        
        // 需要注意，如果对应的Policy被删除，则关联关系也会被自动删除！！！               
        return $this->belongsToMany(Policy::class)->withPivot('value');                   
    } 

    /**                                      
     * User自身的Policies的缓存键            
     */                                      
    private function cacheKey()              
    {                                        
        return 'user.policies.' . $this->id; 
    }  

    /**                                      
     * User自身的Policies的缓存键，按key组织 
     * 才能够在实际系统中高效使用            
     */                                      
    private function groupedCacheKey()       
    {                                        
        return $this->cacheKey() . '.grouped';                                            
    }                                        

    /**                                      
     * 从缓存中读取User Policy               
     * @return  Policy                       
     */                                      
    public function getCachedPolicies()      
    {                                        
        return Cache::get($this->cacheKey(), new Collection([]));                         
    }                                        

    /**                                      
     * 从缓存中读取分组的用户策略，按如下方式组织                                         
     * [key => [type, value]]                
     */                                      
    public function groupedPolicies()        
    {                                        
        return Cache::get($this->groupedCacheKey(), new Collection([]));                  
    }

    /**                                      
     * 生成用户Policy缓存                    
     * $this->cacheKey的缓存其实可以删除了，当前由于有引用暂时保留，后续可以考虑优化掉。  
     */                                      
    public function setCachePolicies()       
    {                                        
        if ($this->policies->count() > 0) {  
            Cache::forever($this->cacheKey(), $this->policies);
            $grouped = [];
            foreach($this->policies as $key => $policy) {
                $grouped[$policy->key] =  [$policy->type, $policy->pivot->value];//user会以该结果作参考写入数据库，故使用数组节省空间
            }
            Cache::forever($this->groupedCacheKey(), $grouped);
        }
        return $this;
    }

    public function getUsageAttribute($value)
    {
        // 用户权限的设计
        if (is_null($value)) {
            return [];
        }
        /* $items = $this->role->groupedPolicies(); */
        $value = json_decode($value, true);
        return $value;
    }

    public function setUsageAttribute($value)
    {
        $this->attributes['usage'] = json_encode($value);
    }

    /**                                      
     * 初始化usage                           
     * @param App\Role $role 角色            
     * @param boolean $clear 默认情况下，初始化策略如果发现有累计值，就保留。该参数设置为将统一清为0                                                                                 
     */                                      
    public function initUsageByRole(Role $role, $clear = false)                           
    {                                        
        try {                                
            $oldUsage = json_decode($this->attributes['usage'], true);                    
        } catch(\Exception $e) {             
            $oldUsage = null;                
        }                                    
        $items = $role->groupedPolicies();   

            /* Log::debug("key:" . $key); */ 
        foreach($items as $key=>$item) {     
            if (!$clear && isset($oldUsage[$key])) {                                      
                $items[$key] = $oldUsage[$key];                                           
                $items[$key][0] = $item[0];  
                $items[$key][1] = $item[1];  
            } else {                         
                $items[$key][2] = 0;         
            }                                
            /* Log::debug("key:" . $key); */
        }
        $this->usage = $items;
        return $items;
    }


    /**                                      
     * 根据自身的Policy去初始化Usage，如果Role中有相同的usage，将覆盖Role的usage          
     */                                      
    public function initUsageByUser($clear = false)                                       
    {                                        
        $policies = $this->getCachedPolicies();                                           

        $usage = $this->usage;
        foreach ($policies as $policy) {
            if (!$policy)
                continue;
            $item = $usage[$policy->key];
            if (!$item)
                $item = [0, 0, 0];
            $item[0] = $policy->type;
            $item[1] = $policy->pivot->value;
            if ($clear)
                $item[2] = 0;
            $usage[$policy->key] = $item;
        }
        $this->usage = $usage;
        return $usage;
    }

    /**                                      
     * 重新初始化usage                       
     * @remark 每次Role或者User的usage有变化时都需要重新初始化                            
     * @warning 假设用户刚好准备更新usage, 而这里在重新初始化，初始化先完成，然后用户的usage又被更新，把初始化的值给覆盖了。由于不会影响到已经统计的值，目前可以先采用对该用户做一次>修复的操作解决。                             
     */                                      
    public function reInitUsage($clear = false)                                           
    {                                        
        $oldUsage = $this->usage;            
        $usage = $this->initUsageByRole($this->role, $clear);                             
        $usage = $this->initUsageByUser($clear);                                          
        if ($usage != $oldUsage)             
            $this->save();                   
    }                              
    /**
     * 获取指定key的策略使用情况             
     * @warning 这是最重要的一个接口，所有修改都必须经过测试                              
     */    
    public function getUsage($key)           
    {                                        
        // 没有初始化则根据角色初始化        
        // 既然usage已经在种子填充阶段完成初始化，因此正常情况不应该走到此分支            
        // 如果走到说明存在用户与角色存在不一定，应该对用户做一次修复                     
        if (!array_key_exists($key, $this->usage)) {                                      
            // 在此处添加Log是错误行为，由于是小概率事件，暂时可以这么做                  
            Log::warning("{$this->email} should be fixed: {$key} , {$this->role->name}"); 
            $items = $this->role->groupedPolicies();                                      
            if (!array_key_exists($key, $items)) {                                        
                return null;                 
            }                                
            $item = $items[$key];            
            $item[2] = 0;                    
            $item[4] = true;                 
        } else {                             
            //测试的时候发现，新加入的权限和策略是直接进入到这个else分支，导致$item[2]没有初始化为0，会导致出现                                                                      
            //下标2未出现的错误。            
            $item = $this->usage[$key];      
            // user的usage的类型和默认值总是实时从用户缓存或者角色缓存中读取              
            // 当角色和用户的策略改变时，只要保证缓存是最新的，用户就能正确读取           
            // 到策略值，不用重新修改用户的usage;                                         
            // 在更新过程（重新生成角色缓存）时，用户读取不到策略，于是仍然从usage        
            // 自身读取，它保证了在更新过程中(100ms以内）的瞬间，用户的访问仍然能够       
            // 正常运作。                    
            // TODO:当删除策略时，用户其实仍然能访问到策略，目前对删除策略的处理其实是    
            // 通过权限控制的（没有对应权限也就没有访问策略的必要了），所以残留的策略值   
            // 并不会有影响，后续考虑是否优化掉无关值以免引起误会                         
            $grouped = $this->groupedPolicies();                                          
            if (!isset($grouped[$key])) {    
                $grouped = $this->role->groupedPolicies();                                
            }                                
            if (isset($grouped[$key])) {     
                $item[0] = $grouped[$key][0];
                $item[1] = $grouped[$key][1];
            }                                
            if(count($item) < 3) {
                $item[2] = 0;
            }
        }
        return $item;
    }

    /**
     * 添加用户独有的Policy
     *
     * @param string $key 策略
     * @param string $value 默认值
     * @remark 添加策略不检查权限，但是在使用上必须检查权限
     */
    public function setPolicy($key, $value)
    {
        $policy = Policy::where('key', $key)->first();
        if (!($policy instanceof Policy)) {
            return false;
        }
        $this->policies()->detach($policy->id);
        $this->policies()->attach($policy->id, ['value' =>  $value]);

        $policies = $this->policies()->get();
        $grouped = [];
        foreach($policies as $key => $policy) {
            $grouped[$policy->key] =  [$policy->type, $policy->pivot->value];//user会以该结果作参考写入数据库，故使用数组节省空间
        }
        Cache::forever($this->cacheKey(), $policies);//直接使用$this->policies在单元测试环境中会发现没更新过来
        Cache::forever($this->groupedCacheKey(), $grouped);//直接使用$this->policies在单元测试环境中会发现没更新过来
        $this->reInitUsage();
        return true;
    }

    /**                                      
     * 删除用户独有的Policy                  
     */                                      
    public function unsetPolicy($key)        
    {                                        
        $policy = Policy::where('key', $key)->first();                                    
        if (!($policy instanceof Policy)) {  
            return false;                    
        }                                    
        $this->policies()->detach($policy->id);                                           
        $policies = $this->policies;
        $grouped = [];
        foreach($policies as $key => $policy) {
            $grouped[$policy->key] =  [$policy->type, $policy->pivot->value];//user会以该结果作参考写入数据库，故使用数组节省空间
        }
        Cache::forever($this->cacheKey(), $policies);
        Cache::forever($this->groupedCacheKey(), $grouped);
        $this->reInitUsage();
        return true;
    }

    /**                                      
     * 获取用户独有的Policy                  
     */                                      
    public function getPolicy($key)          
    {                                        
        foreach ($this->getCachedPolicies() as $policy) {                                 
            if ($policy->key == $key)        
                return $policy;              
        }                                    
        return false;                        
    }                                        


    public function updateUsage($key, $used, $extra)                                      
    {                                        
        $usage = $this->usage;               
        if (!isset($usage[$key]))            
            return false;                    
        $usage[$key][2] = $used;             
        if (!is_null($extra))                
            $usage[$key][3] = $extra;        
        $this->usage = $usage;               
        $this->save();                       
        return true;                         
    }

    public function incUsage($key, $extra)
    {
        $usage = $this->usage;
        if (!isset($usage))
            return false;
        $this->updateUsage($key, count($usage[$key]) > 2 ? $usage[$key][2] + 1 : 1, $extra);
        return true;
    }

    public function getCache($key)           
    {                                        
        $newkey = "{$this->id}:" . $key;     
        return Cache::get($newkey);          
    }                                        

    public function setCache($key, $val)     
    {
        $newkey = "{$this->id}:" . $key;
        Cache::put($newkey, $val, 1440);
    }

    public function can($key, $arguments = [])                                            
    {                                        
        //先检查角色                         
        $count = $this->role->permissions()->where('key', $key)->count();                 
        if ($count > 0)                      
            return true;                     
        //再检查User自身                     
        return $this->userCan($key);         
    }                                        

    public function userCan($key)            
    {                                        
        /* $arr = $this->permissions()->wherePivot('expired', null)->orWherePivot('expired', '>', Carbon::now())->get()->pluck('key')->toArray(); */                                 

        /* if (in_array($key, $arr)) */      
        /*     return true; */               

        $item = $this->permissions()->where('key', $key)->first();//()->wherePivot('expired', null)->orWherePivot('expired', '>', Carbon::now())->where('key', $key)->count();       
        if (!($item instanceof Permission)) {
            return false;                    
        }                                    
        /* echo(json_encode($item)); */      
        if (!($item->pivot['expired'] == null || (new Carbon($item->pivot['expired']))->gt(Carbon::now()))) {                                                                        
            return false;
        }
        return true;
    }
    /**                                      
     * 用户权限与角色权限合并                
     * @warning 需要注意的是以属性形式表示只会加载一次，如果要在一次执行中做修改，那内部应该改用$this->permissions->all()                                                            
     */                                      
    public function getMergedPermissions()   
    {                                        
        $rolePermissions = $this->role->permissions;
        $userPermissions = $this->permissions()->wherePivot('expired', null)->orWherePivot('expired', '>', Carbon::now())->get();                                                    
        $merged = $rolePermissions->merge($userPermissions);                              
        return $merged;
    }                                        

    /**                                      
     * 仅返回user本身的权限，不包含role的权限
     */                                      
    public function permissions()            
    {                                        
        return $this->belongsToMany(Permission::class)->withPivot(['expired']);           
    }                                        

    /**                                      
     * 动态添加权限                          
     */                                      
    public function addPermission($key, $expired = null)                                  
    {                                        
        $permission = Permission::where('key', $key)->first();                            
        if (!($permission instanceof Permission))                                         
            return false;                    
        if ($this->permissions()->wherePivot('permission_id', $permission->id)->count() > 0) {                                                                                       
            $this->permissions()->updateExistingPivot($permission->id, ['expired' => $expired]);                                                                                     
            return true;                     
        }                                    
        $this->permissions()->attach($permission->id, ['expired' => $expired]);           
        return true;                         
    }   

    /**
     * 动态删除权限                          
     */                                      
    public function delPermission($key)      
    {                                        
        $permission = Permission::where('key', $key)->first();                            
        if (!($permission instanceof Permission))                                         
            return false;                    
        $this->permissions()->detach($permission->id);                                    
        return true;                         
    } 

    /**
     * 权限由两部分组成：
     * - 权限列表
     * - 策略列表
     * 角色的权限与策略都是在填充时生成的，所以只能在那个时间点检查，通过Role的checkUsage可检查。
     * 用户的权限与策略的检查：
     * - 权限来自角色，所以无需检查
     * - 检查用户专有的Policy缓存是否与数据库一致
     * - 将Usage与策略对比，有不一致的提示错误
     */
    public function checkUsage()
    {
        $role = $this->role;
        $rawUsage = new Collection(json_decode($this->attributes['usage'], true));
        if (!$role) {
            Log::warning("{$this->email} has no role, role id:{$this->role_id}");
            throw new \ErrorException("({$this->email}) has no valild:{$this->role_id})", 1000);
        }
        // 检查用户的策略是否与数据库的一致
        $cachedPolicies = $this->getCachedPolicies();
        if ($cachedPolicies->count() != $this->policies->count()) {
            throw new \ErrorException("User {$this->email} policy count not the same:" .  $cachedPolicies->count() . " , should be " . $this->policies->count());
        }
        for ($i = 0; $i < $this->policies->count(); ++$i) {
            $p1 = $cachedPolicies[$i];
            $p2 = $this->policies[$i];
            if ($p1->key != $p2->key
                || $p1->pivot->value != $p2->pivot->value) {
                throw new \ErrorException("User policy not the same: {$p1->key} {$p2->key}, {$p1->pivot->value} {$p2->pivot->value}");
            }
        }
        foreach ($role->groupedPolicies()  as $key => $policy) {
            // 如果有设置User Policy，则需要匹配的是User Policy
            $userPolicy = $this->getPolicy($key);
            if ($userPolicy)
                $policy[1] = $userPolicy->pivot->value;
            $usage = $this->usage[$key];
            if (!$usage || $usage[0] != $policy[0] || $usage[1] != $policy[1] || isset($usage[4])) {
                throw new \ErrorException("({$this->email})should be: $key-" . json_encode($policy) . ", but now is : " . ($usage ? json_encode($usage) : "no usage"), 1000);
            }
        }
        return true;
    }

    public function dumpUsage($print)        
    {
        foreach ($this->usage as $key => $value) {
            call_user_func($print, "$key:" . json_encode($this->getUsage($key)));
        }
    }

}
