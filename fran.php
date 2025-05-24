<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shree Unnati - Franchise Opportunities</title>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="unnati">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    /* Reset and common styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f8fafc;
    }

    a {
      text-decoration: none;
    }

    /* Franchise Top Section */
    .franchise-section {
      background-color: #0097cc;
      padding: 100px 20px;
      text-align: center;
      color: white;
    }

    .franchise-section h1 {
      font-size: 36px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .franchise-section p {
      max-width: 700px;
      margin: 0 auto 30px;
      font-size: 20px;
      line-height: 1.6;
    }

    .franchise-section a.button {
      display: inline-block;
      padding: 12px 24px;
      background-color: white;
      color: #0097cc;
      font-weight: bold;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
      transition: background-color 0.3s, color 0.3s;
    }

    .franchise-section a.button:hover {
      background-color: #f2f2f2;
    }

    /* Below Sections */
    h2,
    h3 {
      text-align: center;
      color: #1f2937;
    }

    p {
      text-align: center;
      color: #6b7280;
      margin-top: 0;
    }

    .container {
      max-width: 1200px;
      margin: auto;
      padding: 40px 20px;
    }

    .cards,
    .steps,
    .faq-grid,
    .stories-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      margin-top: 40px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      flex: 1 1 300px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      min-width: 280px;
    }

    .card h4 {
      margin-top: 0;
      margin-bottom: 10px;
      color: #111827;
    }

    .card ul {
      padding-left: 20px;
      color: #6b7280;
    }

    .card ul li {
      margin: 8px 0;
    }


    .btn-red {
      background: #ef4444;
    }

    .btn-lightblue {
      background: #0ea5e9;
    }

    .step {
      background: white;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: auto;
      font-weight: bold;
      font-size: 20px;
      color: #0284c7;
    }

    .step-desc {
      text-align: center;
      margin-top: 10px;
    }

    .form-section {
      background: white;
      padding: 40px 20px;
      border-radius: 10px;
      margin-top: 60px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    form {
      max-width: 600px;
      margin: auto;
    }

    input,
    select,
    textarea {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #d1d5db;
      border-radius: 5px;
    }

    textarea {
      resize: vertical;
      min-height: 100px;
    }

    label {
      font-weight: bold;
      color: #374151;
    }

    .checkbox {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      color: #6b7280;
      margin-top: 10px;
    }

    .submit-btn {
      width: 100%;
      padding: 12px;
      background: #0284c7;
      color: white;
      border: none;
      font-size: 16px;
      font-weight: bold;
      border-radius: 5px;
      margin-top: 20px;
      cursor: pointer;
    }

    .faq-section {
      background: #e0f2f7;
      padding: 40px 20px;
      margin-top: 60px;
    }

    .faq-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 30px;
    }

    .faq-item {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }

    .faq-item h4 {
      color: #0b5394;
      margin-top: 0;
      margin-bottom: 10px;
    }

    .faq-item p {
      text-align: left;
      color: #374151;
      margin-top: 0;
    }

    .stories-section {
      padding: 40px 20px;
      margin-top: 60px;
      background: #f0f8ff;
    }

    .stories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
    }

    .story-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 1px 6px rgba(0, 0, 0, 0.08);
    }

    .story-card blockquote {
      font-style: italic;
      color: #4a5568;
      margin-top: 0;
      margin-bottom: 15px;
      padding: 15px;
      border-left: 3px solid #2b6cb0;
    }

    .story-card .author {
      margin-top: 15px;
      text-align: right;
      font-weight: bold;
      color: #1a202c;
    }

    .story-card .designation {
      text-align: right;
      color: #718096;
      font-size: 0.9em;
    }
  </style>
</head>

