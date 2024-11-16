<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="text-center">
        <div class="flex justify-center mb-4">
            <div class="relative">
                <div class="absolute inset-x-0 top-0 flex justify-center">
                    <div class="h-2 w-8 bg-gray-600"></div>
                </div>
                <div class="h-24 w-24 border-4 border-gray-600 rounded-full flex items-center justify-center">
                    <div class="text-4xl font-bold">500</div>
                </div>
            </div>
        </div>
        <h1 class="text-xl font-semibold mb-2">SOMETHING WENT WRONG</h1>
        <p class="text-gray-600">PLEASE TRY AGAIN LATER</p>
        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            <button onclick="location.reload()" class="btn btn-secondary ml-2">Try Again</button>
        </div>
    </div>
</body>
</html>
