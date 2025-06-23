-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 23, 2025 at 12:59 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u276201717_unnati_wires`
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
  `created_for` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_expenses`
--

INSERT INTO `factory_expenses` (`id`, `description`, `category`, `addedBy`, `amount`, `date`, `Payment_Method`, `Status`, `created_for`) VALUES
('EXP-001', 'Water purifying machine 1000 LPH', 'Machine ', '', 283666.00, '2025-05-26', 'Cash', 'Approved', 'Narayanam Industries'),
('EXP-002', 'Web sealer with shrink machine SS belt W/T AC Drive motor', 'Machine ', '', 283666.00, '2025-05-25', 'Cash', 'Approved', 'Narayanam Industries'),
('EXP-003', 'Liquid Bottle Filling Machine ', 'Machine', '', 283668.00, '2025-05-25', 'Cash', 'Approved', 'Narayanam Industries');

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
  `amount` decimal(10,2) NOT NULL,
  `reorder_point` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `Status` enum('In Stock','Low Stock','Out Of Stock') NOT NULL,
  `primary_supplier` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factory_raw_material`
--

INSERT INTO `factory_raw_material` (`id`, `material`, `category`, `quantity`, `amount`, `reorder_point`, `unit`, `Status`, `primary_supplier`) VALUES
('RM-001', '1 litre long round bottle', 'LONG ROUND BOTTLE', 4500, 0.00, 0, 'Piece', 'In Stock', 'Hotels'),
('RM-002', '1 Litre Oval round bottle', 'Oval Round', 4480, 0.00, 0, 'Piece', 'In Stock', 'Hotel'),
('RM-003', '1 Litre Oval Round bottle', 'Oval Round', 2610, 0.00, 0, 'Piece', 'In Stock', 'Hotel'),
('RM-004', '1 Litre Pyramid bottle', 'Pyramid bottle', 1365, 0.00, 0, 'Piece', 'In Stock', 'Hotel'),
('RM-005', 'Cap black runchi 1.3', 'BLACK CAP', 16000, 0.00, 0, 'Piece', 'In Stock', 'Hotel');

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
(15, 'Web sealer with shrink machine SS belt W/T AC Drive motor', 'Machine ', 1, 283666.00, 'In stock', '2025-06-06'),
(16, 'Water purifying machine 1000 LPH', 'Machine ', 1, 283666.00, 'In stock', '2025-06-06'),
(17, 'Liquid Bottle Filling Machine ', 'Machine ', 1, 283666.00, 'In stock', '2025-06-06');

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
  `product_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('USR-002', 'Narayanam Industries', 'narayanam0016@gmail.com', 'Owner', 'Active', NULL, '[\"Main Dashboard\",\"Billing Desk\",\"Accounting\",\"Inventory\",\"Expenses\",\"Factory Stock\",\"Retail Store\",\"Suppliers\",\"Reports\",\"Settings\"]', '2025-05-24 09:16:57'),
('USR-003', 'Prem', 'prem47626@gmail.com', 'Owner', 'Active', NULL, '[\"Main Dashboard\",\"Billing Desk\",\"Accounting\",\"Inventory\",\"Expenses\",\"Factory Stock\",\"Retail Store\",\"After-Sell Service\",\"Suppliers\",\"Reports\",\"Settings\"]', '2025-06-01 21:32:38'),
('USR-005', 'Shree Unnati', 'shreeunnatitraders@gmail.com', 'Owner', 'Active', NULL, '[]', '2025-06-01 21:37:30'),
('USR-006', 'Unnati', 'unnati@rindustry.com', 'Owner', 'Active', NULL, '[]', '2025-06-01 21:42:23');

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
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `factory_stock`
--
ALTER TABLE `factory_stock`
  MODIFY `stock_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
