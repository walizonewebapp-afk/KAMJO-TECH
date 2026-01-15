<?php
// Service information for Routine Maintenance
$service = [
    'id' => 1,
    'name' => 'Routine Maintenance',
    'description' => 'Regular maintenance is essential to keep your vehicle running smoothly and prevent costly repairs down the road. At Walizone Autotech, we provide comprehensive routine maintenance services to ensure your vehicle remains in optimal condition. Our skilled technicians follow manufacturer-recommended service schedules and use quality parts and fluids to extend your vehicle\'s life and maintain its performance.',
    'icon' => 'wrench',
    'benefits' => [
        'Extends vehicle lifespan and preserves value',
        'Improves fuel efficiency and reduces emissions',
        'Prevents unexpected breakdowns and costly repairs',
        'Ensures optimal performance and safety',
        'Maintains manufacturer warranty requirements'
    ],
    'process' => [
        'Step 1: Comprehensive vehicle inspection',
        'Step 2: Fluid checks and replacements (oil, coolant, brake fluid, etc.)',
        'Step 3: Filter replacements (oil, air, fuel, cabin)',
        'Step 4: Tire inspection, rotation, and pressure adjustment',
        'Step 5: Brake system check and service',
        'Step 6: Battery testing and terminal cleaning',
        'Step 7: Final quality check and road test'
    ],
    'pricing' => [
        'Basic Service' => 'KSh 2,500 - 3,500',
        'Intermediate Service' => 'KSh 4,000 - 6,000',
        'Comprehensive Service' => 'KSh 7,000 - 10,000'
    ],
    'faqs' => [
        [
            'question' => 'How often should I bring my vehicle for routine maintenance?',
            'answer' => 'Most manufacturers recommend servicing every 5,000 to 10,000 kilometers or every 6 months, whichever comes first. However, this can vary based on your vehicle make, model, age, and driving conditions. We can help establish a maintenance schedule tailored to your specific vehicle.'
        ],
        [
            'question' => 'What\'s included in your basic maintenance service?',
            'answer' => 'Our basic service includes oil and oil filter change, fluid level checks and top-ups, tire pressure check and adjustment, battery check, lights and electrical system check, and a general safety inspection.'
        ],
        [
            'question' => 'How long does a routine maintenance service take?',
            'answer' => 'A basic service typically takes 1-2 hours, while more comprehensive services may take 3-4 hours. We strive to complete all services efficiently without compromising on quality.'
        ],
        [
            'question' => 'Do you use original manufacturer parts?',
            'answer' => 'We offer both genuine manufacturer parts and high-quality aftermarket alternatives. We can discuss the options and help you choose based on your preferences and budget.'
        ],
        [
            'question' => 'Will routine maintenance affect my vehicle\'s warranty?',
            'answer' => 'No, regular maintenance at our facility will not void your manufacturer\'s warranty. We follow all manufacturer-specified procedures and use approved parts and fluids.'
        ]
    ],
    'related_services' => [
        [
            'name' => 'Engine & Transmission Repair',
            'description' => 'Complete repair and maintenance for your vehicle\'s powertrain.',
            'link' => 'engine-transmission-repair.php'
        ],
        [
            'name' => 'Computer Diagnostics',
            'description' => 'Advanced electronic diagnostics to identify complex issues.',
            'link' => 'computer-diagnostics.php'
        ],
        [
            'name' => 'Suspension & Steering',
            'description' => 'Ensure a smooth ride with our suspension and steering services.',
            'link' => 'suspension-steering.php'
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($service['name']); ?> - Walizone Autotech</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      scroll-behavior: smooth;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f0f0;
      color: #333;
    }
    header {
      background: url('https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center/cover;
      height: 40vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      padding: 20px;
      position: relative;
    }
    header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
    }
    header h1 {
      font-size: 3em;
      margin-bottom: 10px;
      position: relative;
      z-index: 1;
    }
    header p {
      font-size: 1.2em;
      position: relative;
      z-index: 1;
    }
    nav {
      background: #0d47a1;
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 20px;
      padding: 15px 0;
    }
    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      font-size: 1em;
    }
    nav a:hover {
      text-decoration: underline;
    }
    section {
      padding: 50px 20px;
      max-width: 1200px;
      margin: auto;
      background: white;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #0d47a1;
      text-align: center;
      margin-bottom: 20px;
    }
    h3 {
      color: #0d47a1;
      margin: 20px 0 10px 0;
    }
    p {
      line-height: 1.6;
      margin-bottom: 15px;
    }
    .service-icon {
      font-size: 4em;
      color: #0d47a1;
      text-align: center;
      margin: 20px 0;
    }
    .benefits-list, .process-list {
      list-style-type: none;
      margin: 20px 0;
    }
    .benefits-list li, .process-list li {
      padding: 10px 0;
      border-bottom: 1px solid #eee;
    }
    .benefits-list li:before {
      content: "✓";
      color: #0d47a1;
      margin-right: 10px;
      font-weight: bold;
    }
    .process-list li {
      padding-left: 30px;
      position: relative;
    }
    .process-list li:before {
      content: "";
      position: absolute;
      left: 0;
      top: 15px;
      width: 10px;
      height: 10px;
      background: #0d47a1;
      border-radius: 50%;
    }
    .process-list li:after {
      content: "";
      position: absolute;
      left: 5px;
      top: 25px;
      width: 1px;
      height: 100%;
      background: #0d47a1;
    }
    .process-list li:last-child:after {
      display: none;
    }
    .pricing-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    .pricing-table th, .pricing-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    .pricing-table th {
      background-color: #f5f5f5;
      color: #0d47a1;
    }
    .cta-button {
      display: block;
      width: 200px;
      margin: 30px auto;
      padding: 15px;
      background-color: #0d47a1;
      color: white;
      text-align: center;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s;
    }
    .cta-button:hover {
      background-color: #1565c0;
    }
    .faq-item {
      margin-bottom: 20px;
    }
    .faq-question {
      font-weight: bold;
      color: #0d47a1;
      margin-bottom: 5px;
    }
    .faq-answer {
      padding-left: 20px;
    }
    .related-services {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    .related-service-card {
      background: #e3f2fd;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      transition: transform 0.3s;
    }
    .related-service-card:hover {
      background: #bbdefb;
      transform: translateY(-5px);
    }
    .related-service-card a {
      text-decoration: none;
      color: #333;
    }
    footer {
      background: #0d47a1;
      color: white;
      text-align: center;
      padding: 20px;
      margin-top: 20px;
    }
    @media(max-width: 768px) {
      header h1 {
        font-size: 2em;
      }
      .pricing-table {
        font-size: 0.9em;
      }
    }
  </style>
</head>
<body>
  <nav>
    <a href="../index.php#about">About</a>
    <a href="../index.php#services">Services</a>
    <a href="../services.php">All Services</a>
    <a href="../index.php#contact">Contact</a>
  </nav>

  <header>
    <h1><?php echo htmlspecialchars($service['name']); ?></h1>
    <p>Professional automotive services by Walizone Autotech</p>
  </header>

  <section>
    <div class="service-icon">
      <i class="fas fa-<?php echo htmlspecialchars($service['icon']); ?>"></i>
    </div>
    
    <h2><?php echo htmlspecialchars($service['name']); ?></h2>
    
    <p><?php echo htmlspecialchars($service['description']); ?></p>
    
    <h3>Benefits</h3>
    <ul class="benefits-list">
      <?php foreach ($service['benefits'] as $benefit): ?>
        <li><?php echo htmlspecialchars($benefit); ?></li>
      <?php endforeach; ?>
    </ul>
    
    <h3>Our Process</h3>
    <ul class="process-list">
      <?php foreach ($service['process'] as $step): ?>
        <li><?php echo htmlspecialchars($step); ?></li>
      <?php endforeach; ?>
    </ul>
    
    <h3>Pricing</h3>
    <table class="pricing-table">
      <tr>
        <th>Service Level</th>
        <th>Price</th>
      </tr>
      <?php foreach ($service['pricing'] as $level => $price): ?>
        <tr>
          <td><?php echo htmlspecialchars($level); ?></td>
          <td><?php echo htmlspecialchars($price); ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
    <p class="pricing-note">* Prices may vary depending on vehicle make, model, and condition. Contact us for a precise quote.</p>
    
    <a href="../booking.php" class="cta-button">Book This Service</a>
    
    <h3>Frequently Asked Questions</h3>
    <div class="faq-section">
      <?php foreach ($service['faqs'] as $faq): ?>
        <div class="faq-item">
          <div class="faq-question"><?php echo htmlspecialchars($faq['question']); ?></div>
          <div class="faq-answer"><?php echo htmlspecialchars($faq['answer']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    
    <h3>Related Services</h3>
    <div class="related-services">
      <?php foreach ($service['related_services'] as $related): ?>
        <div class="related-service-card">
          <h4><?php echo htmlspecialchars($related['name']); ?></h4>
          <p><?php echo htmlspecialchars($related['description']); ?></p>
          <a href="<?php echo htmlspecialchars($related['link']); ?>">Learn More</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Walizone Autotech Enterprise. All rights reserved.</p>
  </footer>
</body>
</html>