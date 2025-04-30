<?php

// Database file for retail store mock data
// In the future, replace these arrays and functions with actual database queries

// Mock data arrays
$billing_invoices = [
    [
        "id" => "INV-2025-845",
        "customer" => "Raj Kumar",
        "date" => "2025-04-27",
        "amount" => 4250,
        "status" => "Paid",
        "type" => "Retail",
        "payment" => "UPI"
    ],
    [
        "id" => "INV-2025-101",
        "customer" => "Hari Electricals",
        "date" => "2025-04-08",
        "amount" => 24500,
        "status" => "Paid",
        "type" => "Retail",
        "payment" => "UPI"
    ],
    [
        "id" => "INV-2025-102",
        "customer" => "Krishna Electronics",
        "date" => "2025-04-07",
        "amount" => 18750,
        "status" => "Pending",
        "type" => "Wholesale",
        "payment" => "Cash"
    ],
    [
        "id" => "INV-2025-103",
        "customer" => "Ram Electrical Stores",
        "date" => "2025-04-06",
        "amount" => 35200,
        "status" => "Paid",
        "type" => "Retail",
        "payment" => "Card"
    ],
    [
        "id" => "INV-2025-104",
        "customer" => "Shiva Hardware",
        "date" => "2025-04-05",
        "amount" => 12400,
        "status" => "Overdue",
        "type" => "Wholesale",
        "payment" => "Gateway"
    ]
];

$quotations = [
    [
        "id" => "QUO-2025-001",
        "customer" => "Hari Electricals",
        "date" => "2025-04-08",
        "amount" => 30000,
        "status" => "Pending"
    ],
    [
        "id" => "QUO-2025-002",
        "customer" => "Krishna Electronics",
        "date" => "2025-04-07",
        "amount" => 20000,
        "status" => "Accepted"
    ]
];

$credit_notes = [
    [
        "id" => "CRN-2025-001",
        "invoice_id" => "INV-2025-102",
        "customer" => "Krishna Electronics",
        "date" => "2025-04-07",
        "amount" => 5000,
        "reason" => "Defective product"
    ],
    [
        "id" => "CRN-2025-002",
        "invoice_id" => "INV-2025-104",
        "customer" => "Shiva Hardware",
        "date" => "2025-04-06",
        "amount" => 3000,
        "reason" => "Wrong item shipped"
    ]
];

$sales_returns = [
    [
        "id" => "RET-2025-001",
        "invoice_id" => "INV-2025-103",
        "customer" => "Ram Electrical Stores",
        "date" => "2025-04-06",
        "amount" => 4000,
        "reason" => "Customer changed mind"
    ],
    [
        "id" => "RET-2025-002",
        "invoice_id" => "INV-2025-104",
        "customer" => "Shiva Hardware",
        "date" => "2025-04-05",
        "amount" => 2000,
        "reason" => "Defective item"
    ]
];

$transactions = [
    [
        "id" => "PAY-2854",
        "customer" => "Raj Kumar",
        "date" => "2025-04-12",
        "amount" => 24500,
        "method" => "UPI",
        "status" => "Completed",
        "orderId" => "ORD-3421"
    ],
    [
        "id" => "PAY-2853",
        "customer" => "Priya Singh",
        "date" => "2025-04-10",
        "amount" => 18750,
        "method" => "Cash",
        "status" => "Completed",
        "orderId" => "ORD-3420"
    ],
    [
        "id" => "PAY-2852",
        "customer" => "Vikram Patel",
        "date" => "2025-04-08",
        "amount" => 32250,
        "method" => "Card",
        "status" => "Completed",
        "orderId" => "ORD-3418"
    ],
    [
        "id" => "PAY-2851",
        "customer" => "Anita Sharma",
        "date" => "2025-04-05",
        "amount" => 15800,
        "method" => "UPI",
        "status" => "Pending",
        "orderId" => "ORD-3415"
    ],
    [
        "id" => "PAY-2850",
        "customer" => "Sanjay Mehta",
        "date" => "2025-04-03",
        "amount" => 22450,
        "method" => "Cash",
        "status" => "Completed",
        "orderId" => "ORD-3410"
    ],
    [
        "id" => "PAY-2849",
        "customer" => "Divya Gupta",
        "date" => "2025-04-01",
        "amount" => 28900,
        "method" => "Card",
        "status" => "Failed",
        "orderId" => "ORD-3408"
    ]
];

