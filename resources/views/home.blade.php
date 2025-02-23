<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>

<body class="home-body">
    <img class="Aiai" src="{{ asset('img/aiaiPNG.png') }}" alt="Aiai">

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/login';
        } else {
            document.cookie = `token=${token}; path=/`;
        }
    });
</script>

</html>