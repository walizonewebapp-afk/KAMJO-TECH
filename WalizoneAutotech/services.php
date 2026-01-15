<?php
// Array of all services
$services = [
    [
        'id' => 1,
        'name' => 'Routine Maintenance',
        'description' => 'Regular maintenance is essential to keep your vehicle running smoothly and prevent costly repairs down the road.',
        'icon' => 'wrench',
        'link' => 'services/routine-maintenance.php',
        'image' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'id' => 2,
        'name' => 'Computer Diagnostics',
        'description' => 'Modern vehicles rely heavily on computer systems to monitor and control various components. Our advanced diagnostics can identify issues quickly.',
        'icon' => 'laptop',
        'link' => 'services/computer-diagnostics.php',
        'image' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'id' => 3,
        'name' => 'Engine & Transmission Repair',
        'description' => 'The engine and transmission are the heart of your vehicle. Our skilled technicians specialize in diagnosing and repairing problems of all types.',
        'icon' => 'cogs',
        'link' => 'services/engine-transmission-repair.php',
        'image' => 'https://images.unsplash.com/photo-1580983218765-f663bec07b37?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'id' => 4,
        'name' => 'AC & Heating Services',
        'description' => 'Stay comfortable year-round with our comprehensive AC and heating system services, from recharges to complete system repairs.',
        'icon' => 'snowflake',
        'link' => '#',
        'image' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'id' => 5,
        'name' => 'Suspension & Steering',
        'description' => 'Experience a smoother, safer ride with our suspension and steering services, including shock absorbers, struts, and alignment.',
        'icon' => 'car',
        'link' => '#',
        'image' => 'https://images.unsplash.com/photo-1537984822441-cff330075342?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'id' => 6,
        'name' => 'Panel Beating & Painting',
        'description' => 'Restore your vehicle\'s appearance with our professional panel beating and custom painting services.',
        'icon' => 'paint-brush',
        'link' => '#',
        'image' => 'https://images.unsplash.com/photo-1578844251758-2f71da64c96f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'id' => 7,
        'name' => 'Electrical Systems',
        'description' => 'From battery issues to complex electrical problems, our technicians can diagnose and repair all automotive electrical systems.',
        'icon' => 'bolt',
        'link' => '#',
        'image' => 'https://images.unsplash.com/photo-1558349699-f8878747be34?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'id' => 8,
        'name' => 'Custom Modifications',
        'description' => 'Enhance your vehicle\'s performance and appearance with our custom modification services.',
        'icon' => 'tools',
        'link' => '#',
        'image' => 'https://images.unsplash.com/photo-1562911791-c7a97b729ec5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Our Services - Walizone Autotech</title>
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
      margin-bottom: 30px;
    }
    .services-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 30px;
    }
    .service-card {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .service-card:hover {
      transform: translateY(-10px);
    }
    .service-image {
      height: 200px;
      background-size: cover;
      background-position: center;
    }
    .service-content {
      padding: 20px;
    }
    .service-icon {
      color: #0d47a1;
      font-size: 2em;
      margin-bottom: 10px;
    }
    .service-title {
      font-size: 1.5em;
      margin-bottom: 10px;
      color: #0d47a1;
    }
    .service-description {
      margin-bottom: 15px;
      line-height: 1.6;
    }
    .service-link {
      display: inline-block;
      padding: 8px 15px;
      background-color: #0d47a1;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s;
    }
    .service-link:hover {
      background-color: #1565c0;
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
      .services-container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <nav>
    <a href="index.php#about">About</a>
    <a href="index.php#services">Services</a>
    <a href="index.php#contact">Contact</a>
  </nav>

  <header>
    <h1>Our Services</h1>
    <p>Professional automotive solutions for all your needs</p>
  </header>

  <section>
    <h2>Comprehensive Automotive Services</h2>
    <p style="text-align: center; margin-bottom: 30px;">At Walizone Autotech, we offer a wide range of automotive services to keep your vehicle running at its best. Our skilled technicians use the latest tools and techniques to provide quality service for all makes and models.</p>
    
    <div class="services-container">
      <?php foreach ($services as $service): ?>
        <div class="service-card">
          <div class="service-image" style="background-image: url('<?php echo htmlspecialchars($service['image']); ?>')"></div>
          <div class="service-content">
            <div class="service-icon">
              <i class="fas fa-<?php echo htmlspecialchars($service['icon']); ?>"></i>
            </div>
            <h3 class="service-title"><?php echo htmlspecialchars($service['name']); ?></h3>
            <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
            <a href="<?php echo htmlspecialchars($service['link']); ?>" class="service-link">Learn More</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Walizone Autotech Enterprise. All rights reserved.</p>
  </footer>
</body>
</html>