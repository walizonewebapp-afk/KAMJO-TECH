<?php
// This is a template file for service detail pages
// Replace the placeholders with actual content for each service

// Service information (replace with actual data)
$service = [
    'id' => 0,
    'name' => 'Service Name',
    'description' => 'Detailed service description goes here.',
    'icon' => 'wrench',
    'benefits' => [
        'Benefit 1',
        'Benefit 2',
        'Benefit 3'
    ],
    'process' => [
        'Step 1: Initial assessment',
        'Step 2: Diagnosis',
        'Step 3: Service execution',
        'Step 4: Quality check'
    ],
    'pricing' => [
        'Basic' => 'KSh X,XXX',
        'Standard' => 'KSh X,XXX',
        'Premium' => 'KSh X,XXX'
    ],
    'faqs' => [
        [
            'question' => 'Frequently asked question 1?',
            'answer' => 'Answer to question 1.'
        ],
        [
            'question' => 'Frequently asked question 2?',
            'answer' => 'Answer to question 2.'
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
      background: url('https://images.unsplash.com/photo-1570129477492-45c003edd2be') no-repeat center/cover;
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
    
    <a href="../index.php#contact" class="cta-button">Book This Service</a>
    
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
      <div class="related-service-card">
        <h4>Related Service 1</h4>
        <p>Brief description of related service.</p>
        <a href="#">Learn More</a>
      </div>
      <div class="related-service-card">
        <h4>Related Service 2</h4>
        <p>Brief description of related service.</p>
        <a href="#">Learn More</a>
      </div>
      <div class="related-service-card">
        <h4>Related Service 3</h4>
        <p>Brief description of related service.</p>
        <a href="#">Learn More</a>
      </div>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Walizone Autotech Enterprise. All rights reserved.</p>
  </footer>
</body>
</html>