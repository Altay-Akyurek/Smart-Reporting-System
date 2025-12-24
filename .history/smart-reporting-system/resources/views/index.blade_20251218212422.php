<!DOCTYPE html>
<html lang="tr">

<head>
    <title>Akıllı Raporlama Sistemi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">📊 Akıllı Raporlama Sistemi</h1>
        <form action="{{ route('uploadFile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Excel Dosyası Yükle:</label>
                <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .csv" required>
            </div>
            <button class="btn btn-primary mt-3" type="submit">Yükle ve Analiz Et</button>
        </form>
    </div>
</body>

</html>