$customers = [
    [
        "id" => "CUST-001",
        "name" => "Raj Kumar",
        "type" => "Retail",
        "phone" => "+91 9876543210",
        "email" => "raj.kumar@example.com",
        "totalSpent" => 23450,
        "lastPurchase" => "2025-04-13",
        "status" => "Active",
        "contact" => "+91 9876543210",
        "address" => "123, Market Street, Delhi - 110001"
    ],
    [
        "id" => "CUST-002",
        "name" => "Sanjay Mehta",
        "type" => "Wholesale",
        "phone" => "+91 9012345678",
        "email" => "sanjay.m@example.com",
        "totalSpent" => 105780,
        "lastPurchase" => "2025-04-12",
        "status" => "Active",
        "contact" => "+91 9012345678",
        "address" => "456, Industrial Area, Gurgaon, Haryana - 122001"
    ],
    [
        "id" => "CUST-003",
        "name" => "Neha Singh",
        "type" => "Retail",
        "phone" => "+91 8765432109",
        "email" => "neha.s@example.com",
        "totalSpent" => 12800,
        "lastPurchase" => "2025-04-10",
        "status" => "Active",
        "contact" => "+91 8765432109",
        "address" => "789, Main Road, Noida, UP - 201301"
    ],
    [
        "id" => "CUST-004",
        "name" => "Alok Sharma",
        "type" => "Contractor",
        "phone" => "+91 7890123456",
        "email" => "alok.sharma@example.com",
        "totalSpent" => 182650,
        "lastPurchase" => "2025-04-08",
        "status" => "Active",
        "contact" => "+91 7890123456",
        "address" => "101, Sector 15, Faridabad, Haryana - 121007"
    ],
    [
        "id" => "CUST-005",
        "name" => "Priya Singh",
        "type" => "Retail",
        "phone" => "+91 9876543211",
        "email" => "priya.p@example.com",
        "totalSpent" => 7500,
        "lastPurchase" => "2025-04-05",
        "status" => "Inactive",
        "contact" => "+91 9876543211",
        "address" => "202, MG Road, Delhi - 110030"
    ],
    [
        "id" => "CUST-006",
        "name" => "Vikram Patel",
        "type" => "Wholesale",
        "phone" => "+91 9876543212",
        "email" => "vikram.s@example.com",
        "totalSpent" => 92750,
        "lastPurchase" => "2025-04-01",
        "status" => "Active",
        "contact" => "+91 9876543212",
        "address" => "303, Sector 7, Delhi - 110085"
    ],
    [
        "id" => "CUST-007",
        "name" => "Ritu Desai",
        "type" => "Retail",
        "phone" => "+91 9876543213",
        "email" => "ritu.d@example.com",
        "totalSpent" => 5280,
        "lastPurchase" => "2025-03-28",
        "status" => "Inactive",
        "contact" => "+91 9876543213",
        "address" => "404, Sector 8, Gurgaon, Haryana - 122018"
    ],
    [
        "id" => "CUST-008",
        "name" => "Hari Electricals",
        "type" => "Retail",
        "phone" => "+91 9876543214",
        "email" => "hari.electricals@example.com",
        "totalSpent" => 24500,
        "lastPurchase" => "2025-04-08",
        "status" => "Active",
        "contact" => "+91 9876543214",
        "address" => "123, Market Street, Delhi - 110001"
    ],
    [
        "id" => "CUST-009",
        "name" => "Krishna Electronics",
        "type" => "Wholesale",
        "phone" => "+91 8765432108",
        "email" => "krishna.e@example.com",
        "totalSpent" => 18750,
        "lastPurchase" => "2025-04-07",
        "status" => "Active",
        "contact" => "+91 8765432108",
        "address" => "456, Industrial Area, Gurgaon, Haryana - 122001"
    ],
    [
        "id" => "CUST-010",
        "name" => "Ram Electrical Stores",
        "type" => "Retail",
        "phone" => "+91 7654321098",
        "email" => "ram.electricals@example.com",
        "totalSpent" => 35200,
        "lastPurchase" => "2025-04-06",
        "status" => "Active",
        "contact" => "+91 7654321098",
        "address" => "789, Main Road, Noida, UP - 201301"
    ],
    [
        "id" => "CUST-011",
        "name" => "Shiva Hardware",
        "type" => "Wholesale",
        "phone" => "+91 6543210987",
        "email" => "shiva.h@example.com",
        "totalSpent" => 12400,
        "lastPurchase" => "2025-04-05",
        "status" => "Active",
        "contact" => "+91 6543210987",
        "address" => "101, Sector 15, Faridabad, Haryana - 121007"
    ],
    [
        "id" => "CUST-012",
        "name" => "Anita Sharma",
        "type" => "Retail",
        "phone" => "+91 9876543215",
        "email" => "anita.s@example.com",
        "totalSpent" => 15800,
        "lastPurchase" => "2025-04-05",
        "status" => "Active",
        "contact" => "+91 9876543215",
        "address" => "505, Sector 9, Noida, UP - 201301"
    ],
    [
        "id" => "CUST-013",
        "name" => "Divya Gupta",
        "type" => "Retail",
        "phone" => "+91 9876543216",
        "email" => "divya.g@example.com",
        "totalSpent" => 28900,
        "lastPurchase" => "2025-04-01",
        "status" => "Active",
        "contact" => "+91 9876543216",
        "address" => "606, Sector 10, Delhi - 110075"
    ]
];

