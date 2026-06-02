<!DOCTYPE html>
<html lang="en">
<head>
    <script>if(localStorage.getItem('theme') === 'dark') { document.documentElement.setAttribute('data-bs-theme', 'dark'); }</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxeRent | Rental Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css?v=<?php echo time(); ?>">
    <style>
        .min-vh-80 { min-height: 80vh; }
        .card-custom { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-primary-custom { background-color: #4f46e5; border: none; color: white; border-radius: 8px; }
        .btn-primary-custom:hover { background-color: #4338ca; color: white; }
    </style>
</head>
<body>

<?php include __DIR__ . '/navbar.php'; ?>
