<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">

    <link rel="shortcut icon" href="{{ asset('images/favicon/logo-admin.png') }}">

    <title>@yield("title")</title>

    <script
        src="https://code.jquery.com/jquery-3.6.0.js"
        integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
    @include("admin.assets.css.app")
    @yield("style")
{{--    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">--}}
    @include(".admin.layouts.partials.input")
    <style type="text/css">
        .my-active span{
            background-color: #5cb85c !important;
            color: white !important;
            border-color: #5cb85c !important;
        }
    </style>
{{--    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
</head>

<body>
<div class="wrapper">
    @include("admin.layouts.partials.menu-sidebar")
    @yield("content")
</div>

@yield("script")

</body>

</html>
