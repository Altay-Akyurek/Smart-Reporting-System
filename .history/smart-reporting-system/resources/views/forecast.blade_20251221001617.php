<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahmin Analizi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">📈 Tahmin Analizi</h1>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Mevcut Talep</th>
                    <th>Yeni Tahmin(Sonraki Ay)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($forecastResults as $result)
                    <tr>
                        <th>{{ $result['product'] }}</th>
                        <th>{{ $result['current_demand'] }}</th>
                        <th>{{ $result['forecasted_demand'] }}</th>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tahmin Grafikleri -->
        <h2 class="mt-5">📊 Grafiksel Trendler</h2>
        <canvas id="forecastChart" class="mt-4"></canvas>
    </div>

    <!-- Chart.js Kütüphanesi -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Chart.js Grafiği -->
    <script>
        const ctx = document.getElementById('forecastChart').getContext('2d');

        // Laravel'den gelen Blade verisi: forecastResults
        const chartData = @json($forecastResults);

        // ürün isimlerini (etiket olarak) ve talep verilerini al
        const labels = chartData.map(result => result.product);
        const currentDemand = chartData.map(result => result.current_demand);
        const forecastedDemand = chartData.map(result => result.forecasted_demand);

        // Chart.js ile Bar Grafiği
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels, // x ekseni: ürün isimleri
                datasets: [
                    {
                        label: 'Mevcut Talep',
                        data: currentDemand, // mevcut talep
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Tahmin Edilen Talep',
                        data: forecastedDemand, // tahmin edilen talep
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>