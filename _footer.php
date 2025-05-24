<style>
    .site-footer {
        background-color: #333;
        color: #fff;
        padding: 3rem 0;
        text-align: center;
    }
    .footer-widgets {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }
    .footer-widget {
        margin: 1re  m;
    }
    .footer-widget h3 {
        font-size: 1.2rem;
        margin-bottom: 0.8rem;
        color: #eee;
    }
    .footer-widget ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .footer-widget li {
        margin-bottom: 0.5rem;
    }
    .footer-widget a {
        color: #ccc;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .footer-widget a:hover {
        color: #fff;
    }
    .footer-bottom {
        margin-top: 1rem;
        font-size: 0.9rem;
        color: #ccc;
    }
    .footer-bottom a {
        color: #fff;
        text-decoration: none;
    }
    .footer-bottom a:hover {
         color: #b0e0e6; /* hover color for footer links */
    }
</style>

<body>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-widgets">
                <div class="footer-widget">
                    <div class="footer-widget">
                        <h3>Shree Unnati<br>WIRES & TRADERS</h3>
                        <p class="text-light">Leading manufacturer and supplier of wires and electric hardware in the region.</p>
                    </div>
                    <div class="footer-widget">
                        <h3>Contact Us</h3>
                        <p class="text-light">123 Industrial Area, Business District, City - 123456<br>
                        +91 98765 43210<br>
                        <a href="mailto:info@unnatitraders.com" onclick="showAlert('Email: info@unnatitraders.com')">info@unnatitraders.com</a></p>
                    </div>
                </div>
                <div class="footer-widget">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#" onclick="showAlert('Home')">Home</a></li>
                        <li><a href="#" onclick="showAlert('About Us')">About Us</a></li>
                        <li><a href="#" onclick="showAlert('Products')">Products</a></li>
                        <li><a href="#" onclick="showAlert('Contact')">Contact</a></li>
                        <li><a href="#" onclick="showAlert('Franchise & Distribution')">Franchise & Distribution</a></li>
                    </ul>
                </div>
                <div class="footer-widget">
                    <h3>Our Products</h3>
                    <ul>
                        <li><a href="#" onclick="showAlert('Copper Wires')">Copper Wires</a></li>
                        <li><a href="#" onclick="showAlert('Aluminum Wires')">Aluminum Wires</a></li>
                        <li><a href="#" onclick="showAlert('Switchgear')">Switchgear</a></li>
                        <li><a href="#" onclick="showAlert('Circuit Breakers')">Circuit Breakers</a></li>
                        <li><a href="#" onclick="showAlert('LED Lighting')">LED Lighting</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                © 2025 Shree Unnati Wires & Traders. All rights reserved. | <a href="#" onclick="showAlert('Privacy Policy')">Privacy Policy</a> | <a href="#" onclick="showAlert('Terms of Service')">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>