<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['is_logged_in'])) {
    $_SESSION['is_logged_in'] = false;
}

require './include/connect_db.php';

// Barangays at risk
$barangay_count = $pdo->query("SELECT COUNT(DISTINCT adm4_en) FROM flood_100year")->fetchColumn();
$barangay_total = $pdo->query("SELECT COUNT(adm4_en) FROM barangay")->fetchColumn();

// Flood control projects
$project_count = $pdo->query("SELECT COUNT(name) FROM flood_control_proj")->fetchColumn();

// Average rating
$avg_rating = $pdo->query("SELECT SUM(score) / COUNT(score) FROM rating")->fetchColumn();
$avg_rating = $avg_rating ? round($avg_rating, 2) : 0;

// Population
$population_total = $pdo->query("SELECT SUM(population) FROM population")->fetchColumn();
$population_total = $population_total ? number_format($population_total) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FloS Project | Flood Control Monitoring</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.2/dist/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.waves.min.js"></script>
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #0ea5e9 100%);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .title {
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-800">
    <div id="hero" class="relative text-white overflow-hidden">
        <div id="flood-animation" class="absolute inset-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center" data-aos="fade-up">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">FloS Project</h1>
                <p class="text-xl md:text-2xl max-w-3xl mx-auto mb-8">
                    Harnessing Data for Flood Control Monitoring and Evaluation
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#features"
                        class="bg-white text-blue-800 hover:bg-blue-100 px-8 py-3 rounded-lg font-medium transition duration-300">
                        Learn More
                    </a>
                    <a href="#contact"
                        class="bg-transparent border-2 border-white hover:bg-white hover:text-blue-800 px-8 py-3 rounded-lg font-medium transition duration-300">
                        Get Involved
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gradient-to-b from-white to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">FloS Project Core Features</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Transparency, smarter insights, and reliable data-driven tools that support better flood control
                    monitoring, planning,
                    and evaluation for safer and more resilient communities. </p>
            </div>

            <div class="grid md:grid-cols-3 gap-10">

                <div
                    class="group bg-white p-10 rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 hover:scale-105 transition duration-500">
                    <div
                        class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition duration-500">
                        <i data-feather="file-text" class="w-10 h-10 text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 group-hover:text-blue-700 transition">Flood Control Projects</h3>
                    <p class="text-gray-600 group-hover:text-gray-800 mb-6">
                        Browse clear, accessible data on flood control projects—built to fight corruption and boost
                        accountability.
                    </p>
                    <a href="projects.html"
                        class="inline-block bg-blue-600 text-white px-5 py-2 rounded-full font-medium shadow hover:bg-blue-700 transition">
                        View Projects
                    </a>
                </div>

                <div
                    class="group bg-white p-10 rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 hover:scale-105 transition duration-500">
                    <div
                        class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition duration-500">
                        <i data-feather="map-pin" class="w-10 h-10 text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 group-hover:text-green-700 transition">Interactive Flood Map</h3>
                    <p class="text-gray-600 group-hover:text-gray-800 mb-6">
                        Zoom into flood-prone zones and instantly check if protective projects are already in place with
                        live GIS mapping. </p>
                    <a href="map.html"
                        class="inline-block bg-green-600 text-white px-5 py-2 rounded-full font-medium shadow hover:bg-green-700 transition">
                        Open Map
                    </a>
                </div>

                <div
                    class="group bg-white p-10 rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 hover:scale-105 transition duration-500">
                    <div
                        class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition duration-500">
                        <i data-feather="trending-up" class="w-10 h-10 text-purple-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 group-hover:text-purple-700 transition">Regression Analysis</h3>
                    <p class="text-gray-600 group-hover:text-gray-800 mb-6">
                        Unlock insights with advanced models that measure what really works—and what doesn’t—in flood
                        control. </p>
                    <a href="regression.html"
                        class="inline-block bg-purple-600 text-white px-5 py-2 rounded-full font-medium shadow hover:bg-purple-700 transition">
                        Run Analysis
                    </a>
                </div>

            </div>
        </div>
    </section>

    t

    <!-- Data Visualization Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Comprehensive Data Dashboard</h2>
                    <p class="text-lg text-gray-600 mb-6">Our platform aggregates open-source and government datasets
                        from multiple sources to provide
                        a unified view of flood risks and flood control projects,
                        starting with <span class="font-semibold text-blue-700">Palawan</span>. </p>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                            <span class="text-gray-700">
                                Utilizing <a href="https://noah.up.edu.ph/" target="_blank"
                                    class="text-blue-600 hover:underline font-medium">Project NOAH</a> data for flood
                                risk prediction and hazard mapping
                            </span>
                        </li>
                        <li class="flex items-start">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                            <span class="text-gray-700">
                                Analyzing barangay-level vulnerability in Palawan for localized disaster planning
                            </span>
                        </li>
                        <li class="flex items-start">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                            <span class="text-gray-700">
                                Integrating <a href="https://www.dbm.gov.ph/index.php/programs-projects/project-dime"
                                    target="_blank" class="text-blue-600 hover:underline font-medium">Project DIME</a>
                                data to track flood control infrastructure projects
                            </span>
                        </li>
                        <li class="flex items-start">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                            <span class="text-gray-700">
                                Promoting transparency through open dashboards accessible to all communities
                            </span>
                        </li>
                    </ul>
                </div>
                <div data-aos="fade-left">
                    <img src="./image/palawan.jpg" alt="Data Visualization Dashboard"
                        class="rounded-xl shadow-xl w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-20 bg-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-12" data-aos="fade-up">Key Numbers for Palawan</h2>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="p-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-4xl font-bold mb-2"><?= htmlspecialchars($barangay_count) ?> /
                        <?= htmlspecialchars($barangay_total) ?>
                    </div>
                    <p class="text-blue-200">Barangays at Risk</p>
                </div>

                <div class="p-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-4xl font-bold mb-2"><?= htmlspecialchars($project_count) ?></div>
                    <p class="text-blue-200">Flood Control Projects as of 20xx</p>
                </div>

                <div class="p-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-4xl font-bold mb-2"><?= htmlspecialchars($avg_rating) ?></div>
                    <p class="text-blue-200">Average Project Rating</p>
                </div>

                <div class="p-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="text-4xl font-bold mb-2"><?= htmlspecialchars($population_total) ?>+</div>
                    <p class="text-blue-200">Population Covered</p>
                </div>

            </div>
        </div>
    </section>



    <!-- Project Proponents -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-16" data-aos="fade-left">Meet the Project Developers</h2>
            <div class="grid md:grid-cols-2 gap-8">

                <div class="bg-gray-50 p-8 rounded-xl shadow-sm" data-aos="fade-right" data-aos-delay="100">
                    <div class="flex items-center mb-6">
                        <img src="./image/fernando.jpg" alt="Fernando P. Pineda Jr."
                            class="w-16 h-16 rounded-full mr-4 object-cover">
                        <div>
                            <h4 class="font-semibold">Fernando P. Pineda Jr.</h4>
                            <p class="text-gray-600">4th Year, BS Computer Science<br>Palawan State University</p>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Fernando is passionate about software development, AI, and cybersecurity.
                        He represented PSU in Hack4Gov 2023–2024, winning 2nd place with team AEther,
                        and has built systems like <a class="text-blue-600 hover:underline font-medium" target="_blank"
                            href="https://quarsi.miceff.com/">Quarsi</a> and <a
                            class="text-blue-600 hover:underline font-medium" target="_blank"
                            href="https://dorrafy.com/">Dorrafy</a>.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-xl shadow-sm" data-aos="fade-left" data-aos-delay="200">
                    <div class="flex items-center mb-6">
                        <img src="./image/jamaica.jpg" alt="Jamaica C. Magbanua"
                            class="w-16 h-16 rounded-full mr-4 object-cover">
                        <div>
                            <h4 class="font-semibold">Jamaica C. Magbanua</h4>
                            <p class="text-gray-600">4th Year, BS Computer Science<br>Palawan State University</p>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Jamaica specializes in software development and data science.
                        She placed 3rd in Hack4Gov 2023–2024 and has led projects like
                        <a href="https://quarsi.miceff.com/" target="_blank"
                            class="text-blue-600 hover:underline font-medium">Quarsi</a> and <a
                            href="https://palawansu-usg.com/" target="_blank"
                            class="text-blue-600 hover:underline font-medium">PSU-USG's website</a>.
                    </p>
                </div>

            </div>
        </div>
    </section>


    <!-- CTA Section -->
    <section id="contact" class="py-20 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl font-bold mb-4">Ready to Implement FloS in Your Community?</h2>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Contact us to learn how our flood monitoring system can protect your city or region.
                </p>
            </div>

            <div class="max-w-2xl mx-auto bg-gray-800 rounded-xl p-8" data-aos="fade-up" data-aos-delay="100">
                <form id="contactForm">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400">
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="organization"
                            class="block text-sm font-medium text-gray-300 mb-1">Organization</label>
                        <input type="text" id="organization" name="organization"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400">
                    </div>
                    <div class="mt-6">
                        <label for="message" class="block text-sm font-medium text-gray-300 mb-1">Message</label>
                        <textarea id="message" name="message" rows="4" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400"></textarea>
                    </div>
                    <div class="mt-8">
                        <button id="submitBtn" type="submit"
                            class="flex justify-center items-center h-12 relative w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">

                            <span id="btnLoader" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </span>

                            <span id="btnText">Send Message</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">FloS Project</h3>
                    <p class="text-gray-400">Harnessing Data for Flood Control Monitoring and
                        Evaluation.</p>
                </div>
                <div>
                    <h4 class="text-lg font-medium mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="project.php" class="text-gray-400 hover:text-white transition duration-300">Flood
                                Control Projects</a></li>
                        <li><a href="map.php" class="text-gray-400 hover:text-white transition duration-300">GIS Map</a>
                        </li>
                        <li><a href="regression.php"
                                class="text-gray-400 hover:text-white transition duration-300">Regression Analysis</a>
                        </li>
                        <li>
                            <?php if ($_SESSION['is_logged_in'] == false) { ?>
                                <a onclick="openLoginModal()"
                                    class="text-gray-400 hover:text-white transition duration-300 hover:cursor-pointer">Sign
                                    In</a>
                            <?php } else { ?>
                                <a href="./include/logout.php"
                                    class="text-gray-400 hover:text-white transition duration-300 hover:cursor-pointer">Sign
                                    Out</a>
                            <?php } ?>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-medium mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a target="_blank" href="https://github.com/FPPinedaJr/flos/blob/main/README.md"
                                class="text-gray-400 hover:text-white transition duration-300">Documentation</a>
                        </li>
                        <li><a target="_blank" href="https://github.com/FPPinedaJr/flos"
                                class="text-gray-400 hover:text-white transition duration-300">Source Code</a>
                        </li>
                        <li><a target="_blank"
                                href="https://drive.google.com/drive/u/1/folders/1VvI--qtMjEdF8WO6O29s-zgoTDsKQW1f"
                                class="text-gray-400 hover:text-white transition duration-300">Research Paper</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-medium mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <a target="_blank" href="https://www.facebook.com/jamaica.magbanua.1"
                            class="text-gray-400 hover:text-white transition duration-300"><i
                                data-feather="facebook"></i></a>
                        <a target="_blank" href="#" class="text-gray-400 hover:text-white transition duration-300"><i
                                data-feather="linkedin"></i></a>
                        <a target="_blank" href="https://github.com/FPPinedaJr/"
                            class="text-gray-400 hover:text-white transition duration-300"><i
                                data-feather="github"></i></a>
                        <a target="_blank" href="mailto:fernandojr03.pineda@gmail.com"
                            class="text-gray-400 hover:text-white transition duration-300"><i
                                data-feather="mail"></i></a>
                    </div>
                    <p class="text-gray-400 mt-4">flos.miceff.com</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>© 2025 FloS Project. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <div id="login_modal"
        class="fixed invisible top-0 left-0 right-0 z-50 flex w-full h-full bg-[#2e2c2c69] backdrop-blur-sm justify-center items-center">
        <div id="login_modal_main"
            class="relative flex flex-col w-5/6 p-6 bg-white rounded-lg shadow-lg md:w-1/4 h-fit ">
            <h1 class="mb-3 text-2xl font-bold text-center ">SIGN IN</h1>
            <form id="login_form">
                <label for="email">Email</label>
                <input type="text" name="email" id="s_email" class="w-full p-2 mb-2 border rounded">

                <label for="password">Password</label>
                <div class="relative w-full">
                    <input type="password" name="password" id="password" class="w-full p-2 pr-10 mb-1 border rounded">
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 flex items-center text-gray-500 right-3">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="h-8">
                    <div id="error_div" class="hidden pl-1 text-sm text-red-500 ">
                        <i class="fa-solid fa-triangle-exclamation"></i> <span id="error_msg"></span>
                    </div>
                </div>

                <div class="flex justify-end ">
                    <button id="login_btn" type="submit"
                        class="flex items-center w-20 justify-center  h-10 gap-2 p-2 mr-5 font-semibold text-white bg-blue-500 cursor-pointer rounded-xl">
                        <svg id="s_spinner" class="hidden w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="login_text">Sign in</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl p-8 max-w-sm text-center">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Message Sent!</h3>
            <p class="text-gray-600 mb-6">Thank you for reaching out. We’ll get back to you soon.</p>
            <button id="closeModal"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                Close
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function () {
            $("#contactForm").on("submit", function (e) {
                e.preventDefault();

                // Show loader + disable button
                $("#btnLoader").removeClass("hidden");
                $("#btnText").addClass("hidden");
                $("#submitBtn").prop("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "include/send_mail.php",
                    data: $(this).serialize(),
                    success: function (response) {
                        $("#btnLoader").addClass("hidden");
                        $("#btnText").removeClass("hidden");
                        $("#submitBtn").prop("disabled", false);

                        if (response.trim() === "OK") {
                             $("#successModal").fadeIn(300).css("display","flex");
                        } else {
                            alert("Error: " + response);
                        }
                    },
                    error: function () {
                        $("#btnLoader").addClass("hidden");
                        $("#btnText").removeClass("hidden");
                        $("#submitBtn").prop("disabled", false);
                        alert("Something went wrong. Please try again.");
                    }
                });
            });



            $("#login_form").on("submit", function (e) {
                e.preventDefault();
                $("#error_div").addClass("hidden");

                $("#login_btn").attr("disabled", true);
                $("#s_spinner").removeClass("hidden");
                $("#login_text").addClass("hidden");

                var email = $("#s_email").val().trim();
                var password = $("#password").val().trim();

                if (email === '' || password === '') {
                    $("#error_msg").text("Both fields are required.");
                    $("#error_div").removeClass("hidden");
                    resetButton();
                    return;
                }

                $.ajax({
                    url: "include/authenticate.php",
                    method: "POST",
                    dataType: "json",
                    data: { email: email, password: password },
                    success: function (response) {
                        console.log(response);
                        if (response.status === "error") {
                            $("#error_msg").text(response.message);
                            $("#error_div").removeClass("hidden");
                            resetButton();
                        } else if (response.status === "success") {
                            window.scrollTo(0, 0);
                            location.reload();
                        }
                    },
                    error: function () {
                        $("#error_msg").text("An unexpected error occurred.");
                        $("#error_div").removeClass("hidden");
                        resetButton();
                    }
                });
            });


            $("#closeModal").on("click", function () {
                $("#successModal").fadeOut(300);
            });
        });



        feather.replace();

        VANTA.WAVES({
            el: "#flood-animation",
            mouseControls: true,
            touchControls: true,
            gyroControls: true,
            minHeight: 200.00,
            minWidth: 200.00,
            scale: 1.00,
            scaleMobile: 1.00,
            color: 0x0044b3,      // wave color
            shininess: 40.00,
            waveHeight: 20.00,
            waveSpeed: 1.2,
            zoom: 0.85
        })

        // Initialize AOS and Feather Icons
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            feather.replace();
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });


        function openLoginModal() {
            $('#login_modal').removeClass('invisible');
        }

        function hideLoginModal() {

            $('#login_modal').addClass('invisible');
            $('#s_email').val('');
            $('#password').val('');
            $("#error_div").addClass("hidden");

        }

        function resetButton() {
            $("#login_btn").attr("disabled", false);
            $("#s_spinner").addClass("hidden");
            $("#login_text").removeClass("hidden");
        }

        $(document).on('click', function (event) {
            if (!$(event.target).closest('#login_modal_main').length && $(event.target).closest('#login_modal').length) {
                hideLoginModal();
            }
        })

    </script>
</body>

</html>