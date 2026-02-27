<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pet;
use App\Models\User;
use App\Enums\PetStatus;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar cualquier usuario con rol 'user'
        $user = User::role('user')->first();

        if (!$user) {
            $this->command->warn('No se encontró ningún usuario con rol "user". Ejecuta UserSeeder primero.');
            return;
        }

        $this->command->info('Usando usuario: ' . $user->email);

        // Verificar si ya tiene mascotas
        if ($user->pets()->count() > 0) {
            $this->command->info('El usuario ya tiene mascotas registradas.');
            return;
        }

        $this->command->info('Creando mascotas de ejemplo...');

        // Mascota 1: Perro adulto
        Pet::create([
            'user_id' => $user->id,
            'name' => 'Max',
            'species' => 'Perro',
            'breed' => 'Labrador Retriever',
            'age' => 5,
            'description' => 'Labrador dorado de 5 años, muy activo y cariñoso. Cirugía de cadera en 2024 con recuperación completa. Peso: 32.5kg. Microchip: 982000123456789. Sin alergias conocidas.',
            'status' => PetStatus::Available->value,
        ]);

        // Mascota 2: Gato joven
        Pet::create([
            'user_id' => $user->id,
            'name' => 'Luna',
            'species' => 'Gato',
            'breed' => 'Mestizo (Europeo)',
            'age' => 2,
            'description' => 'Gata blanca con manchas grises de 2 años, hembra esterilizada. Peso: 4.2kg. Microchip: 982000987654321. Alergia a ciertos alimentos (pollo), necesita dieta especial.',
            'status' => PetStatus::Available->value,
        ]);

        // Mascota 3: Cachorro
        Pet::create([
            'user_id' => $user->id,
            'name' => 'Rocky',
            'species' => 'Perro',
            'breed' => 'Golden Retriever',
            'age' => 1,
            'description' => 'Cachorro Golden Retriever dorado claro de 1 año, macho muy juguetón. Peso: 18kg. Aún sin microchip. En plan de vacunación activo.',
            'status' => PetStatus::Available->value,
        ]);

        $this->command->info('✅ 3 mascotas creadas exitosamente:');
        $this->command->info('   - Max (Labrador, 5 años)');
        $this->command->info('   - Luna (Gato, 2 años)');
        $this->command->info('   - Rocky (Golden Retriever, 1 año)');
    }
}
