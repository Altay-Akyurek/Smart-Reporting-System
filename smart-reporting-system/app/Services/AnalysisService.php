<?php

namespace App\Services;

class AnalysisService
{
    /**
     * Calculate basic statistics for an array of numbers.
     */
    public function calculateStatistics(array $data)
    {
        if (empty($data)) {
            return [
                'count' => 0,
                'sum' => 0,
                'min' => 0,
                'max' => 0,
                'average' => 0,
                'median' => 0,
                'std_dev' => 0,
            ];
        }

        $count = count($data);
        $sum = array_sum($data);
        $min = min($data);
        $max = max($data);
        $average = $sum / $count;
        $median = $this->calculateMedian($data);
        $stdDev = $this->calculateStdDev($data, $average);

        return [
            'count' => $count,
            'sum' => $sum,
            'min' => $min,
            'max' => $max,
            'average' => $average,
            'median' => $median,
            'std_dev' => $stdDev,
        ];
    }

    /**
     * Calculate Trend (percentage change) between data points.
     */
    public function calculateTrend(array $data)
    {
        $trends = [];
        $previousValue = null;

        foreach ($data as $value) {
            if ($previousValue !== null && $previousValue != 0) {
                $change = (($value - $previousValue) / abs($previousValue)) * 100;
                $trends[] = round($change, 2);
            } elseif ($previousValue !== null) {
                $trends[] = 0; // Avoid division by zero
            }
            $previousValue = $value;
        }

        return $trends;
    }

    private function calculateMedian(array $data)
    {
        sort($data);
        $count = count($data);
        $middle = floor($count / 2);

        if ($count % 2) {
            return $data[$middle];
        }

        return ($data[$middle - 1] + $data[$middle]) / 2.0;
    }

    private function calculateStdDev(array $data, $average)
    {
        $variance = 0.0;
        foreach ($data as $value) {
            $variance += pow($value - $average, 2);
        }
        return (float) sqrt($variance / count($data));
    }

    public function determineRisk($trend, $stats)
    {
        // Simple logic for demonstration:
        // High risk if trend is consistently negative or if variance is very high relative to mean
        $recentTrend = end($trend) ?: 0;
        $cov = $stats['average'] != 0 ? ($stats['std_dev'] / $stats['average']) : 0; // Coefficient of Variation

        if ($recentTrend < -10) {
            return ['level' => 'Yüksek', 'color' => 'danger', 'message' => 'Ciddi düşüş eğilimi var.'];
        } elseif ($cov > 0.5) {
            return ['level' => 'Orta', 'color' => 'warning', 'message' => 'Verilerde yüksek dalgalanma var.'];
        } else {
            return ['level' => 'Düşük', 'color' => 'success', 'message' => 'Stabil seyrediyor.'];
        }
    }

    /**
     * Generate a smart decision based on statistics and trends.
     */
    public function generateDecision(array $stats, array $trends)
    {
        $avg = $stats['average'];
        $recentTrend = end($trends) ?: 0;
        $stdDev = $stats['std_dev'];

        $decision = [
            'action' => 'Bekle',
            'reason' => 'Veriler stabil görünüyor, şu an için bir aksiyon gerekmiyor.',
            'confidence' => 'Yüksek'
        ];

        if ($recentTrend > 10) {
            $decision = [
                'action' => 'Yatırımı Artır / Stok Ekle',
                'reason' => "Son dönemde %{$recentTrend} oranında güçlü bir artış var. Bu büyüme trendini değerlendirmelisiniz.",
                'confidence' => 'Yüksek'
            ];
        } elseif ($recentTrend < -10) {
            $decision = [
                'action' => 'Önlem Al / Kampanya Yap',
                'reason' => "Son dönemde %{$recentTrend} oranında ciddi bir düşüş var. Acil müdahale gerekebilir.",
                'confidence' => 'Ortalama'
            ];
        } elseif ($stdDev > ($avg * 0.4)) {
            $decision = [
                'action' => 'Dikkatli İzle',
                'reason' => "Verilerde standart sapma çok yüksek ({$stdDev}). Bu belirsizlik risk yaratabilir.",
                'confidence' => 'Orta'
            ];
        }

        return $decision;
    }