$payment_methods = [
    ["value" => "UPI", "label" => "UPI"],
    ["value" => "Cash", "label" => "Cash"],
    ["value" => "Card", "label" => "Card"],
    ["value" => "Credit", "label" => "Credit"]
];

$supply_requests = [
    [
        "id" => "REQ-2025-001",
        "item" => "House Wire 1.5mm (Red)",
        "quantity" => "200 rolls",
        "source" => "Factory Warehouse",
        "status" => "Delivered",
        "date" => "2025-04-08"
    ],
    [
        "id" => "REQ-2025-002",
        "item" => "House Wire 2.5mm (Black)",
        "quantity" => "150 rolls",
        "source" => "Factory Warehouse",
        "status" => "In Transit",
        "date" => "2025-04-10"
    ],
    [
        "id" => "REQ-2025-003",
        "item" => "Switches and Sockets",
        "quantity" => "300 units",
        "source" => "External Supplier",
        "status" => "Ordered",
        "date" => "2025-04-12"
    ],
    [
        "id" => "REQ-2025-004",
        "item" => "MCB and Distribution Boards",
        "quantity" => "50 units",
        "source" => "External Supplier",
        "status" => "Delivered",
        "date" => "2025-04-05"
    ],
    [
        "id" => "REQ-2025-005",
        "item" => "LED Bulbs 9W",
        "quantity" => "240 units",
        "source" => "External Supplier",
        "status" => "In Transit",
        "date" => "2025-04-11"
    ]
];

$low_stock_items = [
    [
        "item" => "House Wire 1.5mm (Yellow)",
        "stock" => "5 rolls",
        "level" => "Critical"
    ],
    [
        "item" => "Wall Switches (White)",
        "stock" => "12 units",
        "level" => "Low"
    ],
    [
        "item" => "MCB 16A",
        "stock" => "8 units",
        "level" => "Low"
    ]
];

$popular_products = [
    [
        "item" => "House Wire 2.5mm",
        "quantity" => "85 rolls",
        "percentage" => 85
    ],
    [
        "item" => "LED Bulbs 9W",
        "quantity" => "62 units",
        "percentage" => 62
    ],
    [
        "item" => "Wall Switches",
        "quantity" => "48 units",
        "percentage" => 48
    ]
];

$supply_analytics = [
    "pendingRequests" => 8,
    "inTransit" => 5,
    "receivedThisWeek" => 12,
    "lowStockItems" => 7
];

