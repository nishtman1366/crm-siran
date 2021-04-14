<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('display_name');       // For MySQL 8.0 use string('name', 125);
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('display_name');       // For MySQL 8.0 use string('name', 125);
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        $roles = [
            ['name' => 'superuser', 'display_name' => 'مدیرکل'],
            ['name' => 'admin', 'display_name' => 'مدیران'],
            ['name' => 'agent', 'display_name' => 'نمایندگان'],
            ['name' => 'marketer', 'display_name' => 'بازاریابان'],
            ['name' => 'office', 'display_name' => 'کارمندان'],
            ['name' => 'technical', 'display_name' => 'کارشناسان فنی'],
        ];
        foreach ($roles as $role) {
            $r = new Role();
            $r->name = $role['name'];
            $r->display_name = $role['display_name'];
            $r->save();
        }

        $permissions = [
            ['name' => 'create admin', 'display_name' => 'ساخت مدیر'],
            ['name' => 'view admin', 'display_name' => 'مشاهده مدیر'],
            ['name' => 'edit admin', 'display_name' => 'ویرایش مدیر'],
            ['name' => 'delete admin', 'display_name' => 'حذف مدیر'],

            ['name' => 'create agent', 'display_name' => 'ساخت نماینده'],
            ['name' => 'view agent', 'display_name' => 'مشاهده نماینده'],
            ['name' => 'edit agent', 'display_name' => 'ویریش نماینده'],
            ['name' => 'delete agent', 'display_name' => 'حذف نماینده'],

            ['name' => 'create marketer', 'display_name' => 'ساخت بازاریاب'],
            ['name' => 'view marketer', 'display_name' => 'مشاهده بازاریاب'],
            ['name' => 'edit marketer', 'display_name' => 'ویرایش بازاریاب'],
            ['name' => 'delete marketer', 'display_name' => 'حذف بازاریاب'],

            ['name' => 'create office', 'display_name' => 'ساخت کارمند'],
            ['name' => 'view office', 'display_name' => 'مشاهده کارمند'],
            ['name' => 'edit office', 'display_name' => 'ویرایش کارمند'],
            ['name' => 'delete office', 'display_name' => 'حذف کارمند'],

            ['name' => 'create technical', 'display_name' => 'ساخت کارشناس فنی'],
            ['name' => 'view technical', 'display_name' => 'مشاهده کارشناس فنی'],
            ['name' => 'edit technical', 'display_name' => 'ویرایش کارشناس فنی'],
            ['name' => 'delete technical', 'display_name' => 'حذف کارشناس فنی'],
        ];
        foreach ($permissions as $permission) {
            $p = new \Spatie\Permission\Models\Permission();
            $p->name = $permission['name'];
            $p->display_name = $permission['display_name'];
            $p->save();
        }

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('permission_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_permission_model_type_primary');
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('role_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_role_model_type_primary');
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
