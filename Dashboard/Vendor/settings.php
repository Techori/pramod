<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';

$user_name = $_SESSION['user_name'];

// Default values for form
$settings = [
    'profile' => [
        'firstName' => '',
        'lastName' => '',
        'email' => '',
        'phone' => '',
        'position' => ''
    ],
    'business' => [
        'companyName' => '',
        'businessType' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'pincode' => '',
        'gstin' => '',
        'panNumber' => '',
        'businessDescription' => ''
    ],
    'payment' => [
        'accountName' => '',
        'bankName' => '',
        'accountNumber' => '',
        'ifscCode' => '',
        'accountType' => '',
        'branch' => '',
        'upiId' => '',
        'qrCode' => ''
    ],
    'shipping' => [
        'sameAsBusiness' => false,
        'shippingAddress' => '',
        'shippingCity' => '',
        'shippingState' => '',
        'shippingPincode' => '',
        'freeShipping' => false,
        'freeShippingThreshold' => '',
        'sameDayProcessing' => false,
        'processingCutoffTime' => '',
        'shippingPartners' => []
    ],
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'Profile') {
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $position = $conn->real_escape_string($_POST['position']);

    // Check if a profile exists for this username
    $check = $conn->query("SELECT id FROM vendor_user_profiles WHERE created_by = '$user_name'");

    if ($check && $check->num_rows > 0) {
        // UPDATE
        $conn->query("
            UPDATE vendor_user_profiles 
            SET 
                first_name = '$firstName',
                last_name = '$lastName',
                email = '$email',
                phone = '$phone',
                position = '$position',
                updated_at = NOW()
            WHERE created_by = '$user_name'
        ");
    } else {
        // INSERT
        $conn->query("
            INSERT INTO vendor_user_profiles 
            (created_by, first_name, last_name, email, phone, position, created_at, updated_at)
            VALUES 
            ('$user_name', '$firstName', '$lastName', '$email', '$phone', '$position', NOW(), NOW())
        ");
    }
}

// Fetch profile for pre-filling form
$result = $conn->query("SELECT * FROM vendor_user_profiles WHERE created_by = '$user_name'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $settings['profile']['firstName'] = $row['first_name'];
    $settings['profile']['lastName'] = $row['last_name'];
    $settings['profile']['email'] = $row['email'];
    $settings['profile']['phone'] = $row['phone'];
    $settings['profile']['position'] = $row['position'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'Business') {
    $companyName = $conn->real_escape_string($_POST['companyName']);
    $businessType = $conn->real_escape_string($_POST['businessType']);
    $address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $state = $conn->real_escape_string($_POST['state']);
    $pincode = $conn->real_escape_string($_POST['pincode']);
    $gstin = $conn->real_escape_string($_POST['gstin']);
    $panNumber = $conn->real_escape_string($_POST['panNumber']);
    $businessDescription = $conn->real_escape_string($_POST['businessDescription']);

    // Check if record exists
    $check = $conn->query("SELECT id FROM vendor_business_profiles WHERE created_by = '$user_name'");

    if ($check && $check->num_rows > 0) {
        // UPDATE
        $conn->query("
            UPDATE vendor_business_profiles SET 
                company_name = '$companyName',
                business_type = '$businessType',
                address = '$address',
                city = '$city',
                state = '$state',
                pincode = '$pincode',
                gstin = '$gstin',
                pan_number = '$panNumber',
                business_description = '$businessDescription',
                updated_at = NOW()
            WHERE created_by = '$user_name'
        ");
    } else {
        // INSERT
        $conn->query("
            INSERT INTO vendor_business_profiles 
                (created_by, company_name, business_type, address, city, state, pincode, gstin, pan_number, business_description, created_at, updated_at)
            VALUES 
                ('$user_name', '$companyName', '$businessType', '$address', '$city', '$state', '$pincode', '$gstin', '$panNumber', '$businessDescription', NOW(), NOW())
        ");
    }
}

// Fetch for prefill
$result = $conn->query("SELECT * FROM vendor_business_profiles WHERE created_by = '$user_name'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $settings['business']['companyName'] = $row['company_name'];
    $settings['business']['businessType'] = $row['business_type'];
    $settings['business']['address'] = $row['address'];
    $settings['business']['city'] = $row['city'];
    $settings['business']['state'] = $row['state'];
    $settings['business']['pincode'] = $row['pincode'];
    $settings['business']['gstin'] = $row['gstin'];
    $settings['business']['panNumber'] = $row['pan_number'];
    $settings['business']['businessDescription'] = $row['business_description'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'Payment') {
    // Sanitize inputs
    $accountName = $conn->real_escape_string($_POST['accountName']);
    $bankName = $conn->real_escape_string($_POST['bankName']);
    $accountNumber = $conn->real_escape_string($_POST['accountNumber']);
    $ifscCode = $conn->real_escape_string($_POST['ifscCode']);
    $accountType = $conn->real_escape_string($_POST['accountType']);
    $branch = $conn->real_escape_string($_POST['branch']);
    $upiId = $conn->real_escape_string($_POST['upiId']);

    // Handle QR Code Upload
    $qrCodeFileName = '';
    $qrCodeFileName = '';
    if (isset($_FILES['qrCodeFile']) && $_FILES['qrCodeFile']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $fileType = mime_content_type($_FILES['qrCodeFile']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = 'uploads/qr_codes/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['qrCodeFile']['name']);
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['qrCodeFile']['tmp_name'], $targetFilePath)) {
                $qrCodeFileName = $fileName;
            } else {
                echo "QR upload failed.";
            }
        } else {
            echo "Invalid QR image format.";
        }
    } else {
        // Keep old QR code if not reuploaded
        $qrCodeFileName = $_POST['qrCode'] ?? '';
    }

    // Check if settings already exist
    $check = $conn->query("SELECT id FROM vendor_payment_settings WHERE created_by = '$user_name'");
    if ($check && $check->num_rows > 0) {
        $conn->query("UPDATE vendor_payment_settings SET
            account_name = '$accountName',
            bank_name = '$bankName',
            account_number = '$accountNumber',
            ifsc_code = '$ifscCode',
            account_type = '$accountType',
            branch = '$branch',
            upi_id = '$upiId',
            qr_code = '$qrCodeFileName'
            WHERE created_by = '$user_name'
        ");
    } else {
        $conn->query("INSERT INTO vendor_payment_settings (
            created_by, account_name, bank_name, account_number, ifsc_code,
            account_type, branch, upi_id, qr_code
        ) VALUES (
            '$user_name', '$accountName', '$bankName', '$accountNumber', '$ifscCode',
            '$accountType', '$branch', '$upiId', '$qrCodeFileName'
        )");
    }
}

// Fetch existing data
$result = $conn->query("SELECT * FROM vendor_payment_settings WHERE created_by = '$user_name'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $settings['payment']['accountName'] = $row['account_name'];
    $settings['payment']['bankName'] = $row['bank_name'];
    $settings['payment']['accountNumber'] = $row['account_number'];
    $settings['payment']['ifscCode'] = $row['ifsc_code'];
    $settings['payment']['accountType'] = $row['account_type'];
    $settings['payment']['branch'] = $row['branch'];
    $settings['payment']['upiId'] = $row['upi_id'];
    $settings['payment']['qrCode'] = $row['qr_code'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'Settings') {
    $sameAsBusiness = isset($_POST['sameAsBusiness']) ? 1 : 0;
    $shippingAddress = $conn->real_escape_string($_POST['shippingAddress']);
    $shippingCity = $conn->real_escape_string($_POST['shippingCity']);
    $shippingState = $conn->real_escape_string($_POST['shippingState']);
    $shippingPincode = $conn->real_escape_string($_POST['shippingPincode']);
    $freeShipping = isset($_POST['freeShipping']) ? 1 : 0;
    $freeShippingThreshold = $conn->real_escape_string($_POST['freeShippingThreshold']);
    $sameDayProcessing = isset($_POST['sameDayProcessing']) ? 1 : 0;
    $processingCutoffTime = $conn->real_escape_string($_POST['processingCutoffTime']);
    $shippingPartners = isset($_POST['shippingPartners']) ? implode(',', $_POST['shippingPartners']) : '';

    // Check if entry exists
    $check = $conn->query("SELECT id FROM vendor_shipping_settings WHERE created_by = '$user_name'");
    if ($check && $check->num_rows > 0) {
        // Update
        $conn->query("UPDATE vendor_shipping_settings SET
            same_as_business = $sameAsBusiness,
            shipping_address = '$shippingAddress',
            shipping_city = '$shippingCity',
            shipping_state = '$shippingState',
            shipping_pincode = '$shippingPincode',
            free_shipping = $freeShipping,
            free_shipping_threshold = '$freeShippingThreshold',
            same_day_processing = $sameDayProcessing,
            processing_cutoff_time = '$processingCutoffTime',
            shipping_partners = '$shippingPartners'
            WHERE created_by = '$user_name'
        ");
    } else {
        // Insert
        $conn->query("INSERT INTO vendor_shipping_settings (
            created_by, same_as_business, shipping_address, shipping_city, shipping_state, shipping_pincode,
            free_shipping, free_shipping_threshold, same_day_processing, processing_cutoff_time, shipping_partners
        ) VALUES (
            '$user_name', $sameAsBusiness, '$shippingAddress', '$shippingCity', '$shippingState', '$shippingPincode',
            $freeShipping, '$freeShippingThreshold', $sameDayProcessing, '$processingCutoffTime', '$shippingPartners'
        )");
    }
}

// Fetch existing values to prefill form
$result = $conn->query("SELECT * FROM vendor_shipping_settings WHERE created_by = '$user_name'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $settings['shipping']['sameAsBusiness'] = (bool) $row['same_as_business'];
    $settings['shipping']['shippingAddress'] = $row['shipping_address'];
    $settings['shipping']['shippingCity'] = $row['shipping_city'];
    $settings['shipping']['shippingState'] = $row['shipping_state'];
    $settings['shipping']['shippingPincode'] = $row['shipping_pincode'];
    $settings['shipping']['freeShipping'] = (bool) $row['free_shipping'];
    $settings['shipping']['freeShippingThreshold'] = $row['free_shipping_threshold'];
    $settings['shipping']['sameDayProcessing'] = (bool) $row['same_day_processing'];
    $settings['shipping']['processingCutoffTime'] = $row['processing_cutoff_time'];
    $settings['shipping']['shippingPartners'] = explode(',', $row['shipping_partners']);
}


$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Navigation items
$nav_items = [
    'profile' => ['icon' => 'fa-user', 'label' => 'Profile Information'],
    'business' => ['icon' => 'fa-building', 'label' => 'Business Details'],
    'payment' => ['icon' => 'fa-credit-card', 'label' => 'Payment Information'],
    'shipping' => ['icon' => 'fa-truck', 'label' => 'Shipping Settings'],
];

// Helper function to build tab URL
function build_tab_url($tab)
{
    $params = ['page' => 'settings', 'tab' => $tab];
    return '?' . http_build_query($params);
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-cog text-primary"></i> Settings</h1>
            <p>Manage your account settings and preferences.</p>
        </div>
    </div>

    <div class="row">
        <!-- Navigation -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <nav class="nav flex-column">
                        <?php foreach ($nav_items as $key => $item): ?>
                            <a class="nav-link d-flex align-items-center gap-2 px-4 py-3 <?php echo $active_tab === $key ? 'bg-primary text-white' : 'text-dark hover:bg-light'; ?>"
                                href="<?php echo build_tab_url($key); ?>">
                                <i class="fas <?php echo $item['icon']; ?>"></i>
                                <?php echo $item['label']; ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="col-md-9">
            <?php if ($active_tab === 'profile'): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Profile Information</h5>
                        <p class="card-text">Update your personal information and contact details</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="?page=settings&tab=profile">
                            <input type="hidden" name="section" value="Profile">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="firstName" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="firstName" name="firstName"
                                                value="<?php echo htmlspecialchars($settings['profile']['firstName']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lastName" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="lastName" name="lastName"
                                                value="<?php echo htmlspecialchars($settings['profile']['lastName']); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="<?php echo htmlspecialchars($settings['profile']['email']); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="<?php echo htmlspecialchars($settings['profile']['phone']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Position/Title</label>
                                        <input type="text" class="form-control" id="position" name="position"
                                            value="<?php echo htmlspecialchars($settings['profile']['position']); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            <?php elseif ($active_tab === 'business'): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Business Details</h5>
                        <p class="card-text">Update your company information and business details</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="?page=settings&tab=business">
                            <input type="hidden" name="section" value="Business">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="companyName" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="companyName" name="companyName"
                                        value="<?php echo htmlspecialchars($settings['business']['companyName']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="businessType" class="form-label">Business Type</label>
                                    <input type="text" class="form-control" id="businessType" name="businessType"
                                        value="<?php echo htmlspecialchars($settings['business']['businessType']); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Business Address</label>
                                <textarea class="form-control" id="address" name="address"
                                    rows="3"><?php echo htmlspecialchars($settings['business']['address']); ?></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="<?php echo htmlspecialchars($settings['business']['city']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="state"
                                        value="<?php echo htmlspecialchars($settings['business']['state']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="pincode" class="form-label">PIN Code</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode"
                                        value="<?php echo htmlspecialchars($settings['business']['pincode']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="gstin" class="form-label">GSTIN</label>
                                    <input type="text" class="form-control" id="gstin" name="gstin"
                                        value="<?php echo htmlspecialchars($settings['business']['gstin']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="panNumber" class="form-label">PAN Number</label>
                                    <input type="text" class="form-control" id="panNumber" name="panNumber"
                                        value="<?php echo htmlspecialchars($settings['business']['panNumber']); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="businessDescription" class="form-label">Business Description</label>
                                <textarea class="form-control" id="businessDescription" name="businessDescription"
                                    rows="4"><?php echo htmlspecialchars($settings['business']['businessDescription']); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>

            <?php elseif ($active_tab === 'payment'): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Payment Information</h5>
                        <p class="card-text">Manage your payment methods and bank account details</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="?page=settings&tab=payment" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="Payment">
                            <h6 class="mb-3">Bank Account Details</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="accountName" class="form-label">Account Holder Name</label>
                                    <input type="text" class="form-control" id="accountName" name="accountName"
                                        value="<?php echo htmlspecialchars($settings['payment']['accountName']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="bankName" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="bankName" name="bankName"
                                        value="<?php echo htmlspecialchars($settings['payment']['bankName']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="accountNumber" class="form-label">Account Number</label>
                                    <input type="password" class="form-control" id="accountNumber" name="accountNumber"
                                        value="<?php echo htmlspecialchars($settings['payment']['accountNumber']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="ifscCode" class="form-label">IFSC Code</label>
                                    <input type="text" class="form-control" id="ifscCode" name="ifscCode"
                                        value="<?php echo htmlspecialchars($settings['payment']['ifscCode']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="accountType" class="form-label">Account Type</label>
                                    <input type="text" class="form-control" id="accountType" name="accountType"
                                        value="<?php echo htmlspecialchars($settings['payment']['accountType']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="branch" class="form-label">Branch</label>
                                    <input type="text" class="form-control" id="branch" name="branch"
                                        value="<?php echo htmlspecialchars($settings['payment']['branch']); ?>">
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">UPI Details</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="upiId" class="form-label">UPI ID</label>
                                    <input type="text" class="form-control" id="upiId" name="upiId"
                                        value="<?php echo htmlspecialchars($settings['payment']['upiId']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="qrCode" class="form-label">QR Code</label>
                                    <div class="input-group">
                                        <!-- Display selected file name -->
                                        <input type="text" class="form-control" id="qrCode" name="qrCode"
                                            value="<?php echo htmlspecialchars($settings['payment']['qrCode'] ?? ''); ?>"
                                            readonly>

                                        <!-- Hidden file input -->
                                        <input type="file" id="qrCodeFile" name="qrCodeFile" accept="image/*"
                                            style="display: none;" onchange="handleQRCodeUpload(event)">

                                        <!-- Button triggers file input -->
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="document.getElementById('qrCodeFile').click()">Upload</button>
                                    </div>
                                </div>

                                <script>
                                    function handleQRCodeUpload(event) {
                                        const file = event.target.files[0];
                                        if (file) {
                                            document.getElementById('qrCode').value = file.name;

                                            // OPTIONAL: Preview or upload via AJAX
                                            // uploadQRCode(file);
                                        }
                                    }
                                </script>

                            </div>
                            <div class="row mb-3">
                                <?php if (!empty($settings['payment']['qrCode'])): ?>
                                    <div class="col-md-6">
                                        <img src="uploads/qr_codes/<?php echo htmlspecialchars($settings['payment']['qrCode']); ?>"
                                            width="300" height="300" alt="QR Code">
                                    </div>
                                <?php endif; ?>

                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>

            <?php elseif ($active_tab === 'shipping'): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Shipping Settings</h5>
                        <p class="card-text">Configure your shipping preferences and delivery options</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="?page=settings&tab=shipping">
                            <input type="hidden" name="section" value="Settings">
                            <h6 class="mb-3">Shipping Address</h6>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="sameAsBusiness" name="sameAsBusiness"
                                    <?php echo $settings['shipping']['sameAsBusiness'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="sameAsBusiness">Same as business address</label>
                            </div>
                            <div class="mb-3">
                                <label for="shippingAddress" class="form-label">Warehouse/Shipping Address</label>
                                <textarea class="form-control" id="shippingAddress" name="shippingAddress"
                                    rows="3"><?php echo htmlspecialchars($settings['shipping']['shippingAddress']); ?></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="shippingCity" class="form-label">City</label>
                                    <input type="text" class="form-control" id="shippingCity" name="shippingCity"
                                        value="<?php echo htmlspecialchars($settings['shipping']['shippingCity']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="shippingState" class="form-label">State</label>
                                    <input type="text" class="form-control" id="shippingState" name="shippingState"
                                        value="<?php echo htmlspecialchars($settings['shipping']['shippingState']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="shippingPincode" class="form-label">PIN Code</label>
                                    <input type="text" class="form-control" id="shippingPincode" name="shippingPincode"
                                        value="<?php echo htmlspecialchars($settings['shipping']['shippingPincode']); ?>">
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">Delivery Preferences</h6>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="freeShipping" name="freeShipping" <?php echo $settings['shipping']['freeShipping'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="freeShipping">Offer Free Shipping</label>
                                <small class="form-text text-muted">Provide free shipping for orders above a certain
                                    value</small>
                            </div>
                            <div class="mb-3">
                                <label for="freeShippingThreshold" class="form-label">Free Shipping Threshold (₹)</label>
                                <input type="number" class="form-control" id="freeShippingThreshold"
                                    name="freeShippingThreshold"
                                    value="<?php echo htmlspecialchars($settings['shipping']['freeShippingThreshold']); ?>">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="sameDayProcessing"
                                    name="sameDayProcessing" <?php echo $settings['shipping']['sameDayProcessing'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="sameDayProcessing">Same-Day Processing</label>
                                <small class="form-text text-muted">Process orders on the same day if placed before cutoff
                                    time</small>
                            </div>
                            <div class="mb-3">
                                <label for="processingCutoffTime" class="form-label">Processing Cutoff Time</label>
                                <input type="time" class="form-control" id="processingCutoffTime"
                                    name="processingCutoffTime"
                                    value="<?php echo htmlspecialchars($settings['shipping']['processingCutoffTime']); ?>">
                            </div>
                            <hr>
                            <h6 class="mb-3">Shipping Partners</h6>
                            <div class="mb-3">
                                <?php $partners = ['delhivery' => 'Delhivery', 'blueDart' => 'Blue Dart', 'dtdc' => 'DTDC', 'ownDelivery' => 'Own Delivery Service']; ?>
                                <?php foreach ($partners as $key => $label): ?>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="<?php echo $key; ?>"
                                            name="shippingPartners[]" value="<?php echo $key; ?>" <?php echo in_array($key, $settings['shipping']['shippingPartners']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="<?php echo $key; ?>"><?php echo $label; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>