$inventory_items = [
    [
        "id" => "ITEM-001",
        "name" => "Havells Wire (1.5mm)",
        "category" => "Wires & Cables",
        "stock" => 12,
        "price" => "₹65/m",
        "lastUpdated" => "2025-04-13",
        "status" => "Low Stock"
    ],
    [
        "id" => "ITEM-002",
        "name" => "LED Bulb (9W)",
        "category" => "Lighting",
        "stock" => 85,
        "price" => "₹120/unit",
        "lastUpdated" => "2025-04-12",
        "status" => "In Stock"
    ],
    [
        "id" => "ITEM-003",
        "name" => "Ceiling Fan (48 inch)",
        "category" => "Fans",
        "stock" => 18,
        "price" => "₹1,450/unit",
        "lastUpdated" => "2025-04-11",
        "status" => "Low Stock"
    ],
    [
        "id" => "ITEM-004",
        "name" => "MCB Switch (32A)",
        "category" => "Switches",
        "stock" => 24,
        "price" => "₹320/unit",
        "lastUpdated" => "2025-04-10",
        "status" => "Low Stock"
    ],
    [
        "id" => "ITEM-005",
        "name" => "PVC Conduit Pipe (20mm)",
        "category" => "Pipes & Fittings",
        "stock" => 120,
        "price" => "₹45/piece",
        "lastUpdated" => "2025-04-09",
        "status" => "In Stock"
    ],
    [
        "id" => "ITEM-006",
        "name" => "Distribution Box (8 Way)",
        "category" => "Electrical Panels",
        "stock" => 30,
        "price" => "₹850/unit",
        "lastUpdated" => "2025-04-08",
        "status" => "In Stock"
    ],
    [
        "id" => "ITEM-007",
        "name" => "LED Panel Light (18W)",
        "category" => "Lighting",
        "stock" => 42,
        "price" => "₹550/unit",
        "lastUpdated" => "2025-04-07",
        "status" => "In Stock"
    ]
];

$inventory_categories = [
    "All Categories",
    "Wires & Cables",
    "Lighting",
    "Fans",
    "Switches",
    "Pipes & Fittings",
    "Electrical Panels"
];

$inventory_analytics = [
    [
        "name" => "Wires & Cables",
        "stock" => 25,
        "percent" => 25
    ],
    [
        "name" => "Lighting",
        "stock" => 85,
        "percent" => 85
    ],
    [
        "name" => "Fans",
        "stock" => 18,
        "percent" => 18
    ],
    [
        "name" => "Switches",
        "stock" => 24,
        "percent" => 24
    ],
    [
        "name" => "Pipes & Fittings",
        "stock" => 120,
        "percent" => 100
    ]
];

$inventory_stats = [
    "totalItems" => 254,
    "lowStockItems" => 18,
    "itemsInTransit" => 12,
    "inventoryValue" => "₹4.35L"
];

$inventory_activities = [
    [
        "type" => "Stock Received",
        "message" => "50 units of LED Bulb (9W) received from supplier",
        "timestamp" => "2025-04-13 10:23:00",
        "icon" => "fa-truck",
        "bgColor" => "bg-success-subtle",
        "iconColor" => "text-success"
    ],
    [
        "type" => "Low Stock Alert",
        "message" => "Havells Wire (1.5mm) has reached low stock threshold",
        "timestamp" => "2025-04-12 14:45:00",
        "icon" => "fa-exclamation-circle",
        "bgColor" => "bg-warning-subtle",
        "iconColor" => "text-warning"
    ],
    [
        "type" => "Price Updated",
        "message" => "Updated price of MCB Switch (32A) from ₹290 to ₹320",
        "timestamp" => "2025-04-11 11:15:00",
        "icon" => "fa-pen",
        "bgColor" => "bg-primary-subtle",
        "iconColor" => "text-primary"
    ]
];

