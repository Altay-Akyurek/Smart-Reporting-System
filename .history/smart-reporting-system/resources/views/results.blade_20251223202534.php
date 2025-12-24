<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Analytics - Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- PDF Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-glass: rgba(30, 41, 59, 0.7);
            --border-glass: rgba(255, 255, 255, 0.1);
            --active-glow: #6366f1;
            --text-primary: #ffffff;
            --text-mute: #94a3b8;
        }

        /* Print Mode Optimization for PDF Export */
        body.print-mode {
            background-color: #0f172a !important; /* Force dark bg */
        }
        body.print-mode .gl-card {
            backdrop-filter: none !important;
            background: #1e293b !important;
            box-shadow: none !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
        }
        body.print-mode .reveal {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
        body.print-mode .btn-glow, 
        body.print-mode .sidebar {
            display: none !important; /* Hide UI elements */
        }
        body.print-mode .main-content {
            margin: 0 !important;
            padding: 20px !important;
            width: 100% !important;
        }

        @media print {
            @page { margin: 0; size: auto; }
            body { margin: 1cm; }
            header, footer { display: none !important; }
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Navigation */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            width: 80px;
            background: rgba(15, 23, 42, 0.95);
            border-right: 1px solid var(--border-glass);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 2rem;
            z-index: 100;
        }

        .nav-item {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--text-mute);
            font-size: 1.2rem;
        }
        .nav-item:hover, .nav-item.active {
            background: var(--active-glow);
            color: #fff;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
        }

        /* Main Content */
        .main-content {
            margin-left: 80px;
            padding: 3rem;
        }

        /* Glass Cards */
        .gl-card {
            background: var(--card-glass);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            padding: 2rem;
            backdrop-filter: blur(12px);
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.3);
        }

        h1, h2, h3 { color: #fff; font-weight: 700; }
        .text-dim { color: var(--text-mute); }

        /* Metrics */
        .metric-value { font-size: 2rem; font-weight: 800; background: linear-gradient(to right, #fff, #cbd5e1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .metric-label { font-size: 0.8rem; text-transform: uppercase; color: var(--text-mute); letter-spacing: 1px; }

        /* Correlation Matrix Grid */
        .corr-grid {
            display: grid;
            gap: 2px;
            margin-top: 1rem;
            overflow: auto;
        }
        .corr-cell {
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            transition: transform 0.2s;
        }
        .corr-cell:hover { transform: scale(1.1); z-index: 5; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }

        /* Section Titles */
        .section-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            display: flex; align-items: center; gap: 15px;
        }
        .section-title span { color: var(--active-glow); }

        /* Action Buttons */
        .btn-glow {
            background: var(--active-glow); color: #fff;
            border: none; padding: 10px 25px; border-radius: 50px;
            font-weight: 600; transition: 0.3s;
            text-decoration: none;
        }
        .btn-glow:hover { box-shadow: 0 0 20px rgba(99, 102, 241, 0.6); color: #fff; }

        /* Scroll Reveal */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease; }
        .reveal.active { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="nav-item active" onclick="scrollToSection('overview')">📊</div>
        <div class="nav-item" onclick="scrollToSection('relationships')">🔗</div>
        @if(Session::has('hypothesis_results'))<div class="nav-item" onclick="scrollToSection('hypothesis')">🔬</div>@endif
        @if(Session::has('group_results'))<div class="nav-item" onclick="scrollToSection('groups')">📂</div>@endif
        <div class="nav-item" onclick="scrollToSection('details')">📝</div>
        <a href="{{ route('home') }}" class="nav-item mt-auto mb-4" title="Yeni Analiz">⬅️</a>
    </nav>

    <div class="main-content">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-5 reveal active">
            <div>
                <h1 class="display-4">Analiz Raporu</h1>
                <p class="text-dim">Yapay Zeka Destekli İçgörüler</p>
            </div>
            <!-- PDF Button -->
            <button onclick="downloadPDF()" class="btn-glow">📥 PDF İndir (Grafikli)</button>
        </div>

        @if(empty($results))
            <div class="gl-card text-center py-5">
                <h3>Veri Bulunamadı</h3>
            </div>
        @else

            <!-- Relationships / Correlation Section (NEW) -->
            @if(Session::has('correlation_matrix') && is_array(Session::get('correlation_matrix')))
            @php $matrix = Session::get('correlation_matrix'); $keys = array_keys($matrix); @endphp
            <section id="relationships" class="mb-5 reveal active">
                <div class="section-title"><span>✦</span> İlişki Haritası ve Bağlantılar</div>
                
                <div class="row">
                    <div class="col-md-7">
                        <div class="gl-card h-100">
                            <h4 class="mb-4">Korelasyon Matrisi (Isı Haritası)</h4>
                            <div class="table-responsive" style="max-height: 500px; overflow: auto; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                <div class="corr-grid p-2" style="grid-template-columns: auto repeat({{ count($matrix) }}, minmax(60px, 1fr));">
                                    <!-- Header Row -->
                                    <div class="corr-cell fw-bold text-white bg-transparent">#</div>
                                    @foreach($keys as $key)
                                        <div class="corr-cell fw-bold text-accent" style="font-size: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; background: rgba(15, 23, 42, 0.8);" title="{{ $key }}">{{ \Illuminate\Support\Str::limit($key, 8) }}</div>
                                    @endforeach

                                    <!-- Data Rows -->
                                    @foreach($matrix as $rowKey => $row)
                                        <!-- Row Header -->
                                        <div class="corr-cell fw-bold text-accent" style="font-size: 0.75rem; text-align: right; padding-right: 10px; background: rgba(15, 23, 42, 0.8);" title="{{ $rowKey }}">{{ \Illuminate\Support\Str::limit($rowKey, 8) }}</div>
                                        
                                        <!-- Cells -->
                                        @foreach($keys as $colKey) <!-- Iterate by keys to ensure order match -->
                                            @php $score = $row[$colKey] ?? 0; @endphp
                                            @php
                                                $alpha = abs($score);
                                                // Color scale: Green for pos, Red for neg
                                                $color = $score > 0 ? "rgba(74, 222, 128, $alpha)" : "rgba(248, 113, 113, $alpha)";
                                                // Text contrast
                                                $textColor = $alpha > 0.6 ? '#000' : '#fff'; 
                                            @endphp
                                            <div class="corr-cell d-flex align-items-center justify-content-center" 
                                                 style="background: {{ $color }}; color: {{ $textColor }}; aspect-ratio: 1;" 
                                                 title="{{ $rowKey }} vs {{ $colKey }}: {{ $score }}">
                                                {{ number_format($score, 2) }}
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-dim mt-2 d-block text-end">* Hücreler korelasyon gücünü gösterir (1.00 = Tam Eşleşme).</small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="gl-card h-100 d-flex flex-column position-relative overflow-hidden">
                             <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                <span style="font-size: 5rem;">💡</span>
                             </div>
                             
                             <h4 class="mb-4">⚡ Kritik İçgörüler</h4>
                             @php $insights = Session::get('relationship_insights', []); @endphp
                             
                             @if(count($insights) > 0)
                                @php 
                                    $top = $insights[0];
                                    $score = $top['score'];
                                    $isPos = $score > 0;
                                    $strength = abs($score) > 0.7 ? 'Çok Güçlü' : (abs($score) > 0.5 ? 'Güçlü' : 'Orta');
                                    $color = $isPos ? '#4ade80' : '#f87171';
                                    $direction = $isPos ? 'Pozitif' : 'Negatif';
                                @endphp

                                <!-- Hero Insight -->
                                <div class="text-center py-4 mb-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                                    <div class="text-dim text-uppercase small letter-spacing-2 mb-2">En Belirgin İlişki</div>
                                    
                                    <h5 class="mb-3">
                                        <span class="text-white">{{ \Illuminate\Support\Str::limit($top['col1'], 20) }}</span>
                                        <br>
                                        <span class="text-dim fs-6">ile</span>
                                        <br>
                                        <span class="text-white">{{ \Illuminate\Support\Str::limit($top['col2'], 20) }}</span>
                                    </h5>

                                    <div class="d-inline-flex align-items-center justify-content-center px-4 py-2 rounded-pill mt-2" 
                                         style="background: {{ $color }}20; border: 1px solid {{ $color }}; color: {{ $color }}">
                                        <span class="h3 mb-0 fw-bold me-2">{{ number_format($score, 2) }}</span>
                                        <span class="opacity-75 small lh-1 text-start">
                                            {{ $strength }}<br>{{ $direction }} İlişki
                                        </span>
                                    </div>
                                </div>

                                <!-- Dynamic Scatter For Top Constraint -->
                                <div class="chart-container mb-3" style="height: 250px; width: 100%; position: relative;">
                                    <canvas id="scatterChart"></canvas>
                                </div>

                                <!-- Other Insights Carousel-like List -->
                                <div class="mt-auto">
                                    <h6 class="text-dim mb-2 small">Diğer Önemli Bağlantılar</h6>
                                    <div class="d-flex gap-2" style="overflow-x: auto; padding-bottom: 5px;">
                                        @foreach(array_slice($insights, 1, 5) as $insight)
                                            <div onclick="updateScatterChart('{{ $insight['col1'] }}', '{{ $insight['col2'] }}')"
                                                 class="flex-shrink-0 p-2 rounded text-center cursor-pointer transition-hover"
                                                 style="background: rgba(255,255,255,0.05); min-width: 100px; border: 1px solid rgba(255,255,255,0.05);">
                                                <div class="small fw-bold {{ $insight['score'] > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($insight['score'], 2) }}
                                                </div>
                                                <div class="text-white small" style="font-size: 0.7rem;">
                                                    {{ \Illuminate\Support\Str::limit($insight['col1'], 8) }}..
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                             @else
                                <div class="text-center py-5 text-dim">
                                    <p>Veriler arasında belirgin bir bağlantı bulunamadı.</p>
                                </div>
                             @endif
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Dynamic Scatter Plot Logic -->
            <script>
                // Safely decode JSON
                let allData = {};
                try {
                    allData = {!! json_encode($results ?? []) !!};
                } catch(e) {
                    console.error("Data Parse Error:", e);
                }

                let myScatterChart = null;

                document.addEventListener("DOMContentLoaded", function() {
                    @if(isset($insights) && count($insights) > 0)
                        // Safely initialize
                        try {
                            updateScatterChart("{{ $insights[0]['col1'] }}", "{{ $insights[0]['col2'] }}");
                        } catch(e) {
                            console.error("Chart Init Error:", e);
                        }
                    @endif
                });

                function updateScatterChart(colX, colY) {
                    const ctx = document.getElementById('scatterChart');
                    if(!ctx) return;

                    // Safety Checks
                    if (!allData || !allData[colX] || !allData[colY]) {
                        console.warn('Viz data missing for:', colX, colY);
                        return;
                    }

                    const xValues = Object.values(allData[colX].values);
                    const yValues = Object.values(allData[colY].values);
                    
                    const scatterData = xValues.map((x, i) => ({x: x, y: yValues[i]}));

                    // Trend Line Calculation
                    const n = xValues.length;
                    const sumX = xValues.reduce((a, b) => a + b, 0);
                    const sumY = yValues.reduce((a, b) => a + b, 0);
                    const sumXY = xValues.reduce((sum, x, i) => sum + x * yValues[i], 0);
                    const sumXX = xValues.reduce((sum, x) => sum + x * x, 0);

                    const slope = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
                    const intercept = (sumY - slope * sumX) / n;
                    
                    const minX = Math.min(...xValues);
                    const maxX = Math.max(...xValues);

                    const trendData = [
                        { x: minX, y: slope * minX + intercept },
                        { x: maxX, y: slope * maxX + intercept }
                    ];

                    // Destroy old chart if exists
                    if (myScatterChart) myScatterChart.destroy();

                    myScatterChart = new Chart(ctx, {
                        type: 'scatter',
                        data: {
                            datasets: [
                                {
                                    label: 'Veri Noktaları',
                                    data: scatterData,
                                    backgroundColor: 'rgba(99, 102, 241, 0.6)',
                                    borderColor: '#6366f1',
                                    borderWidth: 1,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    order: 2
                                },
                                {
                                    type: 'line',
                                    label: 'Trend',
                                    data: trendData,
                                    borderColor: '#f43f5e',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    pointRadius: 0,
                                    fill: false,
                                    order: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { display: true, labels: { color: '#fff' } },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `(${context.raw.x}, ${context.raw.y})`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { 
                                    type: 'linear', position: 'bottom',
                                    grid: { color: 'rgba(255,255,255,0.05)' },
                                    ticks: { color: '#94a3b8' },
                                    title: { display: true, text: colX, color: '#cbd5e1' }
                                },
                                y: { 
                                    grid: { color: 'rgba(255,255,255,0.05)' },
                                    ticks: { color: '#94a3b8' },
                                    title: { display: true, text: colY, color: '#cbd5e1' }
                                }
                            }
                        }
                    });
                }
            </script>

            @endif

            <!-- Hypothesis Testing Interactive Dashboard -->
            <style>
                .hyp-item { cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; }
                .hyp-item:hover, .hyp-item.active { background: rgba(255,255,255,0.05); }
                .hyp-item.active { border-left-color: var(--active-glow); background: rgba(99, 102, 241, 0.1); }
                .hyp-status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; }
            </style>

            @if(Session::has('hypothesis_results') && is_array(Session::get('hypothesis_results')) && count(Session::get('hypothesis_results')) > 0)
            <section id="hypothesis" class="mb-5 reveal">
                <div class="section-title"><span>🔬</span> Hipotez Testleri (T-Test)</div>
                
                <div class="gl-card p-0 overflow-hidden" style="min-height: 500px;">
                    <div class="row g-0 h-100">
                        <!-- Left Sidebar: List of Tests -->
                        <div class="col-md-4 border-end border-secondary" style="background: rgba(0,0,0,0.2); max-height: 600px; overflow-y: auto;">
                            <div class="p-3 border-bottom border-secondary bg-dark-glass sticky-top">
                                <h6 class="text-white mb-0">Test Sonuçları</h6>
                                <small class="text-dim">{{ count(Session::get('hypothesis_results')) }} Analiz Bulundu</small>
                            </div>
                            <div id="hypothesisList">
                                <!-- List Items will be injected here -->
                            </div>
                        </div>

                        <!-- Right Content: Detail View -->
                        <div class="col-md-8 d-flex flex-column">
                            <div class="p-4 flex-grow-1" id="hypothesisDetail">
                                <div class="text-center text-dim mt-5">
                                    <div class="fs-1 mb-3">👈</div>
                                    <p>Detaylarını görmek için soldan bir test seçin.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden Data for JS -->
                <script>
                    let hypothesisData = [];
                    try {
                        hypothesisData = @json(Session::get('hypothesis_results', []) ?? []);
                    } catch(e) {
                        console.error("Hypothesis Data Error", e);
                    }
                    
                    let activeChart = null;
                    let currentResultIndex = 0;

                    document.addEventListener("DOMContentLoaded", function() {
                        if(Array.isArray(hypothesisData) && hypothesisData.length > 0) {
                             renderHypothesisList();
                             loadHypothesisDetail(0); // Auto load first
                        }
                    });

                    function renderHypothesisList() {
                        const listContainer = document.getElementById('hypothesisList');
                        listContainer.innerHTML = '';

                        hypothesisData.forEach((item, index) => {
                            const isSig = item.report.decision.includes('Reddedildi');
                            const colorClass = isSig ? 'text-danger' : 'text-success';
                            const dotColor = isSig ? '#f87171' : '#4ade80';

                            const div = document.createElement('div');
                            div.className = `hyp-item p-3 border-bottom border-secondary ${index === currentResultIndex ? 'active' : ''}`;
                            div.onclick = () => loadHypothesisDetail(index);
                            
                            div.innerHTML = `
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="text-white fw-medium small" style="word-break: break-word;">${item.pair}</span>
                                    <span class="hyp-status-dot mt-1 flex-shrink-0" style="background: ${dotColor};" title="${item.report.decision}"></span>
                                </div>
                                <div class="small ${colorClass}">${isSig ? 'Fark Var' : 'Benzer'}</div>
                            `;
                            listContainer.appendChild(div);
                        });
                    }

                    function loadHypothesisDetail(index) {
                        currentResultIndex = index;
                        renderHypothesisList(); // Update active class
                        
                        const data = hypothesisData[index];
                        const container = document.getElementById('hypothesisDetail');
                        const isSig = data.report.decision.includes('Reddedildi');
                        
                        container.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0 text-white">${data.pair}</h4>
                                <span class="badge ${isSig ? 'bg-danger' : 'bg-success'} px-3 py-2">
                                    ${data.report.decision}
                                </span>
                            </div>

                            <div class="p-3 mb-4 rounded" style="background: rgba(255,255,255,0.03);">
                                <h5 class="text-accent mb-2">📢 Analiz Sonucu</h5>
                                <p class="mb-0 text-dim">${data.report.interpretation}</p>
                            </div>

                            <!-- Chart Controls & Title -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-dim mb-0" id="chartTitle">Görselleştirme Modeli</h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle text-white" type="button" id="chartTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        📊 Grafik Modeli Seç
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark p-2" style="min-width: 200px;">
                                        <li><h6 class="dropdown-header text-dim">Dağılım & İstatistik</h6></li>
                                        <li><a class="dropdown-item active" href="#" onclick="changeChartType('curve', '📈 Çan Eğrisi (Normal Dağılım)')">📈 Çan Eğrisi (Normal Dağılım)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('histogram', '📊 Histogram (Frekans)')">📊 Histogram (Frekans)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('scatter', '✨ Dağılım (Scatter Plot)')">✨ Dağılım (Scatter Plot)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('box', '📦 Kutu Grafiği (Box Plot)')">📦 Kutu Grafiği (Box Plot Sim.)</a></li>
                                        
                                        <li><hr class="dropdown-divider border-secondary"></li>
                                        <li><h6 class="dropdown-header text-dim">Karşılaştırma</h6></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('bar', '📊 Sütun Grafik (Ortalama)')">📊 Sütun Grafik (Ortalama)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('horizontalBar', '➖ Yatay Sütun (Ortalama)')">➖ Yatay Sütun (Ortalama)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('radar', '🕸️ Radar Analizi (Özet)')">🕸️ Radar Analizi (Özet)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('polar', '❄️ Polar Alan (Değişkenlik)')">❄️ Polar Alan (Değişkenlik)</a></li>
                                        
                                        <li><hr class="dropdown-divider border-secondary"></li>
                                        <li><h6 class="dropdown-header text-dim">Diğer</h6></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('line', '📉 Çizgi Grafik (Trend)')">📉 Çizgi Grafik (Trend)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeChartType('doughnut', '🍩 Örneklem Oranı (N)')">🍩 Örneklem Oranı (N)</a></li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Chart Area -->
                            <div class="flex-grow-1" style="min-height: 350px; position: relative;">
                                <canvas id="activeHypothesisChart"></canvas>
                            </div>

                            <!-- Technical Details -->
                            <div class="mt-4">
                                <button class="btn btn-link text-dim p-0 text-decoration-none small" type="button" data-bs-toggle="collapse" data-bs-target="#techDetails">
                                    🔧 Teknik Detaylar (T-Skoru, H0/H1) ▼
                                </button>
                                <div class="collapse mt-2" id="techDetails">
                                    <div class="p-3 rounded border border-secondary bg-dark-glass text-dim small">
                                        <strong>T-Skoru:</strong> ${parseFloat(data.report.t_score).toFixed(3)}<br>
                                        <strong>H0:</strong> ${data.report.h0}<br>
                                        <strong>H1:</strong> ${data.report.h1}
                                    </div>
                                </div>
                            </div>
                        `;

                        renderChart('curve'); // Default
                    }

                    function changeChartType(type, label) {
                        document.getElementById('chartTitle').innerText = label;
                        // Reset active class logic if desired, but dropdown implies selection
                        renderChart(type);
                    }

                    function renderChart(type) {
                        const data = hypothesisData[currentResultIndex];
                        const ctx = document.getElementById('activeHypothesisChart');
                        
                        if(activeChart) activeChart.destroy();

                        // Common Colors
                        const c1 = '#6366f1'; const c1bg = 'rgba(99, 102, 241, 0.4)';
                        const c2 = '#ec4899'; const c2bg = 'rgba(236, 72, 153, 0.4)';

                        if(type === 'curve') {
                            activeChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.report.curve_x,
                                    datasets: [
                                        { label: 'Grup 1', data: data.report.curve_y1, backgroundColor: 'rgba(99, 102, 241, 0.2)', borderColor: c1, borderWidth: 2, pointRadius: 0, fill: true, tension: 0.4 },
                                        { label: 'Grup 2', data: data.report.curve_y2, backgroundColor: 'rgba(236, 72, 153, 0.2)', borderColor: c2, borderWidth: 2, pointRadius: 0, fill: true, tension: 0.4 }
                                    ]
                                },
                                options: { responsive: true, maintainAspectRatio: false, scales: { y: {display:false}, x: {grid: {color:'rgba(255,255,255,0.05)'}, ticks:{color:'#94a3b8', maxTicksLimit: 10}} }, plugins: {legend:{labels:{color:'white'}}} }
                            });

                        } else if (type === 'histogram') {
                            // Needs bucketed data from backend (data.report.histogram)
                            const hist = data.report.histogram || {labels:[], data1:[], data2:[]};
                            activeChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: hist.labels,
                                    datasets: [
                                        { label: 'Grup 1', data: hist.data1, backgroundColor: c1bg, borderColor: c1, borderWidth: 1 },
                                        { label: 'Grup 2', data: hist.data2, backgroundColor: c2bg, borderColor: c2, borderWidth: 1 }
                                    ]
                                },
                                options: { responsive: true, maintainAspectRatio: false, scales: { x: {grid:{display:false}, ticks:{color:'#94a3b8'}}, y: {grid:{color:'rgba(255,255,255,0.1)'}, ticks:{color:'#94a3b8'}} }, plugins: {legend:{labels:{color:'white'}}} }
                            });

                        } else if (type === 'scatter') {
                            const jit = () => (Math.random() - 0.5) * 0.3;
                            const d1 = (data.report.raw_data1 || []).map(val => ({x: 1 + jit(), y: val}));
                            const d2 = (data.report.raw_data2 || []).map(val => ({x: 2 + jit(), y: val}));
                            activeChart = new Chart(ctx, {
                                type: 'scatter',
                                data: { datasets: [ { label: 'Grup 1', data: d1, backgroundColor: c1, pointRadius: 4 }, { label: 'Grup 2', data: d2, backgroundColor: c2, pointRadius: 4 } ] },
                                options: { responsive: true, maintainAspectRatio: false, scales: { x: {min:0, max:3, ticks:{callback:(v)=>v===1?'Grup 1':(v===2?'Grup 2':''), color:'white', font:{weight:'bold'}}}, y:{grid:{color:'rgba(255,255,255,0.1)'}, ticks:{color:'#94a3b8'}} }, plugins: {legend:{labels:{color:'white'}}} }
                            });

                        } else if (type === 'bar' || type === 'horizontalBar') {
                            activeChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['Grup 1', 'Grup 2'],
                                    datasets: [{ label: 'Ortalama Değer', data: [data.report.mean1, data.report.mean2], backgroundColor: [c1, c2], borderRadius: 8 }]
                                },
                                options: { 
                                    indexAxis: type === 'horizontalBar' ? 'y' : 'x',
                                    responsive: true, maintainAspectRatio: false, 
                                    scales: { x: {grid:{display:false}, ticks:{color:'white'}}, y: {grid:{color:'rgba(255,255,255,0.1)'}, ticks:{color:'#94a3b8'}} }, 
                                    plugins: {legend:{display:false}} 
                                }
                            });

                        } else if (type === 'radar' || type === 'polar') {
                            // Requires summary stats (Min, Q1, Median, Q3, Max)
                            const s1 = data.report.summary.group1;
                            const s2 = data.report.summary.group2;
                            // Normalize keys for radar
                            const keys = ['Min', 'Q1', 'Median', 'Q3', 'Max']; // 'Mean' excluded for cleaner radar
                            const v1 = [s1.min, s1.q1, s1.median, s1.q3, s1.max];
                            const v2 = [s2.min, s2.q1, s2.median, s2.q3, s2.max];
                            
                            activeChart = new Chart(ctx, {
                                type: type === 'polar' ? 'polarArea' : 'radar',
                                data: {
                                    labels: keys,
                                    datasets: [
                                        { label: 'Grup 1', data: v1, backgroundColor: 'rgba(99, 102, 241, 0.3)', borderColor: c1, pointRadius: 3 },
                                        { label: 'Grup 2', data: v2, backgroundColor: 'rgba(236, 72, 153, 0.3)', borderColor: c2, pointRadius: 3 }
                                    ]
                                },
                                options: { 
                                    responsive: true, maintainAspectRatio: false, 
                                    scales: { r: { grid: {color:'rgba(255,255,255,0.1)'}, ticks: {backdropColor:'transparent', color:'#94a3b8'} } }, 
                                    plugins: {legend:{labels:{color:'white'}}} 
                                }
                            });

                        } else if (type === 'doughnut') {
                            const n1 = data.report.counts.n1;
                            const n2 = data.report.counts.n2;
                            activeChart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: [`Grup 1 (N=${n1})`, `Grup 2 (N=${n2})`],
                                    datasets: [{ data: [n1, n2], backgroundColor: [c1, c2], borderColor: '#1e293b' }]
                                },
                                options: { responsive: true, maintainAspectRatio: false, plugins: {legend:{labels:{color:'white'}}} }
                            });

                        } else if (type === 'line') {
                            // Raw Data Trend (Point to Point)
                            // We need to limit points if too many, but assumed standard simple data
                            const labels = Array.from({length: Math.max(data.report.raw_data1.length, data.report.raw_data2.length)}, (_, i) => i+1);
                            activeChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: labels,
                                    datasets: [
                                        { label: 'Grup 1', data: data.report.raw_data1, borderColor: c1, pointRadius: 2, borderWidth: 1, tension: 0.2 },
                                        { label: 'Grup 2', data: data.report.raw_data2, borderColor: c2, pointRadius: 2, borderWidth: 1, tension: 0.2 }
                                    ]
                                },
                                options: { responsive: true, maintainAspectRatio: false, scales: { x:{display:false}, y:{grid:{color:'rgba(255,255,255,0.1)'}, ticks:{color:'#94a3b8'}} }, plugins: {legend:{labels:{color:'white'}}} }
                            });

                        } else if (type === 'box') {
                            // Simulating Box with Floating Bars: [Min, Max]
                            // Not a true Box Plot but shows range. A true Box Plot requires plugin.
                            // Better Visualization: Floating Bar for Range (Min to Max) + Line for Mean
                            const s1 = data.report.summary.group1;
                            const s2 = data.report.summary.group2;
                            
                            activeChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['Grup 1', 'Grup 2'],
                                    datasets: [
                                        { 
                                            label: 'Aralık (Min-Max)', 
                                            // Floating Bar format: [min, max]
                                            data: [[s1.min, s1.max], [s2.min, s2.max]], 
                                            backgroundColor: [c1bg, c2bg], borderColor: [c1, c2], borderWidth: 2, barPercentage: 0.4
                                        },
                                        {
                                            type: 'scatter',
                                            label: 'Ortalama',
                                            data: [{x: 'Grup 1', y: s1.mean}, {x: 'Grup 2', y: s2.mean}],
                                            backgroundColor: 'white', borderColor: 'white', pointRadius: 8, pointStyle: 'rectRot'
                                        }
                                    ]
                                },
                                options: { 
                                    responsive: true, maintainAspectRatio: false, 
                                    scales: { y: {grid:{color:'rgba(255,255,255,0.1)'}, ticks:{color:'#94a3b8'}}, x: {grid:{display:false}, ticks:{color:'white'}} }, 
                                    plugins: {legend:{labels:{color:'white'}, display: true}} 
                                }
                            });
                        }
                    }
                </script>
            </section>
            @endif

            <!-- Group Analysis Section -->
            @if(Session::has('group_results') && count(Session::get('group_results')) > 0)
            <section id="groups" class="mb-5 reveal">
                <div class="section-title"><span>📂</span> Kategorik Gruplama Analizi</div>
                <div class="row g-4">
                    @foreach(Session::get('group_results') as $groupName => $stats)
                    <div class="col-md-4">
                        <div class="gl-card h-100 transition-hover">
                            <h5 class="mb-3 text-accent border-bottom border-secondary pb-2">{{ $groupName }}</h5>
                            @if(is_array($stats))
                                @foreach($stats as $catName => $catStats)
                                    @php 
                                        $avg = $catStats['average'] ?? 0;
                                        $cnt = $catStats['count'] ?? 0;
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-1 small text-dim">
                                        <span>{{ $catName }}</span>
                                        <span>(N:{{ $cnt }})</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px; background: rgba(255,255,255,0.1);">
                                            <div class="progress-bar bg-accent" style="width: 50%"></div>
                                        </div>
                                        <span class="text-white fw-bold small">{{ number_format($avg, 2) }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Detailed Analysis Section -->
            <section id="details">
                 @foreach($results as $column => $data)
                    <div class="gl-card reveal">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="h3 mb-0">{{ $column }} Analizi</h2>
                            <span class="badge bg-{{ $data['risk']['color'] }} px-3 py-2 rounded-pill">{{ $data['risk']['level'] }} Risk</span>
                        </div>

                        <!-- Main Grid -->
                        <div class="row g-4">
                            <!-- Stats -->
                            <div class="col-md-4">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.03);">
                                            <div class="metric-label">Ortalama</div>
                                            <div class="metric-value">{{ number_format($data['stats']['average'], 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.03);">
                                            <div class="metric-label">Maksimum</div>
                                            <div class="metric-value">{{ number_format($data['stats']['max'], 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="p-3 rounded" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.3);">
                                            <div class="metric-label text-white">🤖 AI Kararı</div>
                                            <div class="fs-5 fw-bold text-white mt-1">{{ $data['decision']['action'] }}</div>
                                            <div class="small text-dim mt-1">{{ $data['decision']['reason'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Charts -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-8">
                                         <div style="position: relative; height: 300px; width: 100%;">
                                            <canvas id="chart-{{ \Illuminate\Support\Str::slug($column) }}"></canvas>
                                         </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="position: relative; height: 300px; width: 100%;">
                                            <canvas id="radar-{{ \Illuminate\Support\Str::slug($column) }}"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Restore: Detailed Statistics Table -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="p-3 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                    <h5 class="mb-3 text-white">📊 Detaylı İstatistikler</h5>
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm text-white mb-0" style="font-size: 0.9rem;">
                                            <tbody>
                                                <tr>
                                                    <td class="text-dim" style="width: 25%;">Veri Sayısı (N)</td>
                                                    <td class="fw-bold" style="width: 25%;">{{ number_format($data['stats']['count']) }}</td>
                                                    <td class="text-dim" style="width: 25%;">Toplam</td>
                                                    <td class="fw-bold" style="width: 25%;">{{ number_format($data['stats']['sum'], 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-dim">Ortalama</td>
                                                    <td class="fw-bold">{{ number_format($data['stats']['average'], 2) }}</td>
                                                    <td class="text-dim">Medyan</td>
                                                    <td class="fw-bold">{{ number_format($data['stats']['median'], 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-dim">Minimum</td>
                                                    <td class="fw-bold">{{ number_format($data['stats']['min'], 2) }}</td>
                                                    <td class="text-dim">Maksimum</td>
                                                    <td class="fw-bold">{{ number_format($data['stats']['max'], 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-dim">Standart Sapma</td>
                                                    <td class="fw-bold">{{ number_format($data['stats']['std_dev'], 2) }}</td>
                                                    <td class="text-dim">Varyans</td>
                                                    <td class="fw-bold">{{ number_format($data['variance'], 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-dim">Alt Çeyrek (Q1)</td>
                                                    <td class="fw-bold">{{ number_format($data['quartiles']['q1'], 2) }}</td>
                                                    <td class="text-dim">Üst Çeyrek (Q3)</td>
                                                    <td class="fw-bold">{{ number_format($data['quartiles']['q3'], 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-dim">Çeyrekler Açıklığı (IQR)</td>
                                                    <td class="fw-bold">{{ number_format($data['quartiles']['iqr'], 2) }}</td>
                                                    <td class="text-dim">Aykırı Değerler</td>
                                                    <td class="fw-bold text-danger">{{ count($data['outliers']) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-dim">Regresyon Eğimi (Slope)</td>
                                                    <td class="fw-bold">{{ $data['regression']['slope'] }}</td>
                                                    <td class="text-dim">R² (Güvenilirlik)</td>
                                                    <td class="fw-bold">{{ $data['regression']['r2'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart Logic -->
                        <script>
                            new Chart(document.getElementById('chart-{{ Str::slug($column) }}'), {
                                type: 'line',
                                data: {
                                    labels: {!! json_encode(array_keys($data['values'])) !!}.map(i => i + 1),
                                    datasets: [{
                                        label: 'Değerler',
                                        data: {!! json_encode(array_values($data['values'])) !!},
                                        borderColor: '#6366f1',
                                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 0
                                    }]
                                },
                                options: {
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    plugins: { 
                                        legend: { display: false },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                            titleColor: '#fff',
                                            bodyColor: '#cbd5e1',
                                            borderColor: 'rgba(255,255,255,0.2)',
                                            borderWidth: 1,
                                            padding: 10,
                                            displayColors: false
                                        }
                                    },
                                    scales: {
                                        y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                                        x: { display: false }
                                    }
                                }
                            });

                             new Chart(document.getElementById('radar-{{ Str::slug($column) }}'), {
                                type: 'radar',
                                data: {
                                    labels: ['Ort', 'Std', 'Max', 'Trend', 'Güven'],
                                    datasets: [{
                                        data: [
                                            {{ min(100, $data['stats']['average']) }}, 
                                            {{ min(100, $data['stats']['std_dev'] * 2) }}, 
                                            {{ min(100, $data['stats']['max']) }}, 
                                            {{ abs($data['regression']['slope']) * 100 }},
                                            {{ $data['regression']['r2'] * 100 }}
                                        ],
                                        borderColor: '#4ade80',
                                        backgroundColor: 'rgba(74, 222, 128, 0.2)',
                                        pointRadius: 0
                                    }]
                                },
                                options: {
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false } },
                                    scales: {
                                        r: {
                                            grid: { color: 'rgba(255,255,255,0.05)' },
                                            angleLines: { color: 'rgba(255,255,255,0.05)' },
                                            pointLabels: { color: '#cbd5e1', font: { size: 10 } },
                                            ticks: { display: false } 
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                 @endforeach
            </section>
        @endif
    </div>

    <script>
        function scrollToSection(id) {
            document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
        }
        
        // Scroll Reveal Animation
        window.addEventListener('scroll', reveal);
        function reveal() {
            var reveals = document.querySelectorAll('.reveal');
            for(var i = 0; i < reveals.length; i++) {
                var windowheight = window.innerHeight;
                var revealtop = reveals[i].getBoundingClientRect().top;
                var revealpoint = 150;
                if(revealtop < windowheight - revealpoint) {
                    reveals[i].classList.add('active');
                }
            }
        }

        // PDF Generation (Component-Based for Perfect Layout)
        async function downloadPDF() {
            const { jsPDF } = window.jspdf;
            
            // Enable Print Mode
            document.body.classList.add('print-mode');
            document.body.style.cursor = 'wait';
             
            // Wait for styles/charts
            await new Promise(resolve => setTimeout(resolve, 500));

            const pdf = new jsPDF('p', 'mm', 'a4');
            const pdfWidth = pdf.internal.pageSize.getWidth(); // 210mm
            const pdfHeight = pdf.internal.pageSize.getHeight(); // 297mm
            const margin = 10; // 10mm margin
            const contentWidth = pdfWidth - (2 * margin);
            
            let currentY = margin;

            // Select all top-level report sections
            // We want: Header, Relationship Section, Hypothesis Section, Group Section, Details Section (each card)
            // A good strategy: capture the Header, then iterate over all Direct Children of .main-content that are visible
            // But .main-content has scattered elements.
            // Let's capture the Header first.
            const header = document.querySelector('.main-content > .d-flex'); // The header row
            const sections = Array.from(document.querySelectorAll('section')); // All sections
            const noData = document.querySelector('.gl-card.text-center'); // "Veri Yok" card if exists

            const elementsToCapture = [];
            if(header) elementsToCapture.push(header);
            if(noData) elementsToCapture.push(noData);
            
            sections.forEach(sec => {
                // For detailed analysis, hypothesis, and groups, we want to capture individual cards 
                // to avoid creating images that are too large for a single page.
                if(['details', 'hypothesis', 'groups'].includes(sec.id)) {
                    // Find all cards within this section
                    // Note: Hypothesis/Groups use cols, Details uses cards directly. 
                    // We select .gl-card to be safe.
                    const cards = Array.from(sec.querySelectorAll('.gl-card'));
                    
                    // If section has a title, we might want to capture it separately?
                    // The titles are inside 'section-title' divs which are siblings to the row/cards.
                    const title = sec.querySelector('.section-title');
                    if(title) elementsToCapture.push(title);

                    cards.forEach(card => elementsToCapture.push(card));
                } else {
                    // For other small sections (like relationships overview), capture the whole section
                    // But wait, Relationships might be tall too?
                    // Let's stick to capturing whole section if it's not one of the big ones.
                    elementsToCapture.push(sec);
                }
            });

            try {
                for (let i = 0; i < elementsToCapture.length; i++) {
                    const el = elementsToCapture[i];
                    if(!el || el.offsetParent === null) continue; // Skip hidden

                    // Apply temporary style to ensure it looks good isolated
                    const originalMargin = el.style.margin;
                    el.style.margin = '0'; // Remove margin for clean capture
                    
                    // Different styling for Cards vs Titles
                    const isCard = el.classList.contains('gl-card');
                    
                    if(isCard) {
                        el.style.padding = '20px'; 
                        el.style.backgroundColor = '#1e293b'; // Force dark card bg
                        el.style.borderRadius = '12px';
                    }

                    const canvas = await html2canvas(el, {
                        scale: 1.5,
                        useCORS: true,
                        backgroundColor: isCard ? '#1e293b' : '#0f172a', // Card vs Body bg
                        logging: false
                    });

                    // Restore
                    el.style.margin = originalMargin;
                    el.style.padding = '';
                    el.style.backgroundColor = '';
                    el.style.borderRadius = '';

                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = canvas.width;
                    const imgHeight = canvas.height;
                    const ratio = contentWidth / imgWidth;
                    const imgHeightInPdf = imgHeight * ratio;

                    // Check if we need new page
                    if (currentY + imgHeightInPdf > pdfHeight - margin) {
                        pdf.addPage();
                        currentY = margin;
                    }

                    pdf.addImage(imgData, 'PNG', margin, currentY, contentWidth, imgHeightInPdf);
                    currentY += imgHeightInPdf + 10; // add 10mm spacing between blocks
                }
                
                pdf.save('akilli-analiz-raporu.pdf');

            } catch (err) {
                console.error("PDF Hatası:", err);
                alert("PDF oluşturulurken bir hata oluştu: " + err.message);
            } finally {
                document.body.classList.remove('print-mode');
                document.body.style.cursor = 'default';
            }
        }
    </script>
</body>
</html>