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
        $this->upsertAppointment([
            'user_id' => $user->id,
            'pet_id' => $pets->first()->id,
            'appointment_date' => Carbon::now()->addDays(3)->setTime(10, 0),
            'type' => 'consultation',
            'service' => 'general_consultation',
            'status' => 'confirmed',
            'description' => 'Revisión general de rutina. Últimamente ha estado un poco decaído.',
            'notes' => 'Traer cartilla de vacunación',
        ]);

        // Cita 2: Vacunación confirmada (futura)
        if ($pets->count() > 1) {
            $this->upsertAppointment([
                'user_id' => $user->id,
                'pet_id' => $pets->get(1)->id,
                'appointment_date' => Carbon::now()->addDays(5)->setTime(14, 30),
                'type' => 'vaccination',
                'service' => 'annual_vaccination',
                'status' => 'confirmed',
                'description' => 'Refuerzo de vacuna antirrábica anual',
                'notes' => null,
            ]);
        }

        // Cita 3: Consulta confirmada (futura)
        $this->upsertAppointment([
            'user_id' => $user->id,
            'pet_id' => $pets->first()->id,
            'appointment_date' => Carbon::now()->addDays(7)->setTime(16, 0),
            'type' => 'consultation',
            'service' => 'general_consultation',
            'status' => 'confirmed',
            'description' => 'Control post-operatorio. Revisión de suturas.',
            'notes' => 'Cita confirmada por teléfono',
        ]);

        // Cita 4: Cirugía completada (pasada)
        if ($pets->count() > 1) {
            $this->upsertAppointment([
                'user_id' => $user->id,
                'pet_id' => $pets->get(1)->id,
                'appointment_date' => Carbon::now()->subDays(15)->setTime(9, 0),
                'type' => 'surgery',
                'service' => 'sterilization',
                'status' => 'completed',
                'description' => 'Esterilización programada',
                'notes' => 'Operación exitosa. Reposo de 10 días.',
            ]);
        }

        // Cita 5: Estética completada (pasada)
        $this->upsertAppointment([
            'user_id' => $user->id,
            'pet_id' => $pets->first()->id,
            'appointment_date' => Carbon::now()->subDays(7)->setTime(11, 0),
            'type' => 'grooming',
            'service' => 'haircut',
            'status' => 'completed',
            'description' => 'Corte de pelo y baño completo',
            'notes' => 'Se realizó limpieza de oídos y corte de uñas',
        ]);

        // Cita 6: Consulta cancelada
        if ($pets->count() > 2) {
            $this->upsertAppointment([
                'user_id' => $user->id,
                'pet_id' => $pets->get(2)->id,
                'appointment_date' => Carbon::now()->subDays(2)->setTime(15, 0),
                'type' => 'consultation',
                'service' => 'general_consultation',
                'status' => 'cancelled',
                'description' => 'Revisión por pérdida de apetito',
                'notes' => 'Cancelada por el cliente - mascota mejoró',
            ]);
        }

        // Cita 7: Vacunación completada
        $this->upsertAppointment([
            'user_id' => $user->id,
            'pet_id' => $pets->first()->id,
            'appointment_date' => Carbon::now()->subDays(30)->setTime(10, 30),
            'type' => 'vaccination',
            'service' => 'antirabies_vaccination',
            'status' => 'completed',
            'description' => 'Vacuna polivalente (séxtuple)',
            'notes' => 'Próxima dosis en 1 año',
        ]);

        // Cita 8: Consulta confirmada (mañana)
        if ($pets->count() > 1) {
            $this->upsertAppointment([
                'user_id' => $user->id,
                'pet_id' => $pets->get(1)->id,
                'appointment_date' => Carbon::tomorrow()->setTime(12, 0),
                'type' => 'consultation',
                'service' => 'general_consultation',
                'status' => 'confirmed',
                'description' => 'Consulta de emergencia - no come hace 2 días',
                'notes' => 'URGENTE',
            ]);
        }

        $this->command->info('✅ Citas creadas exitosamente');
    }

    private function upsertAppointment(array $data): void
    {
        Appointment::updateOrCreate(
            [
                'pet_id' => $data['pet_id'],
                'appointment_date' => $data['appointment_date'],
            ],
            $data
        );
    }
}
