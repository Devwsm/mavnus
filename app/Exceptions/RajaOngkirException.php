<?php

namespace App\Exceptions;

use Exception;

/**
 * Dilempar kalau RajaOngkirService gagal - baik gagal konek (timeout, DNS,
 * API down) maupun API-nya balikin response gagal. Pesannya udah dalam
 * bahasa yang aman ditampilkan langsung ke pembeli (lihat ShippingController).
 */
class RajaOngkirException extends Exception
{
    //
}