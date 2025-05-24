-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 04:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `unnati-wires`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `Id` int(11) NOT NULL,
  `business_account` decimal(10,2) NOT NULL,
  `saving_account` decimal(10,2) NOT NULL,
  `cash_account` decimal(10,2) NOT NULL,
  `Inserted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`Id`, `business_account`, `saving_account`, `cash_account`, `Inserted_at`) VALUES
(1, 400000.00, 500000.00, 200000.00, '2025-05-05 15:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `admin_billing_settings`
--

CREATE TABLE `admin_billing_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `standard_shift_hours` varchar(50) DEFAULT NULL,
  `payment_terms` int(11) DEFAULT NULL,
  `tax_rate` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_billing_settings`
--

INSERT INTO `admin_billing_settings` (`id`, `standard_shift_hours`, `payment_terms`, `tax_rate`) VALUES
(1, '8', 30, 5);

-- --------------------------------------------------------

--
-- Table structure for table `admin_business_details`
--

CREATE TABLE `admin_business_details` (
  `id` int(11) NOT NULL,
  `factory_name` varchar(255) DEFAULT NULL,
  `factory_address` text DEFAULT NULL,
  `factory_location` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `factory_manager` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_business_details`
--

INSERT INTO `admin_business_details` (`id`, `factory_name`, `factory_address`, `factory_location`, `phone_number`, `factory_manager`) VALUES
(1, 'ABCD', 'afjafna', 'India', '1234567890', 'XYZ');

-- --------------------------------------------------------

--
-- Table structure for table `admin_inventory_settings`
--

CREATE TABLE `admin_inventory_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `stock_buffer` int(11) DEFAULT NULL,
  `lead_time` int(11) DEFAULT NULL,
  `auto_reorder` tinyint(1) DEFAULT 0,
  `fifo_method` tinyint(1) DEFAULT 0,
  `batch_tracking` tinyint(1) DEFAULT 0,
  `material_expiry` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_inventory_settings`
--

