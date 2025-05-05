-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 08:14 PM
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
-- Table structure for table `auto_bill`
--

CREATE TABLE `auto_bill` (
  `sales_return_id` varchar(20) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `tax_rate` enum('5','12','18','28') NOT NULL,
  `item` text NOT NULL,
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL
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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL
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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL
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
  `purchases` int(10) NOT NULL,
  `total_spent` decimal(10,2) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_Id`, `name`, `type`, `contact`, `purchases`, `total_spent`, `date`) VALUES
('CUST-000', 'abc', 'Contractor', '9012345678', 15, 105780.00, '2025-05-04'),
('CUST-001', 'Vikram Mehta', 'Wholesale', '9012345678', 15, 105780.00, '2025-05-04'),
('CUST-002', 'xyz', 'Retail', '1234567890', 25, 65652.00, '2025-05-05');

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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL
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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL
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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `customer_name`, `document_type`, `date`, `due_date`, `tax_rate`, `item_name`, `description`, `quantity`, `price`, `total`, `notes`, `subtotal`, `GST_amount`, `grand_total`) VALUES
('INV-2025-001', 'Customer A', '', '2025-04-27', '2025-04-28', '18', 'Product A,Product B', 'a,b', '1,1', '100,200', '100.00,200.00', 'testing', 300.00, 54.00, 354.00),
('INV-2025-002', 'Customer A', '', '2025-05-07', '2025-05-07', '12', 'Product B', '4', '54', '2', '108.00', 'af', 108.00, 12.96, 120.96),
('INV-2025-003', 'Customer C', '', '2025-05-07', '2025-05-15', '5', 'Product B', 'sa', '1', '500', '500.00', 'afjb', 500.00, 25.00, 525.00),
('INV-2025-004', 'Customer B', '', '2025-05-01', '2025-05-07', '18', 'Product B', 'd', '20', '50', '1000.00', 'asf', 1000.00, 180.00, 1180.00),
('INV-2025-005', 'Customer A', '', '2025-05-01', '2025-05-07', '12', 'Product B', 'af', '1', '23', '23.00', 'af', 23.00, 2.76, 25.76),
('INV-2025-006', 'Customer B', '', '2025-04-28', '2025-05-06', '28', 'Product B', 'a', '1', '5', '5.00', 'afs', 5.00, 1.40, 6.40),
('INV-2025-007', 'Customer C', '', '2025-05-09', '2025-05-06', '28', 'Product C', 'with', '1', '500', '500.00', 'testing', 500.00, 140.00, 640.00),
('INVWO-2025-001', 'Customer B', '', '2025-04-29', '2025-05-06', '', 'Product B', 's', '2', '445', '890.00', 'sfs', 890.00, 0.00, 1139.20),
('INVWO-2025-002', 'Customer C', '', '2025-05-01', '2025-05-08', '', 'Product B', 'wf', '1', '511', '511.00', 'sfs', 511.00, 0.00, 654.08),
('INVWO-2025-003', 'Customer B', 'without GST', '2025-05-09', '2025-05-15', '', 'Product C', 'add', '1', '250', '250.00', 'a', 250.00, 0.00, 250.00),
('INVWO-2025-004', 'Vikram Mehta', 'with GST', '2025-05-02', '2025-05-06', '12', 'Product A', 'aa', '1', '2', '2.00', 'aes', 2.00, 0.24, 2.24),
('INVWO-2025-005', 'abc', 'without GST', '2025-04-28', '2025-05-05', '18', 'Product B', '54', '1', '156', '156.00', 'vbc', 156.00, 0.00, 156.00);

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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL
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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL
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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL
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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL
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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotation`
--

INSERT INTO `quotation` (`invoice_id`, `customer_name`, `document_type`, `date`, `due_date`, `tax_rate`, `item_name`, `description`, `quantity`, `price`, `total`, `notes`, `subtotal`, `GST_amount`, `grand_total`) VALUES
('QT-2025-001', 'Vikram Mehta', 'with GST', '2025-04-29', '2025-05-06', '18', 'Product A', 'demo', '1', '500', '500.00', 'testing', 500.00, 90.00, 590.00);

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
  `description` text NOT NULL,
  `quantity` text NOT NULL,
  `price` text NOT NULL,
  `total` text NOT NULL,
  `notes` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `GST_amount` decimal(10,2) NOT NULL,
  `Grand_total` decimal(10,2) NOT NULL
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
('SUP001', 'aman', 'Distributor', 'Wires, Cables', 4, 1.00, 'Active');

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
  `salt` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`email`, `password`, `user_type`, `user_roll`, `salt`) VALUES
('admin@unnati.com', '366252a58be7185b1aa036e4a40f28e6cbbecc0e49bc8ac4e9a54a5c0211cef8', 'Admin', 'owner', '8c1cf323382d2c2e7a90218fe17d1810'),
('factory@unnati.com', '2018145d9952641d911298a538e888369a0693bbb9b864df8d0a959e0cb56220', 'Factory', 'owner', 'd6c44ef35c3938962752282e8649881f'),
('store@unnati.com', 'd1a2bfa517f8a05cac3c679aee130034113ce74d3e8ab652536a29b97e9a98b6', 'Store', 'owner', 'a4e542c5c0a5c1d313d112ccc364bc25'),
('vendor@unnati.com', '5cb2db83125d45af59746f7fae535411a727b12b9c255b347ba5407fd85903c3', 'Vendor', 'owner', 'bc1bd02f21cc2fa4694623a17a411ea7');

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
('USR-001', 'wqqwfasf', 'aman@gmail.comm', 'Store', 'Active', NULL, '[]', '2025-05-04 08:29:19'),
('USR-002', 'awdd', 'bajpeyaman16@gmail.com', 'Accountant', 'Active', NULL, '[]', '2025-05-04 08:30:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `counter_purchase`
--
ALTER TABLE `counter_purchase`
  ADD PRIMARY KEY (`sales_return_id`);

--
-- Indexes for table `credit_note`
--
ALTER TABLE `credit_note`
  ADD PRIMARY KEY (`sales_return_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_Id`);

--
-- Indexes for table `debit_note`
--
ALTER TABLE `debit_note`
  ADD PRIMARY KEY (`sales_return_id`);

--
-- Indexes for table `delivery_challan`
--
ALTER TABLE `delivery_challan`
  ADD PRIMARY KEY (`sales_return_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `payment_out`
--
ALTER TABLE `payment_out`
  ADD PRIMARY KEY (`sales_return_id`);

--
-- Indexes for table `proforma`
--
ALTER TABLE `proforma`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`Purchase_Id`);

--
-- Indexes for table `purchase_order_bill`
--
ALTER TABLE `purchase_order_bill`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `purchase_return`
--
ALTER TABLE `purchase_return`
  ADD PRIMARY KEY (`sales_return_id`);

--
-- Indexes for table `quotation`
--
ALTER TABLE `quotation`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`Sales_Id`);

--
-- Indexes for table `sales_return`
--
ALTER TABLE `sales_return`
  ADD PRIMARY KEY (`sales_return_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`Supplier_ID`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
