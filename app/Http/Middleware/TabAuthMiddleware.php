<?php

declare(strict_types=1);

namespace App\Http\Middleware;

/**
 * Alias semántico de IsolateTabSession para el aislamiento de sesión por pestaña (`tab_token`).
 */
class TabAuthMiddleware extends IsolateTabSession {}
