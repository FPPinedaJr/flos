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
                        class="text-gray-400 hover:text-white transition duration-300"><i data-feather="github"></i></a>
                    <a target="_blank" href="mailto:fernandojr03.pineda@gmail.com"
                        class="text-gray-400 hover:text-white transition duration-300"><i data-feather="mail"></i></a>
                </div>
                <p class="text-gray-400 mt-4">flos.miceff.com</p>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
            <p>© 2025 FloS Project. All rights reserved.</p>
        </div>
    </div>
</footer>