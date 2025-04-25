<?php
require_once 'database.php';
$settings = get_settings();
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Navigation items
$nav_items = [
    'profile' => ['icon' => 'fa-user', 'label' => 'Profile Information'],
    'business' => ['icon' => 'fa-building', 'label' => 'Business Details'],
    'payment' => ['icon' => 'fa-credit-card', 'label' => 'Payment Information'],
    'shipping' => ['icon' => 'fa-truck', 'label' => 'Shipping Settings'],
    'notifications' => ['icon' => 'fa-bell', 'label' => 'Notifications'],
    'security' => ['icon' => 'fa-lock', 'label' => 'Security & Login'],
    'documents' => ['icon' => 'fa-file-alt', 'label' => 'Documents'],
];

// Helper function to build tab URL
function build_tab_url($tab) {
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
                            <a class="nav-link d-flex align-items-center gap-2 px-4 py-3 <?php echo $active_tab === $key ? 'bg-primary text-white' : 'text-dark hover:bg-light'; ?>" href="<?php echo build_tab_url($key); ?>">
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
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <div class="h-50 w-50 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="font-size: 2rem;">
                                        <?php echo htmlspecialchars($settings['profile']['avatar']); ?>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary mt-3" onclick="alert('Changing photo')">Change Photo</button>
                                </div>
                                <div class="col-md-8">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="firstName" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($settings['profile']['firstName']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lastName" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($settings['profile']['lastName']); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($settings['profile']['email']); ?>">
                                            <span class="input-group-text bg-success text-white"><i class="fas fa-check"></i> Verified</span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($settings['profile']['phone']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Position/Title</label>
                                        <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($settings['profile']['position']); ?>">
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="alert('Profile settings updated')">Save Changes</button>
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
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="companyName" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="companyName" name="companyName" value="<?php echo htmlspecialchars($settings['business']['companyName']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="businessType" class="form-label">Business Type</label>
                                    <input type="text" class="form-control" id="businessType" name="businessType" value="<?php echo htmlspecialchars($settings['business']['businessType']); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Business Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($settings['business']['address']); ?></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($settings['business']['city']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($settings['business']['state']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="pincode" class="form-label">PIN Code</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode" value="<?php echo htmlspecialchars($settings['business']['pincode']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="gstin" class="form-label">GSTIN</label>
                                    <input type="text" class="form-control" id="gstin" name="gstin" value="<?php echo htmlspecialchars($settings['business']['gstin']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="panNumber" class="form-label">PAN Number</label>
                                    <input type="text" class="form-control" id="panNumber" name="panNumber" value="<?php echo htmlspecialchars($settings['business']['panNumber']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="text" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($settings['business']['website']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="taxExemption" class="form-label">Tax Exemption Certificate</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="taxExemption" name="taxExemption" value="<?php echo htmlspecialchars($settings['business']['taxExemption']); ?>" readonly>
                                        <button type="button" class="btn btn-outline-primary" onclick="alert('Uploading tax exemption certificate')">Upload</button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="businessDescription" class="form-label">Business Description</label>
                                <textarea class="form-control" id="businessDescription" name="businessDescription" rows="4"><?php echo htmlspecialchars($settings['business']['businessDescription']); ?></textarea>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="alert('Business settings updated')">Save Changes</button>
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
                        <form method="POST" action="">
                            <h6 class="mb-3">Bank Account Details</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="accountName" class="form-label">Account Holder Name</label>
                                    <input type="text" class="form-control" id="accountName" name="accountName" value="<?php echo htmlspecialchars($settings['payment']['accountName']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="bankName" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="bankName" name="bankName" value="<?php echo htmlspecialchars($settings['payment']['bankName']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="accountNumber" class="form-label">Account Number</label>
                                    <input type="password" class="form-control" id="accountNumber" name="accountNumber" value="<?php echo htmlspecialchars($settings['payment']['accountNumber']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="ifscCode" class="form-label">IFSC Code</label>
                                    <input type="text" class="form-control" id="ifscCode" name="ifscCode" value="<?php echo htmlspecialchars($settings['payment']['ifscCode']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="accountType" class="form-label">Account Type</label>
                                    <input type="text" class="form-control" id="accountType" name="accountType" value="<?php echo htmlspecialchars($settings['payment']['accountType']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="branch" class="form-label">Branch</label>
                                    <input type="text" class="form-control" id="branch" name="branch" value="<?php echo htmlspecialchars($settings['payment']['branch']); ?>">
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">UPI Details</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="upiId" class="form-label">UPI ID</label>
                                    <input type="text" class="form-control" id="upiId" name="upiId" value="<?php echo htmlspecialchars($settings['payment']['upiId']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="qrCode" class="form-label">QR Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="qrCode" name="qrCode" value="<?php echo htmlspecialchars($settings['payment']['qrCode']); ?>" readonly>
                                        <button type="button" class="btn btn-outline-primary" onclick="alert('Uploading QR code')">Upload</button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">Payment Preferences</h6>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="autoInvoice" name="autoInvoice" <?php echo $settings['payment']['autoInvoice'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="autoInvoice">Auto-Generate Invoices</label>
                                    <small class="form-text text-muted">Automatically generate invoices for new orders</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="paymentReminders" name="paymentReminders" <?php echo $settings['payment']['paymentReminders'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="paymentReminders">Payment Reminders</label>
                                    <small class="form-text text-muted">Send automated payment reminders for outstanding invoices</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="alert('Payment settings updated')">Save Changes</button>
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
                        <form method="POST" action="">
                            <h6 class="mb-3">Shipping Address</h6>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="sameAsBusiness" name="sameAsBusiness" <?php echo $settings['shipping']['sameAsBusiness'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="sameAsBusiness">Same as business address</label>
                            </div>
                            <div class="mb-3">
                                <label for="shippingAddress" class="form-label">Warehouse/Shipping Address</label>
                                <textarea class="form-control" id="shippingAddress" name="shippingAddress" rows="3"><?php echo htmlspecialchars($settings['shipping']['shippingAddress']); ?></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="shippingCity" class="form-label">City</label>
                                    <input type="text" class="form-control" id="shippingCity" name="shippingCity" value="<?php echo htmlspecialchars($settings['shipping']['shippingCity']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="shippingState" class="form-label">State</label>
                                    <input type="text" class="form-control" id="shippingState" name="shippingState" value="<?php echo htmlspecialchars($settings['shipping']['shippingState']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="shippingPincode" class="form-label">PIN Code</label>
                                    <input type="text" class="form-control" id="shippingPincode" name="shippingPincode" value="<?php echo htmlspecialchars($settings['shipping']['shippingPincode']); ?>">
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">Delivery Preferences</h6>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="freeShipping" name="freeShipping" <?php echo $settings['shipping']['freeShipping'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="freeShipping">Offer Free Shipping</label>
                                <small class="form-text text-muted">Provide free shipping for orders above a certain value</small>
                            </div>
                            <div class="mb-3">
                                <label for="freeShippingThreshold" class="form-label">Free Shipping Threshold (₹)</label>
                                <input type="number" class="form-control" id="freeShippingThreshold" name="freeShippingThreshold" value="<?php echo htmlspecialchars($settings['shipping']['freeShippingThreshold']); ?>">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="sameDayProcessing" name="sameDayProcessing" <?php echo $settings['shipping']['sameDayProcessing'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="sameDayProcessing">Same-Day Processing</label>
                                <small class="form-text text-muted">Process orders on the same day if placed before cutoff time</small>
                            </div>
                            <div class="mb-3">
                                <label for="processingCutoffTime" class="form-label">Processing Cutoff Time</label>
                                <input type="time" class="form-control" id="processingCutoffTime" name="processingCutoffTime" value="<?php echo htmlspecialchars($settings['shipping']['processingCutoffTime']); ?>">
                            </div>
                            <hr>
                            <h6 class="mb-3">Shipping Partners</h6>
                            <div class="mb-3">
                                <?php $partners = ['delhivery' => 'Delhivery', 'blueDart' => 'Blue Dart', 'dtdc' => 'DTDC', 'ownDelivery' => 'Own Delivery Service']; ?>
                                <?php foreach ($partners as $key => $label): ?>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="<?php echo $key; ?>" name="shippingPartners[]" value="<?php echo $key; ?>" <?php echo in_array($key, $settings['shipping']['shippingPartners']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="<?php echo $key; ?>"><?php echo $label; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="alert('Shipping settings updated')">Save Changes</button>
                        </form>
                    </div>
                </div>

            <?php elseif ($active_tab === 'notifications'): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Notification Settings</h5>
                        <p class="card-text">Configure how and when you receive notifications</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <h6 class="mb-3">Order Notifications</h6>
                            <?php $order_notifications = [
                                'newOrder' => 'New Order',
                                'orderUpdates' => 'Order Updates',
                                'orderCancellations' => 'Order Cancellations'
                            ]; ?>
                            <?php foreach ($order_notifications as $key => $label): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6><?php echo $label; ?></h6>
                                        <p class="text-muted">Get notified when <?php echo strtolower($label); ?> occur</p>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="<?php echo $key; ?>Email" name="<?php echo $key; ?>[email]" <?php echo $settings['notifications'][$key]['email'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $key; ?>Email">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="<?php echo $key; ?>SMS" name="<?php echo $key; ?>[sms]" <?php echo $settings['notifications'][$key]['sms'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $key; ?>SMS">SMS</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="<?php echo $key; ?>App" name="<?php echo $key; ?>[app]" <?php echo $settings['notifications'][$key]['app'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $key; ?>App">App</label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <h6 class="mb-3">Payment Notifications</h6>
                            <?php $payment_notifications = [
                                'paymentReceived' => 'Payment Received',
                                'paymentDue' => 'Payment Due'
                            ]; ?>
                            <?php foreach ($payment_notifications as $key => $label): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6><?php echo $label; ?></h6>
                                        <p class="text-muted">Get notified about <?php echo strtolower($label); ?></p>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="<?php echo $key; ?>Email" name="<?php echo $key; ?>[email]" <?php echo $settings['notifications'][$key]['email'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $key; ?>Email">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="<?php echo $key; ?>SMS" name="<?php echo $key; ?>[sms]" <?php echo $settings['notifications'][$key]['sms'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $key; ?>SMS">SMS</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="<?php echo $key; ?>App" name="<?php echo $key; ?>[app]" <?php echo $settings['notifications'][$key]['app'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $key; ?>App">App</label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <h6 class="mb-3">Inventory Notifications</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6>Low Stock Alerts</h6>
                                    <p class="text-muted">Get notified when products are running low on stock</p>
                                </div>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="lowStockEmail" name="lowStock[email]" <?php echo $settings['notifications']['lowStock']['email'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="lowStockEmail">Email</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="lowStockSMS" name="lowStock[sms]" <?php echo $settings['notifications']['lowStock']['sms'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="lowStockSMS">SMS</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="lowStockApp" name="lowStock[app]" <?php echo $settings['notifications']['lowStock']['app'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="lowStockApp">App</label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="alert('Notification settings updated')">Save Changes</button>
                        </form>
                    </div>
                </div>

            <?php elseif ($active_tab === 'security'): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Security & Login</h5>
                        <p class="card-text">Manage your password and account security settings</p>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Change Password</h6>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="currentPassword" name="currentPassword">
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" name="newPassword">
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="alert('Password updated')">Update Password</button>
                        </form>
                        <hr>
                        <h6 class="mb-3">Two-Factor Authentication</h6>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p>Enhance your account security by enabling two-factor authentication</p>
                                <p class="text-success">Status: <strong>Enabled</strong></p>
                            </div>
                            <button type="button" class="btn btn-outline-primary" onclick="alert('Configuring 2FA')">Configure</button>
                        </div>
                        <hr>
                        <h6 class="mb-3">Login History</h6>
                        <?php foreach ($settings['security']['loginHistory'] as $login): ?>
                            <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-shield-alt <?php echo $login['status'] === 'Active' ? 'text-success' : 'text-muted'; ?>"></i>
                                        <p class="mb-0"><?php echo $login['status'] === 'Active' ? 'Current Session' : 'Previous Login'; ?></p>
                                    </div>
                                    <p class="text-muted small"><?php echo htmlspecialchars($login['location']); ?> - <?php echo htmlspecialchars($login['device']); ?> • <?php echo htmlspecialchars($login['time']); ?></p>
                                </div>
                                <span class="badge <?php echo $login['status'] === 'Active' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $login['status']; ?></span>
                            </div>
                        <?php endforeach; ?>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="alert('Viewing all login activity')">View All Login Activity</button>
                        <hr>
                        <h6 class="mb-3">Account Access</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>Authorized Users</h6>
                                <p class="text-muted">Manage who can access your vendor account</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary" onclick="alert('Managing users')"><i class="fas fa-users"></i> Manage Users</button>
                        </div>
                    </div>
                </div>

            <?php elseif ($active_tab === 'documents'): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Documents</h5>
                        <p class="card-text">Manage your business documents and compliance certificates</p>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Business Documents</h6>
                        <?php foreach (array_slice($settings['documents'], 0, 4) as $doc): ?>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-file-alt <?php echo $doc['status'] === 'Uploaded' ? 'text-primary' : 'text-muted'; ?>"></i>
                                    <div>
                                        <p class="mb-0"><?php echo htmlspecialchars($doc['name']); ?></p>
                                        <p class="text-muted small"><?php echo $doc['status'] === 'Uploaded' ? 'Uploaded on: ' . htmlspecialchars($doc['uploaded']) : 'Not uploaded'; ?></p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <?php if ($doc['status'] === 'Uploaded'): ?>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="alert('Viewing <?php echo htmlspecialchars($doc['name']); ?>')">View</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="alert('Updating <?php echo htmlspecialchars($doc['name']); ?>')">Update</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="alert('Uploading <?php echo htmlspecialchars($doc['name']); ?>')">Upload</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <h6 class="mb-3">Product Certifications</h6>
                        <?php foreach (array_slice($settings['documents'], 4) as $doc): ?>
                            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-file-alt text-primary"></i>
                                    <div>
                                        <p class="mb-0"><?php echo htmlspecialchars($doc['name']); ?></p>
                                        <p class="text-muted small">Uploaded on: <?php echo htmlspecialchars($doc['uploaded']); ?></p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="alert('Viewing <?php echo htmlspecialchars($doc['name']); ?>')">View</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="alert('Updating <?php echo htmlspecialchars($doc['name']); ?>')">Update</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <button type="button" class="btn btn-primary mt-3" onclick="alert('Uploading new document')"><i class="fas fa-file-alt"></i> Upload New Document</button>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>