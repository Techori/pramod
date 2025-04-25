<?php

// Simulated database file for mock data
// In the future, replace these arrays and functions with actual database queries (e.g., MySQL)

// Mock data arrays
$invoices = [
    ["id" => "INV-0001", "customer" => "Rajesh Electronics", "amount" => "₹24,500", "status" => "Paid", "date" => "11/04/2025"],
    ["id" => "INV-0002", "customer" => "Sharma Electrical", "amount" => "₹36,750", "status" => "Pending", "date" => "10/04/2025"],
    ["id" => "INV-0003", "customer" => "Gupta Traders", "amount" => "₹18,300", "status" => "Overdue", "date" => "05/04/2025"],
    ["id" => "INV-0004", "customer" => "Patel Wire Co.", "amount" => "₹42,800", "status" => "Paid", "date" => "01/04/2025"],
    ["id" => "INV-0005", "customer" => "Singh Distributors", "amount" => "₹15,600", "status" => "Pending", "date" => "30/03/2025"],
];

$payments = [
    ["id" => "PAY-0001", "invoice" => "INV-0001", "customer" => "Rajesh Electronics", "amount" => "₹24,500", "method" => "Bank Transfer", "date" => "11/04/2025"],
    ["id" => "PAY-0002", "invoice" => "INV-0004", "customer" => "Patel Wire Co.", "amount" => "₹42,800", "method" => "Cheque", "date" => "01/04/2025"],
    ["id" => "PAY-0003", "invoice" => "INV-0006", "customer" => "Kumar Electric", "amount" => "₹33,200", "method" => "UPI", "date" => "28/03/2025"],
];

$recent_bills = [
    ["id" => "BILL-001", "customer" => "Walk-in Customer", "items" => "4", "amount" => "₹3,200", "method" => "Cash", "date" => "12/04/2025"],
    ["id" => "BILL-002", "customer" => "Sushil Patel", "items" => "2", "amount" => "₹1,850", "method" => "UPI", "date" => "12/04/2025"],
    ["id" => "BILL-003", "customer" => "Amit Kumar", "items" => "5", "amount" => "₹7,500", "method" => "Card", "date" => "11/04/2025"],
    ["id" => "BILL-004", "customer" => "Ravi Sharma", "items" => "1", "amount" => "₹650", "method" => "Cash", "date" => "11/04/2025"],
];

$outstanding_payments = [
    ["id" => "INV-0002", "customer" => "Sharma Electrical", "amount" => "₹36,750", "due_date" => "25/04/2025", "days_overdue" => "-"],
    ["id" => "INV-0003", "customer" => "Gupta Traders", "amount" => "₹18,300", "due_date" => "20/03/2025", "days_overdue" => "15 days"],
    ["id" => "INV-0005", "customer" => "Singh Distributors", "amount" => "₹15,600", "due_date" => "15/04/2025", "days_overdue" => "-"],
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

$deliveries = [
    ["id" => "DEL-485", "order_id" => "ORD-2846", "customer" => "Modern Electricals", "date" => "14 Apr 2025", "status" => "Scheduled"],
    ["id" => "DEL-484", "order_id" => "ORD-2840", "customer" => "City Lights", "date" => "13 Apr 2025", "status" => "In Transit"],
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

// Functions to retrieve mock data
function get_invoices() {
    global $invoices;
    // Future: Replace with database query, e.g., SELECT * FROM invoices
    return $invoices;
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

function get_deliveries() {
    global $deliveries;
    // Future: Replace with database query, e.g., SELECT * FROM deliveries
    return $deliveries;
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
?>