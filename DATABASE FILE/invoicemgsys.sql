SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create the database
CREATE DATABASE IF NOT EXISTS invoicemg;
USE invoicemg;

-- Create customers table
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `county` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `name_ship` varchar(255) NOT NULL,
  `address_1_ship` varchar(255) NOT NULL,
  `address_2_ship` varchar(255) NOT NULL,
  `town_ship` varchar(255) NOT NULL,
  `county_ship` varchar(255) NOT NULL,
  `postcode_ship` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Create invoices table
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice` varchar(255) NOT NULL,
  `custom_email` text NOT NULL,
  `invoice_date` varchar(255) NOT NULL,
  `invoice_due_date` varchar(255) NOT NULL,
  `subtotal` decimal(10,0) NOT NULL,
  `shipping` decimal(10,0) NOT NULL,
  `discount` decimal(10,0) NOT NULL,
  `vat` decimal(10,0) NOT NULL,
  `total` decimal(10,0) NOT NULL,
  `notes` text NOT NULL,
  `invoice_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Create invoice_items table
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice` varchar(255) NOT NULL,
  `product` text NOT NULL,
  `qty` int(11) NOT NULL,
  `price` varchar(255) NOT NULL,
  `discount` varchar(255) NOT NULL,
  `subtotal` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Create products table
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` text NOT NULL,
  `product_desc` text NOT NULL,
  `product_price` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Create store_customers table
