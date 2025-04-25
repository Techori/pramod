<?php

// Database file for mock data
// In the future, replace these arrays and functions with actual database queries

// Mock data arrays
$invoices = [
    [
        "id" => "INV-2025-001",
        "date" => "2025-04-12",
        "dueDate" => "2025-05-12",
        "amount" => 24500,
        "status" => "Paid",
        "type" => "GST",
        "gstNumber" => "29ABCDE1234F1Z5",
        "customer" => "Unnati Traders"
    ],
    [
        "id" => "INV-2025-002",
        "date" => "2025-04-08",
        "dueDate" => "2025-05-08",
        "amount" => 18750,
        "status" => "Pending",
        "type" => "GST",
        "gstNumber" => "29ABCDE1234F1Z5",
        "customer" => "Modern Electricals"
    ],
    [
        "id" => "INV-2025-003",
        "date" => "2025-04-05",
        "dueDate" => "2025-05-05",
        "amount" => 32250,
        "status" => "Paid",
        "type" => "GST",
        "gstNumber" => "29ABCDE1234F1Z5",
        "customer" => "City Lights"
    ],
    [
        "id" => "INV-2025-004",
        "date" => "2025-03-28",
        "dueDate" => "2025-04-28",
        "amount" => 15800,
        "status" => "Overdue",
        "type" => "Non-GST",
        "gstNumber" => "-",
        "customer" => "Sharma Electronics"
    ],
    [
        "id" => "INV-2025-005",
        "date" => "2025-03-20",
        "dueDate" => "2025-04-20",
        "amount" => 22450,
        "status" => "Paid",
        "type" => "GST",
        "gstNumber" => "29ABCDE1234F1Z5",
        "customer" => "Premium Switches"
    ],
    [
        "id" => "INV-2025-006",
        "date" => "2025-03-15",
        "dueDate" => "2025-04-15",
        "amount" => 8700,
        "status" => "Pending",
        "type" => "Non-GST",
        "gstNumber" => "-",
        "customer" => "Electro Mart"
    ]
];

$payment_methods = [
    ["value" => "credit-card", "label" => "Credit Card"],
    ["value" => "net-banking", "label" => "Net Banking"],
    ["value" => "upi", "label" => "UPI"],
    ["value" => "wallet", "label" => "Wallet"],
];

$payments = [
    [
        "id" => "TXN-2025-001",
        "date" => "2025-04-15",
        "amount" => 12500,
        "method" => "Credit Card",
        "status" => "Completed",
        "description" => "Payment for Order #ORD-2025-102",
        "customer" => "Unnati Traders"
    ],
    [
        "id" => "TXN-2025-002",
        "date" => "2025-04-10",
        "amount" => 8750,
        "method" => "UPI",
        "status" => "Pending",
        "description" => "Payment for Invoice #INV-2025-005",
        "customer" => "Modern Electricals"
    ],
    [
        "id" => "TXN-2025-003",
        "date" => "2025-04-05",
        "amount" => 15200,
        "method" => "Net Banking",
        "status" => "Completed",
        "description" => "Payment for Delivery #DEL-2025-008",
        "customer" => "City Lights"
    ],
    [
        "id" => "TXN-2025-004",
        "date" => "2025-03-28",
        "amount" => 5400,
        "method" => "Wallet",
        "status" => "Failed",
        "description" => "Payment for Order #ORD-2025-098",
        "customer" => "Sharma Electronics"
    ],
    [
        "id" => "TXN-2025-005",
        "date" => "2025-03-20",
        "amount" => 9800,
        "method" => "Credit Card",
        "status" => "Completed",
        "description" => "Payment for Invoice #INV-2025-003",
        "customer" => "Premium Switches"
    ],
    [
        "id" => "TXN-2025-006",
        "date" => "2025-03-15",
        "amount" => 6300,
        "method" => "UPI",
        "status" => "Completed",
        "description" => "Payment for Delivery #DEL-2025-005",
        "customer" => "Electro Mart"
    ]
];