    /**
     * Interpret the results based on statistics.
     */
    public function interpretResults(array $stats)
    {
        $interpretations = [];

        $interpretations[] = "Ortalama değer " . number_format($stats['average'], 2) . " olarak hesaplandı.";
        $interpretations[] = "En yüksek değer " . $stats['max'] . " iken, en düşük değer " . $stats['min'] . " seviyesindedir.";

        if ($stats['std_dev'] < ($stats['average'] * 0.1)) {
            $interpretations[] = "Veri seti oldukça tutarlı, sapma düşük.";
        } else {
            $interpretations[] = "Veri seti değişkenlik gösteriyor, uç değerler olabilir.";
        }

        return $interpretations;
    }

    /**
     * Simple linear projection for future prediction.
     */
    public function predictFuture(array $values)
    {
        // Simple moving average of last 3 points for next step
        $count = count($values);
        if ($count < 3) {
            return end($values);
        }

        $last3 = array_slice($values, -3);
        $prediction = array_sum($last3) / count($last3);

        return round($prediction, 2);
    }

    /**
     * Calculate Pearson Correlation Coefficient between two arrays.
     */
    public function calculateCorrelation(array $x, array $y)
    {
        if (count($x) !== count($y)) {
            return 0;
        }

        $count = count($x);
        $meanX = array_sum($x) / $count;
        $meanY = array_sum($y) / $count;

        $ax = [];
        $ay = [];
        $numerator = 0;
        $denomX = 0;
        $denomY = 0;

        for ($i = 0; $i < $count; $i++) {
            $dx = $x[$i] - $meanX;
            $dy = $y[$i] - $meanY;
            $numerator += ($dx * $dy);
            $denomX += ($dx * $dx);
            $denomY += ($dy * $dy);
        }

        $denominator = sqrt($denomX * $denomY);

        if ($denominator == 0) return 0;

        return round($numerator / $denominator, 2);
    }

    public function interpretCorrelation($score)
    {
        if ($score > 0.7) return "Güçlü Pozitif İlişki (Biri artarsa diğeri de artar)";
        if ($score > 0.3) return "Orta Pozitif İlişki";
        if ($score > -0.3) return "İlişki Yok veya Çok Zayıf";
        if ($score > -0.7) return "Orta Negatif İlişki";
        return "Güçlü Negatif İlişki (Biri artarsa diğeri azalır)";
    }

    /**
     * Calculate Variance
     */
    public function calculateVariance(array $values)
    {
        $count = count($values);
        if ($count == 0) return 0;
        $average = array_sum($values) / $count;
        $sumSqDiff = 0;
        foreach ($values as $val) {
            $sumSqDiff += pow($val - $average, 2);
        }
        return $sumSqDiff / $count;
    }

    /**
     * Calculate Quartiles and IQR
     */
    public function calculateQuartiles(array $values)
    {
        sort($values);
        $count = count($values);
        if ($count == 0) return ['q1' => 0, 'median' => 0, 'q3' => 0, 'iqr' => 0];

        $median = $this->calculateMedian($values);
        $q1 = $this->calculateMedian(array_slice($values, 0, floor($count / 2)));
        $q3 = $this->calculateMedian(array_slice($values, ceil($count / 2)));
        $iqr = $q3 - $q1;

        return [
            'q1' => $q1,
            'median' => $median,
            'q3' => $q3,
            'iqr' => $iqr
        ];
    }

    /**
     * Detect Outliers using IQR method
     */
    public function detectOutliers(array $values, array $quartiles)
    {
        $lowerBound = $quartiles['q1'] - (1.5 * $quartiles['iqr']);
        $upperBound = $quartiles['q3'] + (1.5 * $quartiles['iqr']);
        $outliers = [];

        foreach ($values as $val) {
            if ($val < $lowerBound || $val > $upperBound) {
                $outliers[] = $val;
            }
        }
        
        // Return unique sorted outliers
        $outliers = array_unique($outliers);
        sort($outliers);
        return $outliers;
    }

    /**
     * Perform Simple Linear Regression (y = mx + b)
     */
    public function performRegression(array $y)
    {
        $n = count($y);
        if ($n < 2) return ['slope' => 0, 'intercept' => 0, 'r2' => 0, 'trend' => 'Yetersiz Veri'];

        $x = range(1, $n);
        
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumXX = 0;
        $sumYY = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += ($x[$i] * $y[$i]);
            $sumXX += ($x[$i] * $x[$i]);
            $sumYY += ($y[$i] * $y[$i]);
        }

