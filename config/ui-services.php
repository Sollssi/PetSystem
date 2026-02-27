<?php

/**
 * UI Services Registry
 *
 * Lista de servicios que construyen interfaces de usuario.
 * Se usa para resolver qué servicio debe manejar eventos de componentes
 * basándose en el offset del ID del componente.
 *
 * Performance: Este archivo se carga una vez por worker PHP-FPM y se
 * cachea en memoria para lookups instantáneos (~0.001ms).
 */

return [];