CREATE TABLE IF NOT EXISTS `store_customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `county` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `name_ship` varchar(255) NOT NULL,
  `address_1_ship` varchar(255) NOT NULL,
  `address_2_ship` varchar(255) NOT NULL,
  `town_ship` varchar(255) NOT NULL,
  `county_ship` varchar(255) NOT NULL,
  `postcode_ship` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert data into customers table
INSERT INTO `customers` (`invoice`, `name`, `email`, `address_1`, `address_2`, `town`, `county`, `postcode`, `phone`, `name_ship`, `address_1_ship`, `address_2_ship`, `town_ship`, `county_ship`, `postcode_ship`) VALUES
('3', 'Anne B Ruch', 'anner@mail.com', '4039 Overlook Drive', '4039 Overlook Drive', 'Indianapolis', 'US', '46225', '1478500000', 'Anne B Ruch', '4039 Overlook Drive', '4039 Overlook Drive', 'Indianapolis', 'US', '46225'),
('4', 'Albert M Dunford', 'albd@mail.com', '1143 Kuhl Avenue', '1143 Kuhl Avenue', 'Norcross', 'US', '30092', '8520000010', 'Albert M Dunford', '1143 Kuhl Avenue', '1143 Kuhl Avenue', 'Norcross', 'US', '30092'),
('5', 'Anne B Ruch', 'anner@mail.com', '4039 Overlook Drive', '4039 Overlook Drive', 'Indianapolis', 'US', '46225', '1478500000', 'Anne B Ruch', '4039 Overlook Drive', '4039 Overlook Drive', 'Indianapolis', 'US', '46225'),
('6', 'Wendy Reilly', 'wendy@mail.com', '3605 Cost Avenue', '3605 Cost Avenue', 'Wharton', 'US', '77488', '3214444444', 'Wendy Reilly', '3605 Cost Avenue', '3605 Cost Avenue', 'Wharton', 'US', '77488'),
('7', 'Test Customer', 'testc@mail.com', '110 Test Address', '116 Test Address', 'Testown', 'TestCn', '00225', '7777777770', 'Test Customer', '110 Test Address', '116 Test Address', 'Testown', 'TestCn', '00225'),
('8', 'Demo User', 'demouser@mail.com', '115 Demo Address', '115 Demo Address', 'DemoTown', 'DemoCn', '00020', '7777777777', 'Demo User', '115 Demo Address', '115 Demo Address', 'DemoTown', 'DemoCn', '00020'),
('9', 'Wendy Reilly', 'wendy@mail.com', '3605 Cost Avenue', '3605 Cost Avenue', 'Wharton', 'US', '77488', '3214444444', 'Wendy Reilly', '3605 Cost Avenue', '3605 Cost Avenue', 'Wharton', 'US', '77488'),
('10', 'Rose Thompson', 'thompsonr@mail.com', '2374 Berkley Street', '2374 Berkley Street', 'Northampton', 'US', '01010', '7410000020', 'Rose Thompson', '2374 Berkley Street', '2374 Berkley Street', 'Northampton', 'US', '01010');

-- Insert data into invoices table
INSERT INTO `invoices` (`invoice`, `custom_email`, `invoice_date`, `invoice_due_date`, `subtotal`, `shipping`, `discount`, `vat`, `total`, `notes`, `invoice_type`, `status`) VALUES
('1', '', '12/11/2021', '14/11/2021', '523', '55', '6', '58', '636', 'Completed!', 'invoice', 'paid'),
('2', '', '12/11/2021', '13/11/2021', '395', '85', '4', '48', '528', 'none', 'invoice', 'paid'),
('3', '', '13/11/2021', '15/11/2021', '132', '65', '0', '20', '217', 'none', 'invoice', 'paid'),
('4', '', '13/11/2021', '17/11/2021', '270', '65', '3', '34', '369', '', 'invoice', 'open'),
('5', '', '13/11/2021', '17/11/2021', '405', '20', '3', '43', '468', 'none', 'invoice', 'open'),
('6', '', '13/11/2021', '18/11/2021', '534', '40', '7', '57', '631', '', 'invoice', 'open'),
('7', '', '13/11/2021', '16/11/2021', '600', '20', '4', '62', '682', 'Cleared Up!', 'invoice', 'paid'),
('8', '', '13/11/2021', '15/11/2021', '153', '20', '2', '17', '190', '', 'invoice', 'open'),
('9', '', '15/11/2021', '17/11/2021', '115', '25', '0', '14', '154', '', 'invoice', 'open'),
('10', '', '15/11/2021', '16/11/2021', '154', '30', '2', '18', '202', '', 'invoice', 'open');

-- Insert data into invoice_items table
INSERT INTO `invoice_items` (`invoice`, `product`, `qty`, `price`, `discount`, `subtotal`) VALUES
('5', 'Product One - This is a sample product one.', 12, '34', '3', '405.00'),
('4', 'Product Two - This is a sample product two.', 21, '13', '3', '270.00'),
('6', 'Product Four - This is a sample product four.', 5, '5', '2', '23.00'),
('6', 'Product Five - This is a sample product five.', 6, '86', '5', '511.00'),
('8', 'Product Seven - This is a sample product seven.', 5, '23', '0', '115.00'),
('8', 'Product Four - This is a sample product four.', 8, '5', '2', '38.00'),
('9', 'Product Seven - This is a sample product seven.', 5, '23', '0', '115.00'),
('10', 'Product Six - This is a sample product six.', 13, '12', '2', '154.00');

-- Insert data into products table
INSERT INTO `products` (`product_name`, `product_desc`, `product_price`) VALUES
('Product One', 'This is a sample product one.', '34'),
('Product Two', 'This is a sample product two.', '13'),
('Product Three', 'This is a sample product three.', '68'),
('Product Four', 'This is a sample product four.', '5'),
('Product Five', 'This is a sample product five.', '86'),
('Product Six', 'This is a sample product six.', '12'),
('Product Seven', 'This is a sample product seven.', '23'),
('Product Eight', 'This is a sample product eight.', '19');

-- Insert data into store_customers table
INSERT INTO `store_customers` (`name`, `email`, `address_1`, `address_2`, `town`, `county`, `postcode`, `phone`, `name_ship`, `address_1_ship`, `address_2_ship`, `town_ship`, `county_ship`, `postcode_ship`) VALUES
('Wendy Reilly', 'wendy@mail.com', '3605 Cost Avenue', '3605 Cost Avenue', 'Wharton', 'US', '77488', '3214444444', 'Wendy Reilly', '3605 Cost Avenue', '3605 Cost Avenue', 'Wharton', 'US', '77488'),
('Albert M Dunford', 'albd@mail.com', '1143 Kuhl Avenue', '1143 Kuhl Avenue', 'Norcross', 'US', '30092', '8520000010', 'Albert M Dunford', '1143 Kuhl Avenue', '1143 Kuhl Avenue', 'Norcross', 'US', '30092'),
('Anne B Ruch', 'anner@mail.com', '4039 Overlook Drive', '6939 Dt Drive', 'Indianapolis', 'US', '46225', '1478500000', 'Anne B Ruch', '4039 Overlook Drive', '6939 Dt Drive', 'Indianapolis', 'US', '46225'),
('Celeste Prather', 'celeste@mail.com', '421 Fincham Road', '421 Fincham Road', 'San Diego', 'US', '90000', '8021111111', 'Celeste Prather', '421 Fincham Road', '421 Fincham Road', 'San Diego', 'US', '90000'),
('Katharine Mayer', 'kathmay@mail.com', '508 Bernardo Street', '508 Bernardo Street', 'Tampa', 'US', '90000', '9014555500', 'Katharine Mayer', '508 Bernardo Street', '508 Bernardo Street', 'Tampa', 'US', '90000'),
('Rose Thompson', 'thompsonr@mail.com', '2374 Berkley Street', '2374 Berkley Street', 'Northampton', 'US', '01010', '7410000020', 'Rose Thompson', '2374 Berkley Street', '2374 Berkley Street', 'Northampton', 'US', '01010'),
('Ira Turner', 'iratur@mail.com', '1387 Pine Street', '1387 Pine Street', 'Pittsburgh', 'US', '10005', '7890002222', 'Ira Turner', '1387 Pine Street', '1387 Pine Street', 'Pittsburgh', 'US', '10005'),
('Richards', 'richards@mail.com', '311 Bchwood Drive', '311 Bchwood Drive', 'Bridgeville', 'US', '50005', '7410000014', 'Richards', '311 Bchwood Drive', '311 Bchwood Drive', 'Bridgeville', 'US', '50005'),
('Allan Deer', 'allande@mail.com', '1702 Modoc Alley', '1702 Modoc Alley', 'White Bird', 'US', '55550', '8520001450', 'Allan Deer', '1702 Modoc Alley', '1702 Modoc Alley', 'White Bird', 'US', '55550'),
('Demo User', 'demouser@mail.com', '115 Demo Address', '116 Demo Address', 'DemoTown', 'DemoCn', '00020', '7777777777', 'Demo User', '115 Demo Address', '116 Demo Address', 'DemoTown', 'DemoCn', '00020');

-- Insert data into users table
INSERT INTO `users` (`name`, `username`, `email`, `phone`, `password`) VALUES
('harsh', 'harsh', 'harsh@gmail.com', '8434691998', '81dc9bdb52d04dc20036dbd8313ed055'); 