<?php
require_once __DIR__ . '/includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (is_post()) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    if ($name && $email && $message) {
        insert('contact_messages', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        flash('success', 'Thank you for reaching out. We will get back to you shortly.');
        redirect('/cygnetclinics.com/index.php#contact');
    } else {
        flash('error', 'Please fill in the required fields.');
        redirect('/cygnetclinics.com/index.php#contact');
    }
}

$success = flash('success');
$error = flash('error');

$banners = fetch_all('banners');
$about = fetch_all('about_sections');
$services = fetch_all('services');
$blogs = fetch_all('blog_posts');
$contact = fetch_all('contact_details');
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<section class="hero" id="home">
    <div class="container">
        <div class="slides">
            <?php foreach ($banners as $index => $banner): ?>
                <div class="hero-slide<?php echo $index === 0 ? ' active' : ''; ?>" style="background-image: url('<?php echo $banner['image_url']; ?>');">
                    <div class="overlay">
                        <h1><?php echo $banner['title']; ?></h1>
                        <p><?php echo $banner['subtitle']; ?></p>
                        <?php if (!empty($banner['cta_label'])): ?>
                            <a class="btn btn-primary" href="<?php echo $banner['cta_link']; ?>"><?php echo $banner['cta_label']; ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($banners)): ?>
                <div class="hero-slide active" style="background-image: linear-gradient(135deg, #0e7490, #0f172a);">
                    <div class="overlay">
                        <h1>Welcome to Cygnet Clinics</h1>
                        <p>Personalised healthcare, modern facilities and compassionate teams serving our community.</p>
                        <a class="btn btn-primary" href="#contact">Book an appointment</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" id="about">
    <div class="container">
        <h2 class="section-title">About Cygnet Clinics</h2>
        <div class="about-grid">
            <?php foreach ($about as $card): ?>
                <div class="about-card">
                    <h3><?php echo $card['title']; ?></h3>
                    <p><?php echo nl2br($card['content']); ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (empty($about)): ?>
                <div class="about-card">
                    <h3>Trusted, patient-first healthcare</h3>
                    <p>Our multi-disciplinary team provides general medicine, specialist clinics, diagnostics, pharmacy and laboratory services under one roof. We combine state-of-the-art technology with a warm and caring environment.</p>
                </div>
                <div class="about-card">
                    <h3>Accessible and transparent</h3>
                    <p>With extended hours, same-day appointments and transparent billing we make it easy for families to receive care when they need it.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" id="services">
    <div class="container">
        <h2 class="section-title">Comprehensive Services</h2>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <h3><?php echo $service['name']; ?></h3>
                    <p><?php echo nl2br($service['description']); ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (empty($services)): ?>
                <div class="service-card">
                    <h3>General Practice</h3>
                    <p>Family medicine consultations, preventive care, chronic disease management and urgent care.</p>
                </div>
                <div class="service-card">
                    <h3>Diagnostics & Laboratory</h3>
                    <p>Full-service diagnostic imaging, pathology, and screening tests with rapid turnaround times.</p>
                </div>
                <div class="service-card">
                    <h3>Pharmacy & Wellness</h3>
                    <p>On-site pharmacy, vaccination clinic, and personalized wellness programs.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" id="blog">
    <div class="container">
        <h2 class="section-title">Insights from our clinicians</h2>
        <div class="blog-grid">
            <?php foreach ($blogs as $blog): ?>
                <div class="blog-card">
                    <h3><?php echo $blog['title']; ?></h3>
                    <p><?php echo nl2br(substr($blog['content'], 0, 200)); ?>...</p>
                    <p><small>Published on <?php echo date('d M Y', strtotime($blog['published_at'])); ?></small></p>
                </div>
            <?php endforeach; ?>
            <?php if (empty($blogs)): ?>
                <div class="blog-card">
                    <h3>Healthy habits for life</h3>
                    <p>Discover simple daily habits recommended by our physicians to enhance your wellbeing and reduce the risk of chronic illness.</p>
                </div>
                <div class="blog-card">
                    <h3>Preparing for your annual check-up</h3>
                    <p>Learn how to get the most from your consultation with tailored checklists and advice from our family medicine team.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" id="contact">
    <div class="container">
        <div class="contact-section">
            <h2>Contact & Appointments</h2>
            <?php if ($success): ?>
                <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error" data-timeout="5000"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="about-grid">
                <div>
                    <h3>Get in touch</h3>
                    <?php foreach ($contact as $detail): ?>
                        <p><strong><?php echo $detail['label']; ?>:</strong> <?php echo $detail['value']; ?></p>
                    <?php endforeach; ?>
                    <?php if (empty($contact)): ?>
                        <p><strong>Call:</strong> +91 98765 43210</p>
                        <p><strong>Email:</strong> care@cygnetclinics.com</p>
                        <p><strong>Address:</strong> 100 Healthcare Avenue, Bengaluru</p>
                    <?php endif; ?>
                </div>
                <div>
                    <h3>Request a call back</h3>
                    <form method="post">
                        <input type="text" name="name" placeholder="Your full name" required>
                        <input type="email" name="email" placeholder="Email address" required>
                        <input type="tel" name="phone" placeholder="Phone number">
                        <textarea name="message" placeholder="How can we help?" rows="4" required></textarea>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