$recent_bills = [
    ["id" => "BILL-001", "customer" => "Walk-in Customer", "items" => "4", "amount" => "₹3,200", "method" => "Cash", "date" => "12/04/2025"],
    ["id" => "BILL-002", "customer" => "Sushil Patel", "items" => "2", "amount" => "₹1,850", "method" => "UPI", "date" => "12/04/2025"],
    ["id" => "BILL-003", "customer" => "Amit Kumar", "items" => "5", "amount" => "₹7,500", "method" => "Card", "date" => "11/04/2025"],
    ["id" => "BILL-004", "customer" => "Ravi Sharma", "items" => "1", "amount" => "₹650", "method" => "Cash", "date" => "11/04/2025"],
];

$outstanding_payments = [
    ["id" => "INV-2025-002", "customer" => "Modern Electricals", "amount" => "₹18,750", "due_date" => "08/05/2025", "days_overdue" => "-"],
    ["id" => "INV-2025-004", "customer" => "Sharma Electronics", "amount" => "₹15,800", "due_date" => "28/04/2025", "days_overdue" => "-"],
    ["id" => "INV-2025-006", "customer" => "Electro Mart", "amount" => "₹8,700", "due_date" => "15/04/2025", "days_overdue" => "10 days"],
];

$orders = [
    [
        "id" => "ORD-2854",
        "customer" => "Unnati Traders",
        "date" => "2025-04-12",
        "deliveryDate" => "2025-04-19",
        "amount" => 24500,
        "items" => 12,
        "status" => "New",
        "payment" => "Pending"
    ],
    [
        "id" => "ORD-2853",
        "customer" => "Modern Electricals",
        "date" => "2025-04-10",
        "deliveryDate" => "2025-04-17",
        "amount" => 18750,
        "items" => 8,
        "status" => "Processing",
        "payment" => "Paid"
    ],
    [
        "id" => "ORD-2852",
        "customer" => "City Lights",
        "date" => "2025-04-08",
        "deliveryDate" => "2025-04-15",
        "amount" => 32250,
        "items" => 15,
        "status" => "Shipped",
        "payment" => "Paid"
    ],
    [
        "id" => "ORD-2851",
        "customer" => "Sharma Electronics",
        "date" => "2025-04-05",
        "deliveryDate" => "2025-04-12",
        "amount" => 15800,
        "items" => 6,
        "status" => "Delivered",
        "payment" => "Paid"
    ],
    [
        "id" => "ORD-2850",
        "customer" => "Premium Switches",
        "date" => "2025-04-03",
        "deliveryDate" => "2025-04-10",
        "amount" => 22450,
        "items" => 10,
        "status" => "Delivered",
        "payment" => "Paid"
    ],
    [
        "id" => "ORD-2849",
        "customer" => "Electro Mart",
        "date" => "2025-04-02",
        "deliveryDate" => "2025-04-09",
        "amount" => 8700,
        "items" => 4,
        "status" => "Cancelled",
        "payment" => "Refunded"
    ],
    [
        "id" => "ORD-2848",
        "customer" => "Power Solutions",
        "date" => "2025-04-01",
        "deliveryDate" => "2025-04-08",
        "amount" => 42800,
        "items" => 18,
        "status" => "Delivered",
        "payment" => "Partial"
    ],
    [
        "id" => "ORD-2847",
        "customer" => "Wire Distributors",
        "date" => "2025-03-30",
        "deliveryDate" => "2025-04-06",
        "amount" => 36500,
        "items" => 14,
        "status" => "Delivered",
        "payment" => "Paid"
    ]
];

