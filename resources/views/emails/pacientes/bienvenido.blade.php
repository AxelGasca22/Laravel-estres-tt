<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a VidaZen</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #F0F4F8; margin: 0; padding: 40px 0;">
    
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(133,193,233,0.15);">
        
        <tr>
            <td style="background: linear-gradient(135deg, #85C1E9 0%, #A2D9CE 100%); padding: 50px 20px; text-align: center;">
                <img src="../../../assets/Isotipo-Logo Final-04.svg" alt="VidaZen" style="width: 70px; height: auto; margin-bottom: 15px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                <h1 style="color: #ffffff; font-size: 28px; margin: 0; font-weight: 600; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(44,62,80,0.1);">
                    Tu viaje comienza aquí
                </h1>
            </td>
        </tr>

        <tr>
            <td style="padding: 40px 35px;">
                <h2 style="color: #2C3E50; font-size: 22px; margin-bottom: 15px; font-weight: bold;">¡Hola, {{ $user->name }}!</h2>
                
                <p style="color: #5D6D7E; font-size: 16px; line-height: 1.7; margin-bottom: 25px;">
                    Nos alegra mucho tenerte en <strong>VidaZen</strong>. Has dado un gran paso hacia el cuidado de tu bienestar mental, y estamos aquí para acompañarte en cada etapa de este proceso.
                </p>

                <div style="background-color: #F8FBFA; border-left: 4px solid #A2D9CE; padding: 20px; border-radius: 0 12px 12px 0; margin-bottom: 35px;">
                    <p style="margin: 0; color: #34495E; font-size: 15px; line-height: 1.6; font-style: italic;">
                        "El autocuidado no es un gasto, es la mejor inversión que puedes hacer en ti mismo."
                    </p>
                </div>

                <p style="color: #5D6D7E; font-size: 16px; line-height: 1.7; margin-bottom: 30px; text-align: center;">
                    Para empezar a usar tu aplicación móvil, conectar con tu profesional y acceder a tus herramientas, por favor verifica tu correo electrónico:
                </p>

                <div style="text-align: center; margin-bottom: 35px;">
                    <a href="{{ $urlConfirmacion }}" style="display: inline-block; background-color: #2C3E50; color: #ffffff; padding: 16px 36px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 16px; box-shadow: 0 4px 10px rgba(44,62,80,0.2);">
                        Verificar mi cuenta en la app
                    </a>
                </div>

                <p style="color: #95A5A6; font-size: 13px; text-align: center; line-height: 1.5; margin-bottom: 0;">
                    Si tienes problemas con el botón, asegúrate de abrir este correo <strong>desde tu teléfono móvil</strong> donde tienes instalada la aplicación de VidaZen.<br><br>
                    Este enlace caducará en 48 horas.
                </p>
            </td>
        </tr>

        <tr>
            <td style="background-color: #FBFCFC; padding: 25px; text-align: center; border-top: 1px solid #ECF0F1;">
                <p style="color: #BDC3C7; font-size: 12px; margin: 0;">
                    &copy; {{ date('Y') }} VidaZen.
                </p>
            </td>
        </tr>
    </table>

</body>
</html>