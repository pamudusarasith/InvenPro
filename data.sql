-- Sample data for role table
INSERT INTO role (id, role_name, description, created_at) VALUES
(1, 'System Admin', 'Has full access to all system features and configuration', NOW()),
(2, 'Branch Manager', 'Manages operations for a specific branch', NOW()),
(3, 'Inventory Manager', 'Manages inventory, stock levels, and orders', NOW()),
(4, 'Administrative Staff', 'Handles administrative tasks and reporting', NOW()),
(5, 'Cashier', 'Operates point of sale and handles customer transactions', NOW());

-- Sample data for user table (Sri Lankan context)
INSERT INTO user (id, first_name, last_name, email, password, force_profile_setup, role_id, branch_id, created_at) VALUES
-- System Admins (access to all branches)
(1, 'Ashan', 'Perera', 'ashan@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 1, 1, NOW()),
(2, 'Dinesh', 'Fernando', 'dinesh@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 1, 1, NOW()),

-- Branch Managers (one for each branch)
(3, 'Sampath', 'Gunawardena', 'sampath@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 1, NOW()),
(4, 'Priyanthi', 'Silva', 'priyanthi@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 2, NOW()),
(5, 'Lakshman', 'Bandara', 'lakshman@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 3, NOW()),
(6, 'Kamala', 'Wijesinghe', 'kamala@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 4, NOW()),
(7, 'Prasanna', 'Jayawardena', 'prasanna@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 5, NOW()),
(8, 'Chaminda', 'Samaraweera', 'chaminda@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 6, NOW()),
(9, 'Dilini', 'Amarasekara', 'dilini@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 7, NOW()),
(10, 'Gamini', 'Ratnayake', 'gamini@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 8, NOW()),
(11, 'Malini', 'Kumaratunga', 'malini@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 9, NOW()),
(12, 'Rajitha', 'Dissanayake', 'rajitha@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 2, 10, NOW()),

-- Inventory Managers
(13, 'Chandana', 'Liyanage', 'chandana@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 1, NOW()),
(14, 'Indika', 'Karunatilake', 'indika@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 2, NOW()),
(15, 'Tharanga', 'Ranasinghe', 'tharanga@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 3, NOW()),
(16, 'Nisansala', 'Rathnayaka', 'nisansala@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 4, NOW()),
(17, 'Sunimal', 'Karunasena', 'sunimal@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 5, NOW()),
(18, 'Hiruni', 'Peris', 'hiruni@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 6, NOW()),
(19, 'Kapila', 'Kumara', 'kapila@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 7, NOW()),
(20, 'Sarath', 'Weerasinghe', 'sarath@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 8, NOW()),
(21, 'Yamuna', 'Seneviratne', 'yamuna@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 9, NOW()),
(22, 'Ruwan', 'Wickremasinghe', 'ruwan@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 3, 10, NOW()),

-- Administrative Staff
(23, 'Hasitha', 'Weerasinghe', 'hasitha@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 1, NOW()),
(24, 'Dinusha', 'Gunathilaka', 'dinusha@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 1, NOW()),
(25, 'Mahesh', 'Pathirana', 'mahesh@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 2, NOW()),
(26, 'Sanduni', 'Attanayake', 'sanduni@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 2, NOW()),
(27, 'Nuwan', 'Siriwardena', 'nuwan@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 3, NOW()),
(28, 'Lakmini', 'Wickramasinghe', 'lakmini@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 3, NOW()),
(29, 'Ishara', 'Nanayakkara', 'ishara@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 4, NOW()),
(30, 'Buddhika', 'Jayasuriya', 'buddhika@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 4, NOW()),
(31, 'Achini', 'Kulasooriya', 'achini@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 5, NOW()),
(32, 'Chathura', 'Senanayake', 'chathura@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 4, 5, NOW()),

-- Cashiers
(33, 'Roshan', 'Nanayakkara', 'roshan@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 1, NOW()),
(34, 'Nadeesha', 'Senaratne', 'nadeesha@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 1, NOW()),
(35, 'Kasun', 'Premadasa', 'kasun@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 2, NOW()),
(36, 'Amali', 'Fonseka', 'amali@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 2, NOW()),
(37, 'Lahiru', 'Mendis', 'lahiru@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 3, NOW()),
(38, 'Dilrukshi', 'Hewage', 'dilrukshi@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 3, NOW()),
(39, 'Thisara', 'Dharmasena', 'thisara@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 4, NOW()),
(40, 'Sewwandi', 'Perera', 'sewwandi@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 4, NOW()),
(41, 'Gihan', 'Kodituwakku', 'gihan@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 5, NOW()),
(42, 'Madhavi', 'Gunasekara', 'madhavi@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 5, NOW()),
(43, 'Naveen', 'Ekanayake', 'naveen@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 6, NOW()),
(44, 'Tharushi', 'Rajapaksa', 'tharushi@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 6, NOW()),
(45, 'Shehan', 'Soysa', 'shehan@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 7, NOW()),
(46, 'Dilhani', 'Gunawardana', 'dilhani@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 7, NOW()),
(47, 'Thilina', 'Herath', 'thilina@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 8, NOW()),
(48, 'Sachini', 'Jayasekara', 'sachini@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 8, NOW()),
(49, 'Harsha', 'Withanage', 'harsha@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 9, NOW()),
(50, 'Ayesha', 'Ariyaratne', 'ayesha@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 9, NOW()),
(51, 'Dilan', 'Vithanage', 'dilan@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 10, NOW()),
(52, 'Nimesha', 'Weerakoon', 'nimesha@invenpro.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 5, 10, NOW());

-- Sample data for unit table
INSERT INTO unit (id, unit_name, unit_symbol, is_int, description, created_at) VALUES
(1, 'Piece', 'pc', 1, 'Individual item', NOW()),
(2, 'Kilogram', 'kg', 0, 'Weight in kilograms', NOW()),
(3, 'Gram', 'g', 0, 'Weight in grams', NOW()),
(4, 'Liter', 'l', 0, 'Volume in liters', NOW()),
(5, 'Milliliter', 'ml', 0, 'Volume in milliliters', NOW()),
(6, 'Box', 'box', 1, 'Box containing multiple items', NOW()),
(7, 'Packet', 'pkt', 1, 'Packet of items', NOW()),
(8, 'Dozen', 'doz', 1, 'Twelve items', NOW()),
(9, 'Meter', 'm', 0, 'Length in meters', NOW()),
(10, 'Centimeter', 'cm', 0, 'Length in centimeters', NOW()),
(11, 'Set', 'set', 1, 'Set of items', NOW()),
(12, 'Pair', 'pr', 1, 'Pair of items', NOW()),
(13, 'Carton', 'ctn', 1, 'Carton containing multiple boxes', NOW()),
(14, 'Roll', 'roll', 1, 'Roll of material', NOW()),
(15, 'Bundle', 'bdl', 1, 'Bundle of items', NOW());

INSERT INTO branch (id, branch_code, branch_name, address, phone, email, created_at) VALUES
(1, 'CMB001', 'Colombo Main Branch', '123 Galle Road, Colombo 03, Western Province, Sri Lanka', '+94112345678', 'colombo@invenpro.lk', NOW()),
(2, 'KND002', 'Kandy Branch', '45 Peradeniya Road, Kandy, Central Province, Sri Lanka', '+94812345679', 'kandy@invenpro.lk', NOW()),
(3, 'GLL003', 'Galle Branch', '78 Matara Road, Galle, Southern Province, Sri Lanka', '+94912345680', 'galle@invenpro.lk', NOW()),
(4, 'JFN004', 'Jaffna Branch', '12 Stanley Road, Jaffna, Northern Province, Sri Lanka', '+94212345681', 'jaffna@invenpro.lk', NOW()),
(5, 'KRN005', 'Kurunegala Branch', '56 Negombo Road, Kurunegala, North Western Province, Sri Lanka', '+94372345682', 'kurunegala@invenpro.lk', NOW()),
(6, 'ANR006', 'Anuradhapura Branch', '34 Mihintale Road, Anuradhapura, North Central Province, Sri Lanka', '+94582345683', 'anuradhapura@invenpro.lk', NOW()),
(7, 'BTR007', 'Batticaloa Branch', '89 Trincomalee Road, Batticaloa, Eastern Province, Sri Lanka', '+94652345684', 'batticaloa@invenpro.lk', NOW()),
(8, 'RAT008', 'Ratnapura Branch', '21 Main Street, Ratnapura, Sabaragamuwa Province, Sri Lanka', '+94452345685', 'ratnapura@invenpro.lk', NOW()),
(9, 'TRN009', 'Trincomalee Branch', '67 Main Road, Trincomalee, Eastern Province, Sri Lanka', '+94652678901', 'trincomalee@invenpro.lk', NOW()),
(10, 'MAT010', 'Matara Branch', '101 Beach Road, Matara, Southern Province, Sri Lanka', '+94912233445', 'matara@invenpro.lk', NOW());

-- Sample data for category table (Sri Lankan context)
INSERT INTO category (id, category_name, description, parent_id, created_at) VALUES
(1, 'Beverages', 'Soft drinks, juices, and bottled water', NULL, NOW()),
(2, 'Snacks', 'Chips, biscuits, and other snacks', NULL, NOW()),
(3, 'Dairy', 'Milk, cheese, and other dairy products', NULL, NOW()),
(4, 'Bakery', 'Bread, buns, and bakery items', NULL, NOW()),
(5, 'Personal Care', 'Shampoo, soap, and personal hygiene products', NULL, NOW()),
(6, 'Household', 'Cleaning and household items', NULL, NOW()),
(7, 'Rice & Grains', 'Rice, wheat, and other grains', NULL, NOW()),
(8, 'Spices', 'Sri Lankan spices and condiments', NULL, NOW()),
(9, 'Vegetables', 'Fresh vegetables', NULL, NOW()),
(10, 'Fruits', 'Fresh fruits', NULL, NOW()),
(11, 'Frozen Foods', 'Frozen meat, fish, and vegetables', NULL, NOW()),
(12, 'Canned Foods', 'Canned fish, fruits, and vegetables', NULL, NOW()),
(13, 'Baby Care', 'Baby food and care products', NULL, NOW()),
(14, 'Stationery', 'Office and school stationery', NULL, NOW()),
(15, 'Electronics', 'Small household electronics', NULL, NOW()),
(16, 'Tea', 'Sri Lankan tea varieties', 1, NOW()),
(17, 'Coffee', 'Coffee and related products', 1, NOW()),
(18, 'Biscuits', 'Sweet and savory biscuits', 2, NOW()),
(19, 'Chocolates', 'Chocolate bars and candies', 2, NOW()),
(20, 'Soap', 'Bath and washing soaps', 5, NOW()),
(21, 'Soft Drinks', 'Carbonated soft drinks', 1, NOW()),
(22, 'Juices', 'Fruit juices', 1, NOW()),
(23, 'Bottled Water', 'Packaged drinking water', 1, NOW()),
(24, 'Energy Drinks', 'Energy and sports drinks', 1, NOW()),
(25, 'Flavoured Milk', 'Flavoured milk beverages', 1, NOW()),
(26, 'Chips', 'Potato and other chips', 2, NOW()),
(27, 'Nuts', 'Packaged nuts', 2, NOW()),
(28, 'Sweets', 'Traditional and modern sweets', 2, NOW()),
(29, 'Popcorn', 'Packaged popcorn', 2, NOW()),
(30, 'Murukku', 'Sri Lankan murukku and snacks', 2, NOW()),
(31, 'Fresh Milk', 'Fresh milk products', 3, NOW()),
(32, 'Yogurt', 'Yogurt and curd', 3, NOW()),
(33, 'Butter', 'Butter and margarine', 3, NOW()),
(34, 'Ice Cream', 'Ice cream and frozen desserts', 3, NOW()),
(35, 'Cakes', 'Cakes and pastries', 4, NOW()),
(36, 'Roti', 'Sri Lankan roti', 4, NOW()),
(37, 'Patties', 'Short eats and patties', 4, NOW()),
(38, 'Toothpaste', 'Toothpaste and oral care', 5, NOW()),
(39, 'Face Wash', 'Face wash and cleansers', 5, NOW()),
(40, 'Hand Sanitizer', 'Hand sanitizers', 5, NOW()),
(41, 'Detergents', 'Laundry detergents', 6, NOW()),
(42, 'Cleaners', 'Household cleaning products', 6, NOW()),
(43, 'Air Fresheners', 'Room and car air fresheners', 6, NOW()),
(44, 'Paper Products', 'Tissues and paper towels', 6, NOW()),
(45, 'Dishwash Liquid', 'Dishwashing liquids', 6, NOW()),
(46, 'White Rice', 'White rice varieties', 7, NOW()),
(47, 'Red Rice', 'Red rice varieties', 7, NOW()),
(48, 'Basmati', 'Basmati and imported rice', 7, NOW()),
(49, 'Wheat Flour', 'Wheat flour and atta', 7, NOW()),
(50, 'Noodles', 'Instant and regular noodles', 7, NOW()),
(51, 'String Hoppers', 'String hopper flour', 7, NOW()),
(52, 'Curry Powder', 'Curry powder blends', 8, NOW()),
(53, 'Pepper', 'Whole and ground pepper', 8, NOW()),
(54, 'Chili', 'Chili powder and flakes', 8, NOW()),
(55, 'Turmeric', 'Turmeric powder', 8, NOW()),
(56, 'Mustard', 'Mustard seeds and powder', 8, NOW()),
(57, 'Cardamom', 'Cardamom pods and powder', 8, NOW()),
(58, 'Cloves', 'Whole and ground cloves', 8, NOW()),
(59, 'Leafy Greens', 'Leafy green vegetables', 9, NOW()),
(60, 'Root Vegetables', 'Potatoes, carrots, etc.', 9, NOW()),
(61, 'Gourds', 'Gourd family vegetables', 9, NOW()),
(62, 'Legumes', 'Beans and lentils', 9, NOW()),
(63, 'Exotic Vegetables', 'Imported and rare vegetables', 9, NOW()),
(64, 'Citrus', 'Oranges, lemons, etc.', 10, NOW()),
(65, 'Bananas', 'Banana varieties', 10, NOW()),
(66, 'Mangoes', 'Mango varieties', 10, NOW()),
(67, 'Papaya', 'Papaya and related fruits', 10, NOW()),
(68, 'Pineapple', 'Pineapple and related fruits', 10, NOW()),
(69, 'Watermelon', 'Watermelon and melons', 10, NOW()),
(70, 'Frozen Fish', 'Frozen fish and seafood', 11, NOW()),
(71, 'Frozen Chicken', 'Frozen chicken products', 11, NOW()),
(72, 'Frozen Vegetables', 'Frozen vegetables', 11, NOW()),
(73, 'Frozen Paratha', 'Frozen paratha and roti', 11, NOW()),
(74, 'Canned Fish', 'Canned fish and seafood', 12, NOW()),
(75, 'Canned Fruits', 'Canned fruits', 12, NOW()),
(76, 'Canned Vegetables', 'Canned vegetables', 12, NOW()),
(77, 'Canned Milk', 'Canned and condensed milk', 12, NOW());

-- Sample data for customer table (Sri Lankan context)
INSERT INTO customer (id, first_name, last_name, phone, email, address, points, last_points_update, created_at) VALUES
(1, 'Nimal', 'Perera', '+94771234567', 'nimalp@gmail.com', '23 Main Road, Colombo 05, Western Province, Sri Lanka', 150.00, '2025-04-10 10:15:00', NOW()),
(2, 'Kumari', 'Fernando', '+94772345678', 'kumari.f@yahoo.com', '78 Temple Road, Kandy, Central Province, Sri Lanka', 275.50, '2025-04-15 14:30:00', NOW()),
(3, 'Sunil', 'Jayawardena', '+94773456789', 'sunilj@hotmail.com', '45 Beach Road, Galle, Southern Province, Sri Lanka', 320.25, '2025-04-12 09:45:00', NOW()),
(4, 'Malini', 'De Silva', '+94774567890', 'malini.desilva@gmail.com', '32 Main Street, Jaffna, Northern Province, Sri Lanka', 450.75, '2025-04-20 16:20:00', NOW()),
(5, 'Kamal', 'Gunaratne', '+94775678901', 'kamalg@gmail.com', '56 Flower Road, Kurunegala, North Western Province, Sri Lanka', 185.00, '2025-04-18 11:30:00', NOW()),
(6, 'Dilini', 'Bandara', '+94776789012', 'dilini.b@yahoo.com', '89 Temple Lane, Anuradhapura, North Central Province, Sri Lanka', 210.50, '2025-04-22 13:45:00', NOW()),
(7, 'Rohan', 'Weerasinghe', '+94777890123', 'rohanw@gmail.com', '67 Lake Road, Batticaloa, Eastern Province, Sri Lanka', 125.75, '2025-04-19 10:05:00', NOW()),
(8, 'Chamari', 'Peris', '+94778901234', 'chamari.peris@yahoo.com', '34 Hill Street, Ratnapura, Sabaragamuwa Province, Sri Lanka', 190.25, '2025-04-23 15:30:00', NOW()),
(9, 'Lasith', 'Mendis', '+94779012345', 'lasithm@gmail.com', '12 Sea View Road, Trincomalee, Eastern Province, Sri Lanka', 315.00, '2025-04-21 12:15:00', NOW()),
(10, 'Nilmini', 'Ratnayake', '+94770123456', 'nilmini.r@hotmail.com', '78 Beach Side, Matara, Southern Province, Sri Lanka', 275.50, '2025-04-11 09:20:00', NOW()),
(11, 'Pradeep', 'Silva', '+94761234567', 'pradeep.silva@gmail.com', '45 Lotus Lane, Colombo 06, Western Province, Sri Lanka', 135.75, '2025-04-24 14:10:00', NOW()),
(12, 'Anusha', 'Gunasekara', '+94762345678', 'anusha.g@yahoo.com', '67 Market Road, Kandy, Central Province, Sri Lanka', 220.00, '2025-04-17 11:25:00', NOW()),
(13, 'Dinesh', 'Rajapakse', '+94763456789', 'dineshr@gmail.com', '23 Palm Avenue, Galle, Southern Province, Sri Lanka', 170.50, '2025-04-13 16:40:00', NOW()),
(14, 'Thilini', 'Fonseka', '+94764567890', 'thilinif@yahoo.com', '56 Hospital Road, Jaffna, Northern Province, Sri Lanka', 290.25, '2025-04-16 10:35:00', NOW()),
(15, 'Ruwan', 'Wickramasinghe', '+94765678901', 'ruwanw@gmail.com', '34 Rice Mill Road, Kurunegala, North Western Province, Sri Lanka', 310.00, '2025-04-20 13:15:00', NOW()),
(16, 'Geethika', 'Seneviratne', '+94766789012', 'geethika.s@hotmail.com', '78 Heritage Lane, Anuradhapura, North Central Province, Sri Lanka', 175.25, '2025-04-19 15:50:00', NOW()),
(17, 'Mahesh', 'Pathirana', '+94767890123', 'maheshp@gmail.com', '45 Lagoon View, Batticaloa, Eastern Province, Sri Lanka', 240.75, '2025-04-21 09:30:00', NOW()),
(18, 'Udari', 'Dissanayake', '+94768901234', 'udari.d@yahoo.com', '23 Gem City Road, Ratnapura, Sabaragamuwa Province, Sri Lanka', 185.50, '2025-04-22 12:40:00', NOW()),
(19, 'Chathura', 'Herath', '+94769012345', 'chathura.h@gmail.com', '56 Navy Road, Trincomalee, Eastern Province, Sri Lanka', 220.00, '2025-04-18 14:25:00', NOW()),
(20, 'Nimali', 'Amarasekara', '+94760123456', 'nimali.a@yahoo.com', '89 Beach Road, Matara, Southern Province, Sri Lanka', 295.25, '2025-04-14 11:10:00', NOW()),
(21, 'Ajith', 'Kumara', '+94751234567', 'ajithk@gmail.com', '67 Park Street, Colombo 07, Western Province, Sri Lanka', 160.50, '2025-04-23 16:35:00', NOW()),
(22, 'Deepika', 'Jayasinghe', '+94752345678', 'deepika.j@yahoo.com', '34 Tea Estate Road, Nuwara Eliya, Central Province, Sri Lanka', 345.00, '2025-04-15 10:20:00', NOW()),
(23, 'Saman', 'Wijekoon', '+94753456789', 'samanw@hotmail.com', '78 Southern Highway, Matara, Southern Province, Sri Lanka', 130.75, '2025-04-17 13:05:00', NOW()),
(24, 'Nadeesha', 'Ranasinghe', '+94754567890', 'nadeesha.r@gmail.com', '45 Point Pedro Road, Jaffna, Northern Province, Sri Lanka', 265.25, '2025-04-16 15:40:00', NOW()),
(25, 'Lalith', 'Samaraweera', '+94755678901', 'laliths@yahoo.com', '23 Coconut Estate, Kuliyapitiya, North Western Province, Sri Lanka', 210.00, '2025-04-13 09:15:00', NOW()),
(26, 'Iresha', 'Karunatilaka', '+94756789012', 'iresha.k@gmail.com', '56 Ancient City Road, Polonnaruwa, North Central Province, Sri Lanka', 180.50, '2025-04-24 12:30:00', NOW()),
(27, 'Prasad', 'Nanayakkara', '+94757890123', 'prasadn@yahoo.com', '89 Lagoon Road, Batticaloa, Eastern Province, Sri Lanka', 290.75, '2025-04-11 14:45:00', NOW()),
(28, 'Sanduni', 'Gamlath', '+94758901234', 'sanduni.g@gmail.com', '34 Gem Mine Road, Ratnapura, Sabaragamuwa Province, Sri Lanka', 245.25, '2025-04-19 16:10:00', NOW()),
(29, 'Nuwan', 'Rajapaksa', '+94759012345', 'nuwanr@hotmail.com', '78 Harbor View, Trincomalee, Eastern Province, Sri Lanka', 205.00, '2025-04-20 10:25:00', NOW()),
(30, 'Hashini', 'Gunathilake', '+94750123456', 'hashini.g@yahoo.com', '45 Lighthouse Road, Dondra, Southern Province, Sri Lanka', 325.50, '2025-04-14 13:35:00', NOW()),
(31, 'Buddhika', 'Alwis', '+94781234567', 'buddhika.a@gmail.com', '67 Colombo Road, Negombo, Western Province, Sri Lanka', 190.75, '2025-04-12 15:20:00', NOW()),
(32, 'Sewwandi', 'Senanayake', '+94782345678', 'sewwandi.s@yahoo.com', '34 Victoria Park Road, Kandy, Central Province, Sri Lanka', 280.00, '2025-04-18 09:50:00', NOW()),
(33, 'Gayan', 'Wijethunga', '+94783456789', 'gayanw@gmail.com', '23 Lighthouse Street, Galle Fort, Southern Province, Sri Lanka', 215.25, '2025-04-22 14:05:00', NOW()),
(34, 'Hiruni', 'Kumarasinghe', '+94784567890', 'hiruni.k@hotmail.com', '78 Elephant Pass Road, Jaffna, Northern Province, Sri Lanka', 340.50, '2025-04-15 16:30:00', NOW()),
(35, 'Tharanga', 'Ekanayake', '+94785678901', 'tharangae@yahoo.com', '45 Rice Mill Junction, Kurunegala, North Western Province, Sri Lanka', 195.75, '2025-04-10 12:50:00', NOW()),
(36, 'Sachini', 'Dharmawardena', '+94786789012', 'sachini.d@gmail.com', '56 Sacred City Road, Anuradhapura, North Central Province, Sri Lanka', 270.00, '2025-04-13 11:15:00', NOW()),
(37, 'Lahiru', 'Rathnayaka', '+94787890123', 'lahirur@yahoo.com', '89 Bay View Road, Trincomalee, Eastern Province, Sri Lanka', 155.50, '2025-04-24 09:40:00', NOW()),
(38, 'Shalini', 'Warnakulasuriya', '+94788901234', 'shalini.w@gmail.com', '34 Waterfall Road, Ratnapura, Sabaragamuwa Province, Sri Lanka', 300.25, '2025-04-21 16:05:00', NOW()),
(39, 'Kasun', 'Liyanage', '+94789012345', 'kasunl@hotmail.com', '78 Lagoon Road, Batticaloa, Eastern Province, Sri Lanka', 225.75, '2025-04-23 10:55:00', NOW()),
(40, 'Dulanjali', 'Vithanage', '+94780123456', 'dulanjali.v@yahoo.com', '45 Beach Road, Matara, Southern Province, Sri Lanka', 370.00, '2025-04-17 14:20:00', NOW()),
(41, 'Chaminda', 'Weerakoon', '+94771234568', 'chaminda.w@gmail.com', '112 Galle Face Road, Colombo 03, Western Province, Sri Lanka', 285.50, '2025-04-18 09:15:00', NOW()),
(42, 'Eshani', 'Jayasooriya', '+94772345679', 'eshani.j@yahoo.com', '45 Temple Square, Kandy, Central Province, Sri Lanka', 165.25, '2025-04-13 11:30:00', NOW()),
(43, 'Asela', 'Gunawardena', '+94773456790', 'aselag@hotmail.com', '78 Lighthouse Road, Galle, Southern Province, Sri Lanka', 390.50, '2025-04-24 14:45:00', NOW()),
(44, 'Wasanthi', 'Chandrasiri', '+94774567891', 'wasanthi.c@gmail.com', '23 Church Road, Jaffna, Northern Province, Sri Lanka', 210.75, '2025-04-15 10:20:00', NOW()),
(45, 'Upul', 'Kariyawasam', '+94775678902', 'upulk@yahoo.com', '67 Dambulla Road, Kurunegala, North Western Province, Sri Lanka', 145.00, '2025-04-19 15:35:00', NOW()),
(46, 'Nelum', 'Abeysekara', '+94776789013', 'nelum.a@gmail.com', '34 Ancient City Circle, Anuradhapura, North Central Province, Sri Lanka', 325.25, '2025-04-21 12:50:00', NOW()),
(47, 'Manjula', 'Kulathunga', '+94777890124', 'manjula.k@hotmail.com', '56 Coast Road, Batticaloa, Eastern Province, Sri Lanka', 180.50, '2025-04-16 09:05:00', NOW()),
(48, 'Roshini', 'Wickremaratne', '+94778901235', 'roshini.w@gmail.com', '78 Sapphire Street, Ratnapura, Sabaragamuwa Province, Sri Lanka', 255.00, '2025-04-23 14:20:00', NOW()),
(49, 'Harindra', 'Fonseka', '+94779012346', 'harindraf@yahoo.com', '45 Navy Circle, Trincomalee, Eastern Province, Sri Lanka', 195.75, '2025-04-17 11:35:00', NOW()),
(50, 'Kalani', 'Siriwardena', '+94770123457', 'kalani.s@gmail.com', '23 Coconut Grove, Matara, Southern Province, Sri Lanka', 310.25, '2025-04-22 15:50:00', NOW()),
(51, 'Jagath', 'Peiris', '+94761234568', 'jagathp@gmail.com', '89 Independence Avenue, Colombo 07, Western Province, Sri Lanka', 175.00, '2025-04-10 10:05:00', NOW()),
(52, 'Damayanthi', 'Gunatilleke', '+94762345679', 'damayanthi.g@yahoo.com', '56 Kandy Lake Road, Kandy, Central Province, Sri Lanka', 290.50, '2025-04-20 13:20:00', NOW()),
(53, 'Hemantha', 'Samarawickrama', '+94763456790', 'hemanthas@hotmail.com', '34 Ocean View, Galle, Southern Province, Sri Lanka', 235.25, '2025-04-14 16:35:00', NOW()),
(54, 'Lakshmi', 'Weerasekera', '+94764567891', 'lakshmi.w@gmail.com', '67 Point Pedro Lane, Jaffna, Northern Province, Sri Lanka', 375.75, '2025-04-24 12:50:00', NOW()),
(55, 'Mohan', 'Abeynayake', '+94765678902', 'mohana@yahoo.com', '23 Balangoda Road, Kurunegala, North Western Province, Sri Lanka', 220.00, '2025-04-11 09:05:00', NOW()),
(56, 'Danushka', 'Thilakaratne', '+94766789013', 'danushka.t@gmail.com', '78 Sacred Road, Anuradhapura, North Central Province, Sri Lanka', 260.50, '2025-04-21 15:20:00', NOW()),
(57, 'Manel', 'Wickremesinghe', '+94767890124', 'manelw@hotmail.com', '45 East Coast Road, Batticaloa, Eastern Province, Sri Lanka', 185.25, '2025-04-18 10:35:00', NOW()),
(58, 'Sujeewa', 'Karunaratne', '+94768901235', 'sujeewa.k@gmail.com', '12 Gem Mine View, Ratnapura, Sabaragamuwa Province, Sri Lanka', 320.75, '2025-04-15 13:50:00', NOW()),
(59, 'Tharaka', 'Dassanayake', '+94769012346', 'tharakad@yahoo.com', '56 Bay Road, Trincomalee, Eastern Province, Sri Lanka', 245.00, '2025-04-22 16:05:00', NOW()),
(60, 'Anuradha', 'Jayatilaka', '+94760123457', 'anuradha.j@gmail.com', '34 Southern Beach, Matara, Southern Province, Sri Lanka', 170.50, '2025-04-19 11:20:00', NOW()),
(61, 'Sujith', 'De Costa', '+94751234568', 'sujithdc@gmail.com', '23 Parliament Road, Colombo 08, Western Province, Sri Lanka', 295.25, '2025-04-13 15:35:00', NOW()),
(62, 'Shyamali', 'Rajapaksha', '+94752345679', 'shyamali.r@yahoo.com', '67 Peradeniya Gardens, Kandy, Central Province, Sri Lanka', 215.75, '2025-04-20 12:50:00', NOW()),
(63, 'Indunil', 'Muthukumarana', '+94753456790', 'indunilm@gmail.com', '78 Fort Walk, Galle, Southern Province, Sri Lanka', 350.00, '2025-04-15 09:05:00', NOW()),
(64, 'Jeewani', 'Abeysinghe', '+94754567891', 'jeewani.a@yahoo.com', '45 Northern Circuit, Jaffna, Northern Province, Sri Lanka', 205.50, '2025-04-24 15:20:00', NOW()),
(65, 'Nuwan', 'Dharmasena', '+94755678902', 'nuwan.d@hotmail.com', '34 Hill Road, Kurunegala, North Western Province, Sri Lanka', 275.25, '2025-04-11 10:35:00', NOW()),
(66, 'Chathurika', 'Ranatunga', '+94756789013', 'chathurika.r@gmail.com', '23 Temple Road, Anuradhapura, North Central Province, Sri Lanka', 190.75, '2025-04-17 13:50:00', NOW()),
(67, 'Priyanka', 'Warusavitharana', '+94757890124', 'priyanka.w@yahoo.com', '89 Lagoon Side, Batticaloa, Eastern Province, Sri Lanka', 330.00, '2025-04-21 16:05:00', NOW()),
(68, 'Dilantha', 'Mahanama', '+94758901235', 'dilantha.m@gmail.com', '56 Diamond Valley, Ratnapura, Sabaragamuwa Province, Sri Lanka', 230.50, '2025-04-14 11:20:00', NOW()),
(69, 'Gayani', 'Karunathilaka', '+94759012346', 'gayani.k@yahoo.com', '78 Harbor Road, Trincomalee, Eastern Province, Sri Lanka', 285.25, '2025-04-19 14:35:00', NOW()),
(70, 'Ranjith', 'Samarakoon', '+94750123457', 'ranjith.s@gmail.com', '45 Palm Beach, Matara, Southern Province, Sri Lanka', 195.75, '2025-04-23 09:50:00', NOW());

-- Sample data for discount table (Sri Lankan Supermarket context)
INSERT INTO discount (id, branch_id, name, description, discount_type, application_method, value, start_date, end_date, is_combinable, is_active, created_at) VALUES
-- General store-wide discounts (applicable to all branches)
(1, NULL, 'Avurudu Sale', 'Special discount for Sinhala & Tamil New Year', 'percentage', 'regular', 10.00, '2025-04-10 00:00:00', '2025-04-20 23:59:59', 0, 1, NOW()),
(2, NULL, 'Loyal Customer Discount', 'Discount for customers with over 300 loyalty points', 'percentage', 'regular', 5.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 1, 1, NOW()),
(3, NULL, 'Senior Citizens Discount', 'Special discount for customers over 60 years', 'percentage', 'regular', 7.50, '2025-01-01 00:00:00', NULL, 0, 1, NOW()),

-- Category-specific promotions
(4, NULL, 'Daily Essentials Offer', 'Discount on rice, dhal, and essential groceries', 'percentage', 'regular', 3.50, '2025-04-01 00:00:00', '2025-05-31 23:59:59', 1, 1, NOW()),
(5, NULL, 'Fresh Produce Deal', 'Special discount on fresh fruits and vegetables', 'percentage', 'regular', 5.00, '2025-04-25 00:00:00', '2025-05-10 23:59:59', 1, 1, NOW()),
(6, NULL, 'Dairy Products Offer', 'Discount on all dairy products', 'percentage', 'regular', 4.00, '2025-05-01 00:00:00', '2025-05-15 23:59:59', 1, 1, NOW()),

-- Time-specific promotions
(7, NULL, 'Early Bird Special', 'Special discount for purchases between 8-10 AM', 'percentage', 'regular', 3.00, '2025-04-01 00:00:00', '2025-06-30 23:59:59', 0, 1, NOW()),
(8, NULL, 'Happy Hour Deal', 'Special discount for purchases between 2-4 PM', 'percentage', 'regular', 2.50, '2025-04-01 00:00:00', '2025-06-30 23:59:59', 0, 1, NOW()),
(9, NULL, 'Weekend Special', 'Additional discount on all purchases during weekends', 'percentage', 'regular', 2.00, '2025-04-01 00:00:00', '2025-06-30 23:59:59', 1, 1, NOW()),

-- Special promotions (all regular application method)
(10, NULL, 'First Purchase Discount', 'Special discount for first-time shoppers', 'fixed', 'regular', 300.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 0, 1, NOW()),
(11, NULL, 'Special Anniversary Offer', 'Store anniversary celebration discount', 'percentage', 'regular', 12.00, '2025-05-15 00:00:00', '2025-05-25 23:59:59', 0, 1, NOW()),
(12, NULL, 'Buy More Save More', 'Progressive discount for purchases over Rs. 10,000', 'percentage', 'regular', 7.00, '2025-04-01 00:00:00', '2025-06-30 23:59:59', 0, 1, NOW()),
(13, NULL, 'Customer Referral Bonus', 'Discount for referring new customers', 'fixed', 'regular', 250.00, '2025-04-15 00:00:00', '2025-07-15 23:59:59', 0, 1, NOW()),
(14, NULL, 'Customer Feedback Reward', 'Discount for completing customer satisfaction survey', 'fixed', 'regular', 100.00, '2025-04-01 00:00:00', '2025-12-31 23:59:59', 0, 1, NOW()),

-- Branch-specific discounts - Colombo Main Branch
(15, 1, 'Colombo City Special', 'Special promotion for Colombo branch only', 'percentage', 'regular', 5.00, '2025-05-01 00:00:00', '2025-05-31 23:59:59', 0, 1, NOW()),
(16, 1, 'Colombo Anniversary Sale', 'Celebrating 5 years in Colombo', 'percentage', 'regular', 8.00, '2025-06-15 00:00:00', '2025-06-30 23:59:59', 0, 1, NOW()),
(17, 1, 'Colombo Weekday Deal', 'Monday to Thursday special in Colombo', 'fixed', 'regular', 200.00, '2025-04-01 00:00:00', '2025-05-31 23:59:59', 1, 1, NOW()),

-- Branch-specific discounts - Kandy Branch
(18, 2, 'Kandy Cultural Promotion', 'Special discount during Esala Perahera season', 'percentage', 'regular', 7.50, '2025-07-15 00:00:00', '2025-08-15 23:59:59', 0, 1, NOW()),
(19, 2, 'Kandy Rainy Day Special', 'Special discount during monsoon season', 'percentage', 'regular', 4.00, '2025-05-01 00:00:00', '2025-06-30 23:59:59', 1, 1, NOW()),

-- Branch-specific discounts - Galle Branch
(20, 3, 'Southern Coast Special', 'Special discount for beach area shoppers', 'percentage', 'regular', 6.00, '2025-05-01 00:00:00', '2025-09-30 23:59:59', 0, 1, NOW()),

-- Branch-specific discounts - Jaffna Branch
(21, 4, 'Northern Special', 'Exclusive offer for northern province customers', 'percentage', 'regular', 7.00, '2025-06-01 00:00:00', '2025-06-30 23:59:59', 0, 1, NOW()),
(22, 4, 'Jaffna Festival Special', 'Special discount during Nallur festival', 'percentage', 'regular', 10.00, '2025-08-01 00:00:00', '2025-08-25 23:59:59', 0, 1, NOW()),

-- Inventory clearance discounts
(23, NULL, 'Year-End Clearance Sale', 'Year-end inventory clearance sale', 'percentage', 'regular', 15.00, '2025-12-15 00:00:00', '2025-12-31 23:59:59', 0, 1, NOW()),
(24, NULL, 'Stock Clearance - Seasonal Items', 'Discount on seasonal items', 'percentage', 'regular', 20.00, '2025-06-25 00:00:00', '2025-07-10 23:59:59', 0, 1, NOW()),
(25, NULL, 'Short Expiry Items', 'Discount on items nearing expiration date', 'percentage', 'regular', 25.00, '2025-04-01 00:00:00', '2025-12-31 23:59:59', 0, 1, NOW()),

-- Membership tier discounts
(26, NULL, 'Bronze Member Discount', 'Discount for Bronze tier loyalty members', 'percentage', 'regular', 2.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 1, 1, NOW()),
(27, NULL, 'Silver Member Discount', 'Discount for Silver tier loyalty members', 'percentage', 'regular', 3.50, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 1, 1, NOW()),
(28, NULL, 'Gold Member Discount', 'Discount for Gold tier loyalty members', 'percentage', 'regular', 5.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 1, 1, NOW()),
(29, NULL, 'Platinum Member Discount', 'Discount for Platinum tier loyalty members', 'percentage', 'regular', 7.50, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 1, 1, NOW()),

-- Holiday and festival discounts
(30, NULL, 'Vesak Poya Special', 'Special discount for Vesak celebrations', 'percentage', 'regular', 6.50, '2025-05-10 00:00:00', '2025-05-15 23:59:59', 0, 1, NOW()),
(31, NULL, 'Christmas Holiday Offer', 'Special discount for Christmas season', 'percentage', 'regular', 8.00, '2025-12-15 00:00:00', '2025-12-26 23:59:59', 0, 1, NOW()),
(32, NULL, 'Ramadan Special', 'Special discount during Ramadan', 'percentage', 'regular', 5.50, '2025-03-01 00:00:00', '2025-03-31 23:59:59', 0, 1, NOW()),
(33, NULL, 'Independence Day Sale', 'Special discount for Sri Lanka Independence Day', 'percentage', 'regular', 7.40, '2025-02-01 00:00:00', '2025-02-07 23:59:59', 0, 1, NOW()),
(34, NULL, 'Poson Festival Discount', 'Special discount during Poson Poya festival', 'percentage', 'regular', 6.00, '2025-06-20 00:00:00', '2025-06-24 23:59:59', 0, 1, NOW()),
(35, NULL, 'Thai Pongal Discount', 'Special discount for Thai Pongal celebration', 'percentage', 'regular', 5.50, '2025-01-14 00:00:00', '2025-01-15 23:59:59', 0, 1, NOW()),

-- Bundle deals expressed as discounts
(36, NULL, 'Family Essentials Bundle', 'Discount on family essential bundles', 'fixed', 'regular', 350.00, '2025-05-01 00:00:00', '2025-06-30 23:59:59', 0, 1, NOW()),
(37, NULL, 'Breakfast Bundle Offer', 'Discount on breakfast item bundles', 'fixed', 'regular', 150.00, '2025-04-01 00:00:00', '2025-12-31 23:59:59', 0, 1, NOW()),
(38, NULL, 'Tea Time Bundle', 'Discount on tea and snacks bundle', 'fixed', 'regular', 120.00, '2025-04-01 00:00:00', '2025-07-31 23:59:59', 0, 1, NOW()),
(39, NULL, 'Sri Lankan Spices Bundle', 'Discount on traditional spices bundle', 'fixed', 'regular', 200.00, '2025-04-15 00:00:00', '2025-08-15 23:59:59', 0, 1, NOW()),

-- Payment method discounts
(40, NULL, 'Online Payment Discount', 'Additional discount for online payments', 'percentage', 'regular', 2.00, '2025-04-01 00:00:00', '2025-12-31 23:59:59', 1, 1, NOW()),
(41, NULL, 'Bank Card Promotion', 'Special discount for specific bank card holders', 'percentage', 'regular', 3.00, '2025-04-15 00:00:00', '2025-07-15 23:59:59', 0, 1, NOW()),
(42, NULL, 'Mobile Wallet Payment', 'Discount for payments via mobile wallet apps', 'percentage', 'regular', 2.50, '2025-04-01 00:00:00', '2025-09-30 23:59:59', 0, 1, NOW()),

-- Additional demographic discounts
(43, NULL, 'Flash Sale Monday', 'Special Monday-only flash sale discount', 'percentage', 'regular', 12.00, '2025-04-28 00:00:00', '2025-04-28 23:59:59', 0, 1, NOW()),
(44, NULL, 'School Supplies Discount', 'Special discount on school supplies', 'percentage', 'regular', 15.00, '2025-01-01 00:00:00', '2025-01-31 23:59:59', 0, 1, NOW()),
(45, NULL, 'Student ID Discount', 'Special discount for customers with student ID', 'percentage', 'regular', 5.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 0, 1, NOW()),
(46, NULL, 'Healthcare Workers Appreciation', 'Special discount for healthcare professionals', 'percentage', 'regular', 8.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 0, 1, NOW()),
(47, NULL, 'Teacher Appreciation Discount', 'Special discount for educators', 'percentage', 'regular', 7.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 0, 1, NOW()),

-- Inactive/past discounts (for historical purposes)
(48, NULL, 'Valentine\'s Day Special', 'Special discount for Valentine\'s Day', 'percentage', 'regular', 14.00, '2025-02-10 00:00:00', '2025-02-14 23:59:59', 0, 0, NOW()),
(49, NULL, 'Back to School 2025', 'Discount on stationery and school supplies', 'percentage', 'regular', 12.00, '2025-01-01 00:00:00', '2025-01-15 23:59:59', 0, 0, '2025-01-01 00:00:00'),
(50, 1, 'Grand Opening Special', 'Special discount for grand reopening after renovation', 'percentage', 'regular', 15.00, '2025-01-15 00:00:00', '2025-01-31 23:59:59', 0, 0, '2025-01-10 00:00:00');

-- Sample data for discount_condition table (Sri Lankan Supermarket context)
INSERT INTO discount_condition (id, discount_id, condition_type, condition_value, created_at) VALUES
-- Minimum purchase amount conditions
(1, 12, 'min_amount', '{"amount": 10000.00, "currency": "LKR"}', NOW()),
(2, 24, 'min_amount', '{"amount": 5000.00, "currency": "LKR"}', NOW()),
(3, 34, 'min_amount', '{"amount": 2500.00, "currency": "LKR"}', NOW()),
(4, 35, 'min_amount', '{"amount": 1500.00, "currency": "LKR"}', NOW()),
(5, 39, 'min_amount', '{"amount": 1000.00, "currency": "LKR"}', NOW()),

-- Minimum quantity conditions
(6, 4, 'min_quantity', '{"quantity": 5.0, "unit": "kg"}', NOW()),
(7, 5, 'min_quantity', '{"quantity": 3.0, "unit": "kg"}', NOW()),
(8, 6, 'min_quantity', '{"quantity": 2.0, "unit": "l"}', NOW()),
(9, 36, 'min_quantity', '{"quantity": 1.0, "unit": "bundle"}', NOW()),
(10, 37, 'min_quantity', '{"quantity": 1.0, "unit": "bundle"}', NOW()),
(11, 38, 'min_quantity', '{"quantity": 1.0, "unit": "bundle"}', NOW()),

-- Time of day conditions (Happy Hour and Early Bird)
(12, 7, 'time_of_day', '{"start_time": "08:00:00", "end_time": "10:00:00"}', NOW()),
(13, 8, 'time_of_day', '{"start_time": "14:00:00", "end_time": "16:00:00"}', NOW()),
(14, 17, 'time_of_day', '{"start_time": "10:00:00", "end_time": "17:00:00"}', NOW()),
(15, 19, 'time_of_day', '{"start_time": "18:00:00", "end_time": "21:00:00"}', NOW()),
(16, 20, 'time_of_day', '{"start_time": "09:00:00", "end_time": "12:00:00"}', NOW()),

-- Day of week conditions (Weekend Special and Weekday Deals)
(17, 9, 'day_of_week', '{"days": ["saturday", "sunday"]}', NOW()),
(18, 17, 'day_of_week', '{"days": ["monday", "tuesday", "wednesday", "thursday"]}', NOW()),
(19, 43, 'day_of_week', '{"days": ["monday"]}', NOW()),
(20, 20, 'day_of_week', '{"days": ["friday", "saturday", "sunday"]}', NOW()),
(21, 21, 'day_of_week', '{"days": ["tuesday", "wednesday"]}', NOW()),

-- Loyalty points conditions
(22, 2, 'loyalty_points', '{"min_points": 300.0}', NOW()),
(23, 26, 'loyalty_points', '{"min_points": 100.0, "max_points": 299.0}', NOW()),
(24, 27, 'loyalty_points', '{"min_points": 300.0, "max_points": 499.0}', NOW()),
(25, 28, 'loyalty_points', '{"min_points": 500.0, "max_points": 999.0}', NOW()),
(26, 29, 'loyalty_points', '{"min_points": 1000.0}', NOW()),

-- Special day discount conditions (min_amount + day_of_week)
(27, 30, 'min_amount', '{"amount": 2000.00, "currency": "LKR"}', NOW()),
(28, 30, 'day_of_week', '{"days": ["tuesday", "wednesday"]}', NOW()),
(29, 31, 'min_amount', '{"amount": 5000.00, "currency": "LKR"}', NOW()),
(30, 32, 'min_amount', '{"amount": 3000.00, "currency": "LKR"}', NOW()),
(31, 33, 'min_amount', '{"amount": 2500.00, "currency": "LKR"}', NOW()),

-- Payment method discount conditions
(32, 40, 'min_amount', '{"amount": 1000.00, "currency": "LKR"}', NOW()),
(33, 41, 'min_amount', '{"amount": 3000.00, "currency": "LKR"}', NOW()),
(34, 42, 'min_amount', '{"amount": 1500.00, "currency": "LKR"}', NOW()),

-- Student and Professional discount conditions
(35, 44, 'min_amount', '{"amount": 500.00, "currency": "LKR"}', NOW()),
(36, 45, 'min_amount', '{"amount": 1000.00, "currency": "LKR"}', NOW()),
(37, 46, 'min_amount', '{"amount": 1000.00, "currency": "LKR"}', NOW()),
(38, 47, 'min_amount', '{"amount": 1000.00, "currency": "LKR"}', NOW()),

-- Flash sale conditions (min_amount + time_of_day)
(39, 43, 'min_amount', '{"amount": 2000.00, "currency": "LKR"}', NOW()),
(40, 43, 'time_of_day', '{"start_time": "10:00:00", "end_time": "18:00:00"}', NOW());