$factories = [
    [
        "id" => "FAC-001",
        "name" => "Unnati Traders",
        "contact" => "+91 98765 12345",
        "address" => "Plot 45, Industrial Area, Gurgaon, Haryana - 122001"
    ],
    [
        "id" => "FAC-002",
        "name" => "Modern Electricals",
        "contact" => "+91 87654 23456",
        "address" => "Unit 12, Sector 18, Delhi - 110020"
    ],
    [
        "id" => "FAC-003",
        "name" => "City Lights",
        "contact" => "+91 76543 34567",
        "address" => "Block B, Noida Industrial Zone, Noida, UP - 201301"
    ]
];

$deliveries = [
    [
        "id" => "DEL-2025-001",
        "trackingId" => "TR124578965",
        "orderId" => "ORD-2854",
        "factoryName" => "Unnati Traders",
        "date" => "2025-04-12",
        "estimatedDelivery" => "2025-04-17",
        "items" => 12,
        "status" => "In Transit",
        "lastUpdate" => "Package has left the Delhi warehouse"
    ],
    [
        "id" => "DEL-2025-002",
        "trackingId" => "TR124578966",
        "orderId" => "ORD-2853",
        "factoryName" => "Modern Electricals",
        "date" => "2025-04-10",
        "estimatedDelivery" => "2025-04-15",
        "items" => 8,
        "status" => "Out for Delivery",
        "lastUpdate" => "Package is out for delivery in your area"
    ],
    [
        "id" => "DEL-2025-003",
        "trackingId" => "TR124578967",
        "orderId" => "ORD-2852",
        "factoryName" => "City Lights",
        "date" => "2025-04-08",
        "estimatedDelivery" => "2025-04-12",
        "items" => 15,
        "status" => "Delivered",
        "lastUpdate" => "Package delivered"
    ],
    [
        "id" => "DEL-2025-004",
        "trackingId" => "TR124578968",
        "orderId" => "ORD-2851",
        "factoryName" => "Modern Electricals",
        "date" => "2025-04-05",
        "estimatedDelivery" => "2025-04-10",
        "items" => 6,
        "status" => "Delivered",
        "lastUpdate" => "Package delivered"
    ],
    [
        "id" => "DEL-2025-005",
        "trackingId" => "TR124578969",
        "orderId" => "ORD-2850",
        "factoryName" => "Unnati Traders",
        "date" => "2025-04-03",
        "estimatedDelivery" => "2025-04-08",
        "items" => 10,
        "status" => "Processing",
        "lastUpdate" => "Order is being prepared for shipment"
    ]
];

$products = [
    ["id" => 1, "name" => "Copper Wire 1.5mm", "sku" => "CW-1.5-100", "category" => "Wires", "price" => 1250, "stock" => 85, "status" => "In Stock"],
    ["id" => 2, "name" => "Havells MCB 32A", "sku" => "HMCB-32A", "category" => "Switches", "price" => 450, "stock" => 42, "status" => "In Stock"],
    ["id" => 3, "name" => "LED Bulb 9W", "sku" => "LED-9W-CW", "category" => "Lights", "price" => 120, "stock" => 150, "status" => "In Stock"],
    ["id" => 4, "name" => "Electric Cable 2.5mm", "sku" => "EC-2.5-100", "category" => "Wires", "price" => 1800, "stock" => 28, "status" => "Low Stock"],
    ["id" => 5, "name" => "Distribution Box 8 Way", "sku" => "DB-8W", "category" => "Accessories", "price" => 950, "stock" => 12, "status" => "Low Stock"],
    ["id" => 6, "name" => "PVC Conduit Pipe 25mm", "sku" => "PVC-25-10", "category" => "Conduits", "price" => 35, "stock" => 200, "status" => "In Stock"],
    ["id" => 7, "name" => "Electric Iron 1000W", "sku" => "EI-1000W", "category" => "Appliances", "price" => 1200, "stock" => 0, "status" => "Out of Stock"],
    ["id" => 8, "name" => "Ceiling Fan 56\"", "sku" => "CF-56-WH", "category" => "Fans", "price" => 1500, "stock" => 25, "status" => "In Stock"]
];

