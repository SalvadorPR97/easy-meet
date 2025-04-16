<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de cuenta</title>
</head>
<body>
<p>Hola,</p>
<p>Para verificar tu email pincha en el siguiente enlace:</p>
<a href="{{ url( route( 'verifyEmail', $email))  }}">
    <button type="button">Confirmar cuenta</button>
</a>
<p>Si no has sido tu, puedes ignorar este mensaje.</p>
</body>
</html>
