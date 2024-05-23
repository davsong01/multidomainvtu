<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Role;
use App\Models\Category;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->addMerchantIdToAllTables();
        $this->createPermissions();
        // Create new Settings here
        // $this->defaultSettings();
        // Create Annoucements
        
        // Banks for airtime to cash
    }

    public function createPermissions(){
        $menu = [
            'Dashboard',
            'Announcement',
            'Catalogue',
            'API Provider',
            'Categories',
            "Email Management",
            'Products',
            'Customers',
            'All Customers',
            'Active Customers',
            'Suspended Customers',
            'Blacklisted Customers',
            'Customer Levels',
            'User Management',
            'All Admins',
            'All Roles',
            'All Permissions',
            'Financials',
            'Product Purchase Log',
            'Wallet Funding Log',
            'Wallet Log',
            'Earnings Log',
            'Credit Customer',
            'Debit Customer',
            'Reserved Account Numbers',
            'My Profile',
            'Callback Analysis',
            'KYC Management',
            'Payment Gateway Settings',
            'General Settings',
            'Email Setup'
        ];

        $permissions = [
            'product.index',
            'product.show',
            'product.edit',
            'product.update',
            'product.destroy',
            'role.show',
            'role.index',
            'role.edit',
            'role.update',
            'role.destroy',
            'role.create',

            'category.show',
            'category.index',
            'category.edit',
            'category.update',
            'category.destroy',

            'billerlog.index',
            'billerlog.show',
            'billerlog.edit',
            'billerlog.update',
            'billerlog.destroy',

            'customer-blacklist.show',
            'customer-blacklist.edit',
            'customer-blacklist.update',
            'customer-blacklist.destroy',

            'announcement.index',
            'announcement.show',
            'announcement.edit',
            'announcement.update',
            'announcement.destroy',

            'api.show',
            'api.index',
            'api.edit',
            'api.update',
            'api.destroy',

            'customerlevel.show',
            'customerlevel.show',
            'customerlevel.edit',
            'customerlevel.update',
            'customerlevel.destroy',

            'duplicate.product',
            'api.balance',

            'black.list.status',
            'admin.trans',
            'admin.walletlog',
            'admin.walletfundinglog',
            'admin.earninglog',
            'admin.credit.customer',
            'admin.debit.customer',
            'admin.process.credit.debit',
            'admin.verifybiller',
            'admin.verify.post',
            'admin.kyc',
            'admin.reserved.accounts',
            'account.transactions',
            'callback.analysis',
            'reserved_account.delete',
            'admin.single.transaction.view',
            'admin.query.wallet',
            'admin.requery.transaction',
            'customers',
            'customers.active',
            'customers.suspended',
            'customers.edit',
            'customers.update',
            'variations.pull',
            'variations.update',
            'manual.variations.add',
            'variation.delete',
            'create.reserved.account',
            'admins',
            'newAdmin',
            'adminSave',
            'viewAdmin',
            'updateAdmin',
            'settings.edit',
            'settings.update',
            'transaction.verify',
            'paymentgateway.index',
            'emails.index',
            'emails.update',
            'emails.pending',
            'emails.resend',
            'emails.sweep',

            'permission.show',
            'permission.index',
            'permission.edit',
            'permission.update',
            'permission.destroy',
            'permission.create',
            'permission.store',
            'emails-send',
            'customerlevel.index',
            'role.store',
            'emails.destroy',
            'announcement.store',
            'email.setup',
            'email.setup.update',
            'paymentgateway.show',
            'paymentgateway.edit',
            'paymentgateway.update',
            'paymentgateway.destroy',
            'paymentgateway.store',
            'category.repull',
            'category.pull',
            'product.repull',
            'product.pull',
            'customerlevel.create',
            'admin.transaction.pin.reset',
            'admin.password.reset',
            'admin.airtime.2.cash.log'
        ];

        // RolePermission::truncate();

        foreach ($menu as $key => $value) {
            RolePermission::updateOrCreate(
                [
                    'name' => $value,
                    'route' => $value,
                    'type' => 'menu',
                ],
                [
                    'name' => $value,
                    'route' => $value,
                    'type' => 'menu',
                    'status' => 'active',
                ]
            );
        }

        foreach ($permissions as $key => $value) {
            RolePermission::updateOrCreate(
                [
                    'name' => $value,
                    'route' => $value,
                    'type' => 'link',
                ],
                [
                    'name' => $value,
                    'route' => $value,
                    'type' => 'link',
                    'status' => 'active',
                ]
            );
        }
    }

    // public function addMerchantIdToAllTables(){
    //     $tables = \DB::select('SHOW TABLES');
       
    //     foreach ($tables as $t) {
    //         // dd($t->Tables_in_multivtu);
    //         $name = $t->Tables_in_multivtu;
    //         if (!Schema::hasColumn($name, 'merchant_id')) {
    //             Schema::table($name, function (Blueprint $table) {
    //                 $table->integer('merchant_id')->before('created_at');
    //             });
    //         }
    //     }
        
    // }

    public function defaultSettings(){

    }
}
