<!DOCTYPE html>
<html>
    <head>
        <title>Preview</title>

        <link rel="stylesheet" href="{{asset('css/preview.css')}}">

        <script src="{{asset('js/all.js')}}"></script>

    </head>
    <body class="preview-page">
        @yield('dialog')

        <script>

            require.config({
                baseUrl: '{{ asset('js') }}',
                skipDataMain:true,
            });
        
            require(['preview'], function(Preview){
                Preview.load();
            });
        
        </script>
    </body>
</html>
