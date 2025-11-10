<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    {{-- CSS global du template --}}
    <link rel="stylesheet" href="{{ asset('template/css/style.css') }}">
</head>
<body>

    {!! $html !!}

    {{-- Scripts du template --}}
    <script src="{{ asset('template/js/main.js') }}"></script>
</body>
</html>
