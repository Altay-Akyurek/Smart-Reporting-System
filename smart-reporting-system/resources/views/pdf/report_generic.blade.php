<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Analiz Raporu</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #111; font-size: 12px; line-height: 1.5; }
        .page-break { page-break-after: always; }
        h1 { font-size: 24px; color: #2c3e50; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { font-size: 18px; color: #34495e; margin-top: 30px; margin-bottom: 15px; border-left: 5px solid #6366f1; padding-left: 10px; }
        h3 { font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; color: #333; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }

        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; color: white; }
        .bg-success { background-color: #10b981; }
        .bg-warning { background-color: #f59e0b; }
        .bg-danger { background-color: #ef4444; }
        .bg-info { background-color: #3b82f6; }

        .chart-container { text-align: center; margin: 20px 0; page-break-inside: avoid; }
        .chart-img { max-width: 100%; height: auto; border: 1px solid #eee; }
        
        .metric-box { background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px solid #e9ecef; text-align: center; }
        .metric-val { font-size: 16px; font-weight: bold; color: #2c3e50; }
        .metric-lbl { font-size: 10px; color: #7f8c8d; text-transform: uppercase; }
        
        .grid-2 { display: table; width: 100%; table-layout: fixed; border-spacing: 10px; }
        .grid-col { display: table-cell; vertical-align: top; }
    </style>
</head>
<body>

    <!-- Cover Page -->
    <div style="text-align: center; padding-top: 200px;">
        <h1 style="border: none; font-size: 40px; margin-bottom: 10px;">AKILLI ANALİZ RAPORU</h1>
        <p style="color: #666; font-size: 14px;">{{ date('d.m.Y H:i') }}</p>
        <div style="margin-top: 50px;">
            <p><strong>Analiz Edilen Veri Seti</strong></p>
            <p>{{ count($results) }} Değişken İncelendi</p>
        </div>
    </div>
    
    <div class="page-break"></div>

    <!-- Executive Summary / Table of Contents could go here -->

    <!-- Detailed Analysis Loop -->
    @foreach($results as $column => $data)
        @php $slug = \Illuminate\Support\Str::slug($column); @endphp
        
        <h1>{{ $column }} Analizi</h1>
        
        <!-- Metrics Row -->
        <div class="grid-2">
            <div class="grid-col">
                <table style="margin-top: 0;">
                    <tr><th>Ortalama</th><td>{{ number_format($data['stats']['average'], 2) }}</td></tr>
                    <tr><th>Medyan</th><td>{{ number_format($data['stats']['median'], 2) }}</td></tr>
                    <tr><th>Min</th><td>{{ number_format($data['stats']['min'], 2) }}</td></tr>
                    <tr><th>Max</th><td>{{ number_format($data['stats']['max'], 2) }}</td></tr>
                    <tr><th>Std. Sapma</th><td>{{ number_format($data['stats']['std_dev'], 2) }}</td></tr>
                </table>
            </div>
            <div class="grid-col">
                <div style="padding: 15px; background: #fdfdfd; border: 1px solid #eee;">
                    <h3>💡 YZ Kararı</h3>
                     <div style="margin-bottom: 10px;">
                        <span class="badge bg-{{ $data['risk']['color'] }}">{{ $data['risk']['level'] }} Risk</span>
                     </div>
                     <div style="font-weight: bold; color: #333; margin-bottom: 5px;">{{ $data['decision']['action'] }}</div>
                     <div style="color: #666; font-style: italic;">{{ $data['decision']['reason'] }}</div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <h2 style="margin-top: 10px;">Görsel Analiz</h2>
        <div class="grid-2">
            <div class="grid-col">
                @if(isset($charts["chart-$slug"]))
                    <div class="chart-container">
                        <img src="{{ $charts["chart-$slug"] }}" class="chart-img">
                        <p style="font-size: 10px; color: #999;">Trend Analizi</p>
                    </div>
                @else
                    <p style="color: #999; font-style: italic;">Trend grafiği oluşturulamadı.</p>
                @endif
            </div>
            <div class="grid-col">
                @if(isset($charts["radar-$slug"]))
                    <div class="chart-container">
                        <img src="{{ $charts["radar-$slug"] }}" class="chart-img">
                        <p style="font-size: 10px; color: #999;">Yapısal Analiz</p>
                    </div>
                @else
                    <p style="color: #999; font-style: italic;">Radar grafiği oluşturulamadı.</p>
                @endif
            </div>
        </div>

        <!-- Interpretation Text -->
        <h2>Detaylı Yorum</h2>
        <div style="background: #fff; padding: 15px; border: 1px solid #eee; text-align: justify;">
            @if(is_array($data['interpretation']))
                @foreach($data['interpretation'] as $interpretationText)
                    <p>{{ $interpretationText }}</p>
                @endforeach
            @else
                <p>{{ $data['interpretation'] }}</p>
            @endif
        </div>

        <div class="page-break"></div>
    @endforeach

    <!-- Hypothesis Section if available -->
    @if(Session::has('hypothesis_results'))
        <h1>Hipotez Testleri ve Karşılaştırmalar</h1>
        
        @foreach(Session::get('hypothesis_results') as $index => $item)
            <div style="margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                <h3>{{ $item['pair'] }}</h3>
                <p><strong>Karar:</strong> {{ $item['report']['decision'] }}</p>
                <p>{{ $item['report']['interpretation'] }}</p>
                
                @if(isset($charts['activeHypothesisChart']))
                    <div class="chart-container">
                        <img src="{{ $charts['activeHypothesisChart'] }}" class="chart-img">
                        <p style="font-size: 10px; color: #999;">Hipotez Test Grafiği</p>
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <!-- Correlation Matrix Section if available -->
    @if(Session::has('correlation_matrix') && !empty(Session::get('correlation_matrix')))
        <h1>Korelasyon Matrisi</h1>
        @php $matrix = Session::get('correlation_matrix'); @endphp
        <table>
            <thead>
                <tr>
                    <th>Değişken</th>
                    @foreach(array_keys($matrix) as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($matrix as $rowKey => $row)
                    <tr>
                        <th>{{ $rowKey }}</th>
                        @foreach($row as $value)
                            <td>{{ number_format($value, 3) }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @if(Session::has('relationship_insights'))
            <h2>İlişki İçgörüleri</h2>
            @foreach(Session::get('relationship_insights') as $insight)
                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-left: 4px solid #6366f1;">
                    <strong>{{ $insight['pair'] }}</strong>
                    @if(isset($insight['description']))
                        <div style="margin-top: 5px;">{{ $insight['description'] }}</div>
                    @elseif(isset($insight['interpretation']))
                        <div style="margin-top: 5px;">{{ $insight['interpretation'] }}</div>
                    @endif
                    @if(isset($insight['score']))
                        <div style="margin-top: 5px; font-size: 11px; color: #666;">Korelasyon Skoru: {{ number_format($insight['score'], 3) }}</div>
                    @endif
                </div>
            @endforeach
        @endif
        
        @if(isset($charts['scatterChart']))
            <div class="chart-container">
                <img src="{{ $charts['scatterChart'] }}" class="chart-img">
                <p style="font-size: 10px; color: #999;">Dağılım Grafiği</p>
            </div>
        @endif
    @endif

</body>
</html>

