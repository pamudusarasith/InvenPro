CREATE DATABASE invenpro_new;
USE invenpro_new;

CREATE TABLE branch (
    id INT PRIMARY KEY AUTO_INCREMENT,
    branch_code VARCHAR(20) NOT NULL UNIQUE,
    branch_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE permission (
    id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE role (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) DEFAULT NULL,
    last_name VARCHAR(50) DEFAULT NULL,
    display_name VARCHAR(100) GENERATED ALWAYS AS (CONCAT(first_name, ' ', last_name)) STORED,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    force_profile_setup BOOLEAN DEFAULT true,
    role_id INT NOT NULL,
    branch_id INT NOT NULL,
    is_locked BOOLEAN DEFAULT false,
    locked_until TIMESTAMP NULL,
    failed_login_attempts TINYINT DEFAULT 0,
    last_login TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    last_failed_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES role(id),
    FOREIGN KEY (branch_id) REFERENCES branch(id)
);

CREATE INDEX idx_user_auth ON user(email, deleted_at);
CREATE INDEX idx_user_role ON user(role_id);
CREATE INDEX idx_user_branch ON user(branch_id);

CREATE TABLE role_permission (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    granted_by INT,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES role(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permission(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES user(id)
);

CREATE TABLE audit_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    table_name VARCHAR(50) NOT NULL,
    record_id JSON NOT NULL,
    action_type ENUM('INSERT', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT') NOT NULL,
    changes JSON NOT NULL,
    metadata JSON NOT NULL,
    changed_by INT NOT NULL,
    branch_id INT NOT NULL,
    created_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    FOREIGN KEY (changed_by) REFERENCES user(id),
    FOREIGN KEY (branch_id) REFERENCES branch(id)
);

CREATE TABLE category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    parent_category_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (parent_category_id) REFERENCES category(id)
);

CREATE TABLE unit (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_name VARCHAR(20) NOT NULL UNIQUE,
    unit_symbol VARCHAR(10) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(50) UNIQUE,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    unit_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES unit(id)
);

CREATE TABLE product_category (
    product_id INT NOT NULL,
    category_id INT NOT NULL,
    is_primary BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE
);

CREATE TABLE branch_product (
    branch_id INT NOT NULL,
    product_id INT NOT NULL,
    reorder_level DECIMAL(10, 3) NOT NULL DEFAULT 0,
    reorder_quantity DECIMAL(10, 3) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (branch_id, product_id),
    FOREIGN KEY (branch_id) REFERENCES branch(id),
    FOREIGN KEY (product_id) REFERENCES product(id)
);

CREATE TABLE supplier (
    id INT PRIMARY KEY AUTO_INCREMENT,
    branch_id INT,
    supplier_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (branch_id) REFERENCES branch(id)
);

CREATE TABLE supplier_product (
    supplier_id INT NOT NULL,
    product_id INT NOT NULL,
    branch_id INT NOT NULL,
    supplier_product_code VARCHAR(50),
    is_preferred_supplier BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (supplier_id, product_id, branch_id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id),
    FOREIGN KEY (product_id) REFERENCES product(id),
    FOREIGN KEY (branch_id) REFERENCES branch(id)
);

CREATE TABLE purchase_order (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_number VARCHAR(50) NOT NULL UNIQUE,
    branch_id INT NOT NULL,
    supplier_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expected_date DATE,
    status ENUM(
        'draft',
        'pending',
        'approved',
        'ordered',
        'received',
        'cancelled'
    ) NOT NULL DEFAULT 'draft',
    total_amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (branch_id) REFERENCES branch(id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id),
    FOREIGN KEY (created_by) REFERENCES user(id)
);

CREATE TABLE purchase_order_item (
    po_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10, 3) NOT NULL,
    unit_cost DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    notes TEXT,
    PRIMARY KEY (po_id, product_id),
    FOREIGN KEY (po_id) REFERENCES purchase_order(id),
    FOREIGN KEY (product_id) REFERENCES product(id)
);

CREATE TABLE product_batch (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    branch_id INT NOT NULL,
    po_id INT,
    batch_code VARCHAR(50) NOT NULL,
    manufactured_date DATE,
    expiry_date DATE,
    unit_price DECIMAL(12, 2) NOT NULL,
    initial_quantity DECIMAL(10, 3) NOT NULL,
    current_quantity DECIMAL(10, 3) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES product(id),
    FOREIGN KEY (branch_id) REFERENCES branch(id),
    FOREIGN KEY (po_id) REFERENCES purchase_order(id),
    UNIQUE (branch_id, batch_code)
);

CREATE TABLE customer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50),
    phone VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    address TEXT,
    points DECIMAL(10, 2) DEFAULT 0,
    last_points_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE loyalty_transaction (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    points_amount DECIMAL(10, 2) NOT NULL,
    transaction_type ENUM('earn', 'redeem') NOT NULL,
    sale_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customer(id)
);

