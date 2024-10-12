<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel App</title>
    @vite('resources/css/app.css') <!-- Ensure you're linking your CSS -->
</head>
<body class="bg-gray-100">
    <div id="app" class="container mx-auto p-4">
        <!-- Vue app will mount here -->
    </div>
    @vite('resources/js/app.js') <!-- Include your JS file compiled by Vite -->
</body>
</html>
