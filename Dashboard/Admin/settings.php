<?php
session_start();
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"] ,  ['Factory','Store','Vendor'])) {
        header("location:../index.php");
        exit;

    } else if (!($_SESSION["user_type"] == 'Admin')) {
        header("location:../../login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="unnati">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
    <style>
        .cards {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            height: 100%;
        }

        .cards:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
        }

        .card-border {
            border-radius: 0.5rem;
            border-top: none;
            border-right: none;
            border-bottom: none;
        }

        .chart-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
            flex: 1 1 300px;
        }
        h3 {
            margin-bottom: 15px;
        }
        canvas {
            width: 100% !important;
            height: auto !important;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
        }
        .settingTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }
        .settingTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }
        .setting-tab-content {
            display: none;
            padding: 20px 0;
        }
        .setting-tab-content.active {
            display: block;
        }
        /* table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        } */
        .green-bg {
            background-color: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .orange-bg {
            background-color: #fff3cd;
            color: #856404;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .red-bg {
            background-color: #f8d7da;
            color: #721c24;
            padding: 4px 10px;
            border-radius: 10px;
        }

    </style>
</head>
<body class="bg-secondary bg-opacity-10">
    <?php
        include('./_admin_nav.php');
    ?>

<div class="main-content">
    <h1>Setting</h1>
    <p>Configure system preferences and business settings</p>
    
    <div class="col-md-12 card p-3 shadow-sm my-4">
        <div class="tabs">
            <button class="settingTab active" onclick="showsettingTab('general')">General</button>
            <button class="settingTab" onclick="showsettingTab('company')">Company</button>
            <button class="settingTab" onclick="showsettingTab('document')">Document</button>
            <button class="settingTab" onclick="showsettingTab('billing')">Billing</button>
            <button class="settingTab" onclick="showsettingTab('advanced')">Advanced</button>
        </div>

        <div id="general" class="setting-tab-content active">
            <div class="justify-contnt-start">
                <h3>General Settings</h3>
                <p>Configure application preferences and default behavior</p>
            </div>
            <h5>Settings</h5>
            <div class="tabs"></div>
            
            <div class="mb-3 m-3">
                <label for="language">Language</label>
                <select class="form-select form-select-lg mb-3 m-3" aria-label="Language selection">
                    <option selected>English (India)</option>
                    <option value="1">Hindi</option>
                    <option value="2">Gujarati</option>
                    <option value="3">Marathi</option>
                </select>
            </div>
            
            <div class="mb-3 m-3">
                <label for="timezone">Timezone</label>
                <select class="form-select form-select-lg mb-3 m-3" aria-label="Timezone selection">
                    <option selected>Asia/Kolkata (IST)</option>
                    <option value="1">Asia/Dubai (GST)</option>
                    <option value="2">Europe/London (GST)</option>
                    <option value="3">America/New York (EST)</option>
                </select>
            </div>
            
            <div class="mb-3 m-3">
                <label for="date_format">Date Format</label>
                <select class="form-select form-select-lg mb-3 m-3" aria-label="Date format selection">
                    <option selected>DD-MM-YYYY</option>
                    <option value="1">MM-DD-YYYY</option>
                    <option value="2">YYYY-MM-DD</option>
                </select>
            </div>
            
            <div class="mb-3 m-3">
                <label for="theme">Theme</label>
                <select class="form-select form-select-lg mb-3 m-3" aria-label="Theme selection">
                    <option selected>Light</option>
                    <option value="1">Dark</option>
                    <option value="2">System</option>
                </select>
            </div>
            
            <div class="form-check form-switch mx-2">
                <h5>Notifications</h5>
                <input class="form-check-input mx-1" type="checkbox" id="flexSwitchCheckDefault">
                <label class="form-check-label" for="flexSwitchCheckDefault">Enable system notifications</label>
            </div>
            
            <div class="mt-4">
                <h5>System Performance</h5>
                <div class="tabs"></div>
                <div class="form-check form-switch mx-2">
                    <h5>Auto</h5>
                    <input class="form-check-input mx-1" type="checkbox" id="flexSwitchCheckDefault1">
                    <label class="form-check-label" for="flexSwitchCheckDefault1">Enable auto-save for forms</label>
                </div>

                <div class="form-check form-switch mx-2">
                    <h5>Cache</h5>
                    <input class="form-check-input mx-1" type="checkbox" id="flexSwitchCheckDefault2">
                    <label class="form-check-label" for="flexSwitchCheckDefault2">Use browser cache for faster loading</label>
                </div>

                <div class="form-check form-switch mx-2">
                    <h5>Analytics</h5>
                    <input class="form-check-input mx-1" type="checkbox" id="flexSwitchCheckDefault3">
                    <label class="form-check-label" for="flexSwitchCheckDefault3">Enable usage analytics</label>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-save"></i> Save Settings
                    </button>
                </div>
            </div>
        </div>

    <div class="setting-tab-content" id="company">
    <h3>Company Profile</h3>
    <p>Update your company information and branding</p>

    <div class="">
        <h5>Business Details</h5>
        <div class="tabs"></div>
        
        <div class="mb-3 m-3 col">
            <label for="company-name">Company Name</label>
            <input type="text" class="form-control" id="company-name" placeholder="Enter Cmpany name" aria-label="Shree Unnati Traders">
        </div>
        
        <div class="mb-3 m-3 col">
            <label for="company-address">Address</label>
            <textarea class="form-control form-floating" id="company-address" placeholder="123 Main Street, Industrial Area, Mumbai, Maharashtra" style="height: 100px"></textarea>
        </div>

        <div class="mb-3 m-3 col">
            <label for="gst-number">Invoice-Start-Number</label>
            <input type="text" class="form-control" id="gst-number" placeholder="27AABCU9603R1ZX">
        </div>
        
        <div class="mb-3 m-3 col">
            <label for="pan-number">PAN Number</label>
            <input type="text" class="form-control" id="pan-number" placeholder="AABCU9603R">
        </div>

        <div class="mb-3 m-3 col">
            <label for="phone-number">Phone Number</label>
            <input type="number" class="form-control" id="phone-number" placeholder="9876543210">
        </div>

        <div class="mb-3 m-3 col">
            <label for="website">Website</label>
            <input type="text" class="form-control" id="website" placeholder="www.unnatitraders.com">
        </div>
    </div>

        <div class="mb-3 m-3 col">
            <label for="gst-number">GST Number</label>
            <input type="text" class="form-control" id="gst-number" placeholder="27AABCU9603R1ZX">
        </div>

        <div class="mb-3 m-3 col">
            <label for="pan-number">PAN Number</label>
            <input type="text" class="form-control" id="pan-number" placeholder="AABCU9603R">
        </div>

        <div class="mb-3 m-3 col">
            <label for="phone-number">Phone Number</label>
            <input type="number" class="form-control" id="phone-number" placeholder="9876543210">
        </div>

        <div class="mb-3 m-3 col">
            <label for="Email">Email</label>
            <input type="text" class="form-control" id="Enter Your Email" placeholder="youremail@gmail.com">
        </div>

        <div class="mb-3 m-3 col">
            <label for="website">Website</label>
            <input type="text" class="form-control" id="website" placeholder="www.unnatitraders.com">
        </div>
        <div class="text-end">
         <button type="submit" class="btn btn-info text-white">
            <i class="bi bi-save"></i> Save Settings</button>
        </div>
    </div>
    <div class="setting-tab-content" id="document">
      <h3>Document Settings</h3>
      <p>Configure document templates, numbering, and print settings</p>

    <div class="">
        <h5>Document Numbering</h5>
        <div class="tabs"></div>

        <div class="mb-3 m-3 col">
            <label for="invoice-prefix">Invoice Prefix</label>
            <input type="text" class="form-control" id="invoice-prefix" placeholder="INV-">
        </div>

        <div class="mb-3 m-3 col">
            <label for="invoice-start-number">Invoice Start Number</label>
            <input type="number" class="form-control" id="invoice-start-number" placeholder="1001">
        </div>

        <div class="mb-3 m-3 col">
            <label for="quotation-prefix">Quotation Prefix</label>
            <input type="text" class="form-control" id="quotation-prefix" placeholder="QT-">
        </div>

        <div class="mb-3 m-3 col">
            <label for="purchase-order-prefix">Purchase Order Prefix</label>
            <input type="text" class="form-control" id="purchase-order-prefix" placeholder="PO-">
        </div>

        <div class="mb-3 m-3 col">
            <label for="reset-numbering">Reset Numbering</label>
            <select class="form-select form-select-lg" id="reset-numbering">
                <option selected>Every Financial Year</option>
                <option value="1">Every Calendar Year</option>
                <option value="2">Every Month</option>
                <option value="3">Never</option>
            </select>
        </div>

        <h5>Print Settings</h5>
        <div class="tabs"></div>

        <div class="mb-3 m-3 col">
            <label for="paper-size">Default Paper Size</label>
            <select class="form-select form-select-lg" id="paper-size">
                <option selected>A4</option>
                <option value="1">Letter</option>
                <option value="2">Legal</option>
                <option value="3">A5</option>
            </select>
        </div>

        <div class="mb-3 m-3 col">
            <label for="print-orientation">Default Orientation</label>
            <select class="form-select form-select-lg" id="print-orientation">
                <option selected>Portrait</option>
                <option value="1">Landscape</option>
            </select>
        </div>

        <div class="mb-3 m-3 col">
            <label for="company-address">Company Address</label>
            <textarea class="form-control" id="company-address" placeholder="123 Main Street, Industrial Area, Mumbai, Maharashtra" style="height: 100px"></textarea>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-info text-white">
                <i class="bi bi-save"></i> Save Settings
            </button>
        </div>
    </div>
</div>

<div class="setting-tab-content" id="billing">
    <h3>Billing Settings</h3>
    <p>Configure payment terms, taxes, and billing preferences</p>


    <div class="">
        <h5>Payment Settings</h5>
        <div class="tabs"></div>

       <div class="mb-3 m-3 col">
            <label for="Default-Payment-Terms">Default Payment Terms</label>
            <select class="form-select form-select-lg" id="reset-numbering">
                <option selected>Net 30 Days</option>
                <option value="1">Net 7 Days</option>
                <option value="2">Net 15 Days</option>
                <option value="3">Net 60 Days</option>
                <option value="4">Due On Receipt</option>
            </select>
        </div>
        <div class="mb-3 m-3 col">
            <label for="Default-Currency">Default Currency</label>
            <select class="form-select form-select-lg" id="reset-numbering">
                <option selected>Indian Rupee</option>
                <option value="1">US Doller $</option>
                <option value="2">Euro</option>
                <option value="3">British Pound</option>
                <option value="4">Due On Receipt</option>
            </select>
        </div>
        <h4 style="margin:auto; width: fit-content;">Payment Methods</h4>
        <div style="margin:auto; width: fit-content;">
        <div style="padding:10px;">
          <label><input type="checkbox" name="paymode" /> Cash</label><br>
          <label><input type="checkbox" name="paymode" /> Cheque</label><br>
          <label><input type="checkbox" name="paymode" /> Bank Transfer</label><br>
          <label><input type="checkbox" name="paymode" /> UPI</label><br>
          <label><input type="checkbox" name="paymode" /> Card Payment</label><br>
          <label><input type="checkbox" name="paymode" /> Buy Now Pay Later</label>
        </div>
      </div>
      <h5>Payment Settings</h5>
      <div class="tabs"></div>


      <div class="mb-3 m-3 col">
            <label for="Default-Currency">Default Tax Type</label>
            <select class="form-select form-select-lg" id="reset-numbering">
                <option selected>GST</option>
                <option value="1">VAT</option>
                <option value="2">No Tax</option>
            </select>
        </div>
        <div class="mb-3 m-3 col">
            <label for="Default-Currency">Default GST Rate</label>
            <select class="form-select form-select-lg" id="reset-numbering">
                <option selected>18%</option>
                <option value="1">12%</option>
                <option value="2">5%</option>
                <option value="2">0%</option>
                <option value="2">28%</option>
            </select>
        </div>
        <div class="form-check form-switch" >
         <div style="padding:10px;">
                    <h6 class="me-2">Tax Calculation</h6>
                    <input class="form-check-input " type="checkbox" id="flexSwitchCheckDefault1">
                    <label class="form-check-label" for="flexSwitchCheckDefault1">Prices are tax inclusive by default</label>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-info text-white">
                <i class="bi bi-save"></i> Save Settings
            </button>
        </div>
    </div>
</div>

        <div class="setting-tab-content" id="advanced">
    <h3>Advanced Settings</h3>
    <p>Configure advanced system settings and integrations</p>


    <div class="">
        <h5>Backup & Data</h5>
        <div class="tabs"></div>
        <div class="form-check form-switch mt-4">
                    <h6>Auto Backup</h6>
                    <input class="form-check-input " type="checkbox" id="flexSwitchCheckDefault1">
                <label class="form-check-label" for="flexSwitchCheckDefault1">Enable automatic data backup</label>
                </div>
                <div class="mb-3 m-3 col">
            <label for="Default-Payment-Terms">Backup Frequency</label>
            <select class="form-select form-select-lg" id="reset-numbering">
                <option selected>Daily</option>
                <option value="1">Weekly</option>
                <option value="2">monthly</option>
            </select>
        </div>
        <div><button type="button" class="btn btn-outline-primary">Backup Now</button></div>
                <p>Last backup: 12 Apr, 2025 09:45 AM</p>
                </div>

                <h5>Integrations</h5>
                <div class="tabs"></div>
                <div class="form-check form-switch mt-4">
                    <h6>Email Integration</h6>
                    <input class="form-check-input " type="checkbox" id="flexSwitchCheckDefault1">
                <label class="form-check-label" for="flexSwitchCheckDefault1">Enable email sending</label>
                </div>
                <div class="mb-3 m-3 col">
            <label for="invoice-prefix">SMTP Server</label>
            <input type="text" class="form-control" id="invoice-prefix" placeholder="smtp.unnatitraders.com">
             </div>

             <div class="form-check form-switch mt-4">
                    <h6>SMS Integration</h6>
                    <input class="form-check-input " type="checkbox" id="flexSwitchCheckDefault1">
                <label class="form-check-label" for="flexSwitchCheckDefault1">Enable SMS notifications</label>
                </div>

                <div class="form-check form-switch mt-4">
                    <h6>API Access</h6>
                    <input class="form-check-input " type="checkbox" id="flexSwitchCheckDefault1">
                <label class="form-check-label" for="flexSwitchCheckDefault1">Enable API access</label>
                </div>
                <p>Allow third-party applications to access your data via API.</p>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-info text-white">
                <i class="bi bi-save"></i> Save Settings
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

    <script>

        function showsettingTab(id) {
            const tabs = document.querySelectorAll('.settingTab');
            const contents = document.querySelectorAll('.setting-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showsettingTab('${id}')"]`).classList.add('active');
        }
    </script>

</body>
</html>