$transactions = [
    ["id" => "TRX-001", "vendor" => "Modern Electricals", "amount" => "₹36,500", "date" => "10 Apr 2025", "status" => "Paid"],
    ["id" => "TRX-002", "vendor" => "City Lights", "amount" => "₹43,250", "date" => "08 Apr 2025", "status" => "Overdue"],
];

$invoices_page = [
    ["id" => "INV-3845", "customer" => "Unnati Traders", "amount" => "₹36,500", "date" => "12 Apr 2025", "status" => "Paid"],
    ["id" => "INV-3844", "customer" => "City Lights", "amount" => "₹24,500", "date" => "10 Apr 2025", "status" => "Pending"],
];

$notifications = [
    [
        "id" => "NOT-001",
        "type" => "order",
        "title" => "New Order Received",
        "message" => "Order #ORD-2854 received from Modern Electricals",
        "time" => "10 minutes ago",
        "read" => false,
        "icon" => "fa-shopping-cart",
        "color" => "primary"
    ],
    [
        "id" => "NOT-002",
        "type" => "payment",
        "title" => "Payment Received",
        "message" => "Payment of ₹24,500 received for Invoice #INV-3845",
        "time" => "2 hours ago",
        "read" => false,
        "icon" => "fa-wallet",
        "color" => "success"
    ],
    [
        "id" => "NOT-003",
        "type" => "alert",
        "title" => "Low Stock Alert",
        "message" => "Electric Cable 2.5mm is running low on stock",
        "time" => "5 hours ago",
        "read" => true,
        "icon" => "fa-exclamation-triangle",
        "color" => "warning"
    ],
    [
        "id" => "NOT-004",
        "type" => "delivery",
        "title" => "Delivery Update",
        "message" => "Delivery #DEL-484 has been completed",
        "time" => "1 day ago",
        "read" => true,
        "icon" => "fa-truck",
        "color" => "info"
    ]
];

$settings = [
    'profile' => [
        'firstName' => 'Rajesh',
        'lastName' => 'Sharma',
        'email' => 'rajesh.sharma@example.com',
        'phone' => '+91 98765 43210',
        'position' => 'Owner & Managing Director',
        'avatar' => 'RS'
    ],
    'business' => [
        'companyName' => 'Sharma Electricals',
        'businessType' => 'Electrical & Hardware Retailer',
        'address' => '123, Main Market, Sector 15, Gurgaon, Haryana - 122001, India',
        'city' => 'Gurgaon',
        'state' => 'Haryana',
        'pincode' => '122001',
        'gstin' => '06AABCS1429B1Z1',
        'panNumber' => 'AABCS1429B',
        'website' => 'https://www.sharmaelectricals.com',
        'taxExemption' => 'No file uploaded',
        'businessDescription' => 'Sharma Electricals is a leading distributor of electrical products with over 15 years of experience in the industry. We specialize in high-quality electrical wiring, switches, lighting solutions and electrical hardware for residential and commercial applications.'
    ],
    'payment' => [
        'accountName' => 'Rajesh Sharma',
        'bankName' => 'State Bank of India',
        'accountNumber' => '1234567890',
        'ifscCode' => 'SBIN0001234',
        'accountType' => 'Current Account',
        'branch' => 'Sector 14, Gurgaon',
        'upiId' => 'rajesh.sharma@okaxis',
        'qrCode' => 'No file uploaded',
        'bnpl' => false,
        'autoInvoice' => true,
        'paymentReminders' => true
    ],
    'shipping' => [
        'sameAsBusiness' => true,
        'shippingAddress' => '123, Main Market, Sector 15, Gurgaon, Haryana - 122001, India',
        'shippingCity' => 'Gurgaon',
        'shippingState' => 'Haryana',
        'shippingPincode' => '122001',
        'freeShipping' => true,
        'freeShippingThreshold' => 5000,
        'sameDayProcessing' => true,
        'processingCutoffTime' => '14:00',
        'shippingPartners' => ['delhivery', 'blueDart', 'ownDelivery']
    ],
    'notifications' => [
        'newOrder' => ['email' => true, 'sms' => true, 'app' => true],
        'orderUpdates' => ['email' => true, 'sms' => false, 'app' => true],
        'orderCancellations' => ['email' => true, 'sms' => true, 'app' => true],
        'paymentReceived' => ['email' => true, 'sms' => true, 'app' => true],
        'paymentDue' => ['email' => true, 'sms' => true, 'app' => true],
        'lowStock' => ['email' => true, 'sms' => false, 'app' => true]
    ],
    'documents' => [
        ['name' => 'GST Registration', 'uploaded' => '2025-04-15', 'status' => 'Uploaded'],
        ['name' => 'PAN Card', 'uploaded' => '2025-04-15', 'status' => 'Uploaded'],
        ['name' => 'Shop & Establishment Certificate', 'uploaded' => '2025-04-15', 'status' => 'Uploaded'],
        ['name' => 'MSME Certificate', 'uploaded' => null, 'status' => 'Not uploaded'],
        ['name' => 'ISO 9001:2015', 'uploaded' => '2025-03-10', 'status' => 'Uploaded'],
        ['name' => 'BIS Certification', 'uploaded' => '2025-02-23', 'status' => 'Uploaded']
    ],
    'security' => [
        'twoFactor' => true,
        'loginHistory' => [
            ['location' => 'Delhi, India', 'device' => 'Chrome on Windows', 'time' => '2025-04-23 10:45', 'status' => 'Active'],
            ['location' => 'Delhi, India', 'device' => 'Chrome on Windows', 'time' => '2025-04-22 16:30', 'status' => 'Inactive']
        ]
    ]
];

