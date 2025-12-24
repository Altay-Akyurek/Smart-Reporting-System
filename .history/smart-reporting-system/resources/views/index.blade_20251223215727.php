<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Analytics - Masterpiece</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-glass: rgba(255, 255, 255, 0.03);
            --border-glass: rgba(255, 255, 255, 0.08);
            --accent-glow: #6366f1;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
        }

        body {
            margin: 0;
            overflow: hidden;
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Animated Background */
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 50% 50%, #1e293b 0%, #0f172a 100%);
            overflow: hidden;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: float 20s infinite ease-in-out;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: #4f46e5;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: #ec4899;
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 250px;
            height: 250px;
            background: #06b6d4;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .main-card {
            background: var(--card-glass);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border-glass);
            border-radius: 32px;
            padding: 4rem;
            width: 100%;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Subtle Glow Border Animation */
        .main-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 200%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: skewX(-25deg);
            animation: shine 6s infinite;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes shine {
            0% {
                left: -100%;
            }

            20% {
                left: 200%;
            }

            100% {
                left: 200%;
            }
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }

        p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 3rem;
            font-weight: 300;
        }

        .upload-area {
            border: 2px dashed rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 3rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            background: rgba(0, 0, 0, 0.2);
        }

        .upload-area:hover,
        .upload-area.active {
            border-color: var(--accent-glow);
            background: rgba(99, 102, 241, 0.1);
            transform: scale(1.02);
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.2);
        }

        .upload-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--accent-glow);
            text-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
        }

        .btn-magic {
            margin-top: 2.5rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            padding: 16px 48px;
            border-radius: 99px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -10px rgba(99, 102, 241, 0.5);
            position: relative;
            overflow: hidden;
        }

        .btn-magic::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-magic:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-magic:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -10px rgba(99, 102, 241, 0.6);
            filter: brightness(1.1);
        }

        .btn-magic span {
            position: relative;
            z-index: 1;
        }

        .file-info {
            height: 24px;
            margin-top: 15px;
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: #4ade80;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .file-info.show {
            opacity: 1;
            transform: translateY(0);
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(10px);
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(99, 102, 241, 0.2);
            border-top-color: var(--accent-glow);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Upload Area Enhancements */
        .upload-area {
            position: relative;
        }

        .upload-area::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .upload-area.active::after {
            width: 200px;
            height: 200px;
        }
    </style>
</head>

<body>
    <div class="background">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="main-card">
        <h1>Analitik Raporlama </h1>
        <p>Verilerinizi sanata dönüştüren yapay zeka deneyimi.</p>

        <form action="{{ route('uploadFile') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label class="upload-area" for="fileInput">
                <div class="upload-icon">✦</div>
                <div style="font-size: 1.2rem; font-weight: 500; color: #fff;">Dosyayı Sürükleyin</div>
                <div style="color: #64748b; font-size: 0.9rem; margin-top: 5px;">Excel veya CSV</div>
            </label>
            <input type="file" id="fileInput" name="file" accept=".xlsx, .csv" required onchange="handleFile(this)">

            <div id="fileName" class="file-info">
                <i class="fas fa-check-circle"></i>
                <span id="fileNameText"></span>
            </div>

            <button type="submit" class="btn-magic">
                <span>Analizi Başlat ✨</span>
            </button>
        </form>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="loading-spinner mb-3"></div>
            <p class="text-white">Dosya yükleniyor...</p>
        </div>
    </div>

    <script>
        function handleFile(input) {
            const fileName = input.files[0] ? input.files[0].name : '';
            const el = document.getElementById('fileName');
            const textEl = document.getElementById('fileNameText');

            if (fileName) {
                textEl.textContent = "Seçilen: " + fileName;
                el.classList.add('show');
                document.querySelector('.upload-area').classList.add('active');
            } else {
                el.classList.remove('show');
                document.querySelector('.upload-area').classList.remove('active');
            }
        }

        // Form submit loading
        document.querySelector('form').addEventListener('submit', function () {
            document.getElementById('loadingOverlay').style.display = 'flex';
        });

        // Smooth scroll on page load
        window.addEventListener('load', function () {
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.5s';
                document.body.style.opacity = '1';
            }, 100);
        });
    </script>
</body>

</html>