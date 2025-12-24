<canvas id="forecastChart"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('forecastChart').getContext('2d');
    const chartData = @json($forecastResults); // Blade üzerinden veriyi JS’ye geçirelim
    const labels = chartData.map(result => result.product);
    const currentDemand = chartData.map(result => result.current_demand);
    const forecastedDemand = chartData.map(result => result.forecasted_demand);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Mevcut Talep',
                    data: currentDemand,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Tahmin Edilen Talep',
                    data: forecastedDemand,
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
                    position: 'top'
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