$reports_data = [
    'sales' => [
        'monthly' => [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [150000, 165000, 180000, 145000, 160000, 175000],
            'total' => 975000,
            'growth' => 6.5
        ],
        'by_product' => [
            'labels' => ['Copper Wire 1.5mm', 'Havells MCB 32A', 'LED Bulb 9W', 'Electric Cable 2.5mm'],
            'data' => [280000, 225000, 185000, 285000],
            'colors' => ['#0d6efd', '#198754', '#ffc107', '#dc3545']
        ],
        'recent_reports' => [
            [
                'name' => 'Monthly Sales Summary',
                'type' => 'Sales',
                'date' => '10 Apr, 2025',
                'data' => [
                    'total_sales' => 975000,
                    'orders' => 145,
                    'average_order' => 6724
                ]
            ],
            [
                'name' => 'Product Performance',
                'type' => 'Sales',
                'date' => '05 Apr, 2025',
                'data' => [
                    'top_product' => 'Copper Wire 1.5mm',
                    'revenue' => 280000,
                    'units_sold' => 224
                ]
            ]
        ]
    ],
    'payments' => [
        'summary' => [
            'labels' => ['Received', 'Pending', 'Overdue'],
            'data' => [750000, 175000, 50000],
            'colors' => ['#198754', '#ffc107', '#dc3545']
        ],
        'recent_transactions' => [
            [
                'date' => '12 Apr, 2025',
                'type' => 'Payment Received',
                'amount' => 24500,
                'customer' => 'Modern Electricals'
            ],
            [
                'date' => '10 Apr, 2025',
                'type' => 'Payment Pending',
                'amount' => 36750,
                'customer' => 'City Lights'
            ]
        ]
    ],
    'inventory' => [
        'stock_value' => [
            'total' => 520000,
            'growth' => 4
        ],
        'low_stock' => [
            'count' => 8,
            'items' => [
                'Electric Cable 2.5mm',
                'Distribution Box 8 Way'
            ]
        ],
        'stock_movement' => [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'inbound' => [45000, 52000, 48000, 51000, 54000, 49000],
            'outbound' => [42000, 48000, 45000, 47000, 50000, 46000]
        ]
    ]
];

