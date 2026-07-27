<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Dynamic SEO Config -->
    <title>@yield('title', 'Premium Living | PG Management System')</title>
    <meta name="description" content="@yield('meta_description', 'Experience world-class luxury accommodations.')">
    <meta name="keywords" content="@yield('meta_keywords', 'PG, luxury hostel, premium living')">
    <meta name="author" content="PG Management System Team">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/css/premium.css" rel="stylesheet">

    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    <!-- External Utilities (CDNs) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <!-- Lucide Outlined Icons Setup -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-premium">

    @yield('content')

    <!-- Lucide Icons Initializer -->
    <script>
        lucide.createIcons();
    </script>
    
    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dynamic Alerts Trigger -->
    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#2563EB',
                background: '#FFFFFF',
                color: '#0F172A',
                customClass: { popup: 'custom-swal-border' }
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                title: 'Alert!',
                text: "{{ $errors->first() }}",
                icon: 'error',
                confirmButtonColor: '#DC2626',
                background: '#FFFFFF',
                color: '#0F172A',
                customClass: { popup: 'custom-swal-border' }
            });
        </script>
    @endif

    @if(session('info'))
        <script>
            Swal.fire({
                title: 'Information',
                text: "{{ session('info') }}",
                icon: 'info',
                confirmButtonColor: '#2563EB',
                background: '#FFFFFF',
                color: '#0F172A',
                customClass: { popup: 'custom-swal-border' }
            });
        </script>
    @endif

    @yield('scripts')
</body>
</html>