<body>

  <?php
  include('./_main_nav.php');
  ?>

  <!-- Franchise Section -->
  <section class="franchise-section">
    <h1>Franchise & Distribution Opportunities</h1>
    <p class="text-dark">Join the Unnati Traders network and become part of a growing business ecosystem in the electrical industry.</p>
    <a href="#" class="button">Apply Now</a>
  </section>

  <!-- Rest of the page -->
  <div class="container">
    <h2>Our Partnership Models</h2>
    <p>Choose the model that best suits your business goals and resources</p>
    <div class="cards">
      <!-- Cards -->
      <!-- Franchise -->
      <div class="card">
        <h4>Franchise</h4>
        <p>Open your own Unnati Traders branded store with our complete product line and management system.</p>
        <ul>
          <li>Investment: ₹20-30 Lakhs</li>
          <li>Space: 500-800 sq. ft.</li>
          <li>Staff: 3-5 members</li>
          <li>Prime commercial location</li>
        </ul>
        <a href="#" class="btn btn-outline-primary">Apply for Franchise</a>
      </div>
      <!-- Distributor -->
      <div class="card">
        <h4>Distributor</h4>
        <p>Become a regional distributor for our products with exclusive territory rights and attractive margins.</p>
        <ul>
          <li>Investment: ₹10-15 Lakhs</li>
          <li>Storage space: 300+ sq. ft.</li>
          <li>Existing distribution network</li>
          <li>Transportation capabilities</li>
        </ul>
        <a href="#" class="btn btn-outline-danger">Apply as Distributor</a>
      </div>
      <!-- Retailer Partner -->
      <div class="card">
        <h4>Retailer Partner</h4>
        <p>Add our products to your existing electrical store with minimal investment and excellent support.</p>
        <ul>
          <li>Investment: ₹5-8 Lakhs</li>
          <li>Existing electrical shop</li>
          <li>Customer base in electrical sector</li>
          <li>Basic inventory management</li>
        </ul>
        <a href="#" class="btn btn-outline-primary">Become a Retailer Partner</a>
      </div>
    </div>
  </div>

  <!-- Application Process -->
  <div class="container">
    <h2>Application Process</h2>
    <p>Simple steps to become part of the Unnati Traders network</p>
    <div class="steps">
      <div>
        <div class="step">1</div>
        <div class="step-desc">
          <h4>Submit Application</h4>
          <p>Fill out the inquiry form with your details and preferences</p>
        </div>
      </div>
      <div>
        <div class="step">2</div>
        <div class="step-desc">
          <h4>Initial Assessment</h4>
          <p>Our team evaluates your application and location potential</p>
        </div>
      </div>
      <div>
        <div class="step">3</div>
        <div class="step-desc">
          <h4>Business Discussion</h4>
          <p>Meeting to discuss business terms and answer your questions</p>
        </div>
      </div>
      <div>
        <div class="step">4</div>
        <div class="step-desc">
          <h4>Agreement & Launch</h4>
          <p>Sign agreement and begin your journey with Unnati Traders</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Inquiry Form -->
  <div class="container form-section">
    <h2>Franchise & Distribution Inquiry</h2>
    <form>
      <label>First Name*</label>
      <input type="text" required>
      <label>Last Name*</label>
      <input type="text" required>
      <label>Email Address*</label>
      <input type="email" required>
      <label>Phone Number*</label>
      <input type="tel" required>
      <label>City/Location of Interest*</label>
      <input type="text" required>
      <label>Partnership Type*</label>
      <select required>
        <option value="">Select Partnership Type</option>
        <option value="Franchise">Franchise</option>
        <option value="Distributor">Distributor</option>
        <option value="Retailer Partner">Retailer Partner</option>
      </select>
      <label>Investment Capability*</label>
      <select required>
        <option value="">Select Investment Range
        <option value="5-8 Lakhs">5-8 Lakhs</option>
        <option value="10-15 Lakhs">10-15 Lakhs</option>
        <option value="20-30 Lakhs">20-30 Lakhs</option>
      </select>
      <label>Message (Optional)</label>
      <textarea placeholder="Tell us more about your interest..."></textarea>
      <div class="checkbox">
        <input type="checkbox" required>
        <label>I agree to the terms and conditions</label>
      </div>
      <button type="submit" class="submit-btn">Submit Inquiry</button>
    </form>
  </div>

  <!-- FAQs -->
  <section class="faq-section">
    <div class="container">
      <h2>Frequently Asked Questions</h2>
      <div class="faq-grid">
        <div class="faq-item">
          <h4>How much investment is required to start a franchise?</h4>
          <p>The initial investment for a Unnati Traders franchise ranges from ₹20-30 Lakhs depending on location and
            store size.</p>
        </div>
        <div class="faq-item">
          <h4>What support will I get after joining?</h4>
          <p>We provide comprehensive support including store setup, staff training, marketing, product supply, and
            business guidance.</p>
        </div>
        <div class="faq-item">
          <h4>Can I start as a distributor if I already own a store?</h4>
          <p>Yes, if you have existing infrastructure and meet our criteria, you can start as a distributor or retailer
            partner.</p>
        </div>
        <div class="faq-item">
          <h4>How long does the entire process take?</h4>
          <p>Typically, the process from application to launch takes about 45-60 days, depending on factors like
            location readiness.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Success Stories -->
  <section class="stories-section">
    <div class="container">
      <h2>Success Stories</h2>
      <p>Hear from our successful partners across India</p>
      <div class="stories-grid">
        <div class="story-card">
          <blockquote>
            "Joining Unnati Traders completely changed my business life. With their excellent support system, I was
            profitable within the first year!"
          </blockquote>
          <div class="author">Rahul Sharma</div>
          <div class="designation">Franchise Owner, Lucknow</div>
        </div>
        <div class="story-card">
          <blockquote>
            "As a distributor, I get access to quality products and timely service. Unnati Traders is a name I trust in
            the electrical industry."
          </blockquote>
          <div class="author">Priya Verma</div>
          <div class="designation">Distributor, Pune</div>
        </div>
        <div class="story-card">
          <blockquote>
            "Becoming a retailer partner helped me expand my shop’s offerings. My customers love the quality and
            pricing."
          </blockquote>
          <div class="author">Amit Desai</div>
          <div class="designation">Retailer Partner, Ahmedabad</div>
        </div>
      </div>
    </div>
  </section>

  <?php
        include('./_footer.php');
    ?>

</body>

</html>