// Functions to retrieve and save mock data
function get_invoices() {
    global $invoices;
    // Future: Replace with database query, e.g., SELECT * FROM invoices
    return $invoices;
}

function save_invoice($data) {
    global $invoices, $factories;

    // Validate required fields
    $required_fields = ['customer', 'amount', 'type', 'date', 'dueDate'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return ['success' => false, 'message' => "Missing required field: $field"];
        }
    }

    // Validate customer
    $factory_names = array_column($factories, 'name');
    if (!in_array($data['customer'], $factory_names)) {
        return ['success' => false, 'message' => "Invalid customer: {$data['customer']}"];
    }

    // Validate amount
    if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
        return ['success' => false, 'message' => 'Amount must be a positive number'];
    }

    // Validate type
    $valid_types = ['GST', 'Non-GST'];
    if (!in_array($data['type'], $valid_types)) {
        return ['success' => false, 'message' => 'Invalid invoice type'];
    }

    // Validate GST number for GST type
    if ($data['type'] === 'GST') {
        if (empty($data['gstNumber'])) {
            return ['success' => false, 'message' => 'GST number is required for GST invoices'];
        }
        // Basic GST number format check (e.g., 15 characters)
        if (!preg_match('/^[0-9A-Z]{15}$/', $data['gstNumber'])) {
            return ['success' => false, 'message' => 'Invalid GST number format'];
        }
    } else {
        $data['gstNumber'] = '-';
    }

    // Validate dates
    $date = DateTime::createFromFormat('Y-m-d', $data['date']);
    $dueDate = DateTime::createFromFormat('Y-m-d', $data['dueDate']);
    if (!$date || !$dueDate) {
        return ['success' => false, 'message' => 'Invalid date format'];
    }
    if ($dueDate < $date) {
        return ['success' => false, 'message' => 'Due date cannot be before invoice date'];
    }

    // Generate new invoice ID
    $existing_ids = array_column($invoices, 'id');
    $last_id = max(array_map(function ($id) {
        return (int) substr($id, 9);
    }, $existing_ids));
    $new_id = 'INV-2025-' . str_pad($last_id + 1, 3, '0', STR_PAD_LEFT);

    // Create new invoice
    $new_invoice = [
        'id' => $new_id,
        'date' => $data['date'],
        'dueDate' => $data['dueDate'],
        'amount' => (float) $data['amount'],
        'status' => 'Pending', // Default status
        'type' => $data['type'],
        'gstNumber' => $data['gstNumber'],
        'customer' => $data['customer']
    ];

    // Append to invoices array
    $invoices[] = $new_invoice;

    // Future: Replace with database insert, e.g., INSERT INTO invoices (...)
    return ['success' => true, 'message' => "Invoice $new_id created successfully"];
}

function get_payment_methods() {
    global $payment_methods;
    // Future: Replace with database query, e.g., SELECT * FROM payment_methods
    return $payment_methods;
}

function get_payments() {
    global $payments;
    // Future: Replace with database query, e.g., SELECT * FROM payments
    return $payments;
}

function get_recent_bills() {
    global $recent_bills;
    // Future: Replace with database query, e.g., SELECT * FROM recent_bills
    return $recent_bills;
}

function get_outstanding_payments() {
    global $outstanding_payments;
    // Future: Replace with database query, e.g., SELECT * FROM outstanding_payments
    return $outstanding_payments;
}

function get_orders() {
    global $orders;
    // Future: Replace with database query, e.g., SELECT * FROM orders
    return $orders;
}

function get_factories() {
    global $factories;
    // Future: Replace with database query, e.g., SELECT * FROM factories
    return $factories;
}

