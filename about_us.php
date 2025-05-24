<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Unnati Traders</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="unnati">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
      color: #212529;
    }

    .container {
      max-width: 1100px;
      margin: auto;
      padding: 60px 20px;
    }

    .heading {
      text-align: center;
      font-size: 36px;
      font-weight: 700;
      color: #0d6efd;
      margin-bottom: 10px;
    }

    .subheading {
      text-align: center;
      font-size: 18px;
      color: #6c757d;
      margin-bottom: 60px;
    }

    .section {
      background-color: #ffffff;
      border-radius: 12px;
      padding: 40px;
      margin-bottom: 40px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .section:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    }

    h2 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #0d6efd;
      border-left: 4px solid #0d6efd;
      padding-left: 15px;
    }

    p {
      font-size: 16px;
      line-height: 1.7;
      color: #343a40;
    }

    ul {
      list-style: none;
      padding-left: 0;
      margin-top: 20px;
    }

    ul li {
      position: relative;
      padding-left: 25px;
      margin-bottom: 12px;
      font-size: 16px;
      color: #495057;
    }

    ul li::before {
      position: absolute;
      left: 0;
      color: #0d6efd;
    }
  </style>
</head>

<body>
  <?php
  include('./_main_nav.php');
  ?>
  <div class="container">
    <div class="heading">About Unnati Traders</div>
    <div class="subheading">Leading manufacturer and supplier of wires and electric hardware with a vision to empower
      businesses through integrated management solutions.</div>

    <div class="section">
      <h2>Our Story</h2>
      <p>Founded in 2010, <strong>Unnati Traders</strong> began as a small electrical goods shop with a big vision—to
        deliver quality products and unmatched service to local businesses. What started as a humble initiative has
        grown into a dynamic enterprise, expanding into <strong>wire manufacturing</strong> and building a strong
        <strong>distribution network</strong> across the region.</p>
      <p>As we understood the challenges electrical businesses face, we went beyond just supplying products. We
        developed an <strong>integrated business management solution</strong> that simplifies operations, enhances
        inventory control, and streamlines financial tracking.</p>
      <p>Today, we proudly serve <strong>hundreds of businesses</strong> with our dual offering of <strong>premium
          electrical products</strong> and <strong>cutting-edge management tools</strong>, helping them thrive in a
        competitive market.</p>
    </div>

    <div class="section">
      <h2>Unnati Traders Factory</h2>
      <p>We operate with advanced manufacturing capabilities that prioritize <strong>precision, safety, and
          consistency</strong>, enabling us to deliver top-notch products that meet industry standards. Our factory is
        where innovation meets craftsmanship, ensuring every product carries the Unnati guarantee of excellence.</p>
    </div>

    <div class="section">
      <h2>Our Mission</h2>
      <p>To provide <strong>high-quality electrical products</strong> and <strong>innovative business solutions</strong>
        that empower our customers to operate efficiently and grow sustainably. We aim to be the <strong>preferred
          partner</strong> for electrical businesses through unmatched value in both products and services.</p>
    </div>

    <div class="section">
      <h2>Our Vision</h2>
      <p>To become <strong>India’s leading integrated provider</strong> of electrical products and business management
        solutions, recognized for <strong>innovation</strong>, <strong>quality</strong>, and <strong>customer
          satisfaction</strong>. We aspire to revolutionize how electrical businesses operate through our
        <strong>holistic approach</strong> to supply and operational efficiency.</p>
    </div>

    <div class="section">
      <h2>Our Core Values</h2>
      <ul>
        <li><strong>Quality</strong> – We never compromise on the reliability and durability of our products and
          services.</li>
        <li><strong>Integrity</strong> – Our business is built on honesty, transparency, and ethical practices.</li>
        <li><strong>Innovation</strong> – We continuously evolve to meet changing needs and stay ahead of the curve.
        </li>
        <li><strong>Customer Focus</strong> – Our customers are at the heart of everything we do. Their success is our
          success.</li>
      </ul>
    </div>
  </div>
  <?php
        include('./_footer.php');
    ?>
</body>

</html>