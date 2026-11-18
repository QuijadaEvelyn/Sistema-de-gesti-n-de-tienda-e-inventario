<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña · ARCHIVUM</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0f0a17; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1f1b29;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #0f0a17; padding: 40px 10px;">
        <tr>
            <td align="center">
                <!-- Tarjeta Principal de Bóveda -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #fdfbf7; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.45); border: 1px solid rgba(139, 92, 246, 0.25);">
                    
                    <!-- Encabezado con Isotipo e Identidad Curatorial -->
                    <tr>
                        <td align="center" style="background: linear-gradient(180deg, #1d122f 0%, #130a21 100%); padding: 36px 30px; border-bottom: 2px solid #c5a358;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <!-- Monograma Isotipo Miniatura -->
                                    <td align="center" style="padding-bottom: 12px;">
                                        <div style="display: inline-block; width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(180deg, #8b5cf6 0%, #4c1d95 100%); border: 1.5px solid rgba(255,255,255,0.6); box-shadow: 0 6px 16px rgba(124, 58, 237, 0.5); text-align: center; line-height: 56px;">
                                            <span style="font-size: 26px; color: #ffffff;">&#9678;</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="margin: 0; font-family: Georgia, serif; font-size: 24px; font-weight: bold; letter-spacing: 0.22em; color: #ffffff; text-transform: uppercase;">
                                            ARCHIVUM
                                        </h1>
                                        <p style="margin: 6px 0 0 0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.2em; color: #ccbeff; font-family: monospace;">
                                            Bóveda Notarial &middot; Custodia Digital
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Cuerpo de la Comunicación Notarial -->
                    <tr>
                        <td style="padding: 36px 36px 24px 36px;">
                            <h2 style="margin: 0 0 14px 0; font-family: Georgia, serif; font-size: 20px; color: #1e1135; font-weight: bold;">
                                Solicitud de Restablecimiento de Credenciales
                            </h2>
                            <p style="margin: 0 0 16px 0; font-size: 14px; line-height: 1.6; color: #4a4458;">
                                Estimado/a <strong>{{ $usuario->nombre }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #4a4458;">
                                Hemos recibido una solicitud para renovar la contraseña de acceso a tu cuenta vinculada a <strong>{{ $usuario->email }}</strong> en la plataforma ARCHIVUM.
                            </p>

                            <!-- Bloque del Token de Verificación (Copiar y Pegar) -->
                            <div style="background-color: #f3eef8; border: 1.5px dashed #8b5cf6; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 28px;">
                                <span style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.15em; color: #6d28d9; margin-bottom: 8px;">
                                    Tu Código / Token Temporal de Verificación:
                                </span>
                                <div style="font-family: 'Courier New', Courier, monospace; font-size: 15px; font-weight: bold; color: #1e1135; background-color: #ffffff; padding: 12px 16px; border-radius: 8px; border: 1px solid #ddd6fe; word-break: break-all; user-select: all; letter-spacing: 1px;">
                                    {{ $token }}
                                </div>
                                <span style="display: block; font-size: 11px; color: #6b7280; margin-top: 8px;">
                                    Puedes copiar este código y pegarlo directamente en el formulario de ARCHIVUM.
                                </span>
                            </div>

                            <!-- Botón de Acción Directa -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; padding: 14px 32px; background: linear-gradient(180deg, #7c3aed 0%, #5b21b6 100%); color: #ffffff; text-decoration: none; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.15em; border-radius: 12px; box-shadow: 0 6px 18px rgba(109, 40, 217, 0.45); border: 1px solid rgba(255,255,255,0.3);">
                                            Restablecer Contraseña Directamente
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Advertencia de Caducidad y Uso Único -->
                            <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 14px 16px; border-radius: 6px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #92400e;">
                                    <strong>Aviso de Seguridad:</strong> Este código y enlace tienen una validez estricta de <strong>60 minutos</strong> y son de <strong>un solo uso</strong>. Tras actualizar tu clave o transcurrido este tiempo, quedarán automáticamente invalidados por el sistema.
                                </p>
                            </div>

                            <p style="margin: 0 0 10px 0; font-size: 12px; line-height: 1.6; color: #6b7280;">
                                Si no has realizado esta solicitud, por favor desestima este correo. Tus credenciales actuales permanecerán intactas y tu cuenta continuará debidamente protegida.
                            </p>
                        </td>
                    </tr>

                    <!-- Pie Notarial -->
                    <tr>
                        <td align="center" style="background-color: #f5f0eb; padding: 20px 30px; border-top: 1px solid #e9dfd4;">
                            <p style="margin: 0 0 6px 0; font-size: 11px; font-family: monospace; color: #78716c; text-transform: uppercase; letter-spacing: 0.1em;">
                                ARCHIVUM S.A. DE C.V. &middot; SISTEMA DE ARCHIVO &amp; INVENTARIO
                            </p>
                            <p style="margin: 0; font-size: 10px; color: #a8a29e;">
                                Transmisión confidencial cifrada con estándar TLS 1.3. Este mensaje fue generado automáticamente; por favor no responda directamente a esta dirección.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
