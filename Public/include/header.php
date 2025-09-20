<?php
$current = basename($_SERVER['PHP_SELF']); // e.g. "projects.php"
?>

<!-- Header -->
<header class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <!-- Logo / Title -->
            <div class="flex items-center space-x-2 mb-4 md:mb-0">
                <i data-feather="droplet" class="w-8 h-8"></i>
                <h1 class="text-2xl font-bold">FloS Project</h1>
            </div>

            <!-- Navigation -->
            <nav class="flex space-x-6">
                <a href="index.php" class="hover:text-blue-200 transition-colors 
                   <?= $current === 'index.php' ? 'font-bold  text-white' : 'text-blue-100' ?>">
                    Home
                </a>
                <a href="projects.php" class="hover:text-blue-200 transition-colors 
                   <?= $current === 'projects.php' ? 'font-bold  text-white' : 'text-blue-100' ?>">
                    Projects
                </a>
                <a href="map.php" class="hover:text-blue-200 transition-colors 
                   <?= $current === 'map.php' ? 'font-bold  text-white' : 'text-blue-100' ?>">
                    Map
                </a>
                <a href="regression.php" class="hover:text-blue-200 transition-colors 
                   <?= $current === 'regression.php' ? 'font-bold  text-white' : 'text-blue-100' ?>">
                    Regression
                </a>
            </nav>
        </div>
    </div>
</header>