function save_order($data) {
    global $orders, $factories;

    // Validate required fields
    $required_fields = ['customer', 'date', 'deliveryDate', 'amount', 'items', 'status', 'payment'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return ['success' => false, 'message' => "Missing required field: $field"];
        }
    }

    // Validate factory/customer
    $factory_names = array_column($factories, 'name');
    if (!in_array($data['customer'], $factory_names)) {
        return ['success' => false, 'message' => "Invalid factory: {$data['customer']}"];
    }

    // Validate dates
    if (!DateTime::createFromFormat('Y-m-d', $data['date']) || !DateTime::createFromFormat('Y-m-d', $data['deliveryDate'])) {
        return ['success' => false, 'message' => 'Invalid date format'];
    }

    // Validate amount and items
    if (!is_numeric($data['amount']) || $data['amount'] < 0 || !is_numeric($data['items']) || $data['items'] <= 0) {
        return ['success' => false, 'message' => 'Invalid amount or items'];
    }

    // Validate status and payment
    $valid_statuses = ['New', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    $valid_payments = ['Pending', 'Paid', 'Partial', 'Refunded'];
    if (!in_array($data['status'], $valid_statuses) || !in_array($data['payment'], $valid_payments)) {
        return ['success' => false, 'message' => 'Invalid status or payment status'];
    }

    // Generate new order ID
    $existing_ids = array_column($orders, 'id');
    $last_id = max(array_map(function ($id) {
        return (int) substr($id, 4);
    }, $existing_ids));
    $new_id = 'ORD-' . str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);

    // Create new order
    $new_order = [
        'id' => $new_id,
        'customer' => $data['customer'],
        'date' => $data['date'],
        'deliveryDate' => $data['deliveryDate'],
        'amount' => (float) $data['amount'],
        'items' => (int) $data['items'],
        'status' => $data['status'],
        'payment' => $data['payment']
    ];

    // Append to orders array
    $orders[] = $new_order;

    // Future: Replace with database insert, e.g., INSERT INTO orders (...)
    return ['success' => true, 'message' => "Order $new_id created successfully"];
}

function get_deliveries() {
    global $deliveries;
    // Future: Replace with database query, e.g., SELECT * FROM deliveries
    return $deliveries;
}

function confirm_delivery($delivery_id) {
    global $deliveries;

    // Find the delivery
    foreach ($deliveries as &$delivery) {
        if ($delivery['id'] === $delivery_id) {
            if ($delivery['status'] === 'Delivered') {
                return ['success' => false, 'message' => "Delivery $delivery_id is already marked as Delivered"];
            }
            if ($delivery['status'] !== 'In Transit' && $delivery['status'] !== 'Out for Delivery') {
                return ['success' => false, 'message' => "Delivery $delivery_id cannot be confirmed (status: {$delivery['status']})"];
            }
            $delivery['status'] = 'Delivered';
            $delivery['lastUpdate'] = 'Package delivered';
            // Future: Update database, e.g., UPDATE deliveries SET status = 'Delivered', lastUpdate = 'Package delivered' WHERE id = ?
            return ['success' => true, 'message' => "Delivery $delivery_id marked as received"];
        }
    }
    return ['success' => false, 'message' => "Delivery $delivery_id not found"];
}

function get_products() {
    global $products;
    // Future: Replace with database query, e.g., SELECT * FROM products
    return $products;
}

function get_transactions() {
    global $transactions;
    // Future: Replace with database query, e.g., SELECT * FROM transactions
    return $transactions;
}

function get_invoices_page() {
    global $invoices_page;
    // Future: Replace with database query, e.g., SELECT * FROM invoices WHERE page_specific = true
    return $invoices_page;
}

function get_notifications() {
    global $notifications;
    return $notifications;
}

function get_settings() {
    global $settings;
    return $settings;
}

function get_reports_data($type = null, $period = 'last6months') {
    global $reports_data;
    
    if ($type) {
        return isset($reports_data[$type]) ? $reports_data[$type] : null;
    }
    
    return $reports_data;
}
?>