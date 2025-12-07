<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>{{ company_name() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --calva-purple: #6a2e8d;
            --calva-light-purple: #b19cd9;
        }

        /* 🌄 Background Image */
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            background-image: url('{{ asset("assets/images/calvabg.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }

        /* White overlay for readability */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: -1;
        }

        /* Floating glassy section style */
        section {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            margin: 2rem auto;
            padding: 3rem 1.5rem;
            max-width: 1200px;
        }

        header {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(6px);
        }

        footer {
            background-color: rgba(106, 46, 141, 0.95);
        }

        /* 💊 Capsule float animation */
        #eyes-wrapper {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }
    </style>
</head>

<body class="scroll-smooth text-gray-800">
<?php
    send_email(
     to: 'marcniel_christian12@yahoo.com',
     subject: 'Important Notice',
     view: 'emails.notice',
     data: ['message' => 'Hello'],
     fromEmail: 'marcniel@dl-hosting.net',
     fromName: company_name()
    );
?>
    <!-- Navigation -->
    <header class="fixed top-0 left-0 w-full shadow-md z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center p-4">
            <!-- Logo + Brand -->
            <a href="#home" class="flex items-center space-x-3">
                <img src="{{ company_logo(true) }}" alt="{{ company_name() }} Logo"
                    class="border-4 border-purple-600 rounded-full w-10 h-10 object-contain">
                <h1 class="text-2xl font-extrabold" style="color: var(--calva-purple);">
                    {{ company_name() }}
                </h1>
            </a>

            <!-- Navigation Links -->
            <nav>
                <ul class="hidden md:flex space-x-8 font-semibold">
                    <li><a href="#home" class="hover:text-purple-700 transition">Home</a></li>
                    <li><a href="#about" class="hover:text-purple-700 transition">About</a></li>
                    <li><a href="#services" class="hover:text-purple-700 transition">Services</a></li>
                    <li><a href="#products" class="hover:text-purple-700 transition">Products</a></li>
                    <li><a href="#contact" class="hover:text-purple-700 transition">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- 💊 Capsule with Eyes -->
    <div id="eyes-wrapper"
        class="hidden md:flex fixed left-0 top-[10px] w-full justify-center items-center z-50 pointer-events-none">
        <div class="relative w-72 h-28 rounded-full border-4 shadow-lg flex justify-center items-center space-x-10 overflow-hidden"
            style="border-color: var(--calva-purple);
             background: linear-gradient(to right, var(--calva-purple) 0%, var(--calva-purple) 45%, var(--calva-light-purple) 55%, #f4eaff 100%);
             position: relative;">
            <div class="absolute inset-0 bg-gradient-to-b from-white/40 to-transparent rounded-full pointer-events-none"></div>

            <!-- Left Eye -->
            <div
                class="relative w-16 h-16 bg-white rounded-full border-2 border-gray-400 flex justify-center items-center overflow-hidden z-10">
                <div id="pupil-left"
                    class="absolute w-6 h-6 bg-gradient-to-br from-black to-gray-700 rounded-full shadow-md transition-transform duration-75 ease-out"></div>
            </div>

            <!-- Right Eye -->
            <div
                class="relative w-16 h-16 bg-white rounded-full border-2 border-gray-400 flex justify-center items-center overflow-hidden z-10">
                <div id="pupil-right"
                    class="absolute w-6 h-6 bg-gradient-to-br from-black to-gray-700 rounded-full shadow-md transition-transform duration-75 ease-out"></div>
            </div>
        </div>
    </div>

    <!-- Hero -->
    <section id="home"
        class="min-h-screen flex flex-col justify-center items-center text-center px-6 pt-24">
        <h2 class="text-5xl md:text-6xl font-extrabold mb-4" style="color: var(--calva-purple);">
            {{ company_name() }}
        </h2>
        <p class="text-xl md:text-2xl mb-8 font-semibold text-purple-700">
            Kaagapay sa Kalusugan
        </p>
        <a href="#products"
            class="inline-block bg-purple-700 text-white px-8 py-3 rounded shadow hover:bg-purple-800 transition">
            Explore Products
        </a>
    </section>

    <!-- About -->
    <section id="about" class="py-20 text-center">
        <h3 class="text-4xl font-bold mb-6" style="color: var(--calva-purple);">About Us</h3>
        <p class="max-w-3xl mx-auto text-lg text-gray-700">
            {{ company_name() }} is your trusted partner in health, providing quality medicines and compassionate care.
            We are committed to supporting your wellness journey every step of the way.
        </p>
    </section>

    <!-- Services -->
    <section id="services" class="py-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h3 class="text-4xl font-bold mb-12" style="color: var(--calva-purple);">Our Services</h3>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white/80 p-8 rounded-lg shadow hover:shadow-lg transition backdrop-blur-md">
                    <h4 class="text-2xl font-semibold mb-3" style="color: var(--calva-purple);">Prescription Medicines</h4>
                    <p class="text-gray-700">Wide range of prescription medications with expert pharmacist support.</p>
                </div>
                <div class="bg-white/80 p-8 rounded-lg shadow hover:shadow-lg transition backdrop-blur-md">
                    <h4 class="text-2xl font-semibold mb-3" style="color: var(--calva-purple);">Vaccination</h4>
                    <p class="text-gray-700">Seasonal and routine vaccinations for all ages, administered with care.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section id="products" class="py-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h3 class="text-4xl font-bold mb-12" style="color: var(--calva-purple);">Our Products</h3>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-purple-100/80 p-8 rounded-lg shadow hover:shadow-lg transition backdrop-blur-md">
                    <h4 class="text-2xl font-semibold mb-3" style="color: var(--calva-purple);">Vitamins & Supplements</h4>
                    <p class="text-gray-700">High-quality vitamins to support your daily health and wellness.</p>
                </div>
                <div class="bg-purple-100/80 p-8 rounded-lg shadow hover:shadow-lg transition backdrop-blur-md">
                    <h4 class="text-2xl font-semibold mb-3" style="color: var(--calva-purple);">Personal Care</h4>
                    <p class="text-gray-700">Skincare, hygiene, and personal care products for the whole family.</p>
                </div>
                <div class="bg-purple-100/80 p-8 rounded-lg shadow hover:shadow-lg transition backdrop-blur-md">
                    <h4 class="text-2xl font-semibold mb-3" style="color: var(--calva-purple);">Medical Equipment</h4>
                    <p class="text-gray-700">Essential medical devices and equipment for home or clinical use.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="py-20">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h3 class="text-4xl font-bold mb-6" style="color: var(--calva-purple);">Contact Us</h3>
            <p class="mb-6 text-lg text-gray-700">Have questions or need assistance? Reach out to us!</p>
            <form class="grid gap-6" novalidate>
                <input type="text" placeholder="Your Name" required
                    class="p-4 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-700" />
                <input type="email" placeholder="Your Email" required
                    class="p-4 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-700" />
                <textarea rows="5" placeholder="Your Message" required
                    class="p-4 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-700"></textarea>
                <button type="submit"
                    class="bg-purple-700 text-white py-4 rounded hover:bg-purple-800 transition font-semibold">
                    Send Message
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white text-center py-6 mt-12">
        &copy; <span id="current-year"></span> {{ company_name() }}. All rights reserved.
    </footer>

    <!-- Scripts -->
    <script>
        document.getElementById("current-year").textContent = new Date().getFullYear();

        document.addEventListener("DOMContentLoaded", () => {
            if (window.innerWidth < 768) return;
            const leftPupil = document.getElementById('pupil-left');
            const rightPupil = document.getElementById('pupil-right');
            const eyesWrapper = document.getElementById('eyes-wrapper');

            document.addEventListener('mousemove', (e) => {
                const rect = eyesWrapper.getBoundingClientRect();
                const leftEye = leftPupil.parentElement.getBoundingClientRect();
                const rightEye = rightPupil.parentElement.getBoundingClientRect();

                function movePupil(pupil, eyeCenterX, eyeCenterY) {
                    const dx = e.clientX - eyeCenterX;
                    const dy = e.clientY - eyeCenterY;
                    const angle = Math.atan2(dy, dx);
                    const distance = Math.min(Math.sqrt(dx * dx + dy * dy) / 40, 10);
                    const x = Math.cos(angle) * distance;
                    const y = Math.sin(angle) * distance;
                    pupil.style.transform = `translate(${x}px, ${y}px)`;
                }

                movePupil(leftPupil, leftEye.left + leftEye.width / 2, leftEye.top + leftEye.height / 2);
                movePupil(rightPupil, rightEye.left + rightEye.width / 2, rightEye.top + rightEye.height / 2);
            });
        });
    </script>

</body>

</html>