$orders = [
    [
        "id" => "ORD-3421",
        "customer" => "Raj Kumar",
        "date" => "2025-04-12",
        "items" => 5,
        "amount" => 24500,
        "status" => "new",
        "paymentStatus" => "pending"
    ],
    [
        "id" => "ORD-3420",
        "customer" => "Priya Singh",
        "date" => "2025-04-10",
        "items" => 3,
        "amount" => 18750,
        "status" => "processing",
        "paymentStatus" => "paid"
    ],
    [
        "id" => "ORD-3418",
        "customer" => "Vikram Patel",
        "date" => "2025-04-08",
        "items" => 7,
        "amount" => 32250,
        "status" => "ready",
        "paymentStatus" => "paid"
    ],
    [
        "id" => "ORD-3415",
        "customer" => "Anita Sharma",
        "date" => "2025-04-05",
        "items" => 2,
        "amount" => 15800,
        "status" => "delivered",
        "paymentStatus" => "paid"
    ],
    [
        "id" => "ORD-3410",
        "customer" => "Sanjay Mehta",
        "date" => "2025-04-03",
        "items" => 4,
        "amount" => 22450,
        "status" => "delivered",
        "paymentStatus" => "paid"
    ],
    [
        "id" => "ORD-3408",
        "customer" => "Divya Gupta",
        "date" => "2025-04-01",
        "items" => 6,
        "amount" => 28900,
        "status" => "cancelled",
        "paymentStatus" => "refunded"
    ]
];

$order_analytics = [
    "newOrders" => 12,
    "processing" => 8,
    "readyForPickup" => 5,
    "deliveredToday" => 14
];

$customer_analytics = [
    "totalCustomers" => 432,
    "newThisMonth" => 28,
    "avgPurchase" => 4250,
    "repeatRate" => 65
];

$customer_segmentation = [
    [
        "type" => "Retail",
        "percentage" => 65
    ],
    [
        "type" => "Wholesale",
        "percentage" => 25
    ],
    [
        "type" => "Contractor",
        "percentage" => 10
    ]
];

$top_spending_categories = [
    [
        "category" => "Wires & Cables",
        "percentage" => 32
    ],
    [
        "category" => "Lighting",
        "percentage" => 24
    ],
    [
        "category" => "Switchgear",
        "percentage" => 18
    ]
];

$recent_activity = [
    [
        "timestamp" => "2025-04-27 10:45:00",
        "message" => "Raj Kumar made a purchase of ₹5,850",
        "borderColor" => "border-success"
    ],
    [
        "timestamp" => "2025-04-27 09:30:00",
        "message" => "Sanjay Mehta registered as new wholesale customer",
        "borderColor" => "border-primary"
    ],
    [
        "timestamp" => "2025-04-26 16:15:00",
        "message" => "Neha Singh updated contact information",
        "borderColor" => "border-warning"
    ]
];

$payment_analytics = [
    "todaysTransactions" => 24,
    "todaysRevenue" => 42500,
    "pendingPayments" => 3,
    "failedTransactions" => 1
];

$payment_method_data = [
    [
        "name" => "UPI",
        "value" => 45
    ],
    [
        "name" => "Cash",
        "value" => 35
    ],
    [
        "name" => "Card",
        "value" => 15
    ],
    [
        "name" => "Credit",
        "value" => 5
    ]
];

$daily_revenue_data = [
    [
        "name" => "Mon",
        "value" => 28500
    ],
    [
        "name" => "Tue",
        "value" => 22000
    ],
    [
        "name" => "Wed",
        "value" => 31000
    ],
    [
        "name" => "Thu",
        "value" => 26000
    ],
    [
        "name" => "Fri",
        "value" => 34000
    ],
    [
        "name" => "Sat",
        "value" => 42000
    ],
    [
        "name" => "Sun",
        "value" => 31500
    ]
];

$reports = [
    [
        "id" => "RPT-2025-001",
        "name" => "Monthly Sales Report",
        "date" => "Apr 2025",
        "type" => "Sales",
        "status" => "Generated"
    ],
    [
        "id" => "RPT-2025-002",
        "name" => "Inventory Status Report",
        "date" => "Apr 2025",
        "type" => "Inventory",
        "status" => "Generated"
    ],
    [
        "id" => "RPT-2025-003",
        "name" => "Customer Analysis Report",
        "date" => "Q1 2025",
        "type" => "Customer",
        "status" => "Generated"
    ],
    [
        "id" => "RPT-2025-004",
        "name" => "Payment Methods Report",
        "date" => "Apr 2025",
        "type" => "Payment",
        "status" => "Pending"
    ],
    [
        "id" => "RPT-2025-005",
        "name" => "Product Performance Report",
        "date" => "Q1 2025",
        "type" => "Product",
        "status" => "Generated"
    ]
];

