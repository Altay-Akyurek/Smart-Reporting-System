<!DOCTYPE html>
<html>

<head>
    <title>Rapor ve Analiz Sonuçları</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1 class="text-center my-5">📊 Detaylı Ürün Analizi</h1>

        @foreach ($analysisResults as $result)
            <div class="card mb-4">
                <div class="card-header">
                    <h3>{{ $result['product']->name }}</h3>
                </div>
                <div class="card-body">
                    <!-- Alış/Satış Grafikleri -->
                    <canvas id="chart-{{ $result['product']->id }}"></canvas>
                    <script>
                        const ctx{{ $result['product']->id }} = document.getElementById('chart-{{ $result['product']->id }}').getContext('2d');
                        new Chart(ctx{{ $result['product']->id }}, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar'], // Tarihler
                                datasets: [
                                    {
                                        label: 'Alış Trend (%)',
                                        data: {{ json_encode($result['purchase_trend']) }},
                                        borderColor: 'blue',
                                        fill: false
                                    },
                                    {
                                        label: 'Satış Trend (%)',
                                        data: {{ json_encode($result['sale_trend']) }},
                                        borderColor: 'green',
                                        fill: false
                                    },
                                    {
                                        label: 'Talep Trend (%)',
                                        data: {{ json_encode($result['demand_trend']) }},
                                        borderColor: 'orange',
                                        fill: false
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    </script>
                </div>
                <div class="card-footer">
                    Risk Seviyesi: {{ $result['risk_level'] }}
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>