SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `invenpro_new` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `invenpro_new`;

CREATE TABLE `audit_log` (
  `id` bigint(20) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`record_id`)),
  `action_type` enum('INSERT','UPDATE','DELETE','LOGIN','LOGOUT') NOT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`changes`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`metadata`)),
  `changed_by` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `branch` (
  `id` int(11) NOT NULL,
  `branch_code` varchar(20) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `branch_product` (
  `branch_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `reorder_level` decimal(10,3) NOT NULL DEFAULT 0.000,
  `reorder_quantity` decimal(10,3) NOT NULL DEFAULT 0.000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `points` decimal(10,2) DEFAULT 0.00,
  `last_points_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer_return` (
  `id` int(11) NOT NULL,
  `return_number` varchar(50) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer_return_item` (
  `id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `sale_item_id` int(11) NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `discount` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_date` timestamp NULL DEFAULT NULL,
  `is_combinable` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `discount_condition` (
  `id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `condition_type` enum('min_quantity','min_amount','time_of_day','day_of_week','loyalty_points') NOT NULL,
  `condition_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`condition_value`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `discount_usage` (
  `id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `loyalty_transaction` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `points_amount` decimal(10,2) NOT NULL,
  `transaction_type` enum('earn','redeem') NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','error','success') NOT NULL DEFAULT 'info',
  `priority` enum('low','normal','high') NOT NULL DEFAULT 'normal',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permission` (
  `id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permission_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `product_batch` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `batch_code` varchar(50) NOT NULL,
  `manufactured_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `initial_quantity` decimal(10,3) NOT NULL,
  `current_quantity` decimal(10,3) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `product_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `purchase_order` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `order_date` date NOT NULL DEFAULT current_timestamp(),
  `expected_date` date DEFAULT NULL,
  `status` enum('pending','open','completed','canceled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DELIMITER $$
CREATE TRIGGER `purchase_order_after_insert` AFTER INSERT ON `purchase_order` FOR EACH ROW BEGIN
    DECLARE meta_data JSON;
    
    -- Build basic metadata JSON
    SET meta_data = JSON_OBJECT(
        'operation', 'INSERT'
    );
    
    -- Add only core session variables if set
    IF @ip_address IS NOT NULL THEN
        SET meta_data = JSON_SET(meta_data, '$.ip_address', @ip_address);
    END IF;
    
    IF @user_agent IS NOT NULL THEN
        SET meta_data = JSON_SET(meta_data, '$.user_agent', @user_agent);
    END IF;

    -- Insert audit record
    INSERT INTO audit_log (
        table_name,
        record_id,
        action_type,
        changes,
        metadata,
        changed_by,
        branch_id
    )
    VALUES (
        'purchase_order',
        JSON_OBJECT('id', NEW.id),
        'INSERT',
        JSON_OBJECT(
            'reference', NEW.reference,
            'branch_id', NEW.branch_id,
            'supplier_id', NEW.supplier_id,
            'order_date', NEW.order_date,
            'expected_date', NEW.expected_date,
            'status', NEW.status,
            'total_amount', NEW.total_amount,
            'notes', NEW.notes,
            'created_by', NEW.created_by,
            'created_at', NEW.created_at
        ),
        meta_data,
        @user_id,
        @branch_id
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `purchase_order_after_update` AFTER UPDATE ON `purchase_order` FOR EACH ROW BEGIN
    -- Initialize JSON variables
    DECLARE changes JSON;
    DECLARE meta_data JSON;
    
    -- Initialize with empty objects
    SET changes = JSON_OBJECT();
    SET meta_data = JSON_OBJECT('operation', 'UPDATE');
    
    -- Only track fields that actually changed
    IF NOT(OLD.reference <=> NEW.reference) THEN
        SET changes = JSON_SET(changes, '$.reference', JSON_OBJECT('old', OLD.reference, 'new', NEW.reference));
    END IF;
    
    IF NOT(OLD.branch_id <=> NEW.branch_id) THEN
        SET changes = JSON_SET(changes, '$.branch_id', JSON_OBJECT('old', OLD.branch_id, 'new', NEW.branch_id));
    END IF;
    
    IF NOT(OLD.supplier_id <=> NEW.supplier_id) THEN
        SET changes = JSON_SET(changes, '$.supplier_id', JSON_OBJECT('old', OLD.supplier_id, 'new', NEW.supplier_id));
    END IF;
    
    IF NOT(OLD.order_date <=> NEW.order_date) THEN
        SET changes = JSON_SET(changes, '$.order_date', JSON_OBJECT('old', OLD.order_date, 'new', NEW.order_date));
    END IF;
    
    IF NOT(OLD.expected_date <=> NEW.expected_date) THEN
        SET changes = JSON_SET(changes, '$.expected_date', JSON_OBJECT('old', OLD.expected_date, 'new', NEW.expected_date));
    END IF;
    
    IF NOT(OLD.status <=> NEW.status) THEN
        SET changes = JSON_SET(changes, '$.status', JSON_OBJECT('old', OLD.status, 'new', NEW.status));
    END IF;
    
    IF NOT(OLD.total_amount <=> NEW.total_amount) THEN
        SET changes = JSON_SET(changes, '$.total_amount', JSON_OBJECT('old', OLD.total_amount, 'new', NEW.total_amount));
    END IF;
    
    IF NOT(OLD.notes <=> NEW.notes) THEN
        SET changes = JSON_SET(changes, '$.notes', JSON_OBJECT('old', OLD.notes, 'new', NEW.notes));
    END IF;
    
    -- Add core session variables
    IF @ip_address IS NOT NULL THEN
        SET meta_data = JSON_SET(meta_data, '$.ip_address', @ip_address);
    END IF;
    
    IF @user_agent IS NOT NULL THEN
        SET meta_data = JSON_SET(meta_data, '$.user_agent', @user_agent);
    END IF;
    
    -- Only insert audit log if there were changes
    IF JSON_LENGTH(changes) > 0 THEN
        INSERT INTO audit_log (
            table_name,
            record_id,
            action_type,
            changes,
            metadata,
            changed_by,
            branch_id
        )
        VALUES (
            'purchase_order',
            JSON_OBJECT('id', NEW.id),
            'UPDATE',
            changes,
            meta_data,
            @user_id,
            @branch_id
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `purchase_order_hard_delete` BEFORE DELETE ON `purchase_order` FOR EACH ROW BEGIN
    DECLARE meta_data JSON;
    
    -- Build metadata JSON
    SET meta_data = JSON_OBJECT(
        'operation', 'HARD_DELETE'
    );
    
    -- Add core session variables
    IF @ip_address IS NOT NULL THEN
        SET meta_data = JSON_SET(meta_data, '$.ip_address', @ip_address);
    END IF;
    
    IF @user_agent IS NOT NULL THEN
        SET meta_data = JSON_SET(meta_data, '$.user_agent', @user_agent);
    END IF;

    INSERT INTO audit_log (
        table_name,
        record_id,
        action_type,
        changes,
        metadata,
        changed_by,
        branch_id
    )
    VALUES (
        'purchase_order',
        JSON_OBJECT('id', OLD.id),
        'DELETE',
        JSON_OBJECT(
            'reference', OLD.reference,
            'branch_id', OLD.branch_id,
            'supplier_id', OLD.supplier_id,
            'order_date', OLD.order_date,
            'expected_date', OLD.expected_date,
            'status', OLD.status,
            'total_amount', OLD.total_amount,
            'notes', OLD.notes,
            'created_by', OLD.created_by,
            'created_at', OLD.created_at,
            'deleted_at', OLD.deleted_at
        ),
        meta_data,
        @user_id,
        @branch_id
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `purchase_order_soft_delete` AFTER UPDATE ON `purchase_order` FOR EACH ROW BEGIN
    DECLARE meta_data JSON;
    
    -- Only trigger for soft deletes (when deleted_at changes from NULL to a timestamp)
    IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
        -- Build metadata JSON
        SET meta_data = JSON_OBJECT(
            'operation', 'SOFT_DELETE'
        );
        
        -- Add core session variables
        IF @ip_address IS NOT NULL THEN
            SET meta_data = JSON_SET(meta_data, '$.ip_address', @ip_address);
        END IF;
        
        IF @user_agent IS NOT NULL THEN
            SET meta_data = JSON_SET(meta_data, '$.user_agent', @user_agent);
        END IF;

        INSERT INTO audit_log (
            table_name,
            record_id,
            action_type,
            changes,
            metadata,
            changed_by,
            branch_id
        )
        VALUES (
            'purchase_order',
            JSON_OBJECT('id', NEW.id),
            'DELETE',
            JSON_OBJECT(
                'reference', OLD.reference,
                'branch_id', OLD.branch_id,
                'supplier_id', OLD.supplier_id,
                'order_date', OLD.order_date,
                'expected_date', OLD.expected_date,
                'status', OLD.status,
                'total_amount', OLD.total_amount,
                'notes', OLD.notes,
                'deleted_at', NEW.deleted_at
            ),
            meta_data,
        @user_id,
        @branch_id
        );
    END IF;
END
$$
DELIMITER ;

CREATE TABLE `purchase_order_action` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `action` enum('create','update','approve','cancel','receive','delete','complete') NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `purchase_order_item` (
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_qty` decimal(10,3) NOT NULL,
  `received_qty` decimal(10,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_by` int(11) DEFAULT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `sale` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card') NOT NULL,
  `status` enum('pending','completed','cancelled','refunded') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `sale_item` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `discount` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `supplier_product` (
  `supplier_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `is_preferred_supplier` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `supplier_return` (
  `id` int(11) NOT NULL,
  `return_number` varchar(50) NOT NULL,
  `po_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `supplier_return_item` (
  `id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `unit` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(20) NOT NULL,
  `unit_symbol` varchar(10) NOT NULL,
  `is_int` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `display_name` varchar(100) GENERATED ALWAYS AS (concat(`first_name`,' ',`last_name`)) STORED,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `force_profile_setup` tinyint(1) DEFAULT 1,
  `role_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `failed_login_attempts` tinyint(4) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `last_failed_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `branch_id` (`branch_id`);

ALTER TABLE `branch`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_code` (`branch_code`);

ALTER TABLE `branch_product`
  ADD PRIMARY KEY (`branch_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`),
  ADD KEY `parent_category_id` (`parent_id`);

ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `customer_return`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_number` (`return_number`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `customer_return_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_id` (`return_id`),
  ADD KEY `sale_item_id` (`sale_item_id`);

ALTER TABLE `discount`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`);

ALTER TABLE `discount_condition`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_id` (`discount_id`);

ALTER TABLE `discount_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `loyalty_transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `permission_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `unit_id` (`unit_id`);

ALTER TABLE `product_batch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `po_id` (`po_id`);

ALTER TABLE `product_category`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `po_number` (`reference`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `purchase_order_action`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `purchase_order_item`
  ADD PRIMARY KEY (`po_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`),
  ADD KEY `granted_by` (`granted_by`);

ALTER TABLE `sale`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `sale_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `batch_id` (`batch_id`);

ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`);

ALTER TABLE `supplier_product`
  ADD PRIMARY KEY (`supplier_id`,`product_id`,`branch_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `branch_id` (`branch_id`);

ALTER TABLE `supplier_return`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_number` (`return_number`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `supplier_return_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_id` (`return_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `batch_id` (`batch_id`);

ALTER TABLE `unit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_name` (`unit_name`),
  ADD UNIQUE KEY `unit_symbol` (`unit_symbol`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `branch_id` (`branch_id`);


ALTER TABLE `audit_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_return`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_return_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `discount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `discount_condition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `discount_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `loyalty_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `permission_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_batch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `purchase_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `purchase_order_action`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sale_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `supplier_return`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `supplier_return_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`changed_by`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `audit_log_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);

ALTER TABLE `branch_product`
  ADD CONSTRAINT `branch_product_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  ADD CONSTRAINT `branch_product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

ALTER TABLE `category`
  ADD CONSTRAINT `category_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `category` (`id`);

ALTER TABLE `customer_return`
  ADD CONSTRAINT `customer_return_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sale` (`id`),
  ADD CONSTRAINT `customer_return_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

ALTER TABLE `customer_return_item`
  ADD CONSTRAINT `customer_return_item_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `customer_return` (`id`),
  ADD CONSTRAINT `customer_return_item_ibfk_2` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_item` (`id`);

ALTER TABLE `discount`
  ADD CONSTRAINT `discount_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);

ALTER TABLE `discount_condition`
  ADD CONSTRAINT `discount_condition_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discount` (`id`) ON DELETE CASCADE;

ALTER TABLE `discount_usage`
  ADD CONSTRAINT `discount_usage_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discount` (`id`),
  ADD CONSTRAINT `discount_usage_ibfk_3` FOREIGN KEY (`sale_id`) REFERENCES `sale` (`id`),
  ADD CONSTRAINT `discount_usage_ibfk_4` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

ALTER TABLE `loyalty_transaction`
  ADD CONSTRAINT `loyalty_transaction_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `permission`
  ADD CONSTRAINT `permission_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `permission_category` (`id`);

ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`);

ALTER TABLE `product_batch`
  ADD CONSTRAINT `product_batch_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `product_batch_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  ADD CONSTRAINT `product_batch_ibfk_3` FOREIGN KEY (`po_id`) REFERENCES `purchase_order` (`id`);

ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

ALTER TABLE `purchase_order`
  ADD CONSTRAINT `purchase_order_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  ADD CONSTRAINT `purchase_order_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  ADD CONSTRAINT `purchase_order_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

ALTER TABLE `purchase_order_action`
  ADD CONSTRAINT `purchase_order_action_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_order` (`id`),
  ADD CONSTRAINT `purchase_order_action_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

ALTER TABLE `purchase_order_item`
  ADD CONSTRAINT `purchase_order_item_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_order` (`id`),
  ADD CONSTRAINT `purchase_order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_ibfk_3` FOREIGN KEY (`granted_by`) REFERENCES `user` (`id`);

ALTER TABLE `sale`
  ADD CONSTRAINT `sale_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  ADD CONSTRAINT `sale_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
  ADD CONSTRAINT `sale_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

ALTER TABLE `sale_item`
  ADD CONSTRAINT `sale_item_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sale` (`id`),
  ADD CONSTRAINT `sale_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `sale_item_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `product_batch` (`id`);

ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);

ALTER TABLE `supplier_product`
  ADD CONSTRAINT `supplier_product_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  ADD CONSTRAINT `supplier_product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `supplier_product_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);

ALTER TABLE `supplier_return`
  ADD CONSTRAINT `supplier_return_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_order` (`id`),
  ADD CONSTRAINT `supplier_return_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  ADD CONSTRAINT `supplier_return_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

ALTER TABLE `supplier_return_item`
  ADD CONSTRAINT `supplier_return_item_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `supplier_return` (`id`),
  ADD CONSTRAINT `supplier_return_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `supplier_return_item_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `product_batch` (`id`);

ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
