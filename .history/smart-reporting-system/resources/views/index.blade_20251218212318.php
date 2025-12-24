<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akıllı Raporlama Sistemi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">
            <form action="{{ route('uploadFile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">Excel Dosyası YÜkle:</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .csv" required>
                </div>
                <button class="btn btn-primary mt-3" type="submit">Yükle Ve Analiz Et</button>
            </form>
        </h1>
    </div>

</body>

</html>