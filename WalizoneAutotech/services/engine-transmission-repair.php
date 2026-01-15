<?php
// Service information for Engine & Transmission Repair
$service = [
    'id' => 3,
    'name' => 'Engine & Transmission Repair',
    'description' => 'The engine and transmission are the heart of your vehicle, and problems with these components can lead to significant performance issues and costly repairs if not addressed promptly. At Walizone Autotech, our skilled technicians specialize in diagnosing and repairing engine and transmission problems of all types. From minor repairs to complete rebuilds, we have the expertise and equipment to get your vehicle running smoothly again.',
    'icon' => 'cogs',
    'benefits' => [
        'Restoration of optimal vehicle performance and power',
        'Improved fuel efficiency and reduced emissions',
        'Extended lifespan of your vehicle',
        'Prevention of cascading damage to other components',
        'Reliable repairs with quality parts and workmanship'
    ],
    'process' => [
        'Step 1: Comprehensive diagnostic testing',
        'Step 2: Detailed inspection of engine or transmission components',
        'Step 3: Documentation of all issues and development of repair plan',
        'Step 4: Transparent cost estimate and timeline for repairs',
        'Step 5: Skilled execution of necessary repairs or rebuilding',
        'Step 6: Thorough quality testing and verification',
        'Step 7: Road testing to ensure proper operation'
    ],
    'pricing' => [
        'Minor Engine Repairs' => 'KSh 5,000 - 15,000',
        'Major Engine Repairs' => 'KSh 20,000 - 50,000',
        'Engine Rebuild/Replacement' => 'KSh 60,000 - 150,000+',
        'Transmission Service' => 'KSh 8,000 - 15,000',
        'Transmission Repair' => 'KSh 20,000 - 60,000',
        'Transmission Rebuild/Replacement' => 'KSh 70,000 - 180,000+'
    ],
    'faqs' => [
        [
            'question' => 'What are the signs that my engine needs repair?',
            'answer' => 'Common signs include unusual noises (knocking, ticking, grinding), decreased performance, excessive exhaust smoke, overheating, warning lights on the dashboard, reduced fuel efficiency, oil leaks, or the engine stalling or not starting. If you notice any of these symptoms, it\'s best to have your vehicle inspected promptly.'
        ],
        [
            'question' => 'How do I know if my transmission is failing?',
            'answer' => 'Symptoms of transmission problems include difficulty shifting gears, slipping gears, delayed engagement when putting the vehicle in gear, unusual noises (whining, buzzing, clunking), burning smell, leaking transmission fluid, warning lights, or the vehicle shaking or jerking during acceleration.'
        ],
        [
            'question' => 'Is it better to repair or replace an engine/transmission?',
            'answer' => 'This depends on several factors including the extent of damage, age of the vehicle, cost comparison between repair and replacement, and your long-term plans for the vehicle. We provide honest assessments and help you make the most cost-effective decision based on your specific situation.'
        ],
        [
            'question' => 'How long do engine and transmission repairs take?',
            'answer' => 'Minor repairs may take 1-2 days, while major repairs or rebuilds can take 5-10 business days or longer, depending on the complexity of the job and parts availability. We provide estimated timeframes before beginning work.'
        ],
        [
            'question' => 'Do you offer warranties on engine and transmission repairs?',
            'answer' => 'Yes, we stand behind our work. Most major engine and transmission repairs come with a warranty covering both parts and labor. The specific warranty terms depend on the type of repair and parts used. We\'ll provide detailed warranty information before beginning any work.'
        ]
    ],
    'related_services' => [
        [
            'name' => 'Computer Diagnostics',
            'description' => 'Advanced electronic diagnostics to identify complex issues.',
            'link' => 'computer-diagnostics.php'
        ],
        [
            'name' => 'Routine Maintenance',
            'description' => 'Regular maintenance to prevent major engine and transmission problems.',
            'link' => 'routine-maintenance.php'
        ],
        [
            'name' => 'Electrical Systems',
            'description' => 'Diagnosis and repair of electrical components that affect engine performance.',
            'link' => 'electrical-systems.php'
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
      background: url('https://images.unsplash.com/photo-1580983218765-f663bec07b37?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center/cover;
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