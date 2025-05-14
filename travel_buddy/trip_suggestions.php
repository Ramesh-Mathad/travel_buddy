<?php
// filepath: c:\xampp\htdocs\travel_buddy\trip_suggestions.php
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$destination_display = htmlspecialchars($destination);

// Example: You can expand this array or fetch from a database for real data
$suggestions = [
    'Goa' => [
        'budget_options' => [
            'Economy' => 10000,
            'Standard' => 15000,
            'Luxury' => 20000
        ],
        'best_time' => 'November to February',
        'weather' => 'Pleasant, 20°C - 32°C',
        'tips' => [
            'Carry sunscreen and light clothes.',
            'Book hotels in advance during peak season.',
            'Try local Goan cuisine.'
        ],
        'tour_links' => [
            'MakeMyTrip' => 'https://www.makemytrip.com/holidays-india/goa-travel-packages.html',
            'Yatra' => 'https://www.yatra.com/india-tour-packages/holidays-in-goa',
            'TripAdvisor' => 'https://www.tripadvisor.in/Tourism-g297604-Goa-Vacations.html',
            'Booking.com' => 'https://www.booking.com/city/in/goa.html',
            'Weather.com' => 'https://weather.com/en-IN/weather/today/l/Goa+India?canonicalCityId=2b6b6e6e3e7e2e6e3e7e2e6e3e7e2e6e3e7e2e6e3e7e2e6e'
        ]
    ],
    'Manali' => [
        'budget_options' => [
            'Economy' => 8000,
            'Standard' => 12000,
            'Luxury' => 18000
        ],
        'best_time' => 'October to June',
        'weather' => 'Cool, 10°C - 25°C',
        'tips' => [
            'Pack warm clothes.',
            'Ideal for trekking and adventure sports.',
            'Book Volvo buses for comfortable travel.'
        ],
        'tour_links' => [
            'MakeMyTrip' => 'https://www.makemytrip.com/holidays-india/manali-travel-packages.html',
            'Yatra' => 'https://www.yatra.com/india-tour-packages/holidays-in-manali',
            'TripAdvisor' => 'https://www.tripadvisor.in/Tourism-g297618-Manali_Manali_Tehsil_Kullu_District_Himachal_Pradesh-Vacations.html',
            'Booking.com' => 'https://www.booking.com/city/in/manali.html',
            'Weather.com' => 'https://weather.com/en-IN/weather/today/l/Manali+Himachal+Pradesh?canonicalCityId=2b6b6e6e3e7e2e6e3e7e2e6e3e7e2e6e3e7e2e6e3e7e2e6e'
        ]
    ]
];

// Default suggestion if destination not found
$info = $suggestions[$destination] ?? [
    'budget_options' => [
        'Economy' => 7000,
        'Standard' => 12000,
        'Luxury' => 20000
    ],
    'best_time' => 'Check online',
    'weather' => 'Check online',
    'tips' => [
        'Search for local travel blogs for more info.',
        'Compare tour packages for best deals.'
    ],
    'tour_links' => [
        'Google Search' => 'https://www.google.com/search?q=' . urlencode($destination_display) . '+tour+packages',
        'TripAdvisor' => 'https://www.tripadvisor.com/Search?q=' . urlencode($destination_display),
        'Booking.com' => 'https://www.booking.com/searchresults.html?ss=' . urlencode($destination_display),
        'Weather.com' => 'https://weather.com/weather/today/l/' . urlencode($destination_display)
    ]
];