$monthly_sales_data = [
    [
        "name" => "Jan",
        "value" => 152000
    ],
    [
        "name" => "Feb",
        "value" => 165000
    ],
    [
        "name" => "Mar",
        "value" => 178000
    ],
    [
        "name" => "Apr",
        "value" => 182000
    ],
    [
        "name" => "May",
        "value" => 190000
    ],
    [
        "name" => "Jun",
        "value" => 205000
    ]
];

$product_category_data = [
    [
        "name" => "Wires",
        "value" => 35
    ],
    [
        "name" => "Switches",
        "value" => 25
    ],
    [
        "name" => "Lighting",
        "value" => 20
    ],
    [
        "name" => "Fans",
        "value" => 15
    ],
    [
        "name" => "Others",
        "value" => 5
    ]
];

$customer_visit_data = [
    [
        "name" => "Mon",
        "value" => 25
    ],
    [
        "name" => "Tue",
        "value" => 18
    ],
    [
        "name" => "Wed",
        "value" => 32
    ],
    [
        "name" => "Thu",
        "value" => 28
    ],
    [
        "name" => "Fri",
        "value" => 35
    ],
    [
        "name" => "Sat",
        "value" => 45
    ],
    [
        "name" => "Sun",
        "value" => 30
    ]
];

$store_settings = [
    "general" => [
        "store_name" => "Unnati Electrical Store",
        "store_code" => "UNT-MUM-001",
        "store_phone" => "+91 9876543210",
        "store_email" => "mumbai.store@unnati.com",
        "store_address" => "123 Main Street, Andheri East, Mumbai, Maharashtra - 400069",
        "store_manager" => "Rahul Sharma",
        "store_active" => true,
        "accept_online_orders" => true
    ],
    "billing" => [
        "invoice_prefix" => "UNT-INV",
        "receipt_prefix" => "UNT-RCT",
        "currency" => "INR (₹)",
        "tax_rate" => 18,
        "accepted_payment_methods" => ["Cash", "Card", "UPI"],
        "digital_receipt" => true,
        "invoice_logo" => true,
        "terms_on_invoice" => true,
        "invoice_terms" => "1. Goods once sold will not be taken back or exchanged.\n2. Warranty as per manufacturer's terms and conditions.\n3. This is a computer-generated invoice and does not require a signature."
    ],
    "inventory" => [
        "low_stock_threshold" => 20,
        "reorder_point" => 10,
        "auto_reorder" => true,
        "track_serial_numbers" => true,
        "allow_negative_stock" => false,
        "barcode_scanning" => true,
        "inventory_method" => "fifo",
        "stock_count_frequency" => "monthly"
    ],
    "hardware" => [
        "receipt_printer" => true,
        "printer_model" => "epson",
        "barcode_scanner" => true,
        "scanner_model" => "honeywell",
        "customer_display" => false,
        "payment_terminal" => true,
        "terminal_provider" => "pine",
        "cash_drawer" => true
    ],
    "notifications" => [
        "email_notifications" => [
            "low_stock_email" => true,
            "new_order_email" => true,
            "end_of_day_email" => true,
            "customer_feedback_email" => false
        ],
        "sms_notifications" => [
            "order_sms" => true,
            "delivery_sms" => true,
            "promotional_sms" => false
        ],
        "system_notifications" => [
            "dashboard_notifications" => true,
            "browser_notifications" => true,
            "mobile_notifications" => true
        ],
        "notification_emails" => "store.manager@unnati.com, inventory@unnati.com"
    ],
    "users" => [
        [
            "name" => "Rahul Sharma",
            "email" => "rahul@unnati.com",
            "role" => "Store Manager",
            "status" => "Active",
            "last_login" => "Today, 9:41 AM"
        ],
        [
            "name" => "Priya Patel",
            "email" => "priya@unnati.com",
            "role" => "Sales Associate",
            "status" => "Active",
            "last_login" => "Yesterday, 5:30 PM"
        ],
        [
            "name" => "Amit Kumar",
            "email" => "amit@unnati.com",
            "role" => "Inventory Manager",
            "status" => "On Leave",
            "last_login" => "3 days ago"
        ]
    ],
    "roles" => [
        [
            "name" => "Store Manager",
            "description" => "Full access to all store functions",
            "permissions" => [
                "Sales & Billing",
                "Inventory Management",
                "Customer Management",
                "Reports & Analytics",
                "User Management",
                "Settings & Configuration"
            ]
        ],
        [
            "name" => "Sales Associate",
            "description" => "Limited access to sales functions",
            "permissions" => [
                "Sales & Billing",
                "Customer Management"
            ]
        ]
    ],
    "after_sales" => [
        "warranty" => [
            "default_warranty" => 12,
            "extended_warranty" => 24,
            "warranty_tracking" => true
        ],
        "returns" => [
            "return_period" => 7,
            "return_policy" => "both",
            "returns_conditions" => "Products must be in unused condition with original packaging and receipt. Electrical items must not be installed or used."
        ],
        "service_centers" => [
            "centers" => "Mumbai Central Service Center: 123 Main St, Mumbai - 400001\nDelhi Service Center: 456 Market Ave, Delhi - 110001",
            "doorstep_service" => true,
            "express_service" => true
        ],
        "customer_support" => [
            "support_phone" => "+91 1800-123-4567",
            "support_email" => "support@unnatielectric.com",
            "customer_portal" => true
        ]
    ]
];

