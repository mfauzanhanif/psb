<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\FeeComponent;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================
        // 1. ACADEMIC YEAR
        // =====================================================
        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'is_active' => true,
        ]);

        // =====================================================
        // 2. INSTITUTIONS & FEE COMPONENTS
        // =====================================================
        $institutions = [
            [
                'name' => 'Pondok Pesantren Dar Al Tauhid',
                'type' => 'pondok',
                'fees' => [
                    'Pendaftaran Pondok' => 4633000,
                ],
            ],
            [
                'name' => 'Madrasah Dar Al Tauhid',
                'type' => 'madrasah',
                'fees' => [
                    'Pendaftaran Madrasah' => 290000,
                ],
            ],
            [
                'name' => 'SMP Plus Dar Al Tauhid',
                'type' => 'smp',
                'fees' => [
                    'Pendaftaran SMP' => 2295000,
                ],
            ],
            [
                'name' => 'MA Nusantara',
                'type' => 'ma',
                'fees' => [
                    'Pendaftaran MA' => 2530000,
                ],
            ],
            [
                'name' => 'MTsN 3 Cirebon',
                'type' => 'mts',
                'fees' => [],
            ],
        ];

        foreach ($institutions as $instData) {
            $fees = $instData['fees'];
            unset($instData['fees']);

            $institution = Institution::create($instData);

            foreach ($fees as $feeName => $amount) {
                FeeComponent::create([
                    'institution_id' => $institution->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => $feeName,
                    'amount' => $amount,
                ]);
            }
        }

        // Get institutions for user assignment
        $pondok = Institution::where('type', 'pondok')->first();
        $madrasah = Institution::where('type', 'madrasah')->first();
        $smp = Institution::where('type', 'smp')->first();
        $ma = Institution::where('type', 'ma')->first();

        // =====================================================
        // 3. ROLES & PERMISSIONS
        // =====================================================
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_all_students',
            'view_institution_students',
            'create_students',
            'edit_students',
            'delete_students',
            'verify_students',
            'accept_students',
            'view_all_transactions',
            'view_institution_transactions',
            'create_transactions',
            'edit_transactions',
            'manage_users',
            'manage_settings',
            'view_dashboard_stats',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Administrator (Super Admin)
        $roleAdmin = Role::create(['name' => 'Administrator']);
        $roleAdmin->givePermissionTo(Permission::all());

        // Petugas (Global Scope, Input/Edit Student)
        $rolePetugas = Role::create(['name' => 'Petugas']);
        $rolePetugas->givePermissionTo([
            'view_all_students',
            'create_students',
            'edit_students',
            'view_dashboard_stats',
        ]);

        // Bendahara Pondok (Global Scope for Finance)
        $roleBendaharaPondok = Role::create(['name' => 'Bendahara Pondok']);
        $roleBendaharaPondok->givePermissionTo([
            'view_all_transactions',
            'create_transactions',
            'edit_transactions',
            'view_all_students',
        ]);

        // Bendahara Unit (Limited Scope for Finance)
        $roleBendaharaUnit = Role::create(['name' => 'Bendahara Unit']);
        $roleBendaharaUnit->givePermissionTo([
            'view_institution_transactions',
            'create_transactions',
            'view_institution_students',
        ]);

        // Kepala (Read Only)
        $roleKepala = Role::create(['name' => 'Kepala']);
        $roleKepala->givePermissionTo([
            'view_institution_students',
            'view_institution_transactions',
            'view_dashboard_stats',
        ]);

        // =====================================================
        // 4. USERS
        // =====================================================

        // Administrator
        $admin = User::create([
            'name' => 'Fauzan Hanif',
            'email' => 'fauzanhanif2112@gmail.com',
            'position' => 'Kepala Pondok',
            'password' => Hash::make('F@uzan2112'),
            'institution_id' => null,
        ]);
        $admin->assignRole('Administrator');

        // Petugas
        $petugas = User::create([
            'name' => 'Nabil Maulidi',
            'email' => 'nabilmaulidi@psb.daraltauhid.com',
            'position' => 'Panitia PSB',
            'password' => Hash::make('password'),
            'institution_id' => null,
        ]);
        $petugas->assignRole('Petugas');

        // Bendahara Pondok
        $bendaharaPondok = User::create([
            'name' => 'Rohmah Saadah',
            'email' => 'bendahara.pondok@psb.com',
            'position' => 'Bendahara Pondok',
            'password' => Hash::make('password'),
            'institution_id' => $pondok?->id,
        ]);
        $bendaharaPondok->assignRole('Bendahara Pondok');

        // Bendahara Unit SMP
        $bendaharaSMP = User::create([
            'name' => 'Sofiyah',
            'email' => 'bendahara.smp@psb.com',
            'position' => 'Bendahara SMP',
            'password' => Hash::make('password'),
            'institution_id' => $smp?->id,
        ]);
        $bendaharaSMP->assignRole('Bendahara Unit');

        // Bendahara Unit MA
        $bendaharaMA = User::create([
            'name' => 'Fatimah Zahra',
            'email' => 'bendahara.ma@psb.com',
            'position' => 'Bendahara MA',
            'password' => Hash::make('password'),
            'institution_id' => $ma?->id,
        ]);
        $bendaharaMA->assignRole('Bendahara Unit');

        // Bendahara Unit Madrasah
        $bendaharaMadrasah = User::create([
            'name' => 'Khadijah',
            'email' => 'bendahara.madrasah@psb.com',
            'position' => 'Bendahara Madrasah',
            'password' => Hash::make('password'),
            'institution_id' => $madrasah?->id,
        ]);
        $bendaharaMadrasah->assignRole('Bendahara Unit');

        // Pengasuh Pondok
        $kepalaPondok = User::create([
            'name' => 'KH. Abdul Rahman',
            'email' => 'kepala.pondok@psb.com',
            'position' => 'Pengasuh Pondok',
            'password' => Hash::make('password'),
            'institution_id' => $pondok?->id,
        ]);
        $kepalaPondok->assignRole('Kepala');

        // Kepala SMP
        $kepalaSMP = User::create([
            'name' => 'Drs. Muhammad Saleh',
            'email' => 'kepala.smp@psb.com',
            'position' => 'Kepala SMP',
            'password' => Hash::make('password'),
            'institution_id' => $smp?->id,
        ]);
        $kepalaSMP->assignRole('Kepala');

        // Kepala MA
        $kepalaMA = User::create([
            'name' => 'Drs. Ahmad Dahlan, M.Pd',
            'email' => 'kepala.ma@psb.com',
            'position' => 'Kepala MA',
            'password' => Hash::make('password'),
            'institution_id' => $ma?->id,
        ]);
        $kepalaMA->assignRole('Kepala');

        // Kepala Madrasah
        $kepalaMadrasah = User::create([
            'name' => 'Musthofa, S.H.',
            'email' => 'kepala.madrasah@psb.com',
            'position' => 'Kepala Madrasah',
            'password' => Hash::make('password'),
            'institution_id' => $madrasah?->id,
        ]);
        $kepalaMadrasah->assignRole('Kepala');
    }
}
