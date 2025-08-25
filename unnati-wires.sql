-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2025 at 11:44 AM
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
('CUST-001', 'Purvi', 'Retail', '5464646556', '2025-08-25', 'Shree Unnati', 'Shree Unnati'),
('CUST-002', 'Riyansh', 'Retail', '646464646', '2025-08-25', 'Raj', 'Raj');

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

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `id` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `addedBy` varchar(100) NOT NULL,
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

INSERT INTO `expenses` (`expense_id`, `id`, `date`, `category`, `addedBy`, `amount`, `vendor`, `status`, `method`, `created_at`, `updated_at`) VALUES
(5, 'EXP-2025-001', '2025-08-04', 'Utilities', 'ABCD', 308096.00, 'mno', 'In Stock', '', '2025-08-04 09:44:02', '2025-08-04 09:44:02'),
(6, 'EXP-2025-002', '2025-08-03', 'Utilities', 'ABCD', 456.00, 'mno', 'In Stock', '', '2025-08-04 09:47:21', '2025-08-04 09:47:21');

-- --------------------------------------------------------

--
-- Table structure for table `factory_billing_setting`
--

CREATE TABLE `factory_billing_setting` (
  `id` int(11) NOT NULL,
  `payment_terms` int(11) NOT NULL,
  `phone_number_general` varchar(10) NOT NULL,
  `tax_rate` int(11) NOT NULL,
  `downtime_tracking` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `factory_expenses`
--

CREATE TABLE `factory_expenses` (
  `id` varchar(10) NOT NULL,
  `description` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `addedBy` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `Payment_Method` enum('Digital payment','Cash','Payment gateway') NOT NULL,
  `Status` enum('Pending','Approved','Rejected') NOT NULL,
  `created_for` varchar(100) NOT NULL,
  `bankName` varchar(100) DEFAULT NULL,
  `accountNumber` varchar(50) DEFAULT NULL,
  `senderName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_expenses`
--

INSERT INTO `factory_expenses` (`id`, `description`, `category`, `addedBy`, `amount`, `date`, `Payment_Method`, `Status`, `created_for`, `bankName`, `accountNumber`, `senderName`) VALUES
('EXP-001', 'Water purifying machine 1000 LPH', 'Machine ', '', 283666.00, '2025-05-26', 'Cash', 'Approved', 'Narayanam Industries', NULL, NULL, ''),
('EXP-002', 'Web sealer with shrink machine SS belt W/T AC Drive motor', 'Machine ', '', 283666.00, '2025-05-25', 'Cash', 'Approved', 'Narayanam Industries', NULL, NULL, ''),
('EXP-004', 'sfadfd', 'Utilities', 'Raj', 666.00, '2025-07-11', 'Cash', 'Approved', 'Unnati', '', '', 'Aman'),
('EXP-005', 'afaf', 'Utilities', 'Raj', 100.00, '2025-07-10', 'Digital payment', '', 'Unnati', 'fddjydif', '56464646', ''),
('EXP-006', 'jfnsj', 'sdjnas', 'Aman', 3402.00, '2025-07-10', 'Cash', 'Pending', 'Unnati', '', '', 'Raj');

-- --------------------------------------------------------

--
-- Table structure for table `factory_general_settings`
--

CREATE TABLE `factory_general_settings` (
  `id` int(11) NOT NULL,
  `factory_name` varchar(100) NOT NULL,
  `address` varchar(500) NOT NULL,
  `number` int(15) NOT NULL,
  `manager` varchar(100) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_general_settings`
--

INSERT INTO `factory_general_settings` (`id`, `factory_name`, `address`, `number`, `manager`, `created_by`, `created_at`, `updated`) VALUES
(1, 'Shri unnati wire and cables', '  ', 2147483647, 'Sunil Tiwari', 'Unnati', '2025-06-15 06:48:03', '2025-06-15 06:48:03');

-- --------------------------------------------------------

--
-- Table structure for table `factory_inventory_setting`
--

CREATE TABLE `factory_inventory_setting` (
  `id` int(11) NOT NULL,
  `stock_buffer` int(11) NOT NULL,
  `fifo_method` tinyint(1) NOT NULL DEFAULT 0,
  `batch_tracking` tinyint(1) NOT NULL DEFAULT 0,
  `material_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `factory_product`
--

CREATE TABLE `factory_product` (
  `id` varchar(10) NOT NULL,
  `productName` varchar(50) NOT NULL,
  `category` varchar(20) NOT NULL,
  `raw_materials` longtext NOT NULL,
  `raw_material_total_cost` decimal(10,2) NOT NULL,
  `transport_charge` decimal(10,2) NOT NULL,
  `other_cost` decimal(10,2) NOT NULL,
  `product_total_cost` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `profitLoss` decimal(10,2) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `created_for` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_product`
--

INSERT INTO `factory_product` (`id`, `productName`, `category`, `raw_materials`, `raw_material_total_cost`, `transport_charge`, `other_cost`, `product_total_cost`, `mrp`, `selling_price`, `profitLoss`, `created_by`, `created_for`) VALUES
('PR-001', 'XYZ', 'Utilities', '[{\"id\":\"RM-006\",\"material\":\"bottel\",\"quantity\":1,\"cost\":\"5.00\",\"total\":5},{\"id\":\"RM-005\",\"material\":\"Cap black runchi 1.3\",\"quantity\":1,\"cost\":1,\"total\":1}]', 6.00, 0.20, 0.20, 6.40, 8.00, 7.00, 0.60, 'Unnati', 'Unnati'),
('PR-002', 'ABCD', 'Wires & Cables', '[{\"id\":\"RM-006\",\"material\":\"bottel\",\"quantity\":1,\"cost\":\"5.00\",\"total\":5}]', 59.00, 5.00, 2.00, 66.00, 100.00, 60.00, -6.00, 'Unnati', 'Unnati');

-- --------------------------------------------------------

--
-- Table structure for table `factory_production`
--

CREATE TABLE `factory_production` (
  `id` varchar(10) NOT NULL,
  `product` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Pending','Completed','Scheduled') NOT NULL,
  `created_for` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_production`
--

INSERT INTO `factory_production` (`id`, `product`, `quantity`, `unit`, `start_date`, `end_date`, `status`, `created_for`) VALUES
('PRD-002', 'sgd', 565, '0', '2025-08-20', '2025-08-21', 'Scheduled', 'Unnati'),
('PRD-003', 'sjd', 54, '0', '2025-08-20', '2025-08-21', 'Scheduled', 'Unnati'),
('PRD-004', 'isdfisd', 559, '0', '2025-08-20', '2025-08-21', 'Scheduled', 'Unnati'),
('PRD-005', 'shuis', 979, '0', '2025-08-20', '2025-08-21', 'Scheduled', 'Unnati');

-- --------------------------------------------------------

--
-- Table structure for table `factory_production_setting`
--

CREATE TABLE `factory_production_setting` (
  `id` int(11) NOT NULL,
  `capacity` varchar(10) NOT NULL,
  `efficiency` decimal(10,2) NOT NULL,
  `shift` float NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `factory_raw_material`
--

CREATE TABLE `factory_raw_material` (
  `id` varchar(10) NOT NULL,
  `material` varchar(50) NOT NULL,
  `category` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reorder_point` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `number` varchar(20) NOT NULL,
  `Status` enum('In Stock','Low Stock','Out Of Stock') NOT NULL,
  `primary_supplier` varchar(100) NOT NULL,
  `created_for` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_raw_material`
--

INSERT INTO `factory_raw_material` (`id`, `material`, `category`, `quantity`, `cost`, `amount`, `reorder_point`, `unit`, `number`, `Status`, `primary_supplier`, `created_for`) VALUES
('RM-001', '1 litre long round bottle', 'LONG ROUND BOTTLE', 4500, 0.00, 0.00, 0, 'Piece', '', 'In Stock', 'Hotels', 'Narayanam Industries'),
('RM-002', '1 Litre Oval round bottle', 'Oval Round', 4480, 0.00, 0.00, 0, 'Piece', '', 'In Stock', 'Hotel', 'Narayanam Industries'),
('RM-004', '1 Litre Pyramid bottle', 'Pyramid bottle', 1355, 0.00, 0.00, 0, 'Piece', '', 'In Stock', 'Hotel', 'Unnati'),
('RM-005', 'Cap black runchi 1.3', 'BLACK CAP', 15990, 0.00, 0.00, 0, 'Piece', '', 'In Stock', 'Hotel', 'Unnati'),
('RM-006', 'bottel', 'Utilities', 14, 5.00, 100.00, 2, 'Piece', '655656565', 'In Stock', 'jhfuyfiu', 'Unnati');

-- --------------------------------------------------------

--
-- Table structure for table `factory_stock`
--

CREATE TABLE `factory_stock` (
  `stock_id` varchar(20) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `current_quantity` int(11) NOT NULL,
  `sale_value_total` decimal(10,2) NOT NULL,
  `sale_value_piece` decimal(10,2) NOT NULL,
  `total_manufacturing_cost` decimal(10,2) NOT NULL,
  `manufacturing_cost_piece` decimal(10,2) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `previous_stock_value` decimal(10,2) NOT NULL,
  `avg_previous_sale` decimal(10,2) NOT NULL,
  `avg_new_sale` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `record_date` date NOT NULL DEFAULT curdate(),
  `created_for` varchar(100) NOT NULL,
  `created_by` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_stock`
--

INSERT INTO `factory_stock` (`stock_id`, `item_name`, `category`, `quantity`, `value`, `current_quantity`, `sale_value_total`, `sale_value_piece`, `total_manufacturing_cost`, `manufacturing_cost_piece`, `previous_stock`, `previous_stock_value`, `avg_previous_sale`, `avg_new_sale`, `status`, `record_date`, `created_for`, `created_by`) VALUES
('15', 'Web sealer with shrink machine SS belt W/T AC Drive motor', 'Machine ', 1, 283666.00, 0, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0.00, 0.00, 'In stock', '2025-06-06', 'Narayanam Industries', 'Narayanam Industries'),
('INV-002', 'XYZ', 'Utilities', 4, 20.00, 2, 20.00, 10.00, 12.80, 6.40, 0, 0.00, 0.00, 10.00, 'In stock', '2025-08-25', 'Unnati', 'Unnati'),
('INV-003', 'ABCD', 'Wires & Cables', 6, 240.00, 3, 240.00, 80.00, 198.00, 66.00, 0, 0.00, 0.00, 80.00, 'In stock', '2025-08-25', 'Unnati', 'Unnati');

-- --------------------------------------------------------

--
-- Table structure for table `factory_workers`
--

CREATE TABLE `factory_workers` (
  `id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `shift` enum('Morning','Evening','Night') NOT NULL,
  `created_for` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_workers`
--

INSERT INTO `factory_workers` (`id`, `name`, `department`, `role`, `shift`, `created_for`) VALUES
('WR-002', 'ajfnja', 'jkandf', 'andf', 'Morning', 'Unnati');

-- --------------------------------------------------------

--
-- Table structure for table `factory_workers_setting`
--

CREATE TABLE `factory_workers_setting` (
  `id` int(11) NOT NULL,
  `daily_capacity` int(11) NOT NULL,
  `target_efficiency` int(11) NOT NULL,
  `shift_duration` int(11) NOT NULL,
  `overtime_rate` varchar(10) NOT NULL,
  `downtime_tracking` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `product_id` varchar(10) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`Id`, `Product_Name`, `Category`, `Stock`, `Transaction_Type`, `Status`, `Supplier`, `product_id`, `Date`) VALUES
('TRX-001', 'xyz', 'Utilities', '664', 'Add Stock', 'In Stock', 'mno', 'P-001', '2025-08-25 09:32:51'),
('TRX-002', 'ABCD', 'Utilities', '664', 'Add Stock', 'In Stock', 'mno', 'P-002', '2025-08-25 09:32:51'),
('TRX-003', 'ABCD', 'Utilities', '2', 'Add Stock', 'In Stock', 'mno', 'P-002', '2025-08-25 09:32:51');

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
('INV-2025-001', 'Kingraj2101', 'with GST', '2025-08-20', '2025-08-21', '5', 'ABCD', '', '2', '100', '200.00', '', 200.00, 10.00, 210.00, 'SL-001', 'PAY-001', 'Digital payment', 'Unnati', 'Unnati', 'Pending'),
('INV-2025-002', 'Kingraj2101', 'with GST', '2025-08-20', '2025-08-21', '5', 'ABCD', '', '1', '50', '50.00', '', 50.00, 2.50, 52.50, 'SL-002', 'PAY-002', 'Digital payment', 'Unnati', 'Unnati', 'Pending'),
('INV-2025-003', 'Kingraj2101', 'with GST', '2025-08-20', '2025-08-21', '5', 'ABCD', '', '1', '50', '50.00', '', 50.00, 2.50, 52.50, 'SL-003', 'PAY-003', 'Digital payment', 'Unnati', 'Unnati', 'Pending'),
('INV-2025-004', 'Raj', 'with GST', '2025-08-20', '2025-08-21', '5', 'ABCD', '', '1', '50', '50.00', '', 50.00, 2.50, -52.50, 'SL-004', 'PAY-004', 'Digital payment', 'Unnati', 'Unnati', 'Refund'),
('INV-2025-005', 'Shree Unnati', 'with GST', '2025-08-20', '2025-08-21', '5', 'ABCD', '', '1', '50', '50.00', '', 50.00, 2.50, -52.50, 'SL-005', 'PAY-005', 'Cash', 'Unnati', 'Unnati', 'Refund'),
('INV-2025-001', 'Purvi', 'with GST', '2025-08-25', '2025-08-26', '5', 'Havells Wire (1.5mm)', '', '1', '66', '66.00', '', 66.00, 3.30, -69.30, 'SL-006', 'PAY-001', 'Digital payment', 'Shree Unnati', 'Shree Unnati', 'Refund'),
('INV-2025-002', 'Purvi', 'with GST', '2025-08-25', '2025-08-26', '5', 'Havells Wire (1.5mm)', '', '1', '66', '66.00', '', 66.00, 3.30, -69.30, 'SL-007', 'PAY-002', 'Payment gateway', 'Shree Unnati', 'Shree Unnati', 'Refund'),
('INV-2025-001', 'Riyansh', 'with GST', '2025-08-25', '2025-08-26', '5', 'Havells Wire (1.5mm)', '', '1', '646', '646.00', '', 646.00, 32.30, -678.30, 'SL-008', 'PAY-001', 'Digital payment', 'Raj', 'Raj', 'Refund');

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
('P-001', 464.00, 'xyz', 'Utilities', 5.00, 654.00, '2025-08-04 09:44:02', '2025-08-04 09:44:02', 664),
('P-002', 464.00, 'ABCD', 'Utilities', 5.00, 654.00, '2025-08-04 09:44:02', '2025-08-04 09:47:21', 666);

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
('PO-2025-001', '', 564646.00, '2025-06-10', 'sg', 465, 'Bank Transfer', 'Received'),
('PO-2025-002', '', 99999999.99, '2025-08-13', 'sdkvsm', 656, 'Bank Transfer', 'Received'),
('PO-2025-003', 'sbs', 6546464.00, '2025-07-16', 'skjgbd', 864, 'Bank Transfer', 'Received');

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
('ITEM-001', 'Havells Wire (1.5mm)', 'Wires & Cables', 45565, 'm', 66.00, 54, '2025-08-25', 'In stock', 'Shree Unnati');

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
('SUP001', 'sbs', 'Manufacturer', 'Wires, Switches', 35, 64546.00, 'Active'),
('SUP002', 'gdms', 'Distributor', 'Wires, Switches', 645, 64646.00, 'Active'),
('SUP003', 'vds', 'Distributor', 'Wires, Cables', 1, 54654.00, 'Active');

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
('kingraj2101@gmail.com', '4ffe23061091b237d236ad51958bcb7619bcd850de62fe484dfce4167a760db9', 'Store', 'Manager', '7fc3d1391448e5198f834e9bf41ded9c', 'Kingraj2101'),
('narayanam0016@gmail.com', '31ca01093dc4969126624438a78ef6e724e95c23f0d19c5ca5e6a183ea8e4b52', 'Factory', 'Owner', '5dc0112992c94b24ed3ff6a5361ecc33', 'Narayanam Industries'),
('prem47626@gmail.com', '39a106b2ee47c98aadde4f85ca63dd126ddf2797cce0cfa1d0fa01ed26b7cd60', 'Admin', 'Owner', '7bf778967f1221ce575b48c55a35bc67', 'Prem'),
('raj@gmail.com', 'd0c7ecaa4e1ecffac2660438e8fc68a5f5eebd8488496ca278052246d48cb700', 'Vendor', 'Owner', '40e572ca25f4537da4d3ef912fc0cef5', 'Raj'),
('shreeunnatitraders@gmail.com', '612d2fc96635ba673c54cf23ba74cd4704c2789b30fab66ec013d9bc3153afcc', 'Store', 'Owner', '6852f0ed1daef95a2db347d9dcf87bf9', 'Shree Unnati'),
('unnati@rindustry.com', '747954df14e45b61f2ffa8477c3650cf20c0dcb5888317854f364cf04fb1a869', 'Factory', 'Owner', '9b441100a94b57684df119a75be30d13', 'Unnati');

-- --------------------------------------------------------

--
-- Table structure for table `user_management`
--

CREATE TABLE `user_management` (
  `User_ID` varchar(20) NOT NULL,
  `User_Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Role` varchar(20) NOT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `Last_Login` datetime DEFAULT NULL,
  `Permission` text NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_management`
--

INSERT INTO `user_management` (`User_ID`, `User_Name`, `Email`, `Role`, `Status`, `Last_Login`, `Permission`, `Created_At`) VALUES
('USR-002', 'Narayanam Industries', 'narayanam0016@gmail.com', 'Owner', 'Active', NULL, '[\"Main Dashboard\",\"Billing Desk\",\"Accounting\",\"Inventory\",\"Expenses\",\"Factory Stock\",\"Retail Store\",\"Suppliers\",\"Reports\",\"Settings\",\"Delete\"]', '2025-05-24 09:16:57'),
('USR-003', 'Prem', 'prem47626@gmail.com', 'Owner', 'Active', NULL, '[\"Main Dashboard\",\"Billing Desk\",\"Accounting\",\"Inventory\",\"Expenses\",\"Factory Stock\",\"Retail Store\",\"After-Sell Service\",\"Suppliers\",\"Reports\",\"Settings\",\"Delete\"]', '2025-06-01 21:32:38'),
('USR-005', 'Shree Unnati', 'shreeunnatitraders@gmail.com', 'Owner', 'Active', NULL, '[\"Delete\"]', '2025-06-01 21:37:30'),
('USR-006', 'Unnati', 'unnati@rindustry.com', 'Owner', 'Active', NULL, '[\"Accounting\",\"Inventory\",\"Suppliers\", \"Delete\"]', '2025-06-01 21:42:23'),
('USR-007', 'Raj', 'raj@gmail.com', 'Owner', 'Active', NULL, '[\"Delete\"]', '2025-07-05 17:51:35');

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
('ITEM-001', 'Havells Wire (1.5mm)', 'Wires & Cables', 65, '45', 65.00, 646.00, 6, 'In stock', 'Raj', '2025-08-04 12:17:16', '2025-08-04 12:17:16');

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
-- Indexes for table `factory_billing_setting`
--
ALTER TABLE `factory_billing_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory_expenses`
--
ALTER TABLE `factory_expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory_general_settings`
--
ALTER TABLE `factory_general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory_inventory_setting`
--
ALTER TABLE `factory_inventory_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory_orders`
--
ALTER TABLE `factory_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_code` (`order_code`);

--
-- Indexes for table `factory_product`
--
ALTER TABLE `factory_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory_production`
--
ALTER TABLE `factory_production`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory_raw_material`
--
ALTER TABLE `factory_raw_material`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory_stock`
--
ALTER TABLE `factory_stock`
  ADD PRIMARY KEY (`stock_id`);

--
-- Indexes for table `factory_workers`
--
ALTER TABLE `factory_workers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory_workers_setting`
--
ALTER TABLE `factory_workers_setting`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `factory_billing_setting`
--
ALTER TABLE `factory_billing_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `factory_general_settings`
--
ALTER TABLE `factory_general_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `factory_inventory_setting`
--
ALTER TABLE `factory_inventory_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `factory_orders`
--
ALTER TABLE `factory_orders`
  MODIFY `order_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `factory_workers_setting`
--
ALTER TABLE `factory_workers_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
