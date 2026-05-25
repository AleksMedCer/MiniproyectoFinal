<!DOCTYPE html>
<html>
<head>
    <title>Código 2FA</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #333333; text-align: center;">Tu Código de Verificación 2FA</h2>
        <p style="color: #666666; font-size: 16px;">Hola,</p>
        <p style="color: #666666; font-size: 16px;">Para continuar con tu inicio de sesión, por favor ingresa el siguiente código de 6 dígitos. Este código expirará en 5 minutos.</p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #000000; background-color: #f8f9fa; padding: 15px 30px; border-radius: 4px; border: 1px solid #e9ecef;">
                {{ $codigo }}
            </span>
        </div>
        <p style="color: #999999; font-size: 14px; text-align: center;">Si no solicitaste este código, puedes ignorar este correo de forma segura.</p>
    </div>
</body>
</html>
