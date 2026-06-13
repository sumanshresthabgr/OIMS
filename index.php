<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OIMS - (Online Invitation Management System)</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@200;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Base Configuration Hooks */
        :root {
            --transition-speed: 0.5s;
        }

        /* --- PREMIUM DARK AESTHETIC STYLE CONFIG (Midnight Cyber Slate - DEFAULT) --- */
        body.dark-mode {
            background: linear-gradient(-45deg, #090d16, #0f172a, #1e1b4b, #0369a1) !important;
            background-size: 400% 400% !important;
            animation: aestheticGradient 18s ease infinite !important;
            color: #f4f4f5 !important;
        }
        body.dark-mode .card.dark-glass-card {
            background: rgba(15, 23, 42, 0.45) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
            border: 1px solid rgba(255, 255, 255, 0.07) !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45) !important;
        }
        body.dark-mode .dark-main-title { color: #ffffff !important; }
        body.dark-mode .title-gradient {
            background: linear-gradient(135deg, #38bdf8, #818cf8) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }
        body.dark-mode .dark-accent-line { background: linear-gradient(90deg, transparent, #38bdf8, transparent) !important; height: 1px; margin: 20px auto; width: 150px; opacity: 0.6; }
        body.dark-mode .dark-lead-text { color: #cbd5e1 !important; }
        body.dark-mode .dark-feature-box {
            background: rgba(255, 255, 255, 0.02) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        body.dark-mode .dark-feature-box h4 { color: #f1f5f9 !important; }
        body.dark-mode .dark-feature-box p { color: #94a3b8 !important; }
        body.dark-mode .index-header-logo { color: #ffffff !important; }
        body.dark-mode .index-header-link { color: #94a3b8 !important; }
        body.dark-mode .index-header-link.link-highlight { background: rgba(56, 189, 248, 0.1) !important; color: #38bdf8 !important; border: 1px solid rgba(56, 189, 248, 0.2); }
        body.dark-mode .dark-premium-cta { background: linear-gradient(135deg, #0284c7, #4f46e5) !important; color: #ffffff !important; border: none; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3); }
        body.dark-mode .main-footer { color: #64748b !important; }

        /* --- REFINED DARKER LIGHT AESTHETIC STYLE CONFIG (Cosmic Obsidian & Deep Velvet) --- */
        body.light-mode {
            background: linear-gradient(-45deg, #070a12, #111625, #1c1936, #0b132b) !important;
            background-size: 400% 400% !important;
            animation: aestheticGradient 18s ease infinite !important;
            color: #e2e8f0 !important;
        }
        body.light-mode .card.dark-glass-card {
            background: rgba(10, 15, 30, 0.75) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.04) !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6) !important;
        }
        body.light-mode .dark-main-title { color: #ffffff !important; }
        body.light-mode .title-gradient {
            background: linear-gradient(135deg, #a78bfa, #f472b6) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }
        body.light-mode .dark-accent-line { background: linear-gradient(90deg, transparent, #f472b6, transparent) !important; height: 1px; margin: 20px auto; width: 150px; opacity: 0.5; }
        body.light-mode .dark-lead-text { color: #94a3b8 !important; }
        body.light-mode .dark-feature-box {
            background: rgba(255, 255, 255, 0.01) !important;
            border: 1px solid rgba(255, 255, 255, 0.03) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
        }
        body.light-mode .dark-feature-box h4 { color: #f8fafc !important; }
        body.light-mode .dark-feature-box p { color: #64748b !important; }
        body.light-mode .index-header-logo { color: #ffffff !important; }
        body.light-mode .index-header-link { color: #94a3b8 !important; }
        body.light-mode .index-header-link.link-highlight { background: rgba(167, 139, 250, 0.1) !important; color: #a78bfa !important; border: 1px solid rgba(167, 139, 250, 0.2); }
        body.light-mode .dark-premium-cta { background: linear-gradient(135deg, #7c3aed, #db2777) !important; color: #ffffff !important; border: none; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3); }
        body.light-mode .main-footer { color: #475569 !important; }

        /* Universal Cosmic Flow Gradient Animation Loop */
        @keyframes aestheticGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Toggle Button Interface Layout */
        .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 8px 16px;
            border-radius: 30px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            margin-right: 15px;
            color: inherit;
            letter-spacing: 0.3px;
        }
        .theme-toggle-btn:hover {
            transform: translateY(-1px);
        }
        body.dark-mode .theme-toggle-btn:hover { background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.25); }
        body.light-mode .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        body.light-mode .theme-toggle-btn:hover { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.18); }

        /* Structure Safety Controllers */
        html, body {
            max-width: 100% !important;
            overflow-x: hidden !important;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color var(--transition-speed) ease, color var(--transition-speed) ease;
        }
        *, *::before, *::after { box-sizing: border-box !important; }

        .dark-main-title, .dark-lead-text, .feature-item p, .feature-item h4 {
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
        }

        @media (max-width: 768px) {
            .index-center-wrapper {
                padding: 20px 15px !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                width: 100% !important;
            }
            .public-view-container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }
            .card.dark-glass-card { width: 100% !important; max-width: 100% !important; }
            .card-body { padding: 40px 20px !important; }
            .dark-main-title { font-size: clamp(1.6rem, 6.5vw, 2.5rem) !important; line-height: 1.25 !important; }
            .dark-lead-text { font-size: clamp(0.9rem, 3.5vw, 1.05rem) !important; line-height: 1.6 !important; }
            .landing-feature-grid { display: flex !important; flex-direction: column !important; gap: 15px !important; }
            .index-header-container { padding: 20px 15px !important; flex-direction: column !important; gap: 15px !important; text-align: center !important; }
            .main-footer { padding: 25px !important; text-align: center !important; font-size: 0.75rem !important; }
            .theme-toggle-btn { margin-right: 0; margin-bottom: 5px; }
        }
    </style>
</head>
<body class="dark-editorial-bg dark-mode">

<header class="index-minimal-header animate-fade-in">
    <div class="index-header-container">
        <a class="index-header-logo" href="index.php"> OIMS <span style="font-weight:300; opacity:0.6;">ENGINE</span></a>
        <div style="display: inline-flex; align-items: center; flex-wrap: wrap; justify-content: center; gap: 5px;">
            <button id="themeToggle" class="theme-toggle-btn" aria-label="Toggle Theme">
                <span id="themeIcon"></span> <span id="themeText">Dark Mode</span>
            </button>
            <a class="index-header-link" href="admin/login.php">Log-in</a>
            <a class="index-header-link link-highlight" href="admin/register.php">Sign-Up</a>
        </div>
    </div>
</header>

<div class="index-center-wrapper">
    <div class="public-view-container animate-fade-in" style="max-width: 850px; margin: 0;">
        <div class="card dark-glass-card">
            <div class="card-body" style="padding: 60px 50px; text-align: center;">
                
                <h1 class="dark-main-title">
                    Online Invitation <br><span class="title-gradient">Management System</span>
                </h1>
                
                <div class="dark-accent-line"></div>
                
                <p class="dark-lead-text">
                    A frictionless architecture designed to completely drop traditional card invitation setup , bringing dynamic workflow engine directly to public web views inside an accelerated 5-second responsive sequence.
                </p>
                
                <div class="landing-feature-grid">
                    <div class="feature-item dark-feature-box"></div>
                    <div class="feature-item dark-feature-box">
                        <span class="feature-icon dark-icon"></span>
                        <div>
                            <h4>Aesthetic Themes</h4>
                            <p>Stunning spaces powered entirely by custom CSS Grid layouts.</p>
                        </div>
                    </div>
                    <div class="feature-item dark-feature-box">
                        <span class="feature-icon dark-icon"></span>
                        <div>
                            <h4>Secure URL Routing</h4>
                            <p>Isolated token allocation prevents structural data manipulation.</p>
                        </div>
                    </div>
                    <div class="feature-item dark-feature-box">
                        <span class="feature-icon dark-icon"></span>
                        <div>
                            <h4>Live Analytics</h4>
                            <p>Real-time submission trackers directly inside your host ledger.</p>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 55px;">
                    <a href="admin/login.php" class="btn btn-primary dark-premium-cta">
                       Let's Get Started &rarr;
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<footer class="main-footer" style="margin-top: auto; padding-bottom: 30px;">
    &copy; 2026 Dong-Eui University - Department of Intelligence Computing. OIMS project by FARS.
</footer>

<script>
    const themeToggleBtn = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');
    const bodyEl = document.body;

    // Load active preference, falling back cleanly to 'dark' as default
    const activeTheme = localStorage.getItem('oims-theme') || 'dark';
    
    if (activeTheme === 'light') {
        applyLightMode();
    } else {
        applyDarkMode();
    }

    themeToggleBtn.addEventListener('click', () => {
        if (bodyEl.classList.contains('dark-mode')) {
            applyLightMode();
        } else {
            applyDarkMode();
        }
    });

    function applyLightMode() {
        bodyEl.classList.remove('dark-mode');
        bodyEl.classList.add('light-mode');
       
        themeText.textContent = 'Light Mode';
        localStorage.setItem('oims-theme', 'light');
    }

    function applyDarkMode() {
        bodyEl.classList.remove('light-mode');
        bodyEl.classList.add('dark-mode');
        
        themeText.textContent = 'Dark Mode';
        localStorage.setItem('oims-theme', 'dark');
    }
</script>

</body>
</html>