// Calculate average budget
$budget_values = array_values($info['budget_options']);
$average_budget = round(array_sum($budget_values) / count($budget_values));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trip Suggestions - <?= $destination_display ?></title>
    <style>
        body {
            background: linear-gradient(120deg, #e0eafc, #cfdef3 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            background: #fff;
            max-width: 650px;
            margin: 40px auto;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.13);
            padding: 36px 30px 30px 30px;
        }
        .brand-link {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            margin-bottom: 18px;
        }
        .brand-logo {
            font-size: 2.1rem;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 2px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        h1 {
            color: #007bff;
            font-weight: 700;
            margin-bottom: 18px;
            text-align: center;
        }
        h2 {
            text-align: center;
            color: #232946;
            margin-bottom: 28px;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }
        .info-section {
            background: #f7faff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,123,255,0.06);
            padding: 22px 20px 16px 20px;
            margin-bottom: 22px;
            border-left: 5px solid #007bff;
        }
        .section-title {
            color: #007bff;
            font-size: 1.13rem;
            margin-bottom: 7px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .info-value {
            color: #232946;
            font-size: 1.05rem;
            margin-bottom: 8px;
        }
        .budget-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .budget-table th, .budget-table td {
            padding: 7px 12px;
            border-bottom: 1px solid #e0eafc;
            text-align: left;
        }
        .budget-table th {
            background: #e0eafc;
            color: #007bff;
            font-weight: 600;
        }
        .budget-table tr:last-child td {
            border-bottom: none;
        }
        .average-budget {
            background: #e0ffe0;
            color: #218838;
            font-weight: 600;
            padding: 7px 12px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 6px;
        }
        ul {
            margin: 0 0 16px 18px;
        }
        .tips-list li {
            margin-bottom: 6px;
            color: #444;
        }
        .tour-links {
            margin-top: 10px;
        }
        .tour-links a {
            display: inline-block;
            margin: 6px 10px 6px 0;
            color: #fff;
            background: #007bff;
            padding: 7px 18px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 6px rgba(0,123,255,0.07);
        }
        .tour-links a:hover {
            background: #0056b3;
            box-shadow: 0 4px 12px rgba(0,123,255,0.13);
        }
        .back-link {
            display: inline-block;
            margin-top: 18px;
            color: #007bff;
            text-decoration: underline;
            font-size: 1rem;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #0056b3;
        }
        .info-link {
            color: #007bff;
            text-decoration: underline;
            font-size: 0.98em;
            margin-left: 8px;
            transition: color 0.2s;
        }
        .info-link:hover {
            color: #0056b3;
        }
        @media (max-width: 700px) {
            .container {
                max-width: 98vw;
                padding: 10px;
            }
            .info-section {
                padding: 14px 8px 10px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html" class="brand-link">
            <span class="brand-logo">Travel Buddy</span>
        </a>
        <h1>Trip Suggestions</h1>
        <h2><?= $destination_display ? $destination_display : 'Your Destination' ?></h2>
        
        <div class="info-section">
            <div class="section-title">Estimated Budget</div>
            <table class="budget-table">
                <tr>
                    <th>Option</th>
                    <th>Estimated Cost (₹)</th>
                </tr>
                <?php foreach ($info['budget_options'] as $type => $cost): ?>
                <tr>
                    <td><?= htmlspecialchars($type) ?></td>
                    <td><?= number_format($cost) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div class="average-budget">
                Average Budget: ₹<?= number_format($average_budget) ?>
            </div>
        </div>

       <div class="info-section">
            <div class="section-title">Best Places to Visit</div>
            <div class="info-value">
                <?= htmlspecialchars($info['best_time']) ?>
                <?php if ($destination_display): ?>
                    <a class="info-link" href="https://www.google.com/search?q=best+places+to+visit+in+<?= urlencode($destination_display) ?>" target="_blank">
                        More Info
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="info-section">
            <div class="section-title">Weather</div>
            <div class="info-value">
                <?= htmlspecialchars($info['weather']) ?>
                <?php if ($destination_display): ?>
                    <a class="info-link" href="https://www.google.com/search?q=<?= urlencode($destination_display) ?>+weather" target="_blank">
                        Current Weather
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="info-section">
            <div class="section-title">Tips & General Info</div>
            <ul class="tips-list">
                <?php foreach ($info['tips'] as $tip): ?>
                    <li><?= htmlspecialchars($tip) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="info-section">
            <div class="section-title">Tour Packages & More</div>
            <div class="tour-links">
                <?php foreach ($info['tour_links'] as $site => $link): ?>
                    <a href="<?= htmlspecialchars($link) ?>" target="_blank"><?= htmlspecialchars($site) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <a href="javascript:history.back()" class="back-link">&larr; Back</a>
    </div>
</body>
</html>