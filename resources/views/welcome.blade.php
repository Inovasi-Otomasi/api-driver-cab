<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>

<body>
    <div id="app" parameters="{{ json_encode(['test' => 'hehe']) }}"></div>
    {{-- <script src="/main.js"></script> --}}
    {{-- <script src="{{ asset('js/app.jsx') }}"></script> --}}
    {{-- <script>
        window.onload = function() {
            if (window.jQuery) {
                // jQuery is loaded  
                alert("Yeah!");
            } else {
                // jQuery is not loaded
                alert("Doesn't Work");
            }
        }
    </script> --}}
</body>

</html>
