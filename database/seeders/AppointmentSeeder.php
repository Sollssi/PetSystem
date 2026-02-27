<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar cualquier usuario con rol 'user'
        $user = User::role('user')->first();

        if (!$user) {
            $this->command->warn('No se encontró ningún usuario con rol "user".');
            return;
        }

        $this->command->info('Creando citas para: ' . $user->email);

        // Verificar que tenga mascotas
        $pets = $user->pets;

        if ($pets->isEmpty()) {
            $this->command->warn('El usuario no tiene mascotas. Crea mascotas primero con PetSeeder.');
            return;
        }

        $this->command->info('Creando citas de ejemplo...');

        // Cita 1: Consulta general pendiente (futura)
        Appointment::create([
            'user_id' => $user->id,
            'pet_id' => $pets->first()->id,
            'appointment_date' => Carbon::now()->addDays(3)->setTime(10, 0),
            'type' => 'consultation',
            'status' => 'pending',
            'description' => 'Revisión general de rutina. Últimamente ha estado un poco decaído.',
            'notes' => 'Traer cartilla de vacunación',
        ]);

        // Cita 2: Vacunación pendiente (futura)
        if ($pets->count() > 1) {
            Appointment::create([
                'user_id' => $user->id,
                'pet_id' => $pets->get(1)->id,
                'appointment_date' => Carbon::now()->addDays(5)->setTime(14, 30),
                'type' => 'vaccination',
                'status' => 'pending',
                'description' => 'Refuerzo de vacuna antirrábica anual',
                'notes' => null,
            ]);
        }

        // Cita 3: Consulta confirmada (futura)
        Appointment::create([
            'user_id' => $user->id,
            'pet_id' => $pets->first()->id,
            'appointment_date' => Carbon::now()->addDays(7)->setTime(16, 0),
            'type' => 'consultation',
            'status' => 'confirmed',
            'description' => 'Control post-operatorio. Revisión de suturas.',
            'notes' => 'Cita confirmada por teléfono',
        ]);

        // Cita 4: Cirugía completada (pasada)
        if ($pets->count() > 1) {
            Appointment::create([
                'user_id' => $user->id,
                'pet_id' => $pets->get(1)->id,
                'appointment_date' => Carbon::now()->subDays(15)->setTime(9, 0),
                'type' => 'surgery',
                'status' => 'completed',
                'description' => 'Esterilización programada',
                'notes' => 'Operación exitosa. Reposo de 10 días.',
            ]);
        }

        // Cita 5: Estética completada (pasada)
        Appointment::create([
            'user_id' => $user->id,
            'pet_id' => $pets->first()->id,
            'appointment_date' => Carbon::now()->subDays(7)->setTime(11, 0),
            'type' => 'grooming',
            'status' => 'completed',
            'description' => 'Corte de pelo y baño completo',
            'notes' => 'Se realizó limpieza de oídos y corte de uñas',
        ]);

        // Cita 6: Consulta cancelada
        if ($pets->count() > 2) {
            Appointment::create([
                'user_id' => $user->id,
                'pet_id' => $pets->get(2)->id,
                'appointment_date' => Carbon::now()->subDays(2)->setTime(15, 0),
                'type' => 'consultation',
                'status' => 'cancelled',
                'description' => 'Revisión por pérdida de apetito',
                'notes' => 'Cancelada por el cliente - mascota mejoró',
            ]);
        }

        // Cita 7: Vacunación completada
        Appointment::create([
            'user_id' => $user->id,
            'pet_id' => $pets->first()->id,
            'appointment_date' => Carbon::now()->subDays(30)->setTime(10, 30),
            'type' => 'vaccination',
            'status' => 'completed',
            'description' => 'Vacuna polivalente (séxtuple)',
            'notes' => 'Próxima dosis en 1 año',
        ]);

        // Cita 8: Consulta pendiente (mañana)
        if ($pets->count() > 1) {
            Appointment::create([
                'user_id' => $user->id,
                'pet_id' => $pets->get(1)->id,
                'appointment_date' => Carbon::tomorrow()->setTime(12, 0),
                'type' => 'consultation',
                'status' => 'pending',
                'description' => 'Consulta de emergencia - no come hace 2 días',
                'notes' => 'URGENTE',
            ]);
        }

        $this->command->info('✅ Citas creadas exitosamente');
    }
}
