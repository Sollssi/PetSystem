<?php

namespace App\Enums;

enum AppoinmentStatus: string
{
    case PENDIENTE = 'Pendiente';
    case CONFIRMADO = 'Confirmado';
    case COMPLETADO = 'Completado';
    case CANCELADO = 'Cancelado';
}
