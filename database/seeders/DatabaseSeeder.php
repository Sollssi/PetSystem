<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PetSeeder::class,              // Crear mascotas primero
            AppointmentSeeder::class,       // Luego citas
            VaccinationRecordSeeder::class, // Registros de vacunación
        ]);
    }
}
