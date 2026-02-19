<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event App - Book Your Next Experience</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="landing-body">

<!-- NAVIGATION -->
<nav class="landing-nav">
    <div class="nav-container">
        <div class="nav-brand">
            <i class="fa-solid fa-ticket"></i>
            <span>EVENT APP</span>
        </div>
        <div class="nav-links">
            <a href="#home" class="nav-link">Home</a>
            <a href="#events" class="nav-link">Events</a>
            <a href="#features" class="nav-link">Features</a>
            <a href="#stats" class="nav-link">About</a>
            <a href="/login" class="btn-nav-login">Login</a>
            <a href="/register" class="btn-nav-register">Get Started</a>
        </div>
        <div class="nav-toggle" id="navToggle">
            <i class="fa-solid fa-bars"></i>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero" id="home">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-badge">
                <i class="fa-solid fa-sparkles"></i>
                <span>Welcome to Event App</span>
            </div>
            <h1 class="hero-title animate-fade-in">
                Discover Amazing <span class="gradient-text">Events</span>
            </h1>
            <p class="hero-subtitle animate-fade-in-delay">
                Book tickets for concerts, workshops, conferences and more. 
                Your next unforgettable experience is just a click away.
            </p>
            <div class="hero-buttons animate-fade-in-delay-2">
                <a href="/register" class="btn-hero-primary">
                    <i class="fa-solid fa-rocket"></i>
                    Start Exploring
                </a>
                <a href="#events" class="btn-hero-secondary">
                    <i class="fa-solid fa-ticket"></i>
                    Browse Events
                </a>
            </div>
            
            <!-- Trust Badges -->
            <div class="trust-badges">
                <div class="trust-item">
                    <i class="fa-solid fa-shield-check"></i>
                    <span>Secure Payment</span>
                </div>
                <div class="trust-item">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Instant Booking</span>
                </div>
                <div class="trust-item">
                    <i class="fa-solid fa-star"></i>
                    <span>Trusted Platform</span>
                </div>
            </div>
        </div>
        <div class="hero-image animate-slide-right">
            <div class="category-showcase">
                <div class="category-item">
                    <div class="category-icon">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <span class="category-name">Music Festivals</span>
                </div>
                <div class="category-item">
                    <div class="category-icon">
                        <i class="fa-solid fa-microphone"></i>
                    </div>
                    <span class="category-name">Conferences</span>
                </div>
                <div class="category-item">
                    <div class="category-icon">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <span class="category-name">Art & Culture</span>
                </div>
                <div class="category-item">
                    <div class="category-icon">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="category-name">Workshops</span>
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <i class="fa-solid fa-chevron-down"></i>
    </div>
</section>

<!-- STATS SECTION -->
<section class="stats-section" id="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="stat-number" data-target="<?= $totalEvents ?>">0</div>
                <div class="stat-label">Total Events</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-number" data-target="<?= $totalUsers ?>">0</div>
                <div class="stat-label">Happy Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div class="stat-number" data-target="<?= $totalTicketsSold ?>">0</div>
                <div class="stat-label">Tickets Sold</div>
            </div>
        </div>
    </div>
</section>

<!-- EVENTS SECTION -->
<section class="events-section" id="events">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Upcoming Events</h2>
            <p class="section-subtitle">Don't miss out on these amazing experiences</p>
        </div>

        <?php if (empty($upcomingEvents)): ?>
            <div class="no-events">
                <i class="fa-solid fa-calendar-xmark"></i>
                <p>No upcoming events at the moment. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <?php if (!empty($event['image']) && file_exists(FCPATH . 'uploads/events/' . $event['image'])): ?>
                                <img src="/uploads/events/<?= $event['image'] ?>" alt="<?= esc($event['title']) ?>">
                            <?php else: ?>
                                <div class="event-placeholder">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="event-date-badge">
                                <span class="date-day"><?= date('d', strtotime($event['event_date'])) ?></span>
                                <span class="date-month"><?= date('M', strtotime($event['event_date'])) ?></span>
                            </div>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?= esc($event['title']) ?></h3>
                            <p class="event-description"><?= esc(substr($event['description'], 0, 100)) ?>...</p>
                            <div class="event-meta">
                                <span class="event-meta-item">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?= esc($event['location']) ?>
                                </span>
                                <span class="event-meta-item">
                                    <i class="fa-solid fa-clock"></i>
                                    <?= date('H:i', strtotime($event['event_date'])) ?>
                                </span>
                            </div>
                            <div class="event-footer">
                                <div class="event-price">
                                    <?php if ($event['quota'] > 0): ?>
                                        <span class="price-label">Starting from</span>
                                        <span class="price-amount">Rp <?= number_format($event['price'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="sold-out">SOLD OUT</span>
                                    <?php endif; ?>
                                </div>
                                <a href="/login" class="btn-event">
                                    View Details
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="view-all-container">
                <a href="/login" class="btn-view-all">
                    View All Events
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="features-section" id="features">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Why Choose Us?</h2>
            <p class="section-subtitle">Everything you need for a seamless event experience</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 class="feature-title">Instant Booking</h3>
                <p class="feature-description">
                    Book your tickets instantly with our fast and secure checkout process
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="feature-title">Secure Payments</h3>
                <p class="feature-description">
                    Multiple payment options with bank-level security for your peace of mind
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <h3 class="feature-title">Mobile Tickets</h3>
                <p class="feature-description">
                    Get your tickets instantly via email. No printing required!
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h3 class="feature-title">24/7 Support</h3>
                <p class="feature-description">
                    Our support team is always ready to help you with any questions
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <h3 class="feature-title">Easy Refunds</h3>
                <p class="feature-description">
                    Changed your mind? Request a refund easily through our platform
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <h3 class="feature-title">Save Favorites</h3>
                <p class="feature-description">
                    Keep track of events you love and never miss an update
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Start Your Journey?</h2>
            <p class="cta-subtitle">Join thousands of event enthusiasts and book your next experience today</p>
            <div class="cta-buttons">
                <a href="/register" class="btn-cta-primary">
                    <i class="fa-solid fa-user-plus"></i>
                    Create Account
                </a>
                <a href="/login" class="btn-cta-secondary">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    Login Now
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="landing-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand">
                <i class="fa-solid fa-ticket"></i>
                <span>EVENT APP</span>
                <p>Your gateway to amazing events</p>
            </div>
            <div class="footer-links">
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <a href="#home">Home</a>
                    <a href="#events">Events</a>
                    <a href="#features">Features</a>
                    <a href="/login">Login</a>
                </div>
                <div class="footer-column">
                    <h4>Support</h4>
                    <a href="#">Help Center</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Contact Us</a>
                </div>
                <div class="footer-column">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Event App. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="/assets/js/landing.js"></script>

</body>
</html>
