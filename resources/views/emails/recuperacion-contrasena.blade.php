<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña · ARCHIVUM</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0f0a17; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1f1b29;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #0f0a17; padding: 40px 10px;">
        <tr>
            <td align="center">
                <!-- Tarjeta Principal -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #fdfbf7; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.45); border: 1px solid rgba(139, 92, 246, 0.25);">
                    
                    <!-- Encabezado con Logotipo e Identidad ARCHIVUM -->
                    <tr>
                        <td align="center" style="background: linear-gradient(180deg, #1d122f 0%, #130a21 100%); padding: 36px 30px; border-bottom: 2px solid #8b5cf6;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
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
                                            Tienda de Libros, Discos &amp; Coleccionables
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Cuerpo del Mensaje -->
                    <tr>
                        <td style="padding: 36px 36px 24px 36px;">
                            <h2 style="margin: 0 0 14px 0; font-family: Georgia, serif; font-size: 20px; color: #1e1135; font-weight: bold;">
                                Restablecer tu Contraseña
                            </h2>
                            <p style="margin: 0 0 16px 0; font-size: 14px; line-height: 1.6; color: #4a4458;">
                                Hola <strong>{{ $usuario->nombre ?? 'Cliente' }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #4a4458;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta asociada al correo <strong>{{ $usuario->email }}</strong> en <strong>ARCHIVUM</strong>. Haz clic en el siguiente botón para crear una nueva contraseña:
                            </p>

                            <!-- Botón de Acción Directa -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; padding: 15px 34px; background: linear-gradient(180deg, #8b5cf6 0%, #6d28d9 100%); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.12em; border-radius: 12px; box-shadow: 0 6px 18px rgba(109, 40, 217, 0.45); border: 1px solid rgba(255,255,255,0.3);">
                                            Restablecer Contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Bloque del Token de Verificación (Opcional para copiar y pegar) -->
                            <div style="background-color: #f3eef8; border: 1.5px dashed #8b5cf6; border-radius: 12px; padding: 18px; text-align: center; margin-bottom: 24px;">
                                <span style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.12em; color: #6d28d9; margin-bottom: 8px;">
                                    Código de verificación temporal:
                                </span>
                                <div style="font-family: 'Courier New', Courier, monospace; font-size: 14px; font-weight: bold; color: #1e1135; background-color: #ffffff; padding: 10px 14px; border-radius: 8px; border: 1px solid #ddd6fe; word-break: break-all; user-select: all; letter-spacing: 1px;">
                                    {{ $token }}
                                </div>
                                <span style="display: block; font-size: 11px; color: #6b7280; margin-top: 8px;">
                                    Si abres el botón de arriba, el código se llenará automáticamente.
                                </span>
                            </div>

                            <!-- Advertencia de Caducidad (60 minutos) -->
                            <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 14px 16px; border-radius: 6px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #92400e;">
                                    <strong>Importante:</strong> Este enlace de recuperación expira en <strong>60 minutos</strong> y solo puede utilizarse una vez.
                                </p>
                            </div>

                            <p style="margin: 0 0 12px 0; font-size: 12px; line-height: 1.6; color: #6b7280;">
                                Si no solicitaste cambiar tu contraseña, puedes ignorar este mensaje con tranquilidad; tu contraseña actual seguirá funcionando sin cambios.
                            </p>

                            <p style="margin: 16px 0 0 0; font-size: 11px; line-height: 1.5; color: #9ca3af; word-break: break-all;">
                                Si tienes problemas al hacer clic en el botón "Restablecer Contraseña", copia y pega el siguiente enlace en tu navegador:<br>
                                <a href="{{ $resetUrl }}" style="color: #7c3aed; text-decoration: underline;">{{ $resetUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Pie del Correo -->
                    <tr>
                        <td align="center" style="background-color: #f5f0eb; padding: 20px 30px; border-top: 1px solid #e9dfd4;">
                            <p style="margin: 0 0 6px 0; font-size: 11px; font-family: monospace; color: #78716c; text-transform: uppercase; letter-spacing: 0.1em;">
                                ARCHIVUM &middot; SISTEMA DE GESTIÓN DE TIENDA E INVENTARIO
                            </p>
                            <p style="margin: 0; font-size: 10px; color: #a8a29e;">
                                Este es un correo automático enviado de forma segura. Por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
