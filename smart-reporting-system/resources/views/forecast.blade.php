<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Analytics - Tahmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-glass: rgba(30, 41, 59, 0.7);
            --border-glass: rgba(255, 255, 255, 0.1);
            --active-glow: #f59e0b; /* Amber for Forecast */
            --text-primary: #ffffff;
            --text-mute: #94a3b8;
        }

        /* Print Mode */
        body.print-mode { background-color: #0f172a !important; }
        body.print-mode .gl-card {
            backdrop-filter: none !important;
            background: #1e293b !important;
            box-shadow: none !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
        }
        body.print-mode .reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
        body.print-mode .btn-glow, body.print-mode .sidebar { display: none !important; }
        body.print-mode .main-content { margin: 0 !important; padding: 20px !important; width: 100% !important; }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: 80px;
            background: rgba(15, 23, 42, 0.95);
            border-right: 1px solid var(--border-glass);
            display: flex; flex-direction: column; align-items: center; padding-top: 2rem; z-index: 100;
        }
        .nav-item {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem; cursor: pointer; transition: all 0.3s;
            color: var(--text-mute); font-size: 1.2rem;
            text-decoration: none;
        }
        .nav-item:hover, .nav-item.active {
            background: var(--active-glow); color: #fff;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.5);
        }

        .main-content { margin-left: 80px; padding: 3rem; }

        .gl-card {
            background: var(--card-glass);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            padding: 2rem;
            backdrop-filter: blur(12px);
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.3);
        }

        .btn-glow {
            background: var(--active-glow); color: #fff;
            border: none; padding: 10px 25px; border-radius: 50px;
            font-weight: 600; transition: 0.3s; text-decoration: none;
        }
        .btn-glow:hover { box-shadow: 0 0 20px rgba(245, 158, 11, 0.6); color: #fff; }

        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease; }
        .reveal.active { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>

    <nav class="sidebar">
        <a href="{{ route('home') }}" class="nav-item mb-auto" title="Geri">⬅️</a>
        <div class="nav-item active">📈</div>
    </nav>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5 reveal active">
            <div>
                <h1 class="display-4">Gelecek Tahmin Raporu</h1>
                <p class="text-mute">AI Destekli Satış Öncesi Analizi</p>
            </div>
            <button onclick="downloadPDF()" class="btn-glow">📥 Raporu İndir</button>
        </div>

        <section class="reveal active">
            <div class="gl-card">
                <h3 class="mb-4">📊 12 Aylık Projeksiyon</h3>
                <div style="position: relative; height: 400px; width: 100%;">
                    <canvas id="forecastChart"></canvas>
                </div>
            </div>
        </section>

        <section class="reveal">
            <div class="gl-card">
                 <h3 class="mb-4">📋 Detaylı Stok & Talep Verileri</h3>
                 <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0" style="--bs-table-bg: transparent;">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th class="text-end">Mevcut Talep</th>
                                <th class="text-end">Gelecek Ay Tahmini</th>
                                <th class="text-center">Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($forecastResults as $result)
                            @php 
                                $trend = $result['forecasted_demand'] - $result['current_demand'];
                                $trendColor = $trend > 0 ? 'text-success' : 'text-danger';
                                $icon = $trend > 0 ? '↗️' : '↘️';
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ $result['product'] }}</td>
                                <td class="text-end">{{ $result['current_demand'] }}</td>
                                <td class="text-end fw-bold {{ $trendColor }}">{{ $result['forecasted_demand'] }}</td>
                                <td class="text-center">{{ $icon }} {{ number_format(abs($trend), 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                 </div>
            </div>
        </section>
    </div>

    <script>
        // Animations
        window.addEventListener('scroll', () => {
            document.querySelectorAll('.reveal').forEach(el => {
                if(el.getBoundingClientRect().top < window.innerHeight - 150) el.classList.add('active');
            });
        });

        // Chart
        const ctx = document.getElementById('forecastChart');
        if(ctx) {
            const chartData = @json($forecastResults);
            const labels = chartData.map(r => r.product);
            const current = chartData.map(r => r.current_demand);
            const forecast = chartData.map(r => r.forecasted_demand);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Mevcut Dönem',
                            data: current,
                            backgroundColor: 'rgba(148, 163, 184, 0.5)',
                            borderColor: '#94a3b8',
                            borderWidth: 1,
                            borderRadius: 6
                        },
                        {
                            label: 'Gelecek Tahmin',
                            data: forecast,
                            backgroundColor: 'rgba(245, 158, 11, 0.8)',
                            borderColor: '#f59e0b',
                            borderWidth: 1,
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: '#fff' } } },
                    scales: {
                        y: { grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#cbd5e1' } },
                        x: { grid: { display: false }, ticks: { color: '#cbd5e1' } }
                    }
                }
            });
        }

        // PDF Export Logic (Premium Component-Based)
        async function downloadPDF() {
            const { jsPDF } = window.jspdf;
            document.body.classList.add('print-mode');
            document.body.style.cursor = 'wait';
            await new Promise(r => setTimeout(r, 500));

            const pdf = new jsPDF('p', 'mm', 'a4');
            const pdfW = pdf.internal.pageSize.getWidth();
            const pdfH = pdf.internal.pageSize.getHeight();
            const margin = 10;
            let cursorY = margin;

            const elements = [
                document.querySelector('.main-content > .d-flex'),
                ...Array.from(document.querySelectorAll('section'))
            ];

            try {
                for(let el of elements) {
                    if(!el) continue;
                    
                    // Style override for capture
                    const origM = el.style.margin;
                    el.style.margin = '0';
                    el.style.padding = '20px';
                    el.style.background = '#1e293b';
                    el.style.borderRadius = '12px';

                    const canvas = await html2canvas(el, {
                        scale: 1.5, useCORS: true, backgroundColor: '#1e293b', logging: false
                    });

                    // Restore
                    el.style.margin = origM;
                    el.style.padding = '';
                    el.style.background = '';
                    el.style.borderRadius = '';

                    const imgData = canvas.toDataURL('image/png');
                    const imgH = (canvas.height * (pdfW - 2*margin)) / canvas.width;

                    if(cursorY + imgH > pdfH - margin) {
                        pdf.addPage();
                        cursorY = margin;
                    }
                    pdf.addImage(imgData, 'PNG', margin, cursorY, pdfW - 2*margin, imgH);
                    cursorY += imgH + 10;
                }
                pdf.save('gelecek-tahmin-raporu.pdf');
            } catch(e) {
                alert('PDF Hatası: ' + e.message);
            } finally {
                document.body.classList.remove('print-mode');
                document.body.style.cursor = 'default';
            }
        }
    </script>
</body>
</html>