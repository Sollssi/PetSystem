<?php

namespace App\Enums;

enum PetStatus: string
{
    case Available = 'Available';
    case InProcess = 'In Process';
    case Adopted = 'Adopted';
    case MedicalTreatment = 'Medical Treatment';
}