// Functions to retrieve and save mock data
function get_billing_invoices() {
    global $billing_invoices;
    return $billing_invoices;
}

function save_billing_invoice($data) {
    global $billing_invoices, $customers, $payment_methods;

    // Validate required fields
    $required_fields = ['customer', 'amount', 'type', 'payment', 'date', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return ['success' => false, 'message' => "Missing required field: $field"];
        }
    }

    // Validate customer
    $customer_names = array_column($customers, 'name');
    if (!in_array($data['customer'], $customer_names)) {
        return ['success' => false, 'message' => "Invalid customer: {$data['customer']}"];
    }

    // Validate amount
    if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
        return ['success' => false, 'message' => 'Amount must be a positive number'];
    }

    // Validate type
    $valid_types = ['Retail', 'Wholesale'];
    if (!in_array($data['type'], $valid_types)) {
        return ['success' => false, 'message' => 'Invalid invoice type'];
    }

    // Validate payment method
    $payment_values = array_column($payment_methods, 'value');
    if (!in_array($data['payment'], $payment_values)) {
        return ['success' => false, 'message' => 'Invalid payment method'];
    }

    // Validate status
    $valid_statuses = ['Paid', 'Pending', 'Overdue'];
    if (!in_array($data['status'], $valid_statuses)) {
        return ['success' => false, 'message' => 'Invalid status'];
    }

    // Validate date
    $date = DateTime::createFromFormat('Y-m-d', $data['date']);
    if (!$date) {
        return ['success' => false, 'message' => 'Invalid date format'];
    }

    // Generate new invoice ID
    $existing_ids = array_column($billing_invoices, 'id');
    $last_id = max(array_map(function ($id) {
        return (int) substr($id, 9);
    }, $existing_ids));
    $new_id = 'INV-2025-' . str_pad($last_id + 1, 3, '0', STR_PAD_LEFT);

    // Create new invoice
    $new_invoice = [
        'id' => $new_id,
        'customer' => $data['customer'],
        'date' => $data['date'],
        'amount' => (float) $data['amount'],
        'status' => $data['status'],
        'type' => $data['type'],
        'payment' => $data['payment']
    ];

    // Append to invoices array
    $billing_invoices[] = $new_invoice;

    return ['success' => true, 'message' => "Invoice $new_id created successfully"];
}

