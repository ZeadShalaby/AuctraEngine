<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'YouTube Scraper')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        body {
            background-color: #f1f3f7;
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: right;
        }

        /* الـ Header الأزرق الغامق */
        .hero-section {
            background-color: #1e2d40;
            color: white;
            padding: 60px 0 120px 0;
            margin-bottom: -80px;
        }

        /* صندوق الإدخال الأبيض */
        .search-box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .form-control {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background-color: #fcfcfc;
        }

        .btn-primary-custom {
            background-color: #c54b3d;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
        }

        /* ستايل الكارت */
        .course-card {
            background: white;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: 0.3s;
            height: 100%;
        }

        .course-card:hover {
            transform: translateY(-10px);
        }

        .thumb-container {
            position: relative;
        }

        .video-count-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(197, 75, 61, 0.9);
            color: white;
            padding: 2px 10px;
            border-radius: 5px;
            font-size: 12px;
        }

        .duration-badge {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .category-badge {
            background: #fff0ef;
            color: #c54b3d;
            font-size: 12px;
            padding: 5px 15px;
            border-radius: 20px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">YouTube Scraper</a>
        </div>
    </nav>

    <div class="container py-5">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>

</html>
