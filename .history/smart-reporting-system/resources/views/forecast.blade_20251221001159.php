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
    </div>

</body>

</html>