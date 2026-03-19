<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Vidazen</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #F0F4F8; margin: 0; padding: 40px 0;">
    
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        
        <tr>
            <td style="background-color: #A2D9CE; padding: 40px 20px; text-align: center;">
                <img src="../../../assets/Isotipo-Logo Final-04.svg" alt="Vidazen" style="width: 80px; height: auto;">
            </td>
        </tr>

        <tr>
            <td style="padding: 40px 30px;">
                <h1 style="color: #2C3E50; font-size: 24px; margin-bottom: 20px; font-weight: bold; text-align: center;">¡Hola, {{ $psicologo->name }}!</h1>
                
                <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin-bottom: 25px; text-align: center;">
                    Has sido registrado exitosamente como profesional de la salud mental en <strong>Vidazen</strong>. Tu administrador ha generado tus credenciales de acceso temporal.
                </p>

                <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px; margin-bottom: 30px; text-align: center;">
                    <p style="margin: 0 0 10px 0; color: #718096; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Tus datos de acceso</p>
                    <p style="margin: 0 0 8px 0; color: #2C3E50; font-size: 16px;">
                        <strong>Usuario:</strong> {{ $psicologo->email }}
                    </p>
                    <p style="margin: 0; color: #2C3E50; font-size: 16px;">
                        <strong>Contraseña:</strong> <span style="background-color: #edf2f7; padding: 4px 8px; border-radius: 4px; font-family: monospace;">{{ $passwordPlain }}</span>
                    </p>
                    <p style="margin: 15px 0 0 0; color: #e53e3e; font-size: 12px;">
                        *Te sugerimos cambiar esta contraseña en cuanto inicies sesión.
                    </p>
                </div>

                <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin-bottom: 25px; text-align: center;">
                    Para activar tu cuenta y poder ingresar al sistema, haz clic en el siguiente botón:
                </p>

                <div style="text-align: center; margin-bottom: 30px;">
                    <a href="{{ $urlConfirmacion }}" style="display: inline-block; background-color: #2C3E50; color: #ffffff; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 16px;">
                        Confirmar mi cuenta
                    </a>
                </div>

                <p style="color: #718096; font-size: 14px; text-align: center;">
                    Este enlace expirará en 48 horas por motivos de seguridad.<br>
                </p>
            </td>
        </tr>

        <tr>
            <td style="background-color: #FBFCFC; padding: 20px; text-align: center; border-top: 1px solid #edf2f7;">
                <p style="color: #a0aec0; font-size: 12px; margin: 0;">
                    &copy; {{ date('Y') }} Vidazen. Todos los derechos reservados.
                </p>
            </td>
        </tr>
    </table>

</body>
</html>