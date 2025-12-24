<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sütun Seçimi - Smart Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-glass: rgba(30, 41, 59, 0.7);
            --border-glass: rgba(255, 255, 255, 0.1);
            --accent-glow: #6366f1;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --success-glow: #10b981;
            --warning-glow: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg-color);
            color: var(--text-primary);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background */
        .background {
            position: fixed;
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
            opacity: 0.3;
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
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .container {
            position: relative;
            z-index: 1;
            padding: 3rem 1rem;
        }

        .main-card {
            background: var(--card-glass);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border-glass);
            border-radius: 32px;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header-section h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }

        .header-section p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            font-weight: 300;
        }

        .alert {
            border-radius: 16px;
            border: none;
            backdrop-filter: blur(10px);
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-10px);
            }
            75% {
                transform: translateX(10px);
            }
        }

        .select-all-btn {
            background: linear-gradient(135deg, var(--accent-glow), #8b5cf6);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .select-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .accordion {
            --bs-accordion-bg: transparent;
            --bs-accordion-border-color: var(--border-glass);
        }

        .accordion-item {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid var(--border-glass);
            border-radius: 16px !important;
            margin-bottom: 1rem;
            overflow: hidden;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .accordion-item:hover {
            border-color: var(--accent-glow);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.2);
        }

        .accordion-button {
            background: rgba(30, 41, 59, 0.7);
            color: var(--text-primary);
            font-weight: 600;
            border: none;
            padding: 1.25rem;
            font-size: 1.1rem;
        }

        .accordion-button:not(.collapsed) {
            background: rgba(99, 102, 241, 0.2);
            color: var(--text-primary);
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }

        .accordion-body {
            padding: 1.5rem;
        }

        .form-check {
            padding: 1rem;
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            background: rgba(15, 23, 42, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .form-check:hover {
            background: rgba(99, 102, 241, 0.1);
            border-color: var(--accent-glow);
            transform: translateX(5px);
        }

        .form-check-input {
            width: 1.5rem;
            height: 1.5rem;
            margin-top: 0.25rem;
            background-color: rgba(51, 65, 85, 0.8);
            border: 2px solid var(--border-glass);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background: linear-gradient(135deg, var(--accent-glow), #8b5cf6);
            border-color: var(--accent-glow);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);
        }

        .form-check-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .form-check-label {
            color: var(--text-primary);
            font-weight: 500;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding-left: 0.5rem;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .badge-number {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success-glow);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-text {
            background: rgba(148, 163, 184, 0.2);
            color: var(--text-secondary);
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        .badge-date {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning-glow);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent-glow), #8b5cf6);
            border: none;
            color: white;
            padding: 16px 48px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -10px rgba(99, 102, 241, 0.5);
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
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

        .btn-submit:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -10px rgba(99, 102, 241, 0.6);
        }

        .btn-submit span {
            position: relative;
            z-index: 1;
        }

        .btn-cancel {
            background: rgba(51, 65, 85, 0.5);
            border: 1px solid var(--border-glass);
            color: var(--text-secondary);
            padding: 16px 48px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: rgba(51, 65, 85, 0.8);
            border-color: var(--text-secondary);
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        .selection-counter {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--card-glass);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border-glass);
            border-radius: 50px;
            padding: 1rem 2rem;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: slideUp 0.5s ease;
        }

        .selection-counter strong {
            color: var(--accent-glow);
            font-size: 1.2rem;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

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

        @media (max-width: 768px) {
            .main-card {
                padding: 2rem 1.5rem;
            }

            .header-section h2 {
                font-size: 2rem;
            }

            .selection-counter {
                bottom: 1rem;
                right: 1rem;
                padding: 0.75rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="background">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="main-card">
                    <div class="header-section">
                        <h2><i class="fas fa-columns"></i> Veri Seçimi</h2>
                        <p>Analiz etmek istediğiniz sütunları seçin. Sadece sayısal veriler analiz edilebilir.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('processAnalysis') }}" method="POST" id="analysisForm">
                        @csrf
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-secondary">
                                <span class="fw-bold fs-5"><i class="fas fa-list-check"></i> Bulunan Başlıklar</span>
                                <button type="button" class="select-all-btn" id="selectAll">
                                    <i class="fas fa-check-double"></i> Tümünü Seç/Kaldır
                                </button>
                            </div>

                            @php
                                $groupedHeaders = [];
                                foreach($headers as $header => $type) {
                                    $parts = explode(' - ', $header, 2);
                                    $sheet = count($parts) > 1 ? $parts[0] : 'Diğer';
                                    $colName = count($parts) > 1 ? $parts[1] : $header;
                                    $groupedHeaders[$sheet][] = ['full' => $header, 'name' => $colName, 'type' => $type];
                                }
                            @endphp

                            <div class="accordion" id="sheetsAccordion">
                                @foreach($groupedHeaders as $sheetName => $columns)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $loop->index }}">
                                                <i class="fas fa-file-excel me-2"></i> {{ $sheetName }} <span class="badge bg-secondary ms-2">{{ count($columns) }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#sheetsAccordion">
                                            <div class="accordion-body">
                                                <div class="row g-3">
                                                    @foreach($columns as $col)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $col['full'] }}" id="col_{{ Str::slug($col['full']) }}" 
                                                                    {{ $col['type'] === 'Sayısal' ? 'checked' : '' }} 
                                                                    {{ $col['type'] !== 'Sayısal' ? 'disabled' : '' }}
                                                                    onchange="updateCounter()">
                                                                <label class="form-check-label" for="col_{{ Str::slug($col['full']) }}">
                                                                    <span>{{ $col['name'] }}</span>
                                                                    @if($col['type'] === 'Sayısal')
                                                                        <span class="badge badge-number"><i class="fas fa-calculator"></i> Sayısal</span>
                                                                    @elseif($col['type'] === 'Tarih/Zaman 📅')
                                                                        <span class="badge badge-date"><i class="fas fa-calendar"></i> Tarih</span>
                                                                    @else
                                                                        <span class="badge badge-text"><i class="fas fa-font"></i> Metin</span>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn-submit">
                                <span><i class="fas fa-rocket"></i> Analizi Başlat</span>
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-cancel text-center">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="selection-counter" id="selectionCounter" style="display: none;">
        <i class="fas fa-check-circle"></i> <strong id="selectedCount">0</strong> sütun seçildi
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="loading-spinner mb-3"></div>
            <p class="text-white">Analiz hazırlanıyor...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateCounter() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked:not(:disabled)');
            const count = checkboxes.length;
            const counter = document.getElementById('selectionCounter');
            const countElement = document.getElementById('selectedCount');
            
            countElement.textContent = count;
            
            if (count > 0) {
                counter.style.display = 'block';
            } else {
                counter.style.display = 'none';
            }
        }

        document.getElementById('selectAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(:disabled)');
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            checkboxes.forEach(c => {
                c.checked = !allChecked;
            });
            updateCounter();
        });

        document.getElementById('analysisForm').addEventListener('submit', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked:not(:disabled)');
            if (checkboxes.length === 0) {
                event.preventDefault();
                alert('Lütfen en az bir sütun seçin!');
                return false;
            }
            document.getElementById('loadingOverlay').style.display = 'flex';
        });

        // Initialize counter
        updateCounter();

        // Add smooth scroll behavior
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', function() {
                setTimeout(() => {
                    this.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 300);
            });
        });
    </script>
</body>
</html>
