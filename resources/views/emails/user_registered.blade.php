<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bienvenido a Fox Hound</title>
</head>

<body>
    <h1>Bienvenido al Escuadrón Fox Hound, Operativo <strong>{{ $user->name }}</strong></h1>
    <p>Tu ingreso a las fuerzas especiales de Fox Hound ha sido aprobado. A partir de este momento, eres parte de una unidad de élite encargada de las misiones más críticas y confidenciales.</p>

    <p>Tu código de acceso único para asegurar tus operaciones es: <strong>{{ $verificationCode }} </strong><br></p>
    <p>Operativo {{ $user->name }},</p>

    <p>Este código es altamente clasificado. No lo compartas, no lo escribas, y no lo olvides.</p>

    <p>Instrucciones iniciales:</p>

    <p>Familiarízate con nuestros protocolos operativos en el menor tiempo posible.</p>
    <p>Mantén siempre un bajo perfil y utiliza métodos de comunicación encriptados.</p>
    <p>Recuerda: la discreción es clave.</p>
    <p>"En Fox Hound no hay margen para el error, y cada decisión que tomas es una pieza del rompecabezas que asegura el éxito de la misión."</p>

    <p>El resto de tus directivas te serán entregadas en tu primer despliegue. Bienvenido al frente, {{ $user->name }}.</p>

    <p>— Comandante Roy Campbell<br>
        Fox Hound Operations.</p>
</body>

</html>