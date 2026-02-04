<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Project | SPARK'26</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body style="padding-bottom: 80px;"> <!-- Padding for fixed action bar -->

    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <div style="width:30px; height:30px; background:var(--primary); border-radius:8px;"></div>
                SPARK <span>'26</span>
            </a>
            <div class="nav-menu">
                <span class="nav-link" style="color: var(--text-muted);">Review Mode</span>
            </div>
            <button onclick="history.back()" class="btn-outline">Back</button>
        </div>
    </nav>

    <!-- Header -->
    <header class="review-header">
        <div class="container">
            <span class="tag"
                style="background: var(--primary); color: white; border: none; margin-bottom: 1rem;">Pending
                Approval</span>
            <h1 style="font-size: 2.5rem; max-width: 800px;">Smart City Waste Management System using IOT</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-top: 0.5rem;">Submitted by Team "EcoWarriors"
                • Feb 4, 2026</p>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container section">
        <div class="review-card">

            <!-- Abstract -->
            <div class="review-section">
                <label class="r-label">Project Abstract</label>
                <div class="r-content">
                    <p>This project aims to revolutionize urban waste management by deploying smart sensors in public
                        trash bins. These sensors monitor fill-levels in real-time and communicate data to a central
                        cloud dashboard. Using AI route optimization algorithms, waste collection trucks are directed
                        only to bins that need emptying, reducing fuel consumption by an estimated 30% and operational
                        costs significantly.</p>
                </div>
            </div>

            <!-- Tech Stack -->
            <div class="review-section">
                <label class="r-label">Technologies Used</label>
                <div style="margin-top: 0.5rem;">
                    <span class="tag">Arduino / ESP32</span>
                    <span class="tag">LoRaWAN</span>
                    <span class="tag">Python (Flask)</span>
                    <span class="tag">Google Maps API</span>
                    <span class="tag">React Native</span>
                </div>
            </div>

            <!-- Team Details -->
            <div class="review-section">
                <label class="r-label">Team Members</label>
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <div style="padding: 1rem; background: var(--bg-surface); border-radius: var(--radius-md);">
                        <strong>Rahul V.</strong><br>
                        <span style="font-size:0.9rem; color:var(--text-muted);">Team Lead (Hardware)</span>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-surface); border-radius: var(--radius-md);">
                        <strong>Anita S.</strong><br>
                        <span style="font-size:0.9rem; color:var(--text-muted);">Backend Developer</span>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="review-section">
                <label class="r-label">Project Documents</label>
                <div style="margin-top: 1rem;">
                    <button class="btn-outline" style="margin-right: 1rem;"><i class="ri-file-pdf-line"></i>
                        Synopsis.pdf</button>
                    <button class="btn-outline"><i class="ri-image-line"></i> Architecture_Diagram.png</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Sticky Action Bar -->
    <div class="action-bar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 0.9rem; color: var(--text-muted);">
                Reviewing as <strong>Class Advisor</strong>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button class="btn-outline" style="border-color: #ef4444; color: #ef4444;">Request Changes</button>
                <button class="btn-primary" style="background: #10b981;">Approve Project</button>
            </div>
        </div>
    </div>

</body>

</html>