# DEPRECATED!!!
<h1 style="color:red;">Laravel官方出了Nova管理后台以后，经过测试，在设计上远远优于Voyager。并且Voyager中长期得不到解决的问题，在Nova那边已经解决了。因此，改为使用Nova作为管理后台，本包不再维护。</h1>

# 改造说明

`Voyager`是个很优秀的项目，然而却有一些问题限制了它的使用：

- 仍然是`jQuery`+前后端混合那套，前端功能很难做得复杂
- `BREAD`控制器不支持搜索、批量操作等功能
- `CMS`方面，编辑器功能过弱，而在`SEO`等相关设置上又过于繁琐
- 提示语言没有使用多语言机制
- `BREAD`开启`Server-Side`时，不支持排序
- 存在许多很初级的BUG：比如`multiple images`类型，只能增加不能删除，查看详情时也不能正常显示等等

等待官方修复太慢，同时按照目前发展趋势来看，与自己的期望还是有一些距离，于是自己做整改。解决上述问题。

具体修复问题：

- `Voyager::settings`模块增加`Boolean Type`，增加`voyager::settings <key>/ voyager::settings <name> <key> <type> -s`命令获取和配置参数
- dropdown类型：如果使用了外键，比如users表的`role_id`指向Role,则User Model必须存在`public function role_id()`,但是一般我们都使用的是`public function role()`，通过在`details`的`relationship`下面增加`method: "role"`即可覆盖默认值。
- Number类型：增加"step: 0.01" 参数，以支持小数点的编辑。
- Textarea类型：支持JSON格式的处理，当数据库字段类型为JSON，并且在Laravel中设置`protected $casts=['content' => 'json']`这样的处理时，Voyager会编辑时会报错。通过在选项中增加`{"json": true}`，即可支持JSON数据的直接编辑。
- 前端支持vue，并通过webpack打包管理
- 生成权限、策略菜单
<p align="center"><a href="https://the-control-group.github.io/voyager/" target="_blank"><img width="400" src="https://s3.amazonaws.com/thecontrolgroup/voyager.png"></a></p>

# **V**oyager - The Missing Laravel Admin
官方文档: https://the-control-group.github.io/voyager/

常用操作表: https://voyager-cheatsheet.ulties.com/

<hr>

Laravel Admin & BREAD System (Browse, Read, Edit, Add, & Delete), made for Laravel 5.3.

After creating your new Laravel application you can include the Voyager package with the following command: 

```bash
composer require tcg/voyager
```

Next make sure to create a new database and add your database credentials to your .env file:

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Add the Voyager service provider to the `config/app.php` file in the `providers` array:

```php
'providers' => [
    // Laravel Framework Service Providers...
    //...
    
    // Package Service Providers
    TCG\Voyager\VoyagerServiceProvider::class,
    // ...
    
    // Application Service Providers
    // ...
],
```

Lastly, we can install voyager. You can do this either with or without dummy data.
The dummy data will include 1 admin account (if no users already exists), 1 demo page, 4 demo posts, 2 categories and 7 settings.

To install Voyager without dummy simply run

```bash
php artisan voyager:install
```

If you prefer installing it with dummy run

```bash
php artisan voyager:install --with-dummy
```

And we're all good to go! 

Start up a local development server with `php artisan serve` And, visit [http://localhost:8000/admin](http://localhost:8000/admin).

If you did go ahead with the dummy data, a user should have been created for you with the following login credentials:

>**email:** `admin@admin.com`   
>**password:** `password`

NOTE: Please note that a dummy user is **only** created if there are no current users in your database.

If you did not go with the dummy user, you may wish to assign admin priveleges to an existing user.
This can easily be done by running this command:

```bash
php artisan voyager:admin your@email.com
```

If you did not install the dummy data and you wish to create a new admin user you can pass the `--create` flag, like so:

```bash
php artisan voyager:admin your@email.com --create
```

And you will be prompted for the users name and password.

# 生成扩展菜单
默认安装的后台没有权限相关的菜单，可通过执行如下命令生成更多的菜单。
比如角色、权限、权限视图、策略

```bash
php artisan vendor:publish --provider='TCG\Voyager\VoyagerServiceProvider' --force
composer dump-autoload
php artisan db:seed --class=ExtendVoyagerAdminSeeder
```
