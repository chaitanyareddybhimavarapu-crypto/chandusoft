<?php
// Start the session
session_start();

// Include DB connection
require 'db.php';

// Get the page slug from the clean URL
$pageSlug = $_GET['page'] ?? null;  // No need for "index.php?page=..." after .htaccess

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chandusoft</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Optional Meta -->
    <meta name="description" content="Chandusoft delivers IT and BPO solutions with over 15 years of experience.">
</head>
<body>

    <!-- Header Include (Navbar) -->
    <?php include("header.php"); ?>

    <!-- Main Content -->
    <main>
        <?php
        // If a page slug is provided, fetch the page content from the database
        if ($pageSlug) {
            try {
                // Fetch the page from the database based on the slug
                $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND LOWER(status) = 'published'");
                $stmt->execute([$pageSlug]);
                $page = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($page) {
                    // Include the layout for the specific page (this layout will display the page content)
                    include("views/layout.php");  
                } else {
                    // If no page found, show 404 page
                    include("views/404.php");
                }
            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, 'error_log.txt');
                echo "An error occurred. Please try again later.";
            }
        } else {
            // If no page slug is provided, i.e., the homepage is being accessed
            ?>
            <!-- Hero Section for Homepage -->
            <section class="hero">
                <div class="hero-overlay">
                    <div class="hero-content">
                        <h2>Welcome to Chandusoft</h2>
                        <p>Delivering IT & BPO solutions for over 15 years.</p>
                        <a href="services.php" class="hero-btn">Explore Services</a>
                    </div>
                </div>
            </section>

            <!-- Testimonials Section -->
            <section class="testimonials">
                <h2 class="section-title">What Our Clients Say</h2>
                <div class="testimonial-container">
                    <div class="testimonial-card">
                        <p>"Chandusoft helped us streamline our processes. Their 24/7 support means we never miss a client query."</p>
                        <h4>John Smith</h4>
                        <span class="role">Operations Manager, GlobalTech</span>
                    </div>
                    <div class="testimonial-card">
                        <p>"Our e-commerce platform scaled smoothly after migrating with Chandusoft. Sales grew by 40% in just 6 months!"</p>
                        <h4>Priya Verma</h4>
                        <span class="role">Founder, TrendyMart</span>
                    </div>
                    <div class="testimonial-card">
                        <p>"The QA team at Chandusoft made our product launch seamless. Bug-free delivery on time!"</p>
                        <h4>Ahmed Khan</h4>
                        <span class="role">Product Lead, Medisoft</span>
                    </div>
                </div>
            </section>
            <?php
        }
        ?>
    </main>

    <!-- Footer Include -->
    <?php include("footer.php"); ?>

    <!-- Back to Top Button -->
    <button id="backToTop" title="Back to Top" aria-label="Scroll back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Back to Top Script -->
    <script>
        const backToTop = document.getElementById("backToTop");

        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTop.style.display = "block";
            } else {
                backToTop.style.display = "none";
            }
        });

        backToTop.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    </script>

</body>
</html>
