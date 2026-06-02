<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/index.php">
            <i class="bi bi-buildings-fill fs-3"></i> LuxeRent
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-3">
                <?php if(isset($_SESSION['user'])): ?>
                    <?php if($_SESSION['user']['role'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link fw-medium text-dark" href="/admin/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link fw-medium text-dark" href="/admin/messages.php">Messages</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link fw-medium text-dark" href="/user/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link fw-medium text-dark" href="/user/contact.php">Contact Admin</a></li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-3 dropdown">
                        <a class="nav-link fw-bold text-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> 
                            <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="/user/edit_profile.php"><i class="bi bi-pencil-square me-2"></i>Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark" href="/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary-custom btn-sm px-4" href="/register.php">Register</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item ms-lg-2">
                    <button class="btn btn-link nav-link px-0 text-warning fs-5" id="darkModeToggle" title="Toggle Dark Mode">
                        <i class="bi bi-moon-stars-fill" id="darkModeIcon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    // Dark Mode Toggle Logic
    const toggleBtn = document.getElementById('darkModeToggle');
    const icon = document.getElementById('darkModeIcon');
    
    // Check localStorage
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        icon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        icon.classList.replace('text-warning', 'text-light');
    }

    toggleBtn.addEventListener('click', () => {
        let theme = document.documentElement.getAttribute('data-bs-theme');
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('theme', 'light');
            icon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            icon.classList.replace('text-light', 'text-warning');
        } else {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            icon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            icon.classList.replace('text-warning', 'text-light');
        }
    });
</script>