CREATE TABLE sale (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_number VARCHAR(50) NOT NULL UNIQUE,
    branch_id INT NOT NULL,
    customer_id INT,
    user_id INT NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(12, 2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    total_amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
    payment_method ENUM('cash', 'card', 'other') NOT NULL,
    payment_reference VARCHAR(100),
    status ENUM('pending', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (branch_id) REFERENCES branch(id),
    FOREIGN KEY (customer_id) REFERENCES customer(id),
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE sale_item (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    batch_id INT NOT NULL,
    quantity DECIMAL(10, 3) NOT NULL,
    unit_price DECIMAL(12, 2) NOT NULL,
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    subtotal DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sale(id),
    FOREIGN KEY (product_id) REFERENCES product(id),
    FOREIGN KEY (batch_id) REFERENCES product_batch(id)
);

CREATE TABLE customer_return (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_number VARCHAR(50) NOT NULL UNIQUE,
    sale_id INT NOT NULL,
    return_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    total_amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (sale_id) REFERENCES sale(id),
    FOREIGN KEY (created_by) REFERENCES user(id)
);

CREATE TABLE customer_return_item (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    sale_item_id INT NOT NULL,
    quantity DECIMAL(10, 3) NOT NULL,
    unit_price DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    reason TEXT,
    FOREIGN KEY (return_id) REFERENCES customer_return(id),
    FOREIGN KEY (sale_item_id) REFERENCES sale_item(id)
);

CREATE TABLE supplier_return (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_number VARCHAR(50) NOT NULL UNIQUE,
    po_id INT NOT NULL,
    supplier_id INT NOT NULL,
    return_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    total_amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (po_id) REFERENCES purchase_order(id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id),
    FOREIGN KEY (created_by) REFERENCES user(id)
);

CREATE TABLE supplier_return_item (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    product_id INT NOT NULL,
    batch_id INT NOT NULL,
    quantity DECIMAL(10, 3) NOT NULL,
    unit_cost DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    reason TEXT,
    FOREIGN KEY (return_id) REFERENCES supplier_return(id),
    FOREIGN KEY (product_id) REFERENCES product(id),
    FOREIGN KEY (batch_id) REFERENCES product_batch(id)
);

CREATE TABLE discount (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(12, 2) NOT NULL,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    branch_id INT,
    FOREIGN KEY (branch_id) REFERENCES branch(id)
);

CREATE TABLE coupon (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_id INT NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (discount_id) REFERENCES discount(id) ON DELETE CASCADE
);

CREATE TABLE discount_condition (
    id INT PRIMARY KEY AUTO_INCREMENT,
    discount_id INT NOT NULL,
    condition_type ENUM(
        'min_quantity',
        'min_amount',
        'time_of_day',
        'day_of_week',
        'loyalty_points'
    ) NOT NULL,
    condition_value JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (discount_id) REFERENCES discount(id) ON DELETE CASCADE
);

CREATE TABLE notification_type (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    template TEXT NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notification (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    priority ENUM('low', 'normal', 'high') NOT NULL DEFAULT 'normal',
    branch_id INT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (type_id) REFERENCES notification_type(id),
    FOREIGN KEY (branch_id) REFERENCES branch(id),
    FOREIGN KEY (created_by) REFERENCES user(id)
);

CREATE TABLE notification_recipient (
    id INT PRIMARY KEY AUTO_INCREMENT,
    notification_id INT NOT NULL,
    user_id INT NOT NULL,
    is_read BOOLEAN DEFAULT false,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES notification(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id)
);
