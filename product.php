<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="unnati">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .card {
          border: none;
          border-radius: 15px;
          box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
          height: 100%;
        }
    
        .card-img {
          object-fit: cover;
          height: 100%;
          width: 100%;
          transition: transform 0.3s ease;
        }
    
        .card:hover .card-img {
          transform: scale(1.05);
        }
    
        .category-label {
          display: inline-block;
          background: #e0f7fa;
          color: #00796b;
          font-weight: 600;
          padding: 4px 12px;
          border-radius: 50px;
          font-size: 0.75rem;
          text-transform: uppercase;
          letter-spacing: 1px;
          margin-bottom: 0.5rem;
        }
    
        .btn-fancy {
          background: linear-gradient(45deg, #6a11cb, #2575fc);
          color: white;
          border: none;
          transition: all 0.3s ease;
        }
    
        .btn-fancy:hover {
          background: linear-gradient(45deg, #2575fc, #6a11cb);
          transform: scale(1.05);
        }
    
        .card-body {
          padding: 1rem 1.5rem;
        }
    
        .horizontal-card {
          display: flex;
          flex-direction: row;
          height: 100%;
        }
    
        @media (max-width: 768px) {
          .horizontal-card {
            flex-direction: column;
          }
          .card-img {
            height: 200px;
          }
        }

    .process-list li {
      list-style: none;
      padding-left: 1.5rem;
      position: relative;
      margin-bottom: 0.75rem;
    }

    .process-list li::before {
      content: "✓";
      position: absolute;
      left: 0;
      color: red;
      font-weight: bold;
    }

    .qa-box {
      background-color: #f0faff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .qa-step {
      display: flex;
      align-items: start;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .qa-step .number {
      background: #e0f7fa;
      color: #00796b;
      font-weight: bold;
      width: 36px;
      height: 36px;
      text-align: center;
      line-height: 36px;
      border-radius: 50%;
      font-size: 1rem;
    }

    .btn-primary-custom {
      background-color: #0099cc;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 0.5rem;
      font-weight: 600;
    }

    .btn-primary-custom:hover {
      background-color: #007da8;
    }
      </style>
      
</head>
<body>
      <?php
        include('./_main_nav.php');
        ?>
    <div class="bg-opacity-75 text-center" style="background-image: linear-gradient(to bottom right, #33d6ff, white)">
        <h1 class="pt-5">Our Products</h1>
        <p class="fs-5 fw-light pb-5">Explore our comprehensive range of high-quality electrical products manufactured with precision and excellence.</p>
    </div>
    <div class="text-center">
        <p class="fs-3 fw-semibold pt-4">Quality Electrical Products</p>
        <p class="fs-5 fw-light pb-3">We manufacture and supply a wide range of high-quality electrical products for various applications</p>
    </div>
    <div class="container py-4">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card horizontal-card">
              <div class="col-md-5">
                <img src="https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="card-img" alt="Forest" />
              </div>
              <div class="col-md-7 d-flex align-items-center">
                <div class="card-body">
                  <span class="category-label">Wire Manufacturing</span>
                  <h5 class="card-title">Aluminum Wires</h5>
                  <p class="card-text">Durable aluminum wires ideal for industrial and utility distribution.</p>
                  <a href="#" class="btn btn-fancy">View Details</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card horizontal-card">
              <div class="col-md-5">
                <img src="https://images.unsplash.com/photo-1600508774634-4e11d34730e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="card-img" alt="Mountain" />
              </div>
              <div class="col-md-7 d-flex align-items-center">
                <div class="card-body">
                  <span class="category-label">Electric Hardware</span>
                  <h5 class="card-title">Cable Accessories</h5>
                  <p class="card-text">Complete range of cable accessories for installation and management.</p>
                  <a href="#" class="btn btn-fancy">View Details</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card horizontal-card">
              <div class="col-md-5">
                <img src="https://images.unsplash.com/photo-1573030889348-c6b0f8b15e40?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="card-img" alt="Beach" />
              </div>
              <div class="col-md-7 d-flex align-items-center">
                <div class="card-body">
                  <span class="category-label">Electric Hardware</span>
                  <h5 class="card-title">LED Lighting</h5>
                  <p class="card-text">Energy-efficient LED lighting solutions for various applications.</p>
                  <a href="#" class="btn btn-fancy">View Details</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-secondary bg-opacity-10">
        <p class="fs-3 fw-semibold pt-4 text-center">Manufacturing Excellence</p>
        <p class="fs-5 fw-light text-center">Our manufacturing specialties that set us apart in the industry</p>
        <div class="container py-3">
            <div class="row g-4">
              <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                    stroke-linejoin="round" class="lucide lucide-package text-info my-3" data-lov-id="src/pages/Products.tsx:66:12" data-lov-name="Package" data-component-path="src/pages/Products.tsx" 
                    data-component-line="66" data-component-file="Products.tsx" data-component-name="Package" data-component-content="%7B%7D">
                        <path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"></path>
                        <path d="M12 22V12"></path><path d="m3.3 7 7.703 4.734a2 2 0 0 0 1.994 0L20.7 7"></path>
                        <path d="m7.5 4.27 9 5.15"></path>
                    </svg>
                    <h5 class="card-title">Premium Materials</h5>
                    <p class="card-text">We use only the highest quality raw materials sourced from trusted suppliers to ensure product durability and performance.</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                    stroke-linejoin="round" class="lucide lucide-zap text-info my-3" data-lov-id="src/pages/Products.tsx:72:12" data-lov-name="Zap" data-component-path="src/pages/Products.tsx" 
                    data-component-line="72" data-component-file="Products.tsx" data-component-name="Zap" data-component-content="%7B%7D">
                        <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>
                    </svg>
                    <h5 class="card-title">Advanced Manufacturing</h5>
                    <p class="card-text">Our state-of-the-art manufacturing facilities employ the latest technologies to produce precision-engineered electrical products.</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                    stroke-linejoin="round" class="lucide lucide-truck text-info my-3" data-lov-id="src/pages/Products.tsx:78:12" data-lov-name="Truck" data-component-path="src/pages/Products.tsx" 
                    data-component-line="78" data-component-file="Products.tsx" data-component-name="Truck" data-component-content="%7B%7D">
                        <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path>
                        <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path>
                        <circle cx="17" cy="18" r="2"></circle>
                        <circle cx="7" cy="18" r="2"></circle>
                    </svg>
                    <h5 class="card-title">Nationwide Distribution</h5>
                    <p class="card-text">With our extensive distribution network, we ensure timely delivery of products across the country.</p>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="container pb-5 pt-1">
            <div class="row g-4">
              <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                    stroke-linejoin="round" class="lucide lucide-shield-check text-info my-3" data-lov-id="src/pages/Products.tsx:84:12" data-lov-name="ShieldCheck" data-component-path="src/pages/Products.tsx" 
                    data-component-line="84" data-component-file="Products.tsx" data-component-name="ShieldCheck" data-component-content="%7B%7D">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <h5 class="card-title">Quality Assurance</h5>
                    <p class="card-text">Every product undergoes rigorous testing and quality checks to meet international standards and specifications.</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                    stroke-linejoin="round" class="lucide lucide-wrench text-info my-3" data-lov-id="src/pages/Products.tsx:90:12" data-lov-name="Wrench" data-component-path="src/pages/Products.tsx" 
                    data-component-line="90" data-component-file="Products.tsx" data-component-name="Wrench" data-component-content="%7B%7D">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                    </svg>
                    <h5 class="card-title">Custom Solutions</h5>
                    <p class="card-text">We offer customized products tailored to meet specific requirements of diverse industrial and commercial applications.</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                    stroke-linejoin="round" class="lucide lucide-award text-info my-3" data-lov-id="src/pages/Products.tsx:96:12" data-lov-name="Award" data-component-path="src/pages/Products.tsx" 
                    data-component-line="96" data-component-file="Products.tsx" data-component-name="Award" data-component-content="%7B%7D">
                        <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path>
                        <circle cx="12" cy="8" r="6"></circle>
                    </svg>
                    <h5 class="card-title">Certified Products</h5>
                    <p class="card-text">Our products are certified by recognized industry authorities, ensuring compliance with safety and performance standards.</p>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="container py-5">
        <div class="row align-items-start gy-4">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3">Our Manufacturing Process</h2>
                <p>At Unnati Traders, we follow a rigorous manufacturing process to ensure the highest quality products. Our state-of-the-art facilities and skilled workforce combine to create electrical products that meet international standards.</p>
                <ul class="process-list mt-3">
                <li>Sourcing premium quality raw materials</li>
                <li>Precision engineering and manufacturing</li>
                <li>Multi-stage quality control checks</li>
                <li>Advanced testing under various conditions</li>
                <li>Careful packaging and distribution</li>
                </ul>
                <a href="#" class="btn btn-fancy">Learn More About Our Process</a>
            </div>
      
            <div class="col-lg-6">
                <div class="qa-box">
                <h4 class="fw-bold mb-4">Quality Assurance Process</h4>
        
                    <div class="qa-step" data-aos="fade-up" data-aos-delay="100">
                        <div class="number">1</div>
                        <div>
                            <strong>Material Inspection</strong>
                            <p class="mb-0">Thorough inspection of raw materials before entering production.</p>
                        </div>
                    </div>
        
                    <div class="qa-step" data-aos="fade-up" data-aos-delay="100">
                        <div class="number">2</div>
                        <div>
                            <strong>In-Process Testing</strong>
                            <p class="mb-0">Continuous monitoring and testing during manufacturing.</p>
                        </div>
                    </div>
        
                    <div class="qa-step" data-aos="fade-up" data-aos-delay="100">
                        <div class="number">3</div>
                        <div>
                            <strong>Final Product Inspection</strong>
                            <p class="mb-0">Thorough examination of finished products for defects.</p>
                        </div>
                    </div>
        
                    <div class="qa-step" data-aos="fade-up" data-aos-delay="100">
                        <div class="number">4</div>
                        <div>
                            <strong>Performance Testing</strong>
                            <p class="mb-0">Rigorous testing of products under various conditions.</p>
                        </div>
                    </div>
        
                    <div class="qa-step" data-aos="fade-up" data-aos-delay="100">
                        <div class="number">5</div>
                        <div>
                            <strong>Certification</strong>
                            <p class="mb-0">Final certification and documentation before shipping.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-opacity-75 text-center" style="background-image: linear-gradient(to bottom right, #33d6ff, white)">
        <h1 class="pt-5">Ready to Order Our Products?</h1>
        <p class="fs-5 pb-2">Contact us today to discuss your requirements or visit our retail store to explore our full range of electrical products.</p>
        <div class="pt-2 pb-5">
            <a href="#" class="btn btn-fancy">Contact Sales Team</a>&nbsp; &nbsp;
            <a href="#" class="btn btn-fancy">Find Our Retail Store</a>
        </div>
    </div>
    <?php
        include('./_footer.php');
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
    AOS.init();
    </script>
</body>
</html>