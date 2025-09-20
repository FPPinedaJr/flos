<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['is_logged_in'])) {
    $_SESSION['is_logged_in'] = false;
}

require './include/connect_db.php';


// Fetch all projects
$stmt = $pdo->query("SELECT * FROM flood_control_proj ORDER BY proj_status, name");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all average ratings and counts for all projects at once
$projectIds = array_column($projects, 'idflood');
$placeholders = implode(',', array_fill(0, count($projectIds), '?'));

$stmt = $pdo->prepare("
    SELECT idflood, AVG(score) AS avg_score, COUNT(*) AS total_votes
    FROM rating
    WHERE idflood IN ($placeholders)
    GROUP BY idflood
");
$stmt->execute($projectIds);

$ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert to associative array for easier lookup by idflood
$ratingsByProject = [];
foreach ($ratings as $r) {
    $ratingsByProject[$r['idflood']] = [
        'avg_score' => round($r['avg_score'], 1),
        'total_votes' => $r['total_votes']
    ];
}

// $avgScore = round($ratingData['avg_score'], 1); // e.g., 4.2
// $totalVotes = $ratingData['total_votes'];


// Some sample stock water images
$images = [
    "./image/infrastructure.jpg"
];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FloS Project | Flood Control Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.2);
        }

        .rating-modal {
            backdrop-filter: blur(5px);
        }

        .progress-bar {
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include_once('./include/header.php'); ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Flood Control Projects</h2>
            <p class="text-gray-600">Monitoring and evaluation of ongoing and completed flood control projects</p>
        </div>

        <!-- Search and Filter -->
        <div class="mb-8 bg-white p-4 rounded-lg shadow">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Projects</label>
                    <div class="relative">
                        <input type="text" id="search" placeholder="Search by name, location..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <i data-feather="search" class="absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                    <select id="status"
                        class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Projects</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($projects as $proj): ?>
                <?php
                // Pick random image for each card
                $img = $images[array_rand($images)];
                $ratingData = $ratingsByProject[$proj['idflood']] ?? ['avg_score' => 0, 'total_votes' => 0];
                $avgScore = $ratingData['avg_score'];
                $totalVotes = $ratingData['total_votes'];
                ?>
                <div class="project-card bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300"
                    data-aos="fade-up">
                    <div class="h-48 bg-blue-500 relative overflow-hidden">
                        <img src="<?= htmlspecialchars($img) ?>" alt="Project Image" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                            <h3 class="text-xl font-bold text-white"><?= htmlspecialchars($proj['name']) ?></h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <i data-feather="map-pin" class="w-4 h-4 text-gray-500 mr-2"></i>
                            <span class="text-sm text-gray-600">
                                <?= htmlspecialchars($proj['loc_barangay']) ?>,
                                <?= htmlspecialchars($proj['loc_municities']) ?>
                            </span>
                        </div>
                        <div class="flex items-center mb-4">
                            <div class="w-2 h-2 rounded-full 
                        <?= $proj['proj_status'] === 'Completed' ? 'bg-green-500' : 'bg-yellow-500' ?> mr-2"></div>
                            <span class="text-sm font-medium 
                        <?= $proj['proj_status'] === 'Completed' ? 'text-green-600' : 'text-yellow-600' ?>">
                                <?= htmlspecialchars($proj['proj_status']) ?>
                            </span>
                        </div>

                        <div class="space-y-3 text-sm text-gray-700">
                            <div class="flex">
                                <span class="font-medium w-32">Contractor:</span>
                                <span><?= htmlspecialchars($proj['contractors']) ?></span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-32">Fund Source:</span>
                                <span><?= htmlspecialchars($proj['fund_source']) ?></span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-32">Project Cost:</span>
                                <span>₱ <?= htmlspecialchars($proj['proj_cost']) ?></span>
                            </div>
                            <div class="flex">
                                <span class="font-medium w-32">Duration:</span>
                                <span><?= htmlspecialchars($proj['start_date']) ?> -
                                    <?= htmlspecialchars($proj['compl_date']) ?></span>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="flex items-center">
                                        <div class="flex -space-x-1">
                                            <?php
                                            // Round down for full stars
                                            $fullStars = floor($avgScore);
                                            // Determine if there is a half star
                                            $halfStar = ($avgScore - $fullStars) >= 0.5 ? 1 : 0;
                                            // Empty stars
                                            $emptyStars = 5 - $fullStars - $halfStar;

                                            // Full stars
                                            for ($i = 0; $i < $fullStars; $i++) {
                                                echo '<i class="fas fa-star text-yellow-400"></i>';
                                            }

                                            // Half star
                                            if ($halfStar) {
                                                echo '<i class="fas fa-star-half-alt text-yellow-400"></i>';
                                            }

                                            // Empty stars
                                            for ($i = 0; $i < $emptyStars; $i++) {
                                                echo '<i class="far fa-star text-gray-300"></i>';
                                            }
                                            ?>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500"><?= number_format($avgScore, 1) ?>
                                            (<?= $totalVotes ?>)</span>
                                    </div>
                                </div>
                                <button onclick="openRatingModal('<?= $proj['name'] ?>', '<?= $proj['idflood'] ?>')"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Rate Project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Rating Modal -->
    <div id="ratingModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="rating-modal absolute inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4" data-aos="zoom-in">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Rate Project</h3>
                    <button onclick="closeRatingModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <p class="text-gray-600 mb-1">How would you rate <span id="projectName" class="font-medium">Project
                        Name</span>?</p>

                <div class="flex justify-center my-6">
                    <div class="flex space-x-1" id="ratingStars">
                        <i data-feather="star" class="w-8 h-8 text-gray-300 hover:text-yellow-400 cursor-pointer"
                            onmouseover="hoverStar(1)" onmouseout="resetStars()" onclick="setRating(1)"></i>
                        <i data-feather="star" class="w-8 h-8 text-gray-300 hover:text-yellow-400 cursor-pointer"
                            onmouseover="hoverStar(2)" onmouseout="resetStars()" onclick="setRating(2)"></i>
                        <i data-feather="star" class="w-8 h-8 text-gray-300 hover:text-yellow-400 cursor-pointer"
                            onmouseover="hoverStar(3)" onmouseout="resetStars()" onclick="setRating(3)"></i>
                        <i data-feather="star" class="w-8 h-8 text-gray-300 hover:text-yellow-400 cursor-pointer"
                            onmouseover="hoverStar(4)" onmouseout="resetStars()" onclick="setRating(4)"></i>
                        <i data-feather="star" class="w-8 h-8 text-gray-300 hover:text-yellow-400 cursor-pointer"
                            onmouseover="hoverStar(5)" onmouseout="resetStars()" onclick="setRating(5)"></i>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">Additional Feedback
                        (Optional)</label>
                    <textarea id="feedback" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Share your experience with this project..."></textarea>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="closeRatingModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button onclick="submitRating()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Submit Rating
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vanta.js Wave Background Overlay -->
    <div id="ratingOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 hidden">
        <div id="overlayContent" class="text-center text-white">
            <div id="loadingSpinner" class="mb-4">
                <!-- SVG Loading Spinner -->
                <!-- Water Droplet Loader SVG -->
                <svg width="64" height="64" viewBox="0 0 50 50" class="animate-spin mx-auto">
                    <circle cx="25" cy="25" r="20" fill="none" stroke="#ffffff" stroke-width="5" stroke-linecap="round"
                        stroke-dasharray="90 150"></circle>
                </svg>


            </div>
            <div id="thankYouMessage"
                class="hidden flex flex-col items-center justify-center bg-white/90 backdrop-blur-md rounded-2xl p-8 shadow-xl max-w-sm text-center space-y-4">
                <!-- Water/Flood Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-blue-500 animate-bounce"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C12 2 4 10 4 16a8 8 0 0 0 16 0c0-6-8-14-8-14z" />
                </svg>

                <!-- Heading -->
                <h2 class="text-2xl font-bold text-blue-700">Thank you for your rating!</h2>

                <!-- Message -->
                <p class="text-gray-700">Your feedback helps us improve our projects and build better infrastructure.
                </p>

                <!-- Optional Confetti/Checkmark Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-green-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>

            </div>
        </div>
    </div>



    <?php include_once('./include/footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.waves.min.js"></script>

    <script>
        $(document).ready(function () {
            let currentProject = '';
            let currentProjectId = 0;
            let currentRating = 0;

            // Initialize AOS and Feather
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            feather.replace();


            // Open Modal
            window.openRatingModal = function (projectName, projectId) {
                currentProject = projectName;
                currentProjectId = projectId;
                $("#projectName").text(projectName);
                $("#ratingModal").removeClass("hidden");
                $("body").css("overflow", "hidden");
            };

            // Close Modal
            window.closeRatingModal = function () {
                $("#ratingModal").addClass("hidden");
                $("body").css("overflow", "auto");
                resetStars();
                currentRating = 0;
                $("#feedback").val('');
            };

            // Hover stars
            window.hoverStar = function (rating) {
                $('#ratingStars svg').each(function (index) {
                    if (index < rating) {
                        $(this).attr('stroke', '#facc15').attr('fill', '#facc15');
                    } else {
                        $(this).attr('stroke', '#d1d5db').attr('fill', 'none');
                    }
                });
            };

            // Reset stars to current rating
            window.resetStars = function () {
                $('#ratingStars svg').each(function (index) {
                    if (index < currentRating) {
                        $(this).attr('stroke', '#facc15').attr('fill', '#facc15');
                    } else {
                        $(this).attr('stroke', '#d1d5db').attr('fill', 'none');
                    }
                });
            };

            // Set rating
            window.setRating = function (rating) {
                currentRating = rating;
                $('#ratingStars svg').each(function (index) {
                    if (index < rating) {
                        $(this).attr('stroke', '#facc15').attr('fill', '#facc15');
                    } else {
                        $(this).attr('stroke', '#d1d5db').attr('fill', 'none');
                    }
                });
            };

            // Submit rating
            window.submitRating = function () {
                if (currentRating === 0) {
                    alert("Please select a rating before submitting.");
                    return;
                }

                // Show overlay
                $("#overlayContent").removeClass('hidden');
                $("#ratingOverlay").removeClass('hidden');

                $.ajax({
                    type: "POST",
                    url: "include/submit_rating.php",
                    data: {
                        projectId: currentProjectId,
                        score: currentRating,
                        feedback: $("#feedback").val()
                    },
                    success: function (response) {
                        response = response.trim();
                        if (response === "OK") {
                            // Hide spinner and show thank you message
                            $("#loadingSpinner").addClass('hidden');
                            $("#thankYouMessage").removeClass('hidden');

                            // Optionally keep overlay for 2-3 seconds, then refresh
                            setTimeout(function () {
                                location.reload(); // refresh page
                            }, 1000);
                        } else {
                            alert("Failed to submit rating: " + response);
                            $("#ratingOverlay").addClass('hidden');
                        }
                    },
                    error: function () {
                        alert("Error submitting rating.");
                        $("#ratingOverlay").addClass('hidden');
                    }
                });
            };


            // Search functionality
            $("#search").on("input", function () {
                const searchTerm = $(this).val().toLowerCase();
                $(".project-card").each(function () {
                    const title = $(this).find("h3").text().toLowerCase();
                    $(this).toggle(title.includes(searchTerm));
                });
            });

            // Filter functionality
            $("#status").on("change", function () {
                const status = $(this).val();
                $(".project-card").each(function () {
                    const statusElement = $(this).find("span.text-green-600, span.text-yellow-600, span.text-blue-600");
                    let cardStatus = "";
                    if (statusElement.hasClass("text-green-600")) cardStatus = "completed";
                    else if (statusElement.hasClass("text-yellow-600")) cardStatus = "ongoing";

                    $(this).toggle(status === "all" || status === cardStatus);
                });
            });
        });
    </script>

</body>

</html>