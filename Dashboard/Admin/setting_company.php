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
            background-color: #eaeaea;
            color: #007bff;
        }
        .setting-tab-content {
            display: none;
            padding: 20px 0;
        }
        .setting-tab-content.active {
            display: block;
            
        }
      h5
      {
       margin-top: 50px; 
      }
      .form-check-input {
    width: 4rem;
    height: 1.4rem;
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
        <div class="col-md-12  card p-3 shadow-sm my-4">
            <div class="tabs">
            <button class="settingTab active" onclick="showsettingTab('setting.php')">General</button>
            <button class="settingTab" onclick="window.location.href='setting_company.php'">Company</button>
            <button class="settingTab" onclick="showsettingTab('document')">Document</button>
            <button class="settingTab" onclick="showsettingTab('billing')">Billing</button>
            <button class="settingTab" onclick="showsettingTab('advanced')">Advanced</button>
            </div>
            </div>
            <!-- setting_company -->
            <div class="setting-tab-content" id="company">
                    <h3>Cmpany Profile</h3>
                    <p>Update your company information and branding</p>
               <div class="">
                  <h5 >Business Details</h5>
                    <div class="tabs">     
                    </div>
                   <div class= "mb-3 m-3 col">
                      <label for="" >Company Name</label>
                       <input type="text" class="form-control" placeholder="First name" aria-label="Shree Unnati Traders">
                  </div>
                    <div class= "mb-3 m-3   mb-3 m-3col">
                      <label>Address</label>
                           <textarea class="form-control form-floating" placeholder="123 Main Street, Industrial Area, Mumbai, Maharashtra" id="floatingTextarea2" style="height: 100px"></textarea>
                    </div>
                    <div class= "mb-3 m-3 col">
                      <label for="" >GST Number</label>
                       <input type="text" class="form-control" placeholder="27AABCU9603R1ZX" >
                  </div>
                  <div class= "mb-3 m-3 col">
                      <label for="" >PAN Number</label>
                       <input type="text" class="form-control" placeholder="AABCU9603R" >
                  </div>
                    <div class= "mb-3 m-3 col">
                      <label for="" >Phone Number</label>
                       <input type="number" class="form-control" placeholder="info@unnatitraders.com" >
                  </div>
                  <div class= "mb-3 m-3 col">
                      <label for="" >Website</label>
                       <input type="text" class="form-control" placeholder="www.unnatitraders.com" >
                  </div>


                  
                </div>
                <div class="row mb-4">
      <div class="col-md-2 fw-bold">Company Logo</div>
      <div class="col-md-4">
        <div class="logo-preview mb-2">Logo Preview</div>
        <button type="button" class="btn btn-outline-secondary btn-sm">Upload Logo</button>
      </div>
    </div>

    <div class="row mb-4 align-items-center">
      <div class="col-md-2 fw-bold">Primary Color</div>
      <div class="col-md-4 d-flex align-items-center gap-2">
        <div class="color-circle"></div>
        <input type="text" class="form-control" placeholder="black">
      </div>
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-info text-white">
        <i class="bi bi-save"></i> Save Company Profile
      </button>
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

        function showsettingTab(tabId) {
    // Sab buttons se 'active' class hatao
    document.querySelectorAll('.settingTab').forEach(btn => btn.classList.remove('active'));

    // Sab content blocks ko hide karo
    document.querySelectorAll('.setting-tab-content').forEach(tab => tab.classList.remove('active'));

    // Jo button click hua usse active banao
    const activeButton = document.querySelector(`[onclick="showsettingTab('${tabId}')"]`);
    if (activeButton) activeButton.classList.add('active');

    // Us tab ka content show karo
    const activeTab = document.getElementById(tabId);
    if (activeTab) activeTab.classList.add('active');
}
</script>
</body>
</html>