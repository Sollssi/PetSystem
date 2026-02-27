<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VaccinationRecord;
use App\Models\User;
use Carbon\Carbon;

class VaccinationRecordSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar cualquier usuario con rol 'user'
        $user = User::role('user')->first();

        if (!$user) {
            $this->command->warn('No se encontró ningún usuario con rol "user".');
            return;
        }

        $this->command->info('Creando registros de vacunación para: ' . $user->email);

        $pets = $user->pets;

        if ($pets->isEmpty()) {
            $this->command->warn('El usuario no tiene mascotas.');
            return;
        }

        $this->command->info('Creando registros de vacunación...');

        $firstPet = $pets->first();

        // Vacunas para la primera mascota
        VaccinationRecord::create([
            'pet_id' => $firstPet->id,
            'vaccine_name' => 'Rabia',
            'application_date' => Carbon::now()->subMonths(11),
            'next_due_date' => Carbon::now()->addDays(5), // Próxima dentro de 7 días (debe aparecer en dashboard)
            'veterinarian' => 'Dr. Carlos Méndez',
            'notes' => 'Vacuna antirrábica obligatoria. Sin reacciones adversas.',
        ]);

        VaccinationRecord::create([
            'pet_id' => $firstPet->id,
            'vaccine_name' => 'Polivalente (Séxtuple)',
            'application_date' => Carbon::now()->subMonths(13),
            'next_due_date' => Carbon::now()->subMonths(1), // VENCIDA (hace 1 mes)
            'veterinarian' => 'Dra. María González',
            'notes' => 'Protege contra: Moquillo, Hepatitis, Parvovirus, Parainfluenza, Leptospirosis, Coronavirus',
        ]);

        VaccinationRecord::create([
            'pet_id' => $firstPet->id,
            'vaccine_name' => 'Bordetella',
            'application_date' => Carbon::now()->subMonths(6),
            'next_due_date' => Carbon::now()->addMonths(6), // Próxima en 6 meses (al día)
            'veterinarian' => 'Dr. Carlos Méndez',
            'notes' => 'Vacuna contra tos de las perreras. Recomendada por asistir a guardería.',
        ]);

        // Vacunas para segunda mascota (si existe)
        if ($pets->count() > 1) {
            $secondPet = $pets->get(1);

            VaccinationRecord::create([
                'pet_id' => $secondPet->id,
                'vaccine_name' => 'Triple Felina',
                'application_date' => Carbon::now()->subMonths(8),
                'next_due_date' => Carbon::now()->addMonths(4), // Próxima en 4 meses
                'veterinarian' => 'Dra. Ana Rodríguez',
                'notes' => 'Protege contra Rinotraqueitis, Calicivirus y Panleucopenia felina',
            ]);

            VaccinationRecord::create([
                'pet_id' => $secondPet->id,
                'vaccine_name' => 'Rabia',
                'application_date' => Carbon::now()->subMonths(10),
                'next_due_date' => Carbon::now()->addMonths(2), // Próxima en 2 meses
                'veterinarian' => 'Dra. Ana Rodríguez',
                'notes' => 'Vacuna antirrábica obligatoria',
            ]);

            VaccinationRecord::create([
                'pet_id' => $secondPet->id,
                'vaccine_name' => 'Leucemia Felina',
                'application_date' => Carbon::now()->subMonths(14),
                'next_due_date' => Carbon::now()->subMonths(2), // VENCIDA (hace 2 meses)
                'veterinarian' => 'Dr. Carlos Méndez',
                'notes' => 'Importante para gatos que salen al exterior',
            ]);
        }

        // Vacunas para tercera mascota (si existe)
        if ($pets->count() > 2) {
            $thirdPet = $pets->get(2);

            VaccinationRecord::create([
                'pet_id' => $thirdPet->id,
                'vaccine_name' => 'Parvovirus',
                'application_date' => Carbon::now()->subMonths(2),
                'next_due_date' => Carbon::now()->addMonths(10), // Al día por mucho tiempo
                'veterinarian' => 'Dra. María González',
                'notes' => 'Primera dosis de cachorro. Requiere refuerzos.',
            ]);

            VaccinationRecord::create([
                'pet_id' => $thirdPet->id,
                'vaccine_name' => 'Moquillo',
                'application_date' => Carbon::now()->subMonths(2),
                'next_due_date' => Carbon::now()->addMonths(10),
                'veterinarian' => 'Dra. María González',
                'notes' => 'Primera dosis de cachorro',
            ]);

            VaccinationRecord::create([
                'pet_id' => $thirdPet->id,
                'vaccine_name' => 'Polivalente (Séxtuple)',
                'application_date' => Carbon::now()->subWeeks(2),
                'next_due_date' => Carbon::now()->addMonths(11)->addWeeks(2),
                'veterinarian' => 'Dr. Carlos Méndez',
                'notes' => 'Segunda dosis de refuerzo. Cachorro en plan de vacunación completo.',
            ]);
        }

        $this->command->info('✅ Registros de vacunación creados exitosamente');
        $this->command->info('   - Vacunas al día: varias');
        $this->command->info('   - Vacunas próximas a vencer: varias');
        $this->command->info('   - Vacunas vencidas: 2 (requieren atención)');
    }
}
