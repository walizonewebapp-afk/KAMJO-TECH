a<?php
// Start session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['customer_id']);
$customerName = $isLoggedIn ? $_SESSION['customer_name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Walizone Autotech Enterprise - Professional Automotive Services</title>
  <meta name="description" content="Walizone Autotech Enterprise offers professional automotive services in Chinsali, Zambia. Specializing in diagnostics, repairs, maintenance and more.">
  <meta name="theme-color" content="#0d47a1">
  <link rel="icon" href="images/favicon.svg" type="image/svg+xml">
  <link rel="manifest" href="manifest.json">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #0d47a1;
      --primary-light: #5472d3;
      --primary-dark: #002171;
      --secondary-color: #ff6f00;
      --secondary-light: #ffa040;
      --secondary-dark: #c43e00;
      --text-light: #ffffff;
      --text-dark: #333333;
      --background-light: #f5f5f5;
      --background-white: #ffffff;
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      scroll-behavior: smooth;
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--background-light);
      color: var(--text-dark);
      line-height: 1.6;
    }
    
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      color: var(--primary-color);
    }
    
    /* Header & Navigation */
    .top-bar {
      background-color: var(--primary-dark);
      color: var(--text-light);
      padding: 10px 0;
      font-size: 0.9rem;
    }
    
    .top-bar-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .top-contact {
      display: flex;
      gap: 20px;
    }
    
    .top-contact a {
      color: var(--text-light);
      text-decoration: none;
      display: flex;
      align-items: center;
    }
    
    .top-contact i {
      margin-right: 5px;
    }
    
    .social-icons {
      display: flex;
      gap: 15px;
    }
    
    .social-icons a {
      color: var(--text-light);
      font-size: 1.1rem;
      transition: var(--transition);
    }
    
    .social-icons a:hover {
      color: var(--secondary-color);
    }
    
    /* Main Header */
    .main-header {
      background-color: var(--background-white);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: all 0.3s ease;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .main-header.header-scrolled {
      padding: 5px 0;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      background-color: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
    }
    
    .main-header.header-scrolled .logo-img {
      height: 90px;
    }
    
    .main-header.header-scrolled .logo-name {
      font-size: 1.4rem;
    }
    
    .main-header.header-scrolled .logo-tagline {
      font-size: 0.8rem;
    }
    
    .main-header.header-scrolled .nav-link {
      padding: 10px 0;
    }
    
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 10px 20px;
      transition: padding 0.3s ease;
    }
    
    /* Logo */
    .logo {
      display: flex;
      align-items: center;
      text-decoration: none;
      position: relative;
      z-index: 10;
      padding: 8px 0;
    }
    
    .logo-img {
      height: 120px;
      width: auto;
      margin-right: 20px;
      transition: transform 0.3s ease;
      filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.35));
      object-fit: contain;
      max-width: 100%;
      display: block;
    }
    
    .logo:hover .logo-img {
      transform: scale(1.05);
    }
    
    .logo-text {
      display: flex;
      flex-direction: column;
      padding-left: 2px;
    }
    
    .logo-name {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-color);
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
      position: relative;
      display: inline-block;
    }
    
    .logo-highlight {
      color: var(--secondary-color);
      font-weight: 800;
      position: relative;
    }
    
    .logo-highlight::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background-color: var(--secondary-color);
      transform: scaleX(0);
      transform-origin: right;
      transition: transform 0.3s ease;
    }
    
    .logo:hover .logo-highlight::after {
      transform: scaleX(1);
      transform-origin: left;
    }
    
    .logo-tagline {
      font-size: 1rem;
      color: #444;
      letter-spacing: 0.8px;
      transition: all 0.3s ease;
      margin-top: 5px;
      position: relative;
      padding-left: 2px;
      font-weight: 500;
      border-left: 4px solid var(--primary-light);
      padding-left: 10px;
      opacity: 1;
    }
    
    .logo:hover .logo-tagline {
      color: var(--primary-dark);
      transform: translateX(3px);
    }
    
    /* Cursor effect for typing animation */
    .typing-cursor {
      display: inline-block;
      width: 2px;
      height: 1em;
      background-color: var(--primary-color);
      margin-left: 2px;
      animation: blink 1s infinite;
      vertical-align: middle;
    }
    
    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0; }
    }
    
    /* Main Navigation */
    .main-nav {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex: 1;
      margin-left: 20px;
    }
    
    .nav-menu {
      display: flex;
      list-style: none;
      gap: 5px;
      align-items: center;
      margin: 0;
      padding: 0;
    }
    
    .nav-item {
      position: relative;
    }
    
    .nav-link {
      text-decoration: none;
      color: var(--text-dark);
      font-weight: 500;
      font-size: 0.95rem;
      padding: 12px 15px;
      transition: all 0.3s ease;
      display: inline-block;
      position: relative;
      letter-spacing: 0.3px;
      border-radius: 4px;
    }
    
    .nav-link:hover, 
    .nav-link.active {
      color: var(--primary-color);
      background-color: rgba(13, 71, 161, 0.05);
    }
    
    .nav-link.active {
      font-weight: 600;
    }
    
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: 8px;
      left: 15px;
      width: 0;
      height: 2px;
      background-color: var(--primary-color);
      transition: width 0.3s ease;
    }
    
    .nav-link:hover::after, 
    .nav-link.active::after {
      width: calc(100% - 30px);
    }
    
    .nav-link i {
      font-size: 0.75rem;
      margin-left: 3px;
      transition: transform 0.3s ease;
      opacity: 0.8;
    }
    
    .nav-link:hover i {
      transform: rotate(180deg);
      opacity: 1;
    }
    
    /* Book Now Navigation Link */
    .book-now-nav {
      margin-left: 5px;
    }
    
    .book-now-link {
      background-color: var(--secondary-color);
      color: var(--text-light) !important;
      border-radius: 50px;
      padding: 8px 15px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(255, 111, 0, 0.2);
    }
    
    .book-now-link:hover, 
    .book-now-link.active {
      background-color: var(--secondary-dark);
      color: var(--text-light) !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 111, 0, 0.3);
    }
    
    .book-now-link::after {
      display: none;
    }
    
    .book-now-link i {
      margin-right: 4px;
    }
    
    /* Dropdown Menu */
    .dropdown {
      position: relative;
    }
    
    .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      background-color: var(--background-white);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      width: 240px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);
      transition: all 0.3s ease;
      z-index: 1000;
      padding: 10px 0;
      margin-top: 5px;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .dropdown:hover .dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    
    .dropdown-menu a {
      display: block;
      padding: 12px 20px;
      color: var(--text-dark);
      text-decoration: none;
      transition: var(--transition);
      font-size: 0.9rem;
      border-left: 0 solid var(--primary-color);
    }
    
    .dropdown-menu a:hover {
      background-color: rgba(13, 71, 161, 0.05);
      color: var(--primary-color);
      padding-left: 25px;
      border-left: 4px solid var(--primary-color);
    }
    
    /* Navigation Actions */
    .nav-actions {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-left: 20px;
      border-left: 1px solid rgba(0, 0, 0, 0.1);
      padding-left: 20px;
    }
    
    .btn-book-now {
      background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
      color: var(--text-light);
      padding: 12px 24px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 10px rgba(196, 62, 0, 0.2);
      position: relative;
      overflow: hidden;
      z-index: 1;
      letter-spacing: 0.5px;
    }
    
    .btn-book-now::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, var(--secondary-dark), var(--secondary-color));
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: -1;
    }
    
    .btn-book-now:hover::before {
      opacity: 1;
    }
    
    .btn-book-now:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(196, 62, 0, 0.3);
      filter: brightness(1.05);
    }
    
    .btn-book-now i {
      font-size: 1rem;
      transition: transform 0.3s ease;
    }
    
    .btn-book-now:hover i {
      transform: translateX(3px);
    }
    
    .btn-login {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: var(--text-light);
      padding: 12px 22px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 10px rgba(0, 33, 113, 0.2);
      position: relative;
      overflow: hidden;
      z-index: 1;
    }
    
    .btn-login::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: -1;
    }
    
    .btn-login:hover::before {
      opacity: 1;
    }
    
    .btn-login:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 33, 113, 0.3);
    }
    
    .btn-login i {
      font-size: 1rem;
    }
    
    .btn-register {
      background-color: transparent;
      border: 2px solid var(--primary-color);
      color: var(--primary-color);
      padding: 10px 20px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }
    
    .btn-register::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background-color: var(--primary-color);
      transition: width 0.3s ease;
      z-index: -1;
    }
    
    .btn-register:hover::before {
      width: 100%;
    }
    
    .btn-register:hover {
      color: var(--text-light);
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 33, 113, 0.2);
    }
    
    .btn-register i {
      font-size: 1rem;
      transition: transform 0.3s ease;
    }
    
    .btn-register:hover i {
      transform: translateX(3px);
    }
    
    /* User Menu */
    .user-menu {
      position: relative;
    }
    
    .user-menu-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      background-color: var(--primary-color);
      color: var(--text-light);
      padding: 8px 15px;
      border-radius: 4px;
      font-weight: 500;
      font-size: 0.95rem;
      transition: var(--transition);
      text-decoration: none;
      cursor: pointer;
    }
    
    .user-menu-btn:hover {
      background-color: var(--primary-dark);
    }
    
    .user-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      background-color: var(--background-white);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      width: 240px;
      z-index: 1000;
      display: none;
      margin-top: 10px;
      overflow: hidden;
    }
    
    .user-dropdown.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .user-dropdown ul {
      display: block;
      list-style: none;
      padding: 5px 0;
      margin: 0;
    }
    
    .user-dropdown li {
      padding: 0;
      margin: 0;
    }
    
    .user-dropdown a {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: var(--text-dark);
      text-decoration: none;
      transition: var(--transition);
      font-size: 0.95rem;
    }
    
    .user-dropdown a:hover {
      background-color: rgba(13, 71, 161, 0.05);
      color: var(--primary-color);
    }
    
    .user-dropdown a i {
      margin-right: 12px;
      width: 20px;
      text-align: center;
      color: var(--primary-color);
      font-size: 1rem;
    }
    
    /* Mobile Menu Button */
    .mobile-menu-btn {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: var(--primary-color);
      cursor: pointer;
      transition: var(--transition);
      position: relative;
      width: 44px;
      height: 44px;
      padding: 0;
      z-index: 100;
      border-radius: 50%;
      align-items: center;
      justify-content: center;
    }
    
    .mobile-menu-btn:hover {
      color: var(--primary-dark);
      background-color: rgba(13, 71, 161, 0.1);
    }
    
    .mobile-menu-btn.active {
      color: var(--secondary-color);
      background-color: rgba(255, 111, 0, 0.1);
    }
    
    /* Mobile Navigation Styles */
    .main-nav.mobile-active {
      display: block;
    }
    
    /* Dropdown Toggle Styles */
    .dropdown-toggle .fa-chevron-down {
      transition: transform 0.3s ease;
    }
    
    .dropdown-active .dropdown-toggle .fa-chevron-down {
      transform: rotate(180deg);
    }
    
    .dropdown-active .dropdown-menu {
      max-height: 300px !important;
      opacity: 1;
      visibility: visible;
    }
    
    /* Hero Section */
    .hero {
      position: relative;
      padding: 0;
      color: var(--text-light);
      overflow: hidden;
    }
    
    .hero-bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(rgba(0, 33, 113, 0.8), rgba(13, 71, 161, 0.7)), 
                  url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') no-repeat center/cover;
      z-index: -1;
      background-attachment: fixed;
    }
    
    .hero .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 100px 20px;
    }
    
    .hero-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-height: 500px;
    }
    
    .hero-text {
      max-width: 600px;
    }
    
    .hero-badge {
      display: inline-block;
      background-color: rgba(255, 255, 255, 0.15);
      border-radius: 50px;
      padding: 10px 20px;
      margin-bottom: 25px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      transform-origin: left;
      animation: badgePulse 3s infinite alternate;
    }
    
    @keyframes badgePulse {
      0% { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
      100% { box-shadow: 0 8px 20px rgba(255, 111, 0, 0.2); }
    }
    
    .hero-badge span {
      font-size: 0.95rem;
      font-weight: 600;
      letter-spacing: 0.8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .hero-badge i {
      color: var(--secondary-color);
      font-size: 1.1rem;
    }
    
    .hero h1 {
      font-size: 3.2rem;
      line-height: 1.2;
      margin-bottom: 20px;
      color: var(--text-light);
      font-weight: 700;
    }
    
    .hero p {
      font-size: 1.1rem;
      line-height: 1.6;
      margin-bottom: 30px;
      opacity: 0.9;
    }
    
    .hero-features {
      display: flex;
      gap: 20px;
      margin-bottom: 35px;
      flex-wrap: wrap;
    }
    
    .hero-feature {
      display: flex;
      align-items: center;
      gap: 12px;
      background-color: rgba(255, 255, 255, 0.15);
      padding: 12px 18px;
      border-radius: 8px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    .hero-feature:hover {
      transform: translateY(-3px);
      background-color: rgba(255, 255, 255, 0.2);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      box-shadow: 0 4px 8px rgba(196, 62, 0, 0.3);
      transition: all 0.3s ease;
    }
    
    .hero-feature:hover .feature-icon {
      transform: rotate(360deg);
    }
    
    .feature-text {
      font-weight: 600;
      font-size: 0.95rem;
      letter-spacing: 0.5px;
    }
    
    .hero-cta {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }
    
    .hero-cta .btn {
      padding: 14px 28px;
      font-size: 1rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      border-radius: 50px;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      overflow: hidden;
      z-index: 1;
    }
    
    .hero-cta .btn::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.1);
      transform: scaleX(0);
      transform-origin: right;
      transition: transform 0.5s ease;
      z-index: -1;
    }
    
    .hero-cta .btn:hover::after {
      transform: scaleX(1);
      transform-origin: left;
    }
    
    .hero-cta .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    .hero-cta .btn i {
      margin-right: 8px;
      font-size: 1.1rem;
      transition: transform 0.3s ease;
    }
    
    .hero-cta .btn:hover i {
      transform: translateX(3px);
    }
    
    .hero-cta .btn-primary {
      background: linear-gradient(135deg, var(--secondary-color), var(--secondary-dark));
      box-shadow: 0 5px 15px rgba(196, 62, 0, 0.3);
    }
    
    .hero-cta .btn-secondary {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: var(--text-light);
    }
    
    .hero-account {
      background-color: rgba(0, 0, 0, 0.2);
      padding: 12px 15px;
      border-radius: 5px;
      margin-top: 10px;
    }
    
    .hero-account p {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      margin: 0;
      font-size: 0.9rem;
    }
    
    .btn-text {
      color: var(--secondary-color);
      font-weight: 500;
      text-decoration: none;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    
    .btn-text:hover {
      color: var(--secondary-light);
      transform: translateX(3px);
    }
    
    .btn-text i {
      font-size: 0.8rem;
      transition: var(--transition);
    }
    
    .btn-text:hover i {
      transform: translateX(3px);
    }
    
    /* Hero Image */
    .hero-image {
      position: relative;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
      max-width: 500px;
      margin-left: auto;
      transform: perspective(1000px) rotateY(-5deg);
      transition: all 0.5s ease;
      border: 5px solid rgba(255, 255, 255, 0.1);
    }
    
    .hero-image::before {
      content: '';
      position: absolute;
      top: -10px;
      left: -10px;
      right: -10px;
      bottom: -10px;
      background: linear-gradient(45deg, 
        var(--secondary-color), 
        transparent, 
        var(--primary-color), 
        transparent, 
        var(--secondary-color)
      );
      background-size: 400% 400%;
      animation: borderGlow 8s linear infinite;
      z-index: -1;
      border-radius: 20px;
      filter: blur(15px);
      opacity: 0.7;
    }
    
    @keyframes borderGlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    .hero-image:hover {
      transform: perspective(1000px) rotateY(0deg);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
    }
    
    .hero-image img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.7s ease;
      filter: brightness(1.05) contrast(1.05);
    }
    
    .hero-image:hover img {
      transform: scale(1.05);
    }
    
    .hero-image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, rgba(0, 33, 113, 0.2), rgba(13, 71, 161, 0.4));
      pointer-events: none;
    }
    
    /* Scroll Indicator */
    .hero-scroll-indicator {
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      text-align: center;
    }
    
    .scroll-down {
      display: flex;
      flex-direction: column;
      align-items: center;
      color: var(--text-light);
      text-decoration: none;
      font-size: 0.9rem;
      opacity: 0.8;
      transition: var(--transition);
      animation: bounce 2s infinite;
    }
    
    .scroll-down:hover {
      opacity: 1;
    }
    
    .scroll-down span {
      margin-bottom: 5px;
    }
    
    .scroll-down i {
      font-size: 1.2rem;
    }
    
    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
      }
      40% {
        transform: translateY(-10px);
      }
      60% {
        transform: translateY(-5px);
      }
    }
    
    /* Animation Classes */
    .animate-fade-in {
      animation: fadeIn 1s ease forwards;
    }
    
    .animate-slide-up {
      opacity: 0;
      transform: translateY(30px);
      animation: slideUp 0.8s ease forwards;
      animation-delay: 0.2s;
    }
    
    .animate-slide-down {
      opacity: 0;
      transform: translateY(-30px);
      animation: slideDown 0.8s ease forwards;
    }
    
    .animate-slide-in {
      opacity: 0;
      transform: translateX(50px);
      animation: slideIn 1s ease forwards;
      animation-delay: 0.4s;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(50px); }
      to { opacity: 1; transform: translateX(0); }
    }
    
    /* Pulse Animation for CTA Button */
    .pulse-animation {
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(255, 111, 0, 0.4);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(255, 111, 0, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(255, 111, 0, 0);
      }
    }
    
    /* Features Section */
    .features {
      padding: 80px 0;
      background-color: var(--background-white);
    }
    
    .section-header {
      text-align: center;
      margin-bottom: 50px;
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }
    
    .section-header.animated {
      opacity: 1;
      transform: translateY(0);
    }
    
    .section-title {
      font-size: 2.5rem;
      color: var(--primary-color);
      margin-bottom: 15px;
      position: relative;
      display: inline-block;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background-color: var(--secondary-color);
    }
    
    .section-subtitle {
      font-size: 1.1rem;
      color: #666;
      max-width: 700px;
      margin: 0 auto;
      line-height: 1.6;
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
    }
    
    .feature-card {
      background-color: var(--background-white);
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: var(--transition);
      border-bottom: 3px solid transparent;
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.5s ease;
    }
    
    .feature-card.animated {
      opacity: 1;
      transform: translateY(0);
    }
    
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      border-bottom: 3px solid var(--primary-color);
    }
    
    .feature-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
      color: var(--text-light);
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 1.8rem;
      margin-bottom: 20px;
      transition: var(--transition);
      box-shadow: 0 5px 15px rgba(13, 71, 161, 0.2);
    }
    
    .feature-card:hover .feature-icon {
      transform: rotateY(180deg);
      background: linear-gradient(135deg, var(--secondary-color), var(--secondary-light));
    }
    
    .feature-title {
      margin-bottom: 15px;
      font-size: 1.4rem;
      color: var(--primary-color);
      transition: var(--transition);
    }
    
    .feature-card:hover .feature-title {
      color: var(--secondary-color);
    }
    
    .feature-card p {
      color: #666;
      line-height: 1.6;
      margin-bottom: 15px;
    }
    
    .feature-link {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      color: var(--primary-color);
      font-weight: 500;
      text-decoration: none;
      transition: var(--transition);
    }
    
    .feature-link i {
      font-size: 0.8rem;
      transition: var(--transition);
    }
    
    .feature-link:hover {
      color: var(--secondary-color);
    }
    
    .feature-link:hover i {
      transform: translateX(5px);
    }
    
    /* Animation for scroll elements */
    .animate-on-scroll {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }
    
    .animate-on-scroll.animated {
      opacity: 1;
      transform: translateY(0);
    }
    
    /* Team Section */
    .team {
      padding: 100px 0;
      background: linear-gradient(to bottom, #f8f9fa, #ffffff);
      position: relative;
      overflow: hidden;
    }
    
    .team::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%230d47a1' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      opacity: 0.5;
      z-index: 0;
    }
    
    .team .container {
      position: relative;
      z-index: 1;
    }
    
    .team-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 40px;
      margin-top: 60px;
    }
    
    .team-member {
      background-color: var(--background-white);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      opacity: 0;
      transform: translateY(30px);
      position: relative;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .team-member::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
      z-index: 1;
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s ease;
    }
    
    .team-member:hover::before {
      transform: scaleX(1);
    }
    
    .team-member.animated {
      opacity: 1;
      transform: translateY(0);
    }
    
    .team-member:hover {
      transform: translateY(-15px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .member-image {
      position: relative;
      overflow: hidden;
      height: 300px;
    }
    
    .member-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.7s ease;
      filter: brightness(1.05);
    }
    
    .team-member:hover .member-image img {
      transform: scale(1.1);
    }
    
    .member-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to top, 
        rgba(0, 33, 113, 0.9), 
        rgba(13, 71, 161, 0.7) 60%, 
        transparent 100%);
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding-bottom: 25px;
      opacity: 0;
      transition: opacity 0.4s ease;
    }
    
    .team-member:hover .member-overlay {
      opacity: 1;
    }
    
    .member-social {
      display: flex;
      gap: 15px;
    }
    
    .member-social a {
      width: 40px;
      height: 40px;
      background-color: rgba(255, 255, 255, 0.9);
      color: var(--primary-color);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      transform: translateY(30px);
      opacity: 0;
      font-size: 1rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .team-member:hover .member-social a {
      transform: translateY(0);
      opacity: 1;
    }
    
    .team-member:hover .member-social a:nth-child(1) {
      transition-delay: 0.1s;
    }
    
    .team-member:hover .member-social a:nth-child(2) {
      transition-delay: 0.2s;
    }
    
    .team-member:hover .member-social a:nth-child(3) {
      transition-delay: 0.3s;
    }
    
    .member-social a:hover {
      background-color: var(--secondary-color);
      color: var(--text-light);
      transform: translateY(-5px) scale(1.1);
    }
    
    .member-info {
      padding: 25px;
      text-align: center;
      position: relative;
      background: linear-gradient(to bottom, #ffffff, #f9f9f9);
      border-top: 1px solid rgba(0, 0, 0, 0.03);
    }
    
    .member-name {
      font-size: 1.3rem;
      color: var(--primary-dark);
      margin-bottom: 8px;
      font-weight: 700;
      position: relative;
      display: inline-block;
    }
    
    .member-name::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 50%;
      transform: translateX(-50%);
      width: 40px;
      height: 3px;
      background: var(--secondary-color);
      border-radius: 2px;
    }
    
    .member-position {
      color: var(--secondary-color);
      font-weight: 600;
      font-size: 1rem;
      margin-bottom: 15px;
      letter-spacing: 0.5px;
      display: inline-block;
      padding: 5px 15px;
      background-color: rgba(255, 111, 0, 0.1);
      border-radius: 20px;
    }
    
    .member-bio {
      color: #555;
      font-size: 0.95rem;
      line-height: 1.7;
      margin-top: 15px;
    }
    
    /* Buttons */
    .btn {
      display: inline-block;
      padding: 12px 25px;
      text-decoration: none;
      border-radius: 4px;
      font-weight: 500;
      letter-spacing: 0.5px;
      transition: var(--transition);
      border: none;
      cursor: pointer;
    }
    
    .btn-primary {
      background-color: var(--secondary-color);
      color: var(--text-light);
    }
    
    .btn-primary:hover {
      background-color: var(--secondary-dark);
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .btn-secondary {
      background-color: rgba(255, 255, 255, 0.15);
      color: var(--text-light);
      border: 1px solid rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(5px);
    }
    
    .btn-secondary:hover {
      background-color: rgba(255, 255, 255, 0.25);
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    /* Features Section */
    .features {
      padding: 80px 0;
      background-color: var(--background-white);
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .section-header {
      text-align: center;
      margin-bottom: 50px;
    }
    
    .section-title {
      font-size: 2.5rem;
      margin-bottom: 15px;
      position: relative;
      display: inline-block;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background-color: var(--secondary-color);
    }
    
    .section-subtitle {
      font-size: 1.1rem;
      color: #666;
      max-width: 700px;
      margin: 0 auto;
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
    }
    
    .feature-card {
      background-color: var(--background-white);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: var(--transition);
    }
    
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
      width: 70px;
      height: 70px;
      background-color: var(--primary-light);
      color: var(--text-light);
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 2rem;
      border-radius: 50%;
      margin: -35px auto 20px;
      position: relative;
      z-index: 1;
    }
    
    .feature-content {
      padding: 0 25px 25px;
      text-align: center;
    }
    
    .feature-title {
      margin-bottom: 15px;
      font-size: 1.3rem;
    }
    
    /* About Section */
    .about {
      padding: 80px 0;
      background-color: var(--background-light);
    }
    
    .about-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
      margin-top: 40px;
    }
    
    .about-image {
      position: relative;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      transform: perspective(1000px) rotateY(5deg);
      transition: all 0.5s ease;
      border: 8px solid white;
    }
    
    .about-image:hover {
      transform: perspective(1000px) rotateY(0deg);
    }
    
    .about-image img {
      width: 100%;
      display: block;
      transition: transform 0.7s ease;
    }
    
    .about-image:hover img {
      transform: scale(1.05);
    }
    
    .about-image::before {
      content: '';
      position: absolute;
      top: -15px;
      left: -15px;
      right: -15px;
      bottom: -15px;
      border: 5px solid var(--primary-color);
      border-radius: 20px;
      z-index: -1;
      opacity: 0.3;
      animation: pulseBorder 3s infinite;
    }
    
    @keyframes pulseBorder {
      0%, 100% { transform: scale(1); opacity: 0.3; }
      50% { transform: scale(1.05); opacity: 0.5; }
    }
    
    .about-content {
      padding: 20px;
      position: relative;
    }
    
    .about-content h3 {
      font-size: 2rem;
      color: var(--primary-dark);
      margin-bottom: 25px;
      position: relative;
      display: inline-block;
      font-weight: 700;
    }
    
    .about-content h3::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 80px;
      height: 4px;
      background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
      border-radius: 2px;
    }
    
    .about-content h4 {
      font-size: 1.4rem;
      color: var(--primary-color);
      margin: 30px 0 15px;
      font-weight: 600;
    }
    
    .about-content p {
      margin-bottom: 20px;
      font-size: 1.05rem;
      line-height: 1.7;
      color: #444;
    }
    
    .about-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
      margin-top: 40px;
      background: linear-gradient(to right, rgba(13, 71, 161, 0.03), rgba(13, 71, 161, 0.08));
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
    
    .stat-item {
      text-align: center;
      padding: 20px 10px;
      border-radius: 10px;
      background-color: white;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    .stat-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 5px;
      height: 100%;
      background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
    }
    
    .stat-item:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .stat-number {
      font-size: 3rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 10px;
      line-height: 1;
    }
    
    .stat-text {
      font-size: 1rem;
      color: #555;
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    
    /* Services Section */
    .services {
      padding: 100px 0;
      background: linear-gradient(to bottom, #f8f9fa, #ffffff);
      position: relative;
      overflow: hidden;
    }
    
    .services::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%230d47a1' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
      opacity: 0.5;
      z-index: 0;
    }
    
    .services .container {
      position: relative;
      z-index: 1;
    }
    
    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
      gap: 40px;
      margin-top: 60px;
    }
    
    .service-card {
      background-color: var(--background-white);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      border: 1px solid rgba(0, 0, 0, 0.05);
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    
    .service-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s ease;
      z-index: 1;
    }
    
    .service-card:hover::before {
      transform: scaleX(1);
    }
    
    .service-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .service-image {
      height: 220px;
      overflow: hidden;
      position: relative;
    }
    
    .service-image::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 50%;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .service-card:hover .service-image::after {
      opacity: 1;
    }
    
    .service-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.7s ease;
      filter: brightness(1.05);
    }
    
    .service-card:hover .service-image img {
      transform: scale(1.1);
    }
    
    .service-content {
      padding: 30px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      background: linear-gradient(to bottom, #ffffff, #f9f9f9);
    }
    
    .service-title {
      margin-bottom: 15px;
      font-size: 1.4rem;
      color: var(--primary-dark);
      font-weight: 700;
      position: relative;
      padding-bottom: 12px;
    }
    
    .service-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 3px;
      background: var(--secondary-color);
      border-radius: 2px;
    }
    
    .service-description {
      margin-bottom: 25px;
      color: #555;
      font-size: 1rem;
      line-height: 1.7;
      flex-grow: 1;
    }
    
    .service-link {
      display: inline-flex;
      align-items: center;
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      padding: 8px 0;
      position: relative;
      align-self: flex-start;
    }
    
    .service-link::before {
      content: '';
      position: absolute;
      bottom: 5px;
      left: 0;
      width: 100%;
      height: 2px;
      background-color: var(--primary-color);
      transform: scaleX(0);
      transform-origin: right;
      transition: transform 0.3s ease;
    }
    
    .service-link:hover::before {
      transform: scaleX(1);
      transform-origin: left;
    }
    
    .service-link i {
      margin-left: 8px;
      transition: transform 0.3s ease;
    }
    
    .service-link:hover {
      color: var(--primary-dark);
    }
    
    .service-link:hover i {
      transform: translateX(8px);
    }
    
    /* Testimonials Section */
    .testimonials {
      padding: 100px 0;
      background: linear-gradient(to bottom, #ffffff, #f8f9fa);
      position: relative;
      overflow: hidden;
    }
    
    .testimonials::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url("data:image/svg+xml,%3Csvg width='84' height='48' viewBox='0 0 84 48' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h12v6H0V0zm28 8h12v6H28V8zm14-8h12v6H42V0zm14 0h12v6H56V0zm0 8h12v6H56V8zM42 8h12v6H42V8zm0 16h12v6H42v-6zm14-8h12v6H56v-6zm14 0h12v6H70v-6zm0-16h12v6H70V0zM28 32h12v6H28v-6zM14 16h12v6H14v-6zM0 24h12v6H0v-6zm0 8h12v6H0v-6zm14 0h12v6H14v-6zm14 8h12v6H28v-6zm-14 0h12v6H14v-6zm28 0h12v6H42v-6zm14-8h12v6H56v-6zm0-8h12v6H56v-6zm14 8h12v6H70v-6zm0 8h12v6H70v-6zM14 24h12v6H14v-6zm14-8h12v6H28v-6zM14 8h12v6H14V8zM0 8h12v6H0V8z' fill='%230d47a1' fill-opacity='0.02' fill-rule='evenodd'/%3E%3C/svg%3E");
      opacity: 0.5;
    }
    
    .testimonials .container {
      position: relative;
      z-index: 1;
    }
    
    .testimonials-container {
      max-width: 900px;
      margin: 50px auto 0;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }
    
    .testimonial-card {
      background-color: var(--background-white);
      padding: 35px 30px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      position: relative;
      transition: all 0.4s ease;
      border: 1px solid rgba(0, 0, 0, 0.03);
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    
    .testimonial-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }
    
    .testimonial-card::before {
      content: '"';
      position: absolute;
      top: 20px;
      right: 25px;
      font-size: 6rem;
      color: rgba(13, 71, 161, 0.06);
      font-family: Georgia, serif;
      line-height: 1;
      z-index: 0;
    }
    
    .testimonial-card::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s ease;
      border-bottom-left-radius: 15px;
      border-bottom-right-radius: 15px;
    }
    
    .testimonial-card:hover::after {
      transform: scaleX(1);
    }
    
    .testimonial-text {
      font-style: italic;
      margin-bottom: 25px;
      color: #555;
      font-size: 1.05rem;
      line-height: 1.7;
      position: relative;
      z-index: 1;
      flex-grow: 1;
    }
    
    .testimonial-author {
      display: flex;
      align-items: center;
      margin-top: auto;
      position: relative;
      z-index: 1;
      padding-top: 20px;
      border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .author-image {
      width: 65px;
      height: 65px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border: 3px solid white;
    }
    
    .author-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .author-info h4 {
      margin-bottom: 5px;
      color: var(--primary-dark);
      font-weight: 700;
      font-size: 1.1rem;
    }
    
    .author-info p {
      color: var(--secondary-color);
      font-size: 0.9rem;
      font-weight: 600;
    }
    
    /* CTA Section */
    .cta {
      padding: 80px 0;
      background: linear-gradient(rgba(13, 71, 161, 0.9), rgba(13, 71, 161, 0.9)), url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7') no-repeat center/cover;
      color: var(--text-light);
      text-align: center;
    }
    
    .cta h2 {
      color: var(--text-light);
      margin-bottom: 20px;
      font-size: 2.5rem;
    }
    
    .cta p {
      max-width: 700px;
      margin: 0 auto 30px;
      font-size: 1.1rem;
    }
    
    /* Contact Section */
    .contact {
      padding: 80px 0;
      background-color: var(--background-white);
    }
    
    .contact-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px;
    }
    
    .contact-info h3 {
      margin-bottom: 20px;
    }
    
    .contact-details {
      margin-bottom: 30px;
    }
    
    .contact-item {
      display: flex;
      margin-bottom: 15px;
    }
    
    .contact-icon {
      width: 40px;
      height: 40px;
      background-color: var(--primary-light);
      color: var(--text-light);
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      margin-right: 15px;
    }
    
    .contact-text h4 {
      margin-bottom: 5px;
    }
    
    .contact-text p, .contact-text a {
      color: #666;
      text-decoration: none;
    }
    
    .contact-form h3 {
      margin-bottom: 20px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
      transition: var(--transition);
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--primary-color);
    }
    
    textarea.form-control {
      resize: vertical;
      min-height: 150px;
    }
    
    /* Footer */
    .footer {
      background-color: var(--primary-dark);
      color: var(--text-light);
      padding: 50px 0 0;
    }
    
    .footer-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }
    
    .footer-column h3 {
      color: var(--text-light);
      margin-bottom: 20px;
      position: relative;
      padding-bottom: 10px;
    }
    
    .footer-column h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 2px;
      background-color: var(--secondary-color);
    }
    
    .footer-links {
      list-style: none;
    }
    
    .footer-links li {
      margin-bottom: 10px;
    }
    
    .footer-links a {
      color: #bbb;
      text-decoration: none;
      transition: var(--transition);
    }
    
    .footer-links a:hover {
      color: var(--text-light);
      padding-left: 5px;
    }
    
    .footer-contact-item {
      display: flex;
      margin-bottom: 15px;
    }
    
    .footer-contact-icon {
      margin-right: 10px;
      color: var(--secondary-color);
    }
    
    .footer-social {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }
    
    .footer-social a {
      width: 40px;
      height: 40px;
      background-color: rgba(255, 255, 255, 0.1);
      color: var(--text-light);
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      transition: var(--transition);
    }
    
    .footer-social a:hover {
      background-color: var(--secondary-color);
      transform: translateY(-5px);
    }
    
    .footer-bottom {
      background-color: rgba(0, 0, 0, 0.2);
      padding: 20px 0;
      margin-top: 50px;
      text-align: center;
    }
    
    .footer-bottom p {
      margin: 5px 0;
    }
    
    .footer-bottom p:last-child {
      margin-top: 10px;
      font-size: 0.9rem;
      color: #bbdefb;
    }
    
    /* Responsive Styles */
    @media (max-width: 992px) {
      .about-container {
        grid-template-columns: 1fr;
      }
      
      .about-image {
        order: -1;
      }
      
      .contact-container {
        grid-template-columns: 1fr;
      }
      
      .hero-content {
        flex-direction: column;
      }
      
      .hero-text {
        max-width: 100%;
        margin-bottom: 30px;
      }
    }
    
    @media (max-width: 768px) {
      .top-bar-container {
        flex-direction: column;
        gap: 10px;
      }
      
      /* Header & Navigation Mobile Styles */
      .header-container {
        padding: 12px 20px;
      }
      
      .logo-img {
        height: 80px;
        margin-right: 12px;
      }
      
      .logo-name {
        font-size: 1.5rem;
      }
      
      .main-nav {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: var(--background-white);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 0;
        display: none;
        z-index: 999;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
      }
      
      .main-nav.mobile-active {
        display: block;
        animation: slideDown 0.3s ease;
      }
      
      @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      .nav-menu {
        flex-direction: column;
        gap: 0;
        width: 100%;
        padding: 10px 0;
      }
      
      .nav-item {
        width: 100%;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      }
      
      .nav-link {
        padding: 15px 20px;
        width: 100%;
        display: block;
        font-size: 1.1rem;
        font-weight: 500;
        border-radius: 0;
      }
      
      .nav-link.active {
        background-color: rgba(13, 71, 161, 0.05);
        color: var(--primary-color);
        font-weight: 600;
      }
      
      .nav-link::after {
        display: none;
      }
      
      .book-now-link {
        background-color: transparent;
        color: var(--secondary-color) !important;
        box-shadow: none;
        border-left: 4px solid var(--secondary-color);
        border-radius: 0;
        padding: 15px 20px;
      }
      
      .book-now-link:hover,
      .book-now-link.active {
        background-color: rgba(255, 111, 0, 0.1);
        transform: none;
        box-shadow: none;
      }
      
      .dropdown-menu {
        position: static;
        opacity: 1;
        visibility: visible;
        transform: none;
        width: 100%;
        box-shadow: none;
        padding: 0;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background-color: rgba(0, 0, 0, 0.02);
        border-radius: 0;
      }
      
      .dropdown.mobile-dropdown-active .dropdown-menu,
      .dropdown:hover .dropdown-menu {
        max-height: 300px;
      }
      
      /* Add a toggle button for mobile dropdown */
      .dropdown-toggle::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 20px;
        transition: transform 0.3s ease;
      }
      
      .dropdown.mobile-dropdown-active .dropdown-toggle::after {
        transform: rotate(180deg);
      }
      
      .dropdown-menu a {
        padding: 12px 20px 12px 40px;
        font-size: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
      }
      
      .dropdown-menu a:last-child {
        border-bottom: none;
      }
      
      .nav-actions {
        flex-direction: column;
        width: 100%;
        gap: 12px;
        padding: 15px 20px;
        margin-left: 0;
      }
      
      .btn-book-now,
      .btn-login,
      .btn-register {
        width: 100%;
        text-align: center;
        justify-content: center;
      }
      
      .btn-book-now, .btn-login, .btn-register {
        width: 100%;
        text-align: center;
        justify-content: center;
      }
      
      .mobile-menu-btn {
        display: block;
      }
      
      /* Hero Section Mobile Styles */
      .hero .container {
        padding: 60px 20px;
      }
      
      .hero h1 {
        font-size: 2.5rem;
      }
      
      .hero-features {
        flex-direction: column;
        gap: 10px;
      }
      
      .hero-cta {
        flex-direction: column;
        width: 100%;
      }
      
      .section-title {
        font-size: 2rem;
      }
      
      .about-stats {
        grid-template-columns: 1fr 1fr;
      }
    }
    
    @media (max-width: 576px) {
      .top-contact {
        flex-direction: column;
        gap: 5px;
      }
      
      .header-container {
        padding: 10px 15px;
      }
      
      .logo-img {
        height: 70px;
        margin-right: 0;
      }
      
      .logo-text {
        display: block;
      }
      
      .logo-name {
        font-size: 1.3rem;
      }
      
      .logo-tagline {
        font-size: 0.8rem;
      }
      
      .nav-link {
        padding: 12px 15px;
        font-size: 1rem;
      }
      
      .dropdown-menu a {
        padding: 10px 15px 10px 30px;
      }
      
      .nav-actions {
        padding: 10px 15px;
      }
      
      .btn-book-now, 
      .btn-login, 
      .btn-register {
        padding: 10px 15px;
        font-size: 0.9rem;
      }
      
      .hero h1 {
        font-size: 2rem;
      }
      
      .hero-badge {
        font-size: 0.8rem;
        padding: 6px 12px;
      }
      
      .btn {
        display: block;
        width: 100%;
        margin-bottom: 10px;
        text-align: center;
      }
      
      .btn-outline {
        margin-left: 0;
      }
      
      .about-stats {
        grid-template-columns: 1fr;
      }
      
      .services-grid, 
      .testimonial-grid, 
      .footer-container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <!-- Top Bar -->
  <div class="top-bar">
    <div class="top-bar-container">
      <div class="top-contact">
        <a href="tel:+260976664017"><i class="fas fa-phone"></i> 0976664017</a>
        <a href="mailto:mwakamule@gmail.com"><i class="fas fa-envelope"></i> mwakamule@gmail.com</a>
        <span><i class="fas fa-clock"></i> Mon-Sat: 8:00AM - 5:00PM</span>
      </div>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
  </div>

  <!-- Header -->
  <header class="main-header">
    <div class="header-container">
      <a href="index.php" class="logo">
        <img src="logo4.png" alt="Walizone Autotech Enterprise Logo" class="logo-img">
        <div class="logo-text">
          <span class="logo-name">Wali<span class="logo-highlight">zone</span> Autotech</span>
          <span class="logo-tagline" id="typingText">Professional Automotive Services</span>
        </div>
      </a>
      
      <nav class="main-nav">
        <ul class="nav-menu">
          <li class="nav-item"><a href="#home" class="nav-link active">Home</a></li>
          <li class="nav-item"><a href="#about" class="nav-link">About Us</a></li>
          <li class="nav-item dropdown">
            <a href="#services" class="nav-link dropdown-toggle">Services <i class="fas fa-chevron-down"></i></a>
            <div class="dropdown-menu">
              <a href="services/routine-maintenance.php">Routine Maintenance</a>
              <a href="services/computer-diagnostics.php">Computer Diagnostics</a>
              <a href="services/engine-transmission-repair.php">Engine & Transmission</a>
              <a href="services.php">View All Services</a>
            </div>
          </li>
          <li class="nav-item"><a href="#contact" class="nav-link">Contact</a></li>
        </ul>
        
        <div class="nav-actions">
          <?php if ($isLoggedIn): ?>
            <div class="user-menu">
              <a href="javascript:void(0);" class="user-menu-btn" onclick="toggleUserMenu()">
                <i class="fas fa-user-circle"></i> 
                <span><?php echo htmlspecialchars($customerName); ?></span>
                <i class="fas fa-chevron-down"></i>
              </a>
              <div class="user-dropdown" id="userDropdown">
                <ul>
                  <li><a href="customer-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                  <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                  <li><a href="my-vehicles.php"><i class="fas fa-car"></i> My Vehicles</a></li>
                  <li><a href="my-appointments.php"><i class="fas fa-calendar-check"></i> My Appointments</a></li>
                  <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                  <li><a href="admin/index.php"><i class="fas fa-user-shield"></i> Admin Panel</a></li>
                  <?php endif; ?>
                  <li><a href="customer-dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
              </div>
            </div>
          <?php else: ?>
            <a href="booking.php" class="btn-book-now"><i class="fas fa-calendar-check"></i> Book Appointment</a>
            <a href="login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php" class="btn-register"><i class="fas fa-user-plus"></i> Register</a>
          <?php endif; ?>
        </div>
      </nav>
      
      <button class="mobile-menu-btn" id="mobileMenuToggle" aria-label="Toggle navigation menu" aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="hero-bg"></div>
    <div class="container">
      <div class="hero-content">
        <div class="hero-text animate-fade-in">
          <div class="hero-badge animate-slide-down">
            <span><i class="fas fa-certificate"></i> Trusted Automotive Experts Since 2003</span>
          </div>
          <h1 class="animate-slide-up">Professional Automotive Services in Chinsali</h1>
          <p class="animate-fade-in">At Walizone Autotech Enterprise, we provide comprehensive automotive solutions with state-of-the-art equipment and certified technicians. Your vehicle deserves the best care possible.</p>
          
          <div class="hero-features animate-slide-up">
            <div class="hero-feature">
              <div class="feature-icon">
                <i class="fas fa-tools"></i>
              </div>
              <div class="feature-text">Expert Technicians</div>
            </div>
            <div class="hero-feature">
              <div class="feature-icon">
                <i class="fas fa-cog"></i>
              </div>
              <div class="feature-text">Modern Equipment</div>
            </div>
            <div class="hero-feature">
              <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
              </div>
              <div class="feature-text">Quality Guaranteed</div>
            </div>
          </div>
          
          <div class="hero-cta animate-slide-up">
            <a href="booking.php" class="btn btn-primary pulse-animation">
              <i class="fas fa-calendar-check"></i> Book an Appointment
            </a>
            <a href="#services" class="btn btn-secondary">
              <i class="fas fa-tools"></i> Our Services
            </a>
          </div>
          
          <?php if (!$isLoggedIn): ?>
          <div class="hero-account animate-fade-in">
            <p>
              <i class="fas fa-info-circle"></i> 
              <span>Create an account to track your service history and get exclusive offers</span>
              <a href="register.php" class="btn-text">Register Now <i class="fas fa-arrow-right"></i></a>
            </p>
          </div>
          <?php endif; ?>
        </div>
        
        <div class="hero-image animate-slide-in">
          <img src="https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Professional automotive service">
          <div class="hero-image-overlay"></div>
        </div>
      </div>
    </div>
    
    <div class="hero-scroll-indicator">
      <a href="#about" class="scroll-down">
        <span>Scroll Down</span>
        <i class="fas fa-chevron-down"></i>
      </a>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <div class="container">
      <div class="section-header animate-on-scroll">
        <h2 class="section-title">Why Choose Us</h2>
        <p class="section-subtitle">We're committed to providing exceptional automotive services with a focus on quality, reliability, and customer satisfaction.</p>
      </div>
      
      <div class="features-grid">
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon">
            <i class="fas fa-tools"></i>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">Expert Technicians</h3>
            <p>Our certified technicians have years of experience working with all vehicle makes and models.</p>
            <a href="#team" class="feature-link">Meet Our Team <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon">
            <i class="fas fa-cog"></i>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">Modern Equipment</h3>
            <p>We use state-of-the-art diagnostic tools and equipment to ensure accurate and efficient service.</p>
            <a href="#services" class="feature-link">Our Services <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        
        <div class="feature-card animate-on-scroll">
          <div class="feature-icon">
            <i class="fas fa-shield-alt"></i>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">Quality Guaranteed</h3>
            <p>We stand behind our work with service guarantees and use only quality parts for all repairs.</p>
            <a href="#testimonials" class="feature-link">See Reviews <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Team Section -->
  <section class="team" id="team">
    <div class="container">
      <div class="section-header animate-on-scroll">
        <h2 class="section-title">Our Expert Team</h2>
        <p class="section-subtitle">Meet the dedicated professionals who make Walizone Autotech the premier automotive service provider in Chinsali. With decades of combined experience, our team delivers excellence in every service.</p>
      </div>
      
      <div class="team-grid">
        <div class="team-member animate-on-scroll">
          <div class="member-image">
            <img src="images/ceo.jpg" alt="Mwaka Mulenga">
            <div class="member-overlay">
              <div class="member-social">
                <a href="#" aria-label="LinkedIn Profile"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" aria-label="Twitter Profile"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Email Contact"><i class="fas fa-envelope"></i></a>
              </div>
            </div>
          </div>
          <div class="member-info">
            <h3 class="member-name">Mwaka Mulenga</h3>
            <p class="member-position">Chief Executive Officer</p>
            <p class="member-bio">Founder and visionary leader with over 20 years of experience in the automotive industry. Certified Master Technician with expertise in business management and customer service excellence.</p>
          </div>
        </div>
        
        <div class="team-member animate-on-scroll">
          <div class="member-image">
            <img src="" alt="Iness Mulenga">
            <div class="member-overlay">
              <div class="member-social">
                <a href="#" aria-label="LinkedIn Profile"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" aria-label="Twitter Profile"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Email Contact"><i class="fas fa-envelope"></i></a>
              </div>
            </div>
          </div>
          <div class="member-info">
            <h3 class="member-name">Iness Mulenga</h3>
            <p class="member-position">Operations Director</p>
            <p class="member-bio">Oversees daily operations and strategic planning with a focus on service quality and efficiency. MBA graduate with specialized training in automotive business management.</p>
          </div>
        </div>
        
        <div class="team-member animate-on-scroll">
          <div class="member-image">
            <img src="images/Adminstrator.jpg" alt="KATONGO ABRAHAM">
            <div class="member-overlay">
              <div class="member-social">
                <a href="#" aria-label="LinkedIn Profile"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" aria-label="Twitter Profile"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Email Contact"><i class="fas fa-envelope"></i></a>
              </div>
            </div>
          </div>
          <div class="member-info">
            <h3 class="member-name">Katongo Abraham</h3>
            <p class="member-position">Administrator</p>
            <p class="member-bio">Master IT specialist with Bachelors Degree in ICT with Education and Bachelors Degree in Computer Science. I Specializes in Computer diagnostics, Network Configuration,Web Development, Graphic Designing, computer electronics and solving complex IT related problems.</p>
          </div>
        </div>
        
        <div class="team-member animate-on-scroll">
          <div class="member-image">
            <img src="" alt="Jossy Nkhoma">
            <div class="member-overlay">
              <div class="member-social">
                <a href="#" aria-label="LinkedIn Profile"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" aria-label="Twitter Profile"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Email Contact"><i class="fas fa-envelope"></i></a>
              </div>
            </div>
          </div>
          <div class="member-info">
            <h3 class="member-name">Jossy Nkhoma</h3>
            <p class="member-position">Senior Mechanic</p>
            <p class="member-bio">Skilled technician with expertise in preventative maintenance and brake systems. Certified in the latest automotive technologies and committed to precision work.</p>
          </div>
        </div>
        
        <div class="team-member animate-on-scroll">
          <div class="member-image">
            <img src="" alt="Chansa Chiti">
            <div class="member-overlay">
              <div class="member-social">
                <a href="#" aria-label="LinkedIn Profile"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" aria-label="Twitter Profile"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Email Contact"><i class="fas fa-envelope"></i></a>
              </div>
            </div>
          </div>
          <div class="member-info">
            <h3 class="member-name">Chansa Chiti</h3>
            <p class="member-position">Customer Relations</p>
            <p class="member-bio">Dedicated to creating exceptional customer experiences with a background in hospitality management. Ensures clear communication and complete client satisfaction.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="about" id="about">
    <div class="container">
      <div class="section-header animate-on-scroll">
        <h2 class="section-title">About Us</h2>
        <p class="section-subtitle">Learn more about Walizone Autotech and our commitment to excellence in automotive services</p>
      </div>
      <div class="about-container">
        <div class="about-content animate-on-scroll">
          <h3>Your Trusted Automotive Partner Since 2003</h3>
          <p><strong>Established in 2003</strong>, Walizone Autotech Enterprise has been providing reliable, affordable, and high-quality automotive solutions to the Chinsali community and beyond. Our expert team ensures vehicles run at optimal performance with trust and customer satisfaction at the heart of everything we do.</p>
          
          <p>Our state-of-the-art facility is equipped with the latest diagnostic tools and equipment to ensure accurate and efficient service. We pride ourselves on honest pricing, quality workmanship, and exceptional customer service.</p>
          
          <h4><i class="fas fa-bullseye"></i> Our Mission</h4>
          <p>To provide exceptional automotive services and build lasting relationships based on honesty, excellence, and trust.</p>
          
          <h4><i class="fas fa-eye"></i> Our Vision</h4>
          <p>To be the most trusted and reliable auto garage in the region, powered by skilled technicians and modern equipment.</p>
          
          <div class="about-stats">
            <div class="stat-item">
              <div class="stat-number">20+</div>
              <div class="stat-text">Years Experience</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">5000+</div>
              <div class="stat-text">Happy Customers</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">15+</div>
              <div class="stat-text">Expert Technicians</div>
            </div>
          </div>
        </div>
        <div class="about-image animate-on-scroll">
          <img src="images/garage.jpg " alt="Walizone Autotech Professional Garage">
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section class="services" id="services">
    <div class="container">
      <div class="section-header animate-on-scroll">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">We offer a comprehensive range of automotive services to keep your vehicle running at its best</p>
      </div>
      <div class="services-grid">
        <div class="service-card animate-on-scroll">
          <div class="service-image">
            <img src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Routine Maintenance">
          </div>
          <div class="service-content">
            <h3 class="service-title">Routine Maintenance</h3>
            <p class="service-description">Regular maintenance services including oil changes, filter replacements, fluid checks, and more to keep your vehicle running smoothly.</p>
            <a href="services/routine-maintenance.php" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="service-card animate-on-scroll">
          <div class="service-image">
            <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Computer Diagnostics">
          </div>
          <div class="service-content">
            <h3 class="service-title">Computer Diagnostics</h3>
            <p class="service-description">Advanced computerized diagnostics to identify and resolve complex issues with your vehicle's electronic systems.</p>
            <a href="services/computer-diagnostics.php" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="service-card animate-on-scroll">
          <div class="service-image">
            <img src="https://images.unsplash.com/photo-1580983218765-f663bec07b37?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Engine & Transmission Repair">
          </div>
          <div class="service-content">
            <h3 class="service-title">Engine & Transmission Repair</h3>
            <p class="service-description">Comprehensive engine and transmission services, from minor repairs to complete rebuilds and replacements.</p>
            <a href="services/engine-transmission-repair.php" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="service-card animate-on-scroll">
          <div class="service-image">
            <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="AC & Heating Services">
          </div>
          <div class="service-content">
            <h3 class="service-title">AC & Heating Services</h3>
            <p class="service-description">Complete climate control services including AC recharge, heating system repairs, and component replacements.</p>
            <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="service-card animate-on-scroll">
          <div class="service-image">
            <img src="https://images.unsplash.com/photo-1537984822441-cff330075342?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Suspension & Steering">
          </div>
          <div class="service-content">
            <h3 class="service-title">Suspension & Steering</h3>
            <p class="service-description">Expert repairs and maintenance for your vehicle's suspension and steering systems for a smoother, safer ride.</p>
            <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="service-card animate-on-scroll">
          <div class="service-image">
            <img src="https://images.unsplash.com/photo-1578844251758-2e71da64c96f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Panel Beating & Painting">
          </div>
          <div class="service-content">
            <h3 class="service-title">Panel Beating & Painting</h3>
            <p class="service-description">Professional panel beating and custom painting services to restore your vehicle's appearance after damage.</p>
            <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <div class="text-center animate-on-scroll" style="margin-top: 60px;">
        <a href="services.php" class="btn btn-primary">View All Services <i class="fas fa-chevron-right"></i></a>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials" id="testimonials">
    <div class="container">
      <div class="section-header animate-on-scroll">
        <h2 class="section-title">Customer Testimonials</h2>
        <p class="section-subtitle">What our satisfied customers have to say about our services</p>
      </div>
      <div class="testimonials-container">
        <div class="testimonial-card animate-on-scroll">
          <p class="testimonial-text">"I've been taking my vehicles to Walizone Autotech for over 5 years now. Their service is always excellent, prices fair, and the staff is knowledgeable and friendly. I wouldn't trust my car with anyone else!"</p>
          <div class="testimonial-author">
            <div class="author-image">
              <img src="" alt="John Mulenga">
            </div>
            <div class="author-info">
              <h4>John Mulenga</h4>
              <p>Loyal Customer</p>
            </div>
          </div>
        </div>
        <div class="testimonial-card animate-on-scroll">
          <p class="testimonial-text">"When my car broke down on a trip to Chinsali, I was referred to Walizone Autotech. They diagnosed the problem quickly, had the parts on hand, and got me back on the road the same day. Exceptional service!"</p>
          <div class="testimonial-author">
            <div class="author-image">
              <img src="" alt="Sarah Banda">
            </div>
            <div class="author-info">
              <h4>Sarah Banda</h4>
              <p>Satisfied Customer</p>
            </div>
          </div>
        </div>
        <div class="testimonial-card animate-on-scroll">
          <p class="testimonial-text">"The team at Walizone Autotech is honest and transparent. They explained the issues with my vehicle in terms I could understand and provided options that fit my budget. I highly recommend their services!"</p>
          <div class="testimonial-author">
            <div class="author-image">
              <img src="" alt="David Chanda">
            </div>
            <div class="author-info">
              <h4>David Chanda</h4>
              <p>Regular Customer</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta">
    <div class="container">
      <h2>Ready to Experience Quality Service?</h2>
      <p>Book an appointment today and let our expert technicians take care of your vehicle. We're committed to providing the highest quality service at competitive prices.</p>
      <a href="booking.php" class="btn">Book an Appointment</a>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact" id="contact">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Contact Us</h2>
        <p class="section-subtitle">Get in touch with us for any inquiries or to schedule a service</p>
      </div>
      <div class="contact-container">
        <div class="contact-info">
          <h3>Get In Touch</h3>
          <div class="contact-details">
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div class="contact-text">
                <h4>Our Location</h4>
                <p>Chinsali, Shambalakale Road, opposite Jesims Lodge</p>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fas fa-phone"></i>
              </div>
              <div class="contact-text">
                <h4>Phone Number</h4>
                <p><a href="tel:+260976664017">0976664017</a> / <a href="tel:+260965595951">0965595951</a></p>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="contact-text">
                <h4>Email Address</h4>
                <p><a href="mailto:mwakamule@gmail.com">mwakamule@gmail.com</a></p>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fas fa-clock"></i>
              </div>
              <div class="contact-text">
                <h4>Working Hours</h4>
                <p>Monday - Saturday: 8:00 AM - 5:00 PM</p>
                <p>Sunday: Closed</p>
              </div>
            </div>
          </div>
          <div id="map" style="height: 300px; border-radius: 10px; overflow: hidden;">
            <!-- Placeholder for Google Maps -->
            <img src="https://maps.googleapis.com/maps/api/staticmap?center=Chinsali,Zambia&zoom=14&size=600x300&maptype=roadmap&markers=color:red%7CChinsali,Zambia&key=YOUR_API_KEY" alt="Map" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
        </div>
        <div class="contact-form">
          <h3>Send Us a Message</h3>
          <form action="contact.php" method="POST">
            <div class="form-group">
              <input type="text" class="form-control" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
              <input type="email" class="form-control" name="email" placeholder="Your Email" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="subject" placeholder="Subject">
            </div>
            <div class="form-group">
              <textarea class="form-control" name="message" placeholder="Your Message" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-container">
        <div class="footer-column">
          <h3>About Us</h3>
          <p>Walizone Autotech Enterprise is a premier automotive service provider in Chinsali, offering comprehensive vehicle maintenance and repair services since 2003.</p>
          <div class="footer-social">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
          </div>
        </div>
        <div class="footer-column">
          <h3>Our Services</h3>
          <ul class="footer-links">
            <li><a href="services/routine-maintenance.php">Routine Maintenance</a></li>
            <li><a href="services/computer-diagnostics.php">Computer Diagnostics</a></li>
            <li><a href="services/engine-transmission-repair.php">Engine & Transmission Repair</a></li>
            <li><a href="#">AC & Heating Services</a></li>
            <li><a href="#">Suspension & Steering</a></li>
            <li><a href="#">Panel Beating & Painting</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3>Quick Links</h3>
          <ul class="footer-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="services.php">All Services</a></li>
            <li><a href="booking.php">Book an Appointment</a></li>
            <li><a href="#contact">Contact Us</a></li>
            <?php if ($isLoggedIn): ?>
              <li><a href="customer-dashboard.php">My Dashboard</a></li>
              <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <li><a href="admin/index.php">Admin Panel</a></li>
              <?php endif; ?>
            <?php else: ?>
              <li><a href="login.php">Login</a></li>
              <li><a href="register.php">Register</a></li>
              <li><a href="admin/login.php">Admin Login</a></li>
              <li><a href="admin/register.php">Admin Registration</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="footer-column">
          <h3>Contact Info</h3>
          <div class="footer-contact-item">
            <div class="footer-contact-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <p>Chinsali, Shambalakale Road, opposite Jesims Lodge</p>
          </div>
          <div class="footer-contact-item">
            <div class="footer-contact-icon">
              <i class="fas fa-phone"></i>
            </div>
            <p>0976664017 / 0965595951</p>
          </div>
          <div class="footer-contact-item">
            <div class="footer-contact-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <p>mwakamule@gmail.com</p>
          </div>
          <div class="footer-contact-item">
            <div class="footer-contact-icon">
              <i class="fas fa-clock"></i>
            </div>
            <p>Mon-Sat: 8:00AM - 5:00PM</p>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container">
        <p>&copy; 2025 Walizone Autotech Enterprise. All rights reserved. | <a href="admin/login.php" style="color: #bbdefb; text-decoration: none;">Admin Login</a></p>
        <p>Developed by MultTech company. email: <a href="mailto:abkatongo98@gmail.com" style="color: #bbdefb; text-decoration: none;">abkatongo98@gmail.com</a></p>
      </div>
    </div>
  </footer>

  <!-- JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Mobile Menu Toggle
      document.getElementById('mobileMenuToggle').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const nav = document.querySelector('.main-nav');
        nav.classList.toggle('mobile-active');
        this.classList.toggle('active');
        
        // Toggle aria-expanded for accessibility
        const expanded = this.getAttribute('aria-expanded') === 'true' || false;
        this.setAttribute('aria-expanded', !expanded);
        
        // Change icon when menu is open/closed
        const icon = this.querySelector('i');
        if (nav.classList.contains('mobile-active')) {
          icon.classList.remove('fa-bars');
          icon.classList.add('fa-times');
          
          // Add slight animation to the menu items
          const menuItems = document.querySelectorAll('.nav-item');
          menuItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(10px)';
            setTimeout(() => {
              item.style.transition = 'all 0.3s ease';
              item.style.opacity = '1';
              item.style.transform = 'translateY(0)';
            }, 50 * index);
          });
        } else {
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
        }
      });
      
      // Handle dropdown toggle on mobile
      const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
      dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
          // Only handle click on mobile view
          if (window.innerWidth <= 768) {
            e.preventDefault();
            const dropdown = this.parentElement;
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown').forEach(item => {
              if (item !== dropdown) {
                item.classList.remove('mobile-dropdown-active');
              }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('mobile-dropdown-active');
          }
        });
      });
      
      // Close mobile menu when clicking outside
      document.addEventListener('click', function(e) {
        const nav = document.querySelector('.main-nav');
        const mobileBtn = document.getElementById('mobileMenuToggle');
        
        if (nav.classList.contains('mobile-active') && 
            !nav.contains(e.target) && 
            e.target !== mobileBtn && 
            !mobileBtn.contains(e.target)) {
          nav.classList.remove('mobile-active');
          mobileBtn.classList.remove('active');
          
          const icon = mobileBtn.querySelector('i');
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
          
          // Close all dropdowns
          document.querySelectorAll('.dropdown').forEach(dropdown => {
            dropdown.classList.remove('mobile-dropdown-active');
          });
        }
      });
      
      // User Menu Toggle
      window.toggleUserMenu = function() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('active');
      };
      
      // Close dropdown when clicking outside
      document.addEventListener('click', function(event) {
        const userMenuBtn = document.querySelector('.user-menu-btn');
        const dropdown = document.getElementById('userDropdown');
        
        if (userMenuBtn && dropdown && !userMenuBtn.contains(event.target) && 
            dropdown.classList.contains('active')) {
          dropdown.classList.remove('active');
        }
      });
      
      // Smooth Scrolling for anchor links
      document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
          e.preventDefault();
          
          const targetId = this.getAttribute('href');
          const targetElement = document.querySelector(targetId);
          
          if (targetElement) {
            // Close mobile menu if open
            const mobileNav = document.querySelector('.main-nav');
            if (mobileNav.classList.contains('mobile-active')) {
              mobileNav.classList.remove('mobile-active');
              document.getElementById('mobileMenuToggle').classList.remove('active');
              
              const icon = document.querySelector('.mobile-menu-btn i');
              icon.classList.remove('fa-times');
              icon.classList.add('fa-bars');
            }
            
            // Scroll to target with a nice animation
            targetElement.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
            
            // Add a small delay before updating the active class
            setTimeout(() => {
              // Update active nav link
              document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
              });
              
              // Only add active class if it's a nav link
              if (this.classList.contains('nav-link')) {
                this.classList.add('active');
              } else {
                // Find the corresponding nav link and make it active
                const correspondingLink = document.querySelector(`.nav-link[href="${targetId}"]`);
                if (correspondingLink) {
                  correspondingLink.classList.add('active');
                }
              }
            }, 500);
          }
        });
      });
      
      // Add parallax effect to hero background
      window.addEventListener('scroll', function() {
        const heroSection = document.querySelector('.hero');
        const scrollPosition = window.pageYOffset;
        
        if (heroSection && scrollPosition <= heroSection.offsetHeight) {
          const heroBg = document.querySelector('.hero-bg');
          heroBg.style.transform = `translateY(${scrollPosition * 0.4}px)`;
        }
      });
      
      // Add animation to elements when they come into view
      const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        elements.forEach(element => {
          const elementPosition = element.getBoundingClientRect().top;
          const windowHeight = window.innerHeight;
          
          if (elementPosition < windowHeight - 100) {
            element.classList.add('animated');
            
            // If this is a feature card, add a delay to each card
            if (element.classList.contains('feature-card')) {
              const cards = document.querySelectorAll('.feature-card');
              cards.forEach((card, index) => {
                setTimeout(() => {
                  card.classList.add('animated');
                }, 200 * index);
              });
            }
            
            // If this is a team member, add a delay to each member
            if (element.classList.contains('team-member')) {
              const members = document.querySelectorAll('.team-member');
              members.forEach((member, index) => {
                setTimeout(() => {
                  member.classList.add('animated');
                }, 150 * index);
              });
            }
          }
        });
      };
      
      // Run animation check on scroll
      window.addEventListener('scroll', animateOnScroll);
      
      // Run animation check on page load
      window.addEventListener('load', function() {
        // Animate hero elements immediately
        setTimeout(() => {
          document.querySelectorAll('.animate-fade-in, .animate-slide-up, .animate-slide-down, .animate-slide-in').forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translate(0, 0)';
          });
        }, 300);
        
        // Run the scroll animation check
        animateOnScroll();
      });
      
      // Add hover effects to feature cards
      document.querySelectorAll('.feature-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.querySelector('.feature-icon').style.transform = 'rotateY(180deg)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.querySelector('.feature-icon').style.transform = 'rotateY(0)';
        });
      });
      
      // Typing animation for logo tagline
      function typeWriter(element, text, speed = 100, delay = 1000) {
        // First clear the element
        element.textContent = '';
        
        // Add cursor element
        const cursor = document.createElement('span');
        cursor.className = 'typing-cursor';
        element.parentNode.appendChild(cursor);
        
        // Wait before starting
        setTimeout(() => {
          let i = 0;
          const timer = setInterval(() => {
            if (i < text.length) {
              element.textContent += text.charAt(i);
              i++;
            } else {
              clearInterval(timer);
              // Remove cursor after typing is complete
              setTimeout(() => {
                cursor.style.display = 'none';
              }, 1500);
            }
          }, speed);
        }, delay);
      }
      
      // Start typing animation when page loads
      window.addEventListener('load', function() {
        const typingElement = document.getElementById('typingText');
        if (typingElement) {
          const originalText = typingElement.textContent;
          typeWriter(typingElement, originalText, 50, 800);
        }
      });
      
      // Add scroll effect to header
      window.addEventListener('scroll', function() {
        const header = document.querySelector('.main-header');
        if (window.scrollY > 30) {
          header.classList.add('header-scrolled');
        } else {
          header.classList.remove('header-scrolled');
        }
      });
      
      // Navbar scroll effect
      window.addEventListener('scroll', function() {
        const header = document.querySelector('.main-header');
        if (window.scrollY > 50) {
          header.classList.add('scrolled');
        } else {
          header.classList.remove('scrolled');
        }
      });
      
      // Mobile dropdown toggle
      const mobileDropdownToggles = document.querySelectorAll('.dropdown-toggle');
      
      // For mobile devices, make dropdown toggles clickable
      if (window.innerWidth <= 768) {
        mobileDropdownToggles.forEach(toggle => {
          toggle.addEventListener('click', function(e) {
            // Only prevent default if we're on mobile
            if (window.innerWidth <= 768) {
              e.preventDefault();
              
              const parent = this.parentElement;
              const dropdownMenu = parent.querySelector('.dropdown-menu');
              
              // Toggle the active class
              parent.classList.toggle('dropdown-active');
              
              // Toggle dropdown visibility
              if (parent.classList.contains('dropdown-active')) {
                dropdownMenu.style.maxHeight = dropdownMenu.scrollHeight + 'px';
                // Rotate chevron icon
                this.querySelector('.fa-chevron-down').style.transform = 'rotate(180deg)';
              } else {
                dropdownMenu.style.maxHeight = '0';
                // Reset chevron icon
                this.querySelector('.fa-chevron-down').style.transform = 'rotate(0)';
              }
            }
          });
        });
      }
      
      // Highlight active nav item based on scroll position
      window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPosition = window.scrollY + 100;
        
        sections.forEach(section => {
          const sectionTop = section.offsetTop;
          const sectionHeight = section.offsetHeight;
          const sectionId = section.getAttribute('id');
          
          if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            document.querySelectorAll('.nav-link').forEach(link => {
              link.classList.remove('active');
              if (link.getAttribute('href') === '#' + sectionId && 
                  !link.classList.contains('dropdown-toggle')) {
                link.classList.add('active');
              }
            });
          }
        });
      });
    });
    
    // Service Worker Registration for PWA
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
          .then(registration => {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
          })
          .catch(error => {
            console.log('ServiceWorker registration failed: ', error);
          });
      });
    }
  </script>
</body>
</html>