        $denominator = ($n * $sumXX) - ($sumX * $sumX);
        if ($denominator == 0) return ['slope' => 0, 'intercept' => 0, 'r2' => 0, 'trend' => 'Sabit'];

        $slope = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
        $intercept = ($sumY - ($slope * $sumX)) / $n;

        // Calculate R-Squared
        $meanY = $sumY / $n;
        $ssTotal = 0;
        $ssRes = 0;
        for ($i = 0; $i < $n; $i++) {
            $predictedY = ($slope * $x[$i]) + $intercept;
            $ssTotal += pow($y[$i] - $meanY, 2);
            $ssRes += pow($y[$i] - $predictedY, 2);
        }
        $r2 = $ssTotal == 0 ? 0 : 1 - ($ssRes / $ssTotal);

        $trend = $slope > 0 ? 'Artış Eğilimi 📈' : ($slope < 0 ? 'Azalış Eğilimi 📉' : 'Yatay Seyir ➖');

        return [
            'slope' => round($slope, 4),
            'intercept' => round($intercept, 4),
            'r2' => round($r2, 4),
            'trend' => $trend
        ];
    }

    /**
     * Calculate Statistics Grouped by Category (ANOVA-style preparation)
     */
    public function groupStatistics(array $categories, array $values)
    {
        if (count($categories) !== count($values)) return [];

        $groups = [];
        foreach ($categories as $i => $cat) {
            $cat = empty($cat) ? 'Belirsiz' : (string)$cat; // Handle missing categories
            if (!isset($groups[$cat])) $groups[$cat] = [];
            $groups[$cat][] = $values[$i];
        }

        $results = [];
        foreach ($groups as $groupName => $groupValues) {
            $avg = array_sum($groupValues) / count($groupValues);
            $results[$groupName] = [
                'count' => count($groupValues),
                'average' => round($avg, 2),
                'sum' => array_sum($groupValues)
            ];
        }

        // Sort by Average Descending
        uasort($results, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        return $results;
    }


    /**
     * Perform Independent Two-Sample T-Test (Welch's T-Test assumption for unequal variances usually safer)
     * Returns ['t_stat', 'df', 'p_value_approx', 'significant']
     * Note: P-value calculation is complex in PHP without stats lib, we will use critical value approach for typical alpha=0.05
     */
    public function performTTest(array $sample1, array $sample2)
    {
        $n1 = count($sample1);
        $n2 = count($sample2);
        
        if ($n1 < 2 || $n2 < 2) return null;

        $mean1 = array_sum($sample1) / $n1;
        $mean2 = array_sum($sample2) / $n2;

        $var1 = $this->calculateVariance($sample1);
        $var2 = $this->calculateVariance($sample2);

        // Standard Error Calculation
        $se = sqrt(($var1 / $n1) + ($var2 / $n2));
        
        if ($se == 0) return null;

        $tStat = ($mean1 - $mean2) / $se;
        
        // Degrees of Freedom (Welch-Satterthwaite equation)
        $dfNum = pow(($var1 / $n1) + ($var2 / $n2), 2);
        $dfDenom = (pow($var1 / $n1, 2) / ($n1 - 1)) + (pow($var2 / $n2, 2) / ($n2 - 1));
        $df = $dfDenom == 0 ? $n1 + $n2 - 2 : $dfNum / $dfDenom;

        // Critical Value Approximation for Alpha = 0.05 (Two-tailed)
        // For df > 30, critical value is approx 1.96. For smaller df, it's higher.
        // We act simply: if |t| > 1.96, it's significant (p < 0.05).
        $criticalValue = 1.96;
        $isSignificant = abs($tStat) > $criticalValue;

        return [
            't_stat' => round($tStat, 4),
            'df' => round($df, 2),
            'significant' => $isSignificant,
            'mean1' => $mean1,
            'mean2' => $mean2,
            'std1' => sqrt($var1),
            'std2' => sqrt($var2)
        ];
    }

    public function generateHypothesisReport($label1, $label2, $result, $sample1 = [], $sample2 = [])
    {
        if (!$result) return null;

        // Generate Distribution Curve Data
        $curveData = $this->generateNormalCurve(
            $result['mean1'], 
            $result['std1'] ?? 1, 
            $result['mean2'], 
            $result['std2'] ?? 1
        );

        return [
            'h0' => "H0 (Yokluk Hipotezi): \"{$label1}\" ve \"{$label2}\" grupları arasında istatistiksel olarak anlamlı bir fark YOKTUR. Gözlemlenen farklar tamamen şans eseridir.",
            'h1' => "H1 (Alternatif Hipotez): \"{$label1}\" ve \"{$label2}\" grupları arasında istatistiksel olarak anlamlı ve gerçek bir fark VARDIR. Bu fark şans ile açıklanamaz.",
            't_score' => $result['t_stat'],
            'decision' => $result['significant'] ? '🔴 H0 Reddedildi (Fark Var)' : '🟢 H0 Kabul Edildi (Fark Yok)',
            'interpretation' => $result['significant']
                ? "Sonuç: İki veri seti birbirinden farklı davranıyor. Yapılan T-Testi sonucunda, %95 güven aralığında bu iki grubun ortalamalarının eşit olmadığı kanıtlanmıştır. Yani, aradaki fark 'anlamlıdır' ve dikkate alınmalıdır."
                : "Sonuç: İki veri seti benzer davranıyor. İstatistiksel olarak bu iki grup arasında kayda değer bir fark bulunamamıştır. Görülen ufak farklar rastlantısal olabilir.",
            'mean1' => $result['mean1'],
            'mean2' => $result['mean2'],
            'curve_x' => $curveData['x'],
            'curve_y1' => $curveData['y1'],
            'curve_y2' => $curveData['y2'],
            'curve_y2' => $curveData['y2'],
            'raw_data1' => $sample1, 
            'raw_data2' => $sample2,
            'histogram' => $this->calculateCombinedHistogram($sample1, $sample2),
            'summary' => [
                'group1' => $this->get5PointSummary($sample1),
                'group2' => $this->get5PointSummary($sample2)
            ],
            'counts' => ['n1' => count($sample1), 'n2' => count($sample2)]
        ];
    }

    /**
     * Generate Normal Distribution Curve Data
     */
    private function generateNormalCurve($mean1, $std1, $mean2, $std2, $points = 100)
    {
        // Determine range (mean +/- 4 std deviations to cover 99.9% of data)
        // Handle zero std dev edge case
        $s1 = $std1 == 0 ? 0.1 : $std1;
        $s2 = $std2 == 0 ? 0.1 : $std2;

        $min = min($mean1 - 4 * $s1, $mean2 - 4 * $s2);
        $max = max($mean1 + 4 * $s1, $mean2 + 4 * $s2);
        
        $step = ($max - $min) / $points;
        
        $xValues = [];
        $y1Values = [];
        $y2Values = [];

        for ($i = 0; $i <= $points; $i++) {
            $x = $min + ($i * $step);
            $xValues[] = number_format($x, 2);
            
            // Normal Distribution PDF formula: (1 / (std * sqrt(2*pi))) * exp(-0.5 * ((x - mean)/std)^2)
            $y1 = (1 / ($s1 * sqrt(2 * M_PI))) * exp(-0.5 * pow(($x - $mean1) / $s1, 2));
            $y2 = (1 / ($s2 * sqrt(2 * M_PI))) * exp(-0.5 * pow(($x - $mean2) / $s2, 2));
            
            $y1Values[] = $y1;
            $y2Values[] = $y2;
        }

        return ['x' => $xValues, 'y1' => $y1Values, 'y2' => $y2Values];
    }

    /**
     * Calculate 5-Point Summary (Min, Q1, Median, Q3, Max) + Mean, StdDev for Radar/Box
     */
    private function get5PointSummary($values)
    {
        if (empty($values)) return ['min'=>0, 'q1'=>0, 'median'=>0, 'q3'=>0, 'max'=>0, 'mean'=>0, 'std'=>0];
        
        sort($values);
        $count = count($values);
        $min = $values[0];
        $max = $values[$count - 1];
        
        // Median
        $median = $this->calculateMedian($values);
        
        // Q1/Q3 Approx
        $q1 = $values[floor($count * 0.25)];
        $q3 = $values[floor($count * 0.75)];
        
        $mean = array_sum($values) / $count;
        $variance = 0;
        foreach($values as $v) $variance += pow($v-$mean, 2);
        $std = sqrt($variance/$count);
        
        return compact('min', 'q1', 'median', 'q3', 'max', 'mean', 'std');
    }

    /**
     * Calculate Combined Histogram for 2 Groups (same buckets)
     */
    private function calculateCombinedHistogram($v1, $v2, $buckets = 6)
    {
        $all = array_merge($v1, $v2);
        if(empty($all)) return ['labels'=>[], 'data1'=>[], 'data2'=>[]];

        $min = min($all);
        $max = max($all);
        $step = ($max - $min) / $buckets;
        if($step == 0) $step = 1;

        $labels = [];
        $d1 = array_fill(0, $buckets, 0);
        $d2 = array_fill(0, $buckets, 0);

        for($i=0; $i<$buckets; $i++) {
            $start = $min + ($i * $step);
            $end = $start + $step;
            $labels[] = number_format($start, 1) . '-' . number_format($end, 1);
        }

        foreach($v1 as $v) {
            $idx = floor(($v - $min) / $step);
            if($idx >= $buckets) $idx = $buckets - 1;
            $d1[$idx]++;
        }
        foreach($v2 as $v) {
            $idx = floor(($v - $min) / $step);
            if($idx >= $buckets) $idx = $buckets - 1;
            $d2[$idx]++;
        }

        return ['labels' => $labels, 'data1' => $d1, 'data2' => $d2];
    }

    /**
     * Calculate Histogram (Frequency Distribution) for Pie/Bar charts
     */
    public function calculateHistogram(array $values, $buckets = 5)
    {
        if (empty($values)) return [];
        
        $min = min($values);
        $max = max($values);
        $range = $max - $min;
        
        if ($range == 0) return ["{$min}" => count($values)];

        $step = $range / $buckets;
        $histogram = [];

        // Initialize buckets
        for ($i = 0; $i < $buckets; $i++) {
            $start = $min + ($i * $step);
            $end = $start + $step;
            $label = number_format($start, 0) . " - " . number_format($end, 0);
            $histogram[$label] = 0;
        }
        
        // Fill buckets
        foreach ($values as $val) {
            $bucketIndex = floor(($val - $min) / $step);
            if ($bucketIndex >= $buckets) $bucketIndex = $buckets - 1; // max value goes to last bucket
            
            $start = $min + ($bucketIndex * $step);
            $end = $start + $step;
            $label = number_format($start, 0) . " - " . number_format($end, 0);
            
            $histogram[$label]++;
        }

        return $histogram;
    }

    /** 
     * Calculate Correlation Matrix for multiple columns
     */
    public function calculateCorrelationMatrix(array $columnsData)
    {
        $keys = array_keys($columnsData);
        $matrix = [];

        foreach ($keys as $rowKey) {
            foreach ($keys as $colKey) {
                if ($rowKey === $colKey) {
                    $matrix[$rowKey][$colKey] = 1.0;
                } else {
                    // Optimized: only calc if not already done (symmetrical)
                    if (isset($matrix[$colKey][$rowKey])) {
                        $matrix[$rowKey][$colKey] = $matrix[$colKey][$rowKey];
                    } else {
                        $score = $this->calculateCorrelation($columnsData[$rowKey], $columnsData[$colKey]);
                        $matrix[$rowKey][$colKey] = $score;
                    }
                }
            }
        }
        return $matrix;
    }

    /**
     * Find significant relationships from matrix
     */
    public function generateRelationshipInsights(array $matrix)
    {
        $insights = [];
        $processed = [];

        foreach ($matrix as $col1 => $row) {
            foreach ($row as $col2 => $score) {
                if ($col1 === $col2) continue;
                
                // Avoid duplicates (A-B same as B-A)
                $pairKey = strcmp($col1, $col2) < 0 ? "$col1|$col2" : "$col2|$col1";
                if (in_array($pairKey, $processed)) continue;
                $processed[] = $pairKey;

                if (abs($score) >= 0.7) {
                    $type = $score > 0 ? 'Güçlü Pozitif' : 'Güçlü Negatif';
                    $desc = $score > 0 
                        ? "Bu iki veri birlikte artıp azalıyor. Aralarında doğrusal bir ilişki olabilir."
                        : "Biri artarken diğeri azalıyor. Ters orantı söz konusu.";
                    
                    $insights[] = [
                        'pair' => "$col1 & $col2",
                        'col1' => $col1,
                        'col2' => $col2,
                        'score' => $score,
                        'type' => $type,
                        'description' => $desc
                    ];
                }
            }
        }
        
        // Sort by correlation strength (abs)
        usort($insights, function($a, $b) {
            return abs($b['score']) <=> abs($a['score']);
        });

        return $insights;
    }
}
