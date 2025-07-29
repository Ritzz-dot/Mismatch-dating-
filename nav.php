<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<nav class="fixed top-0 left-0 w-full bg-white shadow-md border-b border-gray-200 z-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <div class="flex-shrink-0 text-xl font-bold text-indigo-600">
                Mismatch
            </div>

            <!-- Links -->
            <div class="flex space-x-6">
                <a href="explore.php" class="text-gray-700 hover:text-indigo-600 font-medium transition">Explore</a>
                <a href="matches.php" class="text-gray-700 hover:text-indigo-600 font-medium transition">Matches</a>
                <a href="logout.php" class="text-red-500 hover:text-red-600 font-medium transition">Logout</a>
            </div>
        </div>
    </div>
</nav>
