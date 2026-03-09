<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Unibs CRM')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Select2 Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

    @stack('styles')
</head>

<body>

    @if (isset($hideLayout) && $hideLayout === true)
        <!-- Login / Simple Page -->
        <div class="d-flex align-items-center justify-content-center min-vh-100">
            @yield('content')
        </div>
    @else
        <div class="d-flex">

            <!-- Sidebar -->
            @include('partials.sidebar')

            <!-- Main Content -->
            <div class="content w-100">

                <!-- Navbar -->
                @include('partials.navbar')

                <!-- Page Content Wrapper -->
                <div class="d-flex flex-column min-vh-100">

                    <!-- Page Content -->
                    <main class="p-4 flex-grow-1">
                        @yield('content')
                    </main>

                    <!-- Footer -->
                    <footer class="text-center text-muted small py-3 border-top bg-light">
                        © {{ date('Y') }} UNI Business Solution. All Rights Reserved.
                    </footer>

                </div>
            </div>
        </div>
    @endif

    <!-- jQuery (MUST be first) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script src="{{ asset('assets/js/custome.js') }}"></script>

    @if (!isset($hideLayout))
        <script>
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            const toggleIcon = document.getElementById('toggleIcon');

            toggleBtn.addEventListener('click', function() {

                sidebar.classList.toggle('collapsed');

                if (sidebar.classList.contains('collapsed')) {
                    toggleIcon.classList.replace('fa-arrow-left', 'fa-arrow-right');

                    // Close open dropdowns
                    document.querySelectorAll('.sidebar .collapse.show').forEach(el => {
                        bootstrap.Collapse.getOrCreateInstance(el, {
                            toggle: false
                        }).hide();
                    });

                } else {
                    toggleIcon.classList.replace('fa-arrow-right', 'fa-arrow-left');
                }
            });

            // Prevent dropdown open when sidebar collapsed
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (sidebar.classList.contains('collapsed')) {
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endif

    @stack('scripts')

</body>

</html>
