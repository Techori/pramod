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
            <button class="settingTab active" onclick="showsettingTab('general')">General</button>
            <button class="settingTab" onclick="window.location.href='setting_company.php'">Company</button>
            <button class="settingTab" onclick="showsettingTab('document')">Document</button>
            <button class="settingTab" onclick="showsettingTab('billing')">Billing</button>
            <button class="settingTab" onclick="showsettingTab('advanced')">Advanced</button>


            </div>
            </div>
        <div class="col-md-12  card p-3 shadow-sm my-4 setting-tab-content active" id="general">
            <div class="">
                    <h3>General Settings</h3>
                    <p>Configure application preferences and default behavior</p>
               <div class="">
                  <h5 >Setting</h5>
                    <div class="tabs">     
                    </div>
                   <div class= "mb-3 m-3">
                      <label for="" >Language</label>
                         <select class="form-select form-select-lg mb-3 m-3"  aria-label="Large select example">
                           <option selected>English (india)</option>
                           <option value="1">Hindi</option>
                           <option value="2">Gujrati</option>
                           <option value="3">Marathi</option>
                         </select>
                    </div>
                    <div class= "mb-3 m-3">
                      <label for="" >Timezone</label>
                         <select class="form-select form-select-lg mb-3 m-3"  aria-label="Large select example">
                           <option selected>Asia/Kolkata(IST)</option>
                           <option value="1">Asia/Dubai(GST)</option>
                           <option value="2">Euop/London(GST)</option>
                           <option value="3">America/Newyork(EST)</option>
                         </select>
                    </div>
                    <div class= "mb-3 m-3">
                      <label for="" >Date Format</label>
                         <select class="form-select form-select-lg mb-3 m-3"  aria-label="Large select example">
                           <option selected>DD-MM-YYYY</option>
                           <option value="1">MM-DD-YYYY</option>
                           <option value="2">YYYY-MM-DD</option>
                         </select>
                    </div>
                    <div class= "mb-3 m-3">
                      <label for="" >Theme</label>
                         <select class="form-select form-select-lg mb-3 m-3"  aria-label="Large select example">
                           <option selected>Light</option>
                           <option value="1">Dark</option>
                           <option value="2"> System</option>
                         </select>
                    </div>
                    <div class="form-check form-switch mx-2" >
                       </span> <h5>Notifications</h5>
                        <input class="form-check-input mx-1 " type="checkbox" id="flexSwitchCheckDefault">
                        <label class="form-check-label ">Enable system notifications</label>
                    </div>
                </div>
                <div class= "mb-3" >
                  <h5 >System Performance</h5>
                  <div class="form-check form-switch mx-2" >
                  <div class = " tabs"></div>   
                       </span> <h5>Auto</h5>
                        <input class="form-check-input mx-1 " type="checkbox" id="flexSwitchCheckDefault">
                        <label class="form-check-label ">Enable auto-save for forms</label>
                    </div>
                    <div class="form-check form-switch mx-2" >
                       </span> <h5>Cache</h5>
                        <input class="form-check-input mx-1 " type="checkbox" id="flexSwitchCheckDefault">
                        <label class="form-check-label ">Use browser cache for faster loading</label>
                    </div>
                    <div class="form-check form-switch mx-2" >
                       </span> <h5>Analytics</h5>
                        <input class="form-check-input mx-1 " type="checkbox" id="flexSwitchCheckDefault">
                        <label class="form-check-label ">Enable usage analytics</label>
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