function get_quotations() {
    global $quotations;
    return $quotations;
}

function get_credit_notes() {
    global $credit_notes;
    return $credit_notes;
}

function get_sales_returns() {
    global $sales_returns;
    return $sales_returns;
}

function get_transactions() {
    global $transactions;
    return $transactions;
}

function get_customers() {
    global $customers;
    return $customers;
}

function save_customer($data) {
    global $customers;

    // Validate required fields
    $required_fields = ['name', 'type', 'phone', 'email', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return ['success' => false, 'message' => "Missing required field: $field"];
        }
    }

    // Validate type
    $valid_types = ['Retail', 'Wholesale', 'Contractor'];
    if (!in_array($data['type'], $valid_types)) {
        return ['success' => false, 'message' => 'Invalid customer type'];
    }

    // Validate status
    $valid_statuses = ['Active', 'Inactive'];
    if (!in_array($data['status'], $valid_statuses)) {
        return ['success' => false, 'message' => 'Invalid status'];
    }

    //

    // Validate phone
    if (!preg_match('/^\+91 [0-9]{10}$/', $data['phone'])) {
        return ['success' => false, 'message' => 'Invalid phone number format. Use +91 followed by 10 digits'];
    }

    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }

    // Generate new customer ID
    $existing_ids = array_column($customers, 'id');
    $last_id = max(array_map(function ($id) {
        return (int) substr($id, 5);
    }, $existing_ids));
    $new_id = 'CUST-' . str_pad($last_id + 1, 3, '0', STR_PAD_LEFT);

    // Create new customer
    $new_customer = [
        'id' => $new_id,
        'name' => $data['name'],
        'type' => $data['type'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'totalSpent' => 0,
        'lastPurchase' => null,
        'status' => $data['status'],
        'contact' => $data['phone'],
        'address' => isset($data['address']) ? $data['address'] : ''
    ];

    // Append to customers array
    $customers[] = $new_customer;

    return ['success' => true, 'message' => "Customer $new_id created successfully"];
}

function get_payment_methods() {
    global $payment_methods;
    return $payment_methods;
}

function get_supply_requests() {
    global $supply_requests;
    return $supply_requests;
}

function get_low_stock_items() {
    global $low_stock_items;
    return $low_stock_items;
}

function get_popular_products() {
    global $popular_products;
    return $popular_products;
}

function get_supply_analytics() {
    global $supply_analytics;
    return $supply_analytics;
}

function get_inventory_items() {
    global $inventory_items;
    return $inventory_items;
}

function get_inventory_categories() {
    global $inventory_categories;
    return $inventory_categories;
}

function get_inventory_analytics() {
    global $inventory_analytics;
    return $inventory_analytics;
}

function get_inventory_stats() {
    global $inventory_stats;
    return $inventory_stats;
}

function get_inventory_activities() {
    global $inventory_activities;
    return $inventory_activities;
}

function get_orders() {
    global $orders;
    return $orders;
}

function get_order_analytics() {
    global $order_analytics;
    return $order_analytics;
}

function get_customer_analytics() {
    global $customer_analytics;
    return $customer_analytics;
}

function get_customer_segmentation() {
    global $customer_segmentation;
    return $customer_segmentation;
}

function get_top_spending_categories() {
    global $top_spending_categories;
    return $top_spending_categories;
}

function get_recent_activity() {
    global $recent_activity;
    return $recent_activity;
}

function get_payment_analytics() {
    global $payment_analytics;
    return $payment_analytics;
}

function get_payment_method_data() {
    global $payment_method_data;
    return $payment_method_data;
}

function get_daily_revenue_data() {
    global $daily_revenue_data;
    return $daily_revenue_data;
}

function get_reports() {
    global $reports;
    return $reports;
}

function get_monthly_sales_data() {
    global $monthly_sales_data;
    return $monthly_sales_data;
}

function get_product_category_data() {
    global $product_category_data;
    return $product_category_data;
}

function get_customer_visit_data() {
    global $customer_visit_data;
    return $customer_visit_data;
}

function get_store_settings() {
    global $store_settings;
    return $store_settings;
}
?>