INSERT INTO `admin_inventory_settings` (`id`, `stock_buffer`, `lead_time`, `auto_reorder`, `fifo_method`, `batch_tracking`, `material_expiry`) VALUES
(1, 80, 10, 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `admin_production_settings`
--

CREATE TABLE `admin_production_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `daily_capacity` int(11) DEFAULT NULL,
  `target_efficiency` int(11) DEFAULT NULL,
  `shift_duration` int(11) DEFAULT NULL,
  `auto_scheduling` tinyint(1) DEFAULT 0,
  `downtime_tracking` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_production_settings`
--

INSERT INTO `admin_production_settings` (`id`, `daily_capacity`, `target_efficiency`, `shift_duration`, `auto_scheduling`, `downtime_tracking`) VALUES
(1, 20, 50, 8, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_workers_settings`
--

CREATE TABLE `admin_workers_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `standard_shift_hours` int(11) DEFAULT NULL,
  `overtime_rate` float DEFAULT NULL,
  `lateness_threshold` int(11) DEFAULT NULL,
  `attendance_method` varchar(50) DEFAULT NULL,
  `auto_timesheet` tinyint(1) DEFAULT NULL,
  `skill_tracking` tinyint(1) DEFAULT NULL,
  `safety_alerts` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_workers_settings`
--

INSERT INTO `admin_workers_settings` (`id`, `standard_shift_hours`, `overtime_rate`, `lateness_threshold`, `attendance_method`, `auto_timesheet`, `skill_tracking`, `safety_alerts`) VALUES
(1, 8, 20, 30, 'Biometric', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `auto_bill`
--

CREATE TABLE `auto_bill` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counter_purchase`
--

CREATE TABLE `counter_purchase` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL,
  `Sales_Id` varchar(20) NOT NULL,
  `payment_id` varchar(20) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `credit_note`
--

CREATE TABLE `credit_note` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_for` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_note`
--

INSERT INTO `credit_note` (`sales_return_id`, `customer_name`, `date`, `tax_rate`, `item`, `description`, `quantity`, `price`, `total`, `notes`, `subtotal`, `GST_amount`, `Grand_total`, `payment_method`, `created_by`, `created_for`) VALUES
('CN-2025-001', 'Vikram Mehta', '2025-05-08', '5', 'Product A', 'demo', '1', '2565', '2565.00', 'ssdn', 0.00, 718.20, 3283.20, 'Cash', 'Store', 'Store'),
('SL-001', 'xyz', '2025-05-07', '5', 'Product A', 'demo', '1', '251236', '251236.00', 'testing', 0.00, 30148.32, 281384.32, 'Cash', 'Store', 'Store');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_Id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` enum('Contractor','Wholesale','Retail') NOT NULL,
  `contact` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_for` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_Id`, `name`, `type`, `contact`, `date`, `created_by`, `created_for`) VALUES
('CUST-000', 'abc', 'Contractor', '9012345678', '2025-05-04', '', 'Store'),
('CUST-001', 'Vikram Mehta', 'Wholesale', '9012345678', '2025-05-04', '', 'Store'),
('CUST-002', 'xyz', 'Retail', '1234567890', '2025-05-05', '', 'Vendor'),
('CUST-003', 'js', 'Contractor', '1234567890', '2025-05-16', '', ''),
('CUST-004', 'dkfm', 'Wholesale', '1234567890', '2025-05-16', '', ''),
('CUST-005', 'mno', 'Retail', '1234567890', '2025-05-16', 'Admin', 'Store'),
('CUST-006', 'Purvi', 'Retail', '9012345678', '2025-05-16', 'Store', 'Store'),
('CUST-007', 'Sivam', 'Wholesale', '1234567890', '2025-05-17', 'Store', 'Store');

-- --------------------------------------------------------

--
-- Table structure for table `debit_note`
--

CREATE TABLE `debit_note` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_challan`
--

CREATE TABLE `delivery_challan` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_challan`
--

INSERT INTO `delivery_challan` (`sales_return_id`, `customer_name`, `date`, `tax_rate`, `item`, `description`, `quantity`, `price`, `total`, `notes`, `subtotal`, `GST_amount`, `Grand_total`, `payment_method`) VALUES
('DC-2025-001', 'Vikram Mehta', '2025-05-06', '5', 'Product A', 'demo', '1', '51', '51.00', 'testing', 0.00, 6.12, 57.12, 'Digital payment');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `id` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `id`, `date`, `category`, `amount`, `vendor`, `status`, `method`, `created_at`, `updated_at`) VALUES
(1, 'EXP-2025-001', '2025-05-03', 'Transport', 29000.50, 'Unnati-vendor', 'Pending', 'Cash', '2025-05-03 05:34:02', '2025-05-03 05:53:30'),
(2, 'EXP-2025-002', '2025-05-03', 'Salaries', 100000.00, 'Null', 'Approved', 'Bank Transfer', '2025-05-03 06:20:11', '2025-05-03 06:20:11'),
(3, 'EXP-2025-003', '2025-05-03', 'Transport', 6000.00, 'Unnati-transport', 'Approved', 'Cash', '2025-05-03 15:37:43', '2025-05-03 15:37:43'),
(4, 'EXP-2025-004', '2025-05-03', 'Transport', 10000.00, 'Vendor', 'Rejected', 'Bank Transfer', '2025-05-03 15:41:47', '2025-05-03 15:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `factory_orders`
--

CREATE TABLE `factory_orders` (
  `order_id` int(20) NOT NULL,
  `order_code` varchar(20) DEFAULT NULL,
  `item` varchar(255) NOT NULL,
  `quantity` varchar(50) NOT NULL,
  `supplier` varchar(255) NOT NULL,
  `delivery_date` date NOT NULL,
  `status` enum('Ordered','In Transit','Delivered') DEFAULT 'Ordered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_orders`
--

INSERT INTO `factory_orders` (`order_id`, `order_code`, `item`, `quantity`, `supplier`, `delivery_date`, `status`) VALUES
(1, 'SUP-2025-001', 'Copper Wire 2.5mm', '2000 kg', 'Hindalco Industries', '2025-04-08', 'Delivered'),
(2, 'SUP-2025-002', 'PVC Insulation', '1500 kg', 'Polycab Ltd', '2025-04-10', 'In Transit'),
(3, 'SUP-2025-003', 'Aluminum Wire', '3000 kg', 'Sterlite Technologies', '2025-04-12', 'Ordered'),
(4, 'SUP-2025-004', 'Packaging Material', '500 units', 'Packaging Solutions', '2025-04-05', 'Delivered'),
(5, 'SUP-2025-005', 'Machine Parts', '24 units', 'Industrial Machines Ltd', '2025-04-11', 'In Transit');

-- --------------------------------------------------------

--
-- Table structure for table `factory_stock`
--

CREATE TABLE `factory_stock` (
  `stock_id` int(20) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `record_date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_stock`
--

INSERT INTO `factory_stock` (`stock_id`, `item_name`, `category`, `quantity`, `value`, `status`, `record_date`) VALUES
(1, 'Copper Wire 1.5mm', 'Raw Materials', 450, 115200.00, 'In Stock', '2025-05-01'),
(2, 'PVC Conduit Pipes', 'Components', 325, 45500.00, 'In Stock', '2025-04-15'),
(3, 'Circuit Breakers 16A', 'Finished Goods', 279, 98000.00, 'In Stock', '2025-03-10'),
(4, 'LED Bulbs 9W', 'Finished Goods', 620, 62000.00, 'In Stock', '2025-02-20'),
(5, 'sample item', 'sample category', 0, 0.00, 'Out of Stock', '2025-05-02'),
(6, 'Terminal Blocks', 'Components', 84, 8500.00, 'Low Stock', '2025-01-05'),
(7, 'kiva', 'kivax', 5, 12000.00, 'Low Stock', '2025-05-03'),
(8, 'Copper Wire 1.6mm', 'Raw Materials', 450, 115200.00, 'In Stock', '2025-05-01'),
(9, 'my Conduit Pipes', 'Components', 0, 0.00, 'Out of Stock', '2025-04-15'),
(10, 'Circuit Breakers 17A', 'Finished Goods', 84, 98000.00, 'Low Stock', '2025-03-10'),
(11, 'LED Bulbs 9W', 'Finished Goods', 620, 62000.00, 'In Stock', '2025-02-20'),
(12, 'sample item', 'sample category', 100, 100000.00, 'In Stock', '2025-05-02'),
(13, 'Terminal Blocks', 'Components', 198, 85000.00, 'In Stock', '2025-01-05'),
(14, 'Circuit Breakers 16A', 'Components', 325, 116000.00, 'In Stock', '2025-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `Id` varchar(20) NOT NULL,
  `Product_Name` varchar(20) NOT NULL,
  `Category` varchar(20) NOT NULL,
  `Stock` varchar(50) NOT NULL,
  `Transaction_Type` varchar(20) NOT NULL,
  `Status` enum('In Stock','Low Stock') NOT NULL,
  `Supplier` varchar(50) NOT NULL,
  `product_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`Id`, `Product_Name`, `Category`, `Stock`, `Transaction_Type`, `Status`, `Supplier`, `product_id`) VALUES
('TRX-001', 'Aluminum Wire 2mm', 'Wires', '15', 'Add Stock', 'In Stock', 'ghg', 'P-002'),
('TRX-002', 'abcd', 'Wires and Cables', '35', 'Add Stock', 'In Stock', 'xyz', 'P-011'),
('TRX-003', 'Circuit Breaker', 'Safety Components', '10', 'Add Stock', 'In Stock', 'mno', 'P-006');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `document_type` enum('with GST','without GST') NOT NULL,
  `date` date NOT NULL,
  `due_date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item_name` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `Sales_Id` varchar(20) NOT NULL,
  `payment_id` varchar(20) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_for` varchar(50) NOT NULL,
  `status` enum('Completed','Pending','Refund') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `customer_name`, `document_type`, `date`, `due_date`, `tax_rate`, `item_name`, `description`, `quantity`, `price`, `total`, `notes`, `subtotal`, `GST_amount`, `grand_total`, `Sales_Id`, `payment_id`, `payment_method`, `created_by`, `created_for`, `status`) VALUES
('INV-2025-001', 'Vikram Mehta', 'with GST', '2025-05-13', '2025-05-28', '18', 'Aluminum Wire 2mm', 'demo', '1', '166646', '166646.00', 'final testing', 166646.00, 29996.28, 196642.28, 'SL-001', 'PAY-001', 'Payment gateway', 'Admin', 'Vendor', 'Completed'),
('INV-2025-001', 'abc', 'with GST', '2025-05-12', '2025-05-27', '18', 'Circuit Breaker', 'demo', '20', '650', '13000.00', 'testing', 13000.00, 2340.00, 15340.00, 'SL-002', 'PAY-001', 'Cash', 'Admin', 'Store', 'Completed'),
('INV-2025-002', 'abc', 'with GST', '2025-05-12', '2025-05-27', '12', 'Circuit Breaker', 'demo', '20', '650', '13000.00', 'testing', 13000.00, 1560.00, 14560.00, 'SL-003', 'PAY-002', 'Cash', 'Admin', 'Store', 'Completed'),
('INV-2025-003', 'js', 'with GST', '2025-05-12', '2025-05-27', '18', 'Aluminum Wire 2mm', 'demo', '10', '2000', '20000.00', 'testing', 20000.00, 3600.00, 23600.00, 'SL-004', 'PAY-003', 'Digital payment', 'Admin', 'Store', 'Refund'),
('INVWO-2025-001', 'Purvi', 'without GST', '2025-05-12', '2025-05-20', '', 'Havells Wire (1.5mm)', 'demo', '2', '545646', '1091292.00', 'testing', 1091292.00, 0.00, 1091292.00, 'SL-005', 'PAY-004', 'BNPL', 'Store', 'Store', 'Completed'),
('INVWO-2025-001', 'js', 'without GST', '2025-05-14', '2025-05-21', '', 'abcd', 'demo', '15', '545451', '8181765.00', 'testing', 8181765.00, 0.00, 8181765.00, 'SL-006', 'PAY-001', 'Digital payment', 'Admin', 'Factory', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` enum('inventory','billing') DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL,
  `user_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_out`
--

CREATE TABLE `payment_out` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` varchar(50) NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `gst_rate` decimal(5,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `mrp`, `name`, `category`, `gst_rate`, `selling_price`, `created_at`, `updated_at`, `stock_quantity`) VALUES
('P-001', 1200.00, 'Copper Wire 1mm', 'Wires', 18.00, 1350.00, '2025-05-01 03:30:00', '2025-05-19 06:31:08', 50),
('P-003', 50.00, 'PVC Insulated Tape', 'Accessories', 12.00, 60.00, '2025-05-02 04:30:00', '2025-05-02 04:30:00', 0),
('P-004', 250.00, 'Terminal Block', 'Connectors', 18.00, 280.00, '2025-05-02 06:00:00', '2025-05-04 08:50:00', 0),
('P-008', 40.00, 'LED Indicator', 'Components', 18.00, 45.00, '2025-05-04 08:30:00', '2025-05-05 05:30:00', 0),
('P-009', 2200.00, 'Flexible Cable 3mm', 'Wires', 18.00, 2400.00, '2025-05-05 02:30:00', '2025-05-05 02:30:00', 0),
('P-010', 120.00, 'Fuse Holder', 'Safety Components', 18.00, 140.00, '2025-05-05 03:30:00', '2025-05-05 03:30:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `proforma`
--

CREATE TABLE `proforma` (
  `invoice_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `document_type` enum('with GST','without GST') NOT NULL,
  `date` date NOT NULL,
  `due_date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item_name` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order`
--

CREATE TABLE `purchase_order` (
  `Purchase_Id` varchar(20) NOT NULL,
  `Customer_Name` varchar(100) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Date` date NOT NULL,
  `Item` varchar(100) NOT NULL,
  `Unit` int(50) NOT NULL,
  `Payment_Method` enum('Bank Transfer','Cash','UPI','Cheque','Card') NOT NULL,
  `Status` enum('Received','In Transit','Ordered') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order`
--

INSERT INTO `purchase_order` (`Purchase_Id`, `Customer_Name`, `Amount`, `Date`, `Item`, `Unit`, `Payment_Method`, `Status`) VALUES
('PO-2025-001', 'Aman B', 141.00, '2025-05-06', 'steel', 2, 'Bank Transfer', 'In Transit'),
('PO-2025-002', 'aman', 21312.00, '2025-05-21', 'steel', 33, 'Bank Transfer', 'Received'),
('PO-2025-003', 'vaibhav', 333.00, '2025-05-09', 'metal', 2, 'Bank Transfer', 'Received');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_bill`
--

CREATE TABLE `purchase_order_bill` (
  `invoice_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `document_type` enum('with GST','without GST') NOT NULL,
  `date` date NOT NULL,
  `due_date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item_name` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_bill`
--

INSERT INTO `purchase_order_bill` (`invoice_id`, `customer_name`, `document_type`, `date`, `due_date`, `tax_rate`, `item_name`, `description`, `quantity`, `price`, `total`, `notes`, `subtotal`, `GST_amount`, `grand_total`, `payment_method`) VALUES
('-2025-001', 'xyz', 'with GST', '2025-05-01', '2025-05-06', '18', 'Product A', 'demo', '1', '12', '12.00', 'testing', 12.00, 2.16, 14.16, 'Digital payment');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return`
--

CREATE TABLE `purchase_return` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation`
--

CREATE TABLE `quotation` (
  `invoice_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `document_type` enum('with GST','without GST') NOT NULL,
  `date` date NOT NULL,
  `due_date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item_name` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotation`
--

INSERT INTO `quotation` (`invoice_id`, `customer_name`, `document_type`, `date`, `due_date`, `tax_rate`, `item_name`, `description`, `quantity`, `price`, `total`, `notes`, `subtotal`, `GST_amount`, `grand_total`, `payment_method`) VALUES
('QT-2025-001', 'Vikram Mehta', 'with GST', '2025-04-29', '2025-05-06', '18', 'Product A', 'demo', '1', '500', '500.00', 'testing', 500.00, 90.00, 590.00, 'Digital payment');

-- --------------------------------------------------------

--
-- Table structure for table `retail_invetory`
--

CREATE TABLE `retail_invetory` (
  `Id` varchar(20) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `stock` int(10) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `reorder_point` int(10) NOT NULL,
  `last_updated` date NOT NULL,
  `status` enum('In stock','Low stock','Out of stock') NOT NULL,
  `inventory_of` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retail_invetory`
--

INSERT INTO `retail_invetory` (`Id`, `item_name`, `category`, `stock`, `unit`, `price`, `reorder_point`, `last_updated`, `status`, `inventory_of`) VALUES
('ITEM-001', 'Havells Wire (1.5mm)', 'Wires and Cables', 35, 'm', 95.00, 5, '2025-05-19', 'In stock', 'Store');

-- --------------------------------------------------------

--
-- Table structure for table `retail_store_cash`
--

CREATE TABLE `retail_store_cash` (
  `Id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `opening_balance` int(20) DEFAULT NULL,
  `cash_deposit` varchar(50) DEFAULT NULL,
  `cash_deposit_amount` decimal(10,2) DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retail_store_cash`
--

INSERT INTO `retail_store_cash` (`Id`, `user_id`, `opening_balance`, `cash_deposit`, `cash_deposit_amount`, `date`) VALUES
(1, 'store@unnati.com', NULL, '', 0.00, '2025-05-08'),
(2, 'store@unnati.com', NULL, 'abc bank', 1000.00, '2025-05-08'),
(3, 'store@unnati.com', NULL, 'abc bank', 1000.00, '2025-05-08'),
(4, 'store@unnati.com', NULL, 'xyz bank', 1000.00, '2025-05-08'),
(5, 'store@unnati.com', NULL, 'mno bank', 1000.00, '2025-05-08'),
(6, 'store@unnati.com', NULL, 'mno bank', 1000.00, '2025-05-08'),
(7, 'store@unnati.com', NULL, 'mno bank', 1000.00, '2025-05-08'),
(8, 'store@unnati.com', NULL, 'mno bank', 1000.00, '2025-05-08'),
(9, 'store@unnati.com', NULL, 'mno bank', 1000.00, '2025-05-08'),
(10, 'store@unnati.com', NULL, 'mno bank', 1000.00, '2025-05-08'),
(11, 'store@unnati.com', NULL, 'mno bank', 1000.00, '2025-05-08');

-- --------------------------------------------------------

--
-- Table structure for table `retail_store_orders`
--

CREATE TABLE `retail_store_orders` (
  `order_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `delivery_date` date NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `quantity` int(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL,
  `payment_status` enum('Paid','Pending','Refunded') NOT NULL,
  `status` enum('Processing','Ready for Pickup','Delivered','Cancelled') NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_for` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retail_store_orders`
--

INSERT INTO `retail_store_orders` (`order_id`, `customer_name`, `date`, `delivery_date`, `item_name`, `quantity`, `amount`, `payment_method`, `payment_status`, `status`, `created_by`, `created_for`) VALUES
('ORD-001', 'Vikram Mehta', '2025-05-11', '2025-05-20', 'Ceiling Fan (48 inch)', 10, 8750.00, 'Cash', 'Paid', 'Ready for Pickup', 'Store', 'Store'),
('ORD-002', 'Vikram Mehta', '2025-05-12', '2025-05-25', 'Havells Wire (1.5mm)', 5, 5000.00, 'Cash', 'Paid', 'Delivered', 'Store', 'Store');

-- --------------------------------------------------------

--
-- Table structure for table `retail_store_stock_request`
--

CREATE TABLE `retail_store_stock_request` (
  `date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `request_id` varchar(20) NOT NULL,
  `tracking_id` varchar(20) NOT NULL,
  `delivery_id` varchar(20) DEFAULT NULL,
  `request_to` varchar(50) NOT NULL,
  `shop_name` varchar(50) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(10) NOT NULL,
  `location` varchar(100) NOT NULL,
  `requested_by` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `received_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retail_store_stock_request`
--

INSERT INTO `retail_store_stock_request` (`date`, `delivery_date`, `received_date`, `request_id`, `tracking_id`, `delivery_id`, `request_to`, `shop_name`, `item_name`, `category`, `quantity`, `location`, `requested_by`, `status`, `received_by`) VALUES
('2025-05-10', NULL, NULL, 'RQST-001', 'TRCK-001', NULL, 'Factory', 'ABC', 'Ceiling Fan (48 inch)', 'Fans', 50, 'XYZ', 'Store', 'Ordered', NULL),
('2025-05-19', '2025-05-29', '2025-05-26', 'RQST-002', 'TRCK-002', 'DELS-001', 'Vendor', 'ajnfa', 'Havells Wire (1.5mm)', 'wires', 10, 'sjdbdf', 'Store', 'Received', 'MNO');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `Sales_Id` varchar(20) NOT NULL,
  `Customer_Name` varchar(100) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Date` date NOT NULL,
  `Item` int(50) NOT NULL,
  `Payment_Method` enum('Bank Transfer','Cash','UPI','Cheque','Card') NOT NULL,
  `Status` enum('Completed','Pending') NOT NULL,
  `Category` enum('Wires and Cables','Switches and Sockets','Lighting','Fans','MCBs and DBs','Accessories') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`Sales_Id`, `Customer_Name`, `Amount`, `Date`, `Item`, `Payment_Method`, `Status`, `Category`) VALUES
('SL-001', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'Fans'),
('SL-002', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'Wires and Cables'),
('SL-003', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'Switches and Sockets'),
('SL-004', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'Accessories'),
('SL-005', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'MCBs and DBs'),
('SL-006', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'Fans'),
('SL-007', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'Switches and Sockets'),
('SL-008', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'Wires and Cables'),
('SL-009', 'Priya Sharma', 8750.00, '2025-05-02', 5, 'UPI', 'Completed', 'Lighting'),
('SL-010', 'Priya Sharma', 8750.00, '2025-05-03', 5, 'Bank Transfer', 'Completed', 'Lighting');

-- --------------------------------------------------------

--
-- Table structure for table `sales_return`
--

CREATE TABLE `sales_return` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('Digital payment','Cash','BNPL','Payment gateway') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_return`
--

INSERT INTO `sales_return` (`sales_return_id`, `customer_name`, `date`, `tax_rate`, `item`, `description`, `quantity`, `price`, `total`, `notes`, `subtotal`, `GST_amount`, `Grand_total`, `payment_method`) VALUES
('SR-2025-001', 'Select customer', '0000-00-00', '5', '', '', '', '', '', 'testing', 0.00, 90.00, 590.00, 'Digital payment'),
('SR-2025-002', 'Select customer', '0000-00-00', '5', 'Product C', 'demo', '1', '1000', '1000.00', 'testing', 0.00, 120.00, 1120.00, 'Digital payment'),
('SR-2025-003', 'Select customer', '2025-05-06', '5', 'Product A', 'demo', '1', '500', '500.00', 'testing', 0.00, 60.00, 560.00, 'Digital payment');

-- --------------------------------------------------------

--
-- Table structure for table `store_after_sales_settings`
--

CREATE TABLE `store_after_sales_settings` (
  `id` int(11) NOT NULL,
  `default_warranty` int(11) DEFAULT NULL,
  `extended_warranty` int(11) DEFAULT NULL,
  `warranty_tracking` tinyint(1) DEFAULT NULL,
  `return_period` int(11) DEFAULT NULL,
  `return_policy` varchar(50) DEFAULT NULL,
  `returns_conditions` text DEFAULT NULL,
  `service_centers` text DEFAULT NULL,
  `doorstep_service` tinyint(1) DEFAULT NULL,
  `express_service` tinyint(1) DEFAULT NULL,
  `support_phone` varchar(20) DEFAULT NULL,
  `support_email` varchar(100) DEFAULT NULL,
  `customer_portal` tinyint(1) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_after_sales_settings`
--

INSERT INTO `store_after_sales_settings` (`id`, `default_warranty`, `extended_warranty`, `warranty_tracking`, `return_period`, `return_policy`, `returns_conditions`, `service_centers`, `doorstep_service`, `express_service`, `support_phone`, `support_email`, `customer_portal`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 12, 24, 1, 7, 'exchange', 'Products must be in unused condition with original packaging and receipt. Electrical items must not be installed or used.', 'Mumbai Central Service Center: 123 Main St, Mumbai - 400001\r\nDelhi Service Center: 456 Market Ave, Delhi - 110001', 0, 0, '+91 1800-123-4567', 'support@unnatielectric.com', 1, 'Store', '2025-05-17 10:43:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `store_hardware_settings`
--

CREATE TABLE `store_hardware_settings` (
  `id` int(11) NOT NULL,
  `receipt_printer` tinyint(1) DEFAULT 0,
  `printer_model` varchar(100) DEFAULT NULL,
  `barcode_scanner` tinyint(1) DEFAULT 0,
  `scanner_model` varchar(100) DEFAULT NULL,
  `customer_display` tinyint(1) DEFAULT 0,
  `payment_terminal` tinyint(1) DEFAULT 0,
  `cash_drawer` tinyint(1) DEFAULT 0,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_hardware_settings`
--

INSERT INTO `store_hardware_settings` (`id`, `receipt_printer`, `printer_model`, `barcode_scanner`, `scanner_model`, `customer_display`, `payment_terminal`, `cash_drawer`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 0, 'star', 1, 'symbol', 0, 1, 0, 'Store', '2025-05-17 09:58:59', '2025-05-17 09:58:59');

-- --------------------------------------------------------

--
-- Table structure for table `store_inventory_settings`
--

CREATE TABLE `store_inventory_settings` (
  `id` int(11) NOT NULL,
  `low_stock_threshold` int(11) DEFAULT 10,
  `reorder_point` int(11) DEFAULT 5,
  `track_serial_numbers` tinyint(1) DEFAULT 0,
  `allow_negative_stock` tinyint(1) DEFAULT 0,
  `barcode_scanning` tinyint(1) DEFAULT 0,
  `inventory_method` enum('fifo','lifo','avg') DEFAULT 'fifo',
  `stock_count_frequency` enum('weekly','biweekly','monthly','quarterly') DEFAULT 'monthly',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_inventory_settings`
--

INSERT INTO `store_inventory_settings` (`id`, `low_stock_threshold`, `reorder_point`, `track_serial_numbers`, `allow_negative_stock`, `barcode_scanning`, `inventory_method`, `stock_count_frequency`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 20, 10, 1, 0, 1, '', 'weekly', 'Store', '2025-05-17 09:23:25', '2025-05-17 09:23:25');

-- --------------------------------------------------------

--
-- Table structure for table `store_service_requests`
--

CREATE TABLE `store_service_requests` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `issue_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_service_requests`
--

INSERT INTO `store_service_requests` (`id`, `customer_name`, `contact_number`, `product_name`, `purchase_date`, `issue_type`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Priya Sharma', '1516516515', 'XYZ', '2025-05-06', 'maintenance', 'Testing', 'Store', '2025-05-17 06:49:19', '2025-05-17 06:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `store_settings_general`
--

CREATE TABLE `store_settings_general` (
  `id` int(11) NOT NULL,
  `store_name` varchar(255) DEFAULT NULL,
  `store_code` varchar(100) DEFAULT NULL,
  `store_phone` varchar(20) DEFAULT NULL,
  `store_email` varchar(255) DEFAULT NULL,
  `store_address` text DEFAULT NULL,
  `store_manager` varchar(255) DEFAULT NULL,
  `store_active` tinyint(1) DEFAULT 0,
  `accept_online_orders` tinyint(1) DEFAULT 0,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_settings_general`
--

INSERT INTO `store_settings_general` (`id`, `store_name`, `store_code`, `store_phone`, `store_email`, `store_address`, `store_manager`, `store_active`, `accept_online_orders`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'ABC', 'ST566', '1234567890', 'store@unnati.com', 'jskdnasdjn', 'XYZ', 0, 0, 'Store', '2025-05-17 07:20:17', '2025-05-17 07:20:17');

-- --------------------------------------------------------

--
-- Table structure for table `store_warranty_claims`
--

CREATE TABLE `store_warranty_claims` (
  `id` int(11) NOT NULL,
  `warranty_number` varchar(100) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `claim_type` varchar(100) DEFAULT NULL,
  `claim_details` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_warranty_claims`
--

INSERT INTO `store_warranty_claims` (`id`, `warranty_number`, `customer_name`, `product_name`, `serial_number`, `claim_type`, `claim_details`, `created_by`, `created_at`) VALUES
(1, 'WAR1515', 'ABC', 'MNO', '54', 'damage', 'testing', 'Store', '2025-05-17 07:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `Supplier_ID` varchar(10) NOT NULL,
  `Supplier_Name` varchar(100) NOT NULL,
  `Type` enum('Manufacturer','Distributor') NOT NULL,
  `Items` enum('Wires, Switches','Wires, Cables','Fans, Lights','Appliances','Switches, Sockets') NOT NULL,
  `Orders` int(11) NOT NULL DEFAULT 0,
  `Spending` decimal(12,2) NOT NULL DEFAULT 0.00,
  `Actions` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`Supplier_ID`, `Supplier_Name`, `Type`, `Items`, `Orders`, `Spending`, `Actions`) VALUES
('SUP001', 'aman', 'Distributor', 'Wires, Cables', 4, 1.00, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `customer` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `issue_description` text NOT NULL,
  `product` varchar(255) NOT NULL,
  `priority` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `customer`, `date`, `issue_description`, `product`, `priority`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Unnati-traders', '2025-05-02', 'Fan not moving.', 'Fan', 'High', 'Open', '2025-05-01 21:00:48', '2025-05-01 21:00:48'),
(2, 'Unnati-traders1', '2025-05-02', 'Fan not moving.', 'Fan', 'Low', 'Open', '2025-05-02 16:48:14', '2025-05-02 16:48:14'),
(3, 'Unnati-traders2', '2025-05-03', 'Fan not moving.', 'Fan', 'Low', 'In Progress', '2025-05-02 16:49:39', '2025-05-02 16:49:39');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `Transaction_ID` varchar(18) NOT NULL,
  `Date` date NOT NULL,
  `Description` text NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Status` enum('Completed','Pending') NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Bank Transfer','Cash','UPI','Cheque','Card') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`Transaction_ID`, `Date`, `Description`, `Type`, `Status`, `Amount`, `payment_method`) VALUES
('TRX-001', '2025-05-02', 'Supplier Payment - Havells', 'Expense', 'Completed', 45600.00, 'Bank Transfer'),
('TRX-002', '2025-05-08', 'Supplier Payment - Havells', 'Expense', 'Completed', 45600.00, 'Bank Transfer'),
('TRX-003', '2025-05-02', 'Supplier Payment - Havells', 'Expense', 'Completed', 45600.00, 'Bank Transfer'),
('TRX-004', '2025-05-02', 'Supplier Payment - Havells', 'Expense', 'Completed', 45600.00, 'Bank Transfer'),
('TRX-005', '2025-04-29', 'Supplier Payment - Havells', 'Expense', 'Completed', 45600.00, 'UPI'),
('TRX-006', '2025-05-01', 'Supplier Payment - Havells', 'Expense', 'Completed', 45600.00, 'Cash'),
('TRX-007', '2025-05-03', 'Supplier Payment - Havells', 'Expense', 'Pending', 45600.00, 'Bank Transfer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `user_roll` varchar(50) NOT NULL,
  `salt` varchar(256) NOT NULL,
  `user_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`email`, `password`, `user_type`, `user_roll`, `salt`, `user_name`) VALUES
('admin@unnati.com', '366252a58be7185b1aa036e4a40f28e6cbbecc0e49bc8ac4e9a54a5c0211cef8', 'Admin', 'owner', '8c1cf323382d2c2e7a90218fe17d1810', 'Admin'),
('factory@unnati.com', '2018145d9952641d911298a538e888369a0693bbb9b864df8d0a959e0cb56220', 'Factory', 'owner', 'd6c44ef35c3938962752282e8649881f', 'Factory'),
('store@unnati.com', 'd1a2bfa517f8a05cac3c679aee130034113ce74d3e8ab652536a29b97e9a98b6', 'Store', 'owner', 'a4e542c5c0a5c1d313d112ccc364bc25', 'Store'),
('vendor@unnati.com', '5cb2db83125d45af59746f7fae535411a727b12b9c255b347ba5407fd85903c3', 'Vendor', 'owner', 'bc1bd02f21cc2fa4694623a17a411ea7', 'Vendor');

-- --------------------------------------------------------

--
-- Table structure for table `user_management`
--

CREATE TABLE `user_management` (
  `User_ID` varchar(20) NOT NULL,
  `User_Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Role` enum('Manager','Accountant','Store','Admin') NOT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `Last_Login` datetime DEFAULT NULL,
  `Permission` text NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_management`
--

INSERT INTO `user_management` (`User_ID`, `User_Name`, `Email`, `Role`, `Status`, `Last_Login`, `Permission`, `Created_At`) VALUES
('USR-001', 'wqqwfasf', 'aman@gmail.comm', 'Store', 'Active', NULL, '[\"Billing Desk\",\"Accounting\"]', '2025-05-04 08:29:19'),
('USR-002', 'awdd', 'bajpeyaman16@gmail.com', 'Accountant', 'Active', NULL, '[]', '2025-05-04 08:30:32');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_business_profiles`
--

CREATE TABLE `vendor_business_profiles` (
  `id` int(11) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `gstin` varchar(20) DEFAULT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `business_description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_business_profiles`
--

INSERT INTO `vendor_business_profiles` (`id`, `created_by`, `company_name`, `business_type`, `address`, `city`, `state`, `pincode`, `gstin`, `pan_number`, `business_description`, `created_at`, `updated_at`) VALUES
(1, 'Vendor', 'ABC', 'Hardware', 'XYZ', 'gwalior', 'Madhya Pradesh', '564654', 'aj5646aedh', 'hssbaf', 'ahkbabidaiasdl', '2025-05-18 20:25:26', '2025-05-18 20:25:26');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_payment_settings`
--

CREATE TABLE `vendor_payment_settings` (
  `id` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(50) DEFAULT NULL,
  `account_type` varchar(100) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `upi_id` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_payment_settings`
--

INSERT INTO `vendor_payment_settings` (`id`, `created_by`, `account_name`, `bank_name`, `account_number`, `ifsc_code`, `account_type`, `branch`, `upi_id`, `qr_code`, `created_at`, `updated_at`) VALUES
(1, 'Vendor', 'Vendor', 'ABC', '64646464646456', 'nfjsn5456', 'Current', 'XYZ', 'ssihf@jafn', '1747582321_ganesh g.jpg', '2025-05-18 20:54:16', '2025-05-18 21:02:01');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_product`
--

CREATE TABLE `vendor_product` (
  `product_id` varchar(20) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `stock` int(10) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `reorder_point` int(10) NOT NULL,
  `status` enum('In stock','Low stock','Out of stock') NOT NULL,
  `product_of` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_product`
--

INSERT INTO `vendor_product` (`product_id`, `product_name`, `category`, `stock`, `unit`, `mrp`, `selling_price`, `reorder_point`, `status`, `product_of`, `created_at`, `updated_at`) VALUES
('ITEM-001', 'Havells Wire (1.5mm)', 'Wires and Cables', 6544, 'm', 500.00, 550.00, 66, 'In stock', 'Vendor', '2025-05-18 17:23:20', '2025-05-18 18:04:18'),
('ITEM-002', 'Ceiling Fan (48 inch)', 'Fans', 6565, 'unit', 1400.00, 1500.00, 50, 'In stock', 'Vendor', '2025-05-19 06:29:47', '2025-05-19 11:59:10');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_shipping_settings`
--

CREATE TABLE `vendor_shipping_settings` (
  `id` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `same_as_business` tinyint(1) DEFAULT 0,
  `shipping_address` text DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_pincode` varchar(20) DEFAULT NULL,
  `free_shipping` tinyint(1) DEFAULT 0,
  `free_shipping_threshold` decimal(10,2) DEFAULT NULL,
  `same_day_processing` tinyint(1) DEFAULT 0,
  `processing_cutoff_time` time DEFAULT NULL,
  `shipping_partners` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_shipping_settings`
--

INSERT INTO `vendor_shipping_settings` (`id`, `created_by`, `same_as_business`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_pincode`, `free_shipping`, `free_shipping_threshold`, `same_day_processing`, `processing_cutoff_time`, `shipping_partners`, `created_at`, `updated_at`) VALUES
(1, 'Vendor', 0, 'ABCD', 'gwalior', 'Madhya Pradesh', '64636', 0, 0.00, 1, '20:34:00', 'blueDart,ownDelivery', '2025-05-18 20:34:22', '2025-05-18 20:34:37');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_stock_request`
--

CREATE TABLE `vendor_stock_request` (
  `request_id` varchar(20) NOT NULL,
  `tracking_id` varchar(20) NOT NULL,
  `delivery_id` varchar(20) DEFAULT NULL,
  `date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `recieved_date` date DEFAULT NULL,
  `request_to` varchar(50) NOT NULL,
  `shop_name` varchar(50) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(10) NOT NULL,
  `location` varchar(100) NOT NULL,
  `requested_by` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `recieved_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_stock_request`
--

INSERT INTO `vendor_stock_request` (`request_id`, `tracking_id`, `delivery_id`, `date`, `delivery_date`, `recieved_date`, `request_to`, `shop_name`, `item_name`, `category`, `quantity`, `location`, `requested_by`, `status`, `recieved_by`) VALUES
('RQST-001', 'TRCK-001', NULL, '2025-05-19', NULL, NULL, 'Factory', 'ABC', 'Ceiling Fan (48 inch)', 'Fans', 35, 'XYZ', 'Vendor', 'Ordered', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_user_profiles`
--

CREATE TABLE `vendor_user_profiles` (
  `id` int(11) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_user_profiles`
--

INSERT INTO `vendor_user_profiles` (`id`, `created_by`, `first_name`, `last_name`, `email`, `phone`, `position`, `created_at`, `updated_at`) VALUES
(1, 'Vendor', 'ABC', 'XYZ', 'vendor@unnati.com', '01234567890', 'Owner', '2025-05-18 20:15:34', '2025-05-18 20:15:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `admin_billing_settings`
--
ALTER TABLE `admin_billing_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_business_details`
--
ALTER TABLE `admin_business_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_inventory_settings`
--
ALTER TABLE `admin_inventory_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_production_settings`
--
ALTER TABLE `admin_production_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_workers_settings`
--
ALTER TABLE `admin_workers_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `factory_orders`
--
ALTER TABLE `factory_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_code` (`order_code`);

--
-- Indexes for table `factory_stock`
--
ALTER TABLE `factory_stock`
  ADD PRIMARY KEY (`stock_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`Sales_Id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`Purchase_Id`);

--
-- Indexes for table `retail_invetory`
--
ALTER TABLE `retail_invetory`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `retail_store_cash`
--
ALTER TABLE `retail_store_cash`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `retail_store_orders`
--
ALTER TABLE `retail_store_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `retail_store_stock_request`
--
ALTER TABLE `retail_store_stock_request`
  ADD PRIMARY KEY (`tracking_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`Sales_Id`);

--
-- Indexes for table `store_after_sales_settings`
--
ALTER TABLE `store_after_sales_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_hardware_settings`
--
ALTER TABLE `store_hardware_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_inventory_settings`
--
ALTER TABLE `store_inventory_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_service_requests`
--
ALTER TABLE `store_service_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_settings_general`
--
ALTER TABLE `store_settings_general`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_warranty_claims`
--
ALTER TABLE `store_warranty_claims`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warranty_number` (`warranty_number`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`Supplier_ID`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`Transaction_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `user_management`
--
ALTER TABLE `user_management`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `vendor_business_profiles`
--
ALTER TABLE `vendor_business_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `created_by` (`created_by`);

--
-- Indexes for table `vendor_payment_settings`
--
ALTER TABLE `vendor_payment_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_shipping_settings`
--
ALTER TABLE `vendor_shipping_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_stock_request`
--
ALTER TABLE `vendor_stock_request`
  ADD PRIMARY KEY (`tracking_id`);

--
-- Indexes for table `vendor_user_profiles`
--
ALTER TABLE `vendor_user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_business_details`
--
ALTER TABLE `admin_business_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `factory_orders`
--
ALTER TABLE `factory_orders`
  MODIFY `order_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `factory_stock`
--
ALTER TABLE `factory_stock`
  MODIFY `stock_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `retail_store_cash`
--
ALTER TABLE `retail_store_cash`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `store_after_sales_settings`
--
ALTER TABLE `store_after_sales_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_hardware_settings`
--
ALTER TABLE `store_hardware_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_inventory_settings`
--
ALTER TABLE `store_inventory_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_service_requests`
--
ALTER TABLE `store_service_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_settings_general`
--
ALTER TABLE `store_settings_general`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_warranty_claims`
--
ALTER TABLE `store_warranty_claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendor_business_profiles`
--
ALTER TABLE `vendor_business_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_payment_settings`
--
ALTER TABLE `vendor_payment_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_shipping_settings`
--
ALTER TABLE `vendor_shipping_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_user_profiles`
--
ALTER TABLE `vendor_user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
