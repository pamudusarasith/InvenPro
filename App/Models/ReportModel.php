<?php

namespace App\Models;

use App\Core\Model;
use DateTime;

class ReportModel extends Model
{
    /**
     * Get top selling products
     */
    public function getTopSellingProducts($startDate = null, $endDate = null): array
    {
        $query = "
            SELECT 
                p.product_name AS product_name, 
                SUM(si.quantity) AS quantity, 
                SUM((si.unit_price - si.discount) * si.quantity) AS revenue
            FROM sale_item si
            JOIN product p ON si.product_id = p.id
            JOIN sale s ON si.sale_id = s.id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            AND s.status = 'completed'
            AND s.deleted_at IS NULL
            GROUP BY p.id, p.product_name
            ORDER BY revenue DESC
            LIMIT 10
        ";
        $params = [':startDate' => $startDate, ':endDate' => $endDate];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'product_name' => $row['product_name'],
                'quantity' => (int)$row['quantity'],
                'revenue' => 'LKR ' . number_format($row['revenue'], 2)
            ];
        }

        return $formattedResults;
    }

    /**
     * Get supplier performance
     */
    public function getSupplierPerformance($startDate, $endDate): array
    {
        // Note: The original query references `delivery_date` and `quality_rating`, which are not in the schema.
        // Assuming these are tracked elsewhere, I'll provide a placeholder query.
        // You may need to adjust based on actual data sources for on-time delivery and quality.
        $query = "
            SELECT 
                s.supplier_name AS name,
                COUNT(CASE WHEN po.order_date <= po.expected_date THEN 1 END) / COUNT(*) * 100 AS on_time,
                85 AS quality -- Placeholder: Quality rating not in schema
            FROM purchase_order po
            JOIN supplier s ON po.supplier_id = s.id
            WHERE po.deleted_at IS NULL
            AND po.order_date BETWEEN :startDate AND :endDate
            AND po.status = 'completed'
            GROUP BY s.id, s.supplier_name
            ORDER BY on_time DESC
            LIMIT 5
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'name' => $row['name'],
                'on_time' => round($row['on_time'], 2),
                'quality' => round($row['quality'], 2)
            ];
        }

        return $formattedResults;
    }

    /**
     * Get category revenue data
     */
    public function getCategoryRevenueData($startDate, $endDate): array
    {
        $query = "
            SELECT 
                c.category_name AS name,
                COALESCE(SUM((si.unit_price - si.discount) * si.quantity), 0) AS revenue
            FROM category c
            LEFT JOIN product_category pc ON c.id = pc.category_id
            LEFT JOIN product p ON pc.product_id = p.id
            LEFT JOIN sale_item si ON p.id = si.product_id
            LEFT JOIN sale s ON si.sale_id = s.id
            WHERE (s.sale_date BETWEEN :startDate AND :endDate OR s.sale_date IS NULL)
            AND c.deleted_at IS NULL
            AND s.status = 'completed'
            AND s.deleted_at IS NULL

            GROUP BY c.id, c.category_name
            ORDER BY revenue DESC
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'name' => $row['name'],
                'revenue' => (float)$row['revenue']
            ];
        }

        return $formattedResults;
    }

    /**
     * Get sales data for charts
     */
    public function getSalesData($startDate, $endDate): array
    {
        $query = "
            SELECT 
                DATE(s.sale_date) AS date,
                SUM((si.unit_price - si.discount) * si.quantity) AS revenue
            FROM sale s
            JOIN sale_item si ON s.id = si.sale_id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            AND s.status = 'completed'
            AND s.deleted_at IS NULL
            GROUP BY DATE(s.sale_date)
            HAVING SUM((si.unit_price - si.discount) * si.quantity) > 0
            ORDER BY DATE(s.sale_date) ASC
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'date' => (new DateTime($row['date']))->format('M d'),
                'sales' => (float)$row['revenue']
            ];
        }

        return $formattedResults;
    }

    /**
     * Get count and revenue for sales
     */
    public function getCountAndRevenue($startDate, $endDate): array
    {
        $query = "
            SELECT 
                COUNT(DISTINCT s.id) AS order_count,
                COALESCE(SUM((si.unit_price - si.discount) * si.quantity), 0) AS revenue
            FROM sale s
            LEFT JOIN sale_item si ON s.id = si.sale_id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            AND s.status = 'completed'
            AND s.deleted_at IS NULL
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [
            'order_count' => 0,
            'revenue' => 0.0
        ];

        if (!empty($results)) {
            $row = $results[0];
            $formattedResults = [
                'order_count' => (int)$row['order_count'],
                'revenue' => (float)$row['revenue']
            ];
        }

        return $formattedResults;
    }

    /**
     * Get category data
     */
    public function getCategoryData(): array
    {
        $query = "
            SELECT 
                c.category_name AS name,
                COUNT(DISTINCT p.id) AS product_count
            FROM category c
            LEFT JOIN product_category pc ON c.id = pc.category_id
            LEFT JOIN product p ON pc.product_id = p.id
            WHERE c.deleted_at IS NULL
            AND (p.deleted_at IS NULL OR p.id IS NULL)
            GROUP BY c.id, c.category_name
            ORDER BY product_count DESC
        ";

        $results = self::$db->query($query)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'name' => $row['name'],
                'count' => (int)$row['product_count']
            ];
        }

        return $formattedResults;
    }

    /**
     * Get expiring batches
     */
    public function getExpiringBatches($startDate, $endDate): array
    {
        $query = "
            SELECT 
                p.product_name AS product_name,
                b.batch_code AS batch_code,
                b.expiry_date AS expiry_date,
                b.current_quantity AS quantity,
                DATEDIFF(b.expiry_date, CURDATE()) AS days_left,
                u.unit_symbol AS unit
            FROM product_batch b
            JOIN product p ON b.product_id = p.id
            JOIN unit u ON p.unit_id = u.id
            WHERE b.expiry_date BETWEEN :startDate AND :endDate
            AND b.current_quantity > 0
            AND b.deleted_at IS NULL
            AND p.deleted_at IS NULL
            ORDER BY b.expiry_date ASC
            LIMIT 10
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'product_name' => $row['product_name'],
                'batch_code' => $row['batch_code'],
                'expiry_date' => (new DateTime($row['expiry_date']))->format('Y-m-d'),
                'quantity' => (float)$row['quantity'],
                'days_left' => (int)$row['days_left'],
                'unit' => $row['unit']
            ];
        }

        return $formattedResults;
    }

    /**
     * Get low stock items
     */
    public function getLowStock(): array
    {
        $query = "
            SELECT 
                p.product_name AS product_name,
                SUM(b.current_quantity) AS current_stock,
                bp.reorder_level AS reorder_level,
                u.unit_symbol AS unit,
                u.is_int AS is_int
            FROM product_batch b
            JOIN product p ON b.product_id = p.id
            JOIN unit u ON p.unit_id = u.id
            JOIN branch_product bp ON p.id = bp.product_id AND b.branch_id = bp.branch_id
            WHERE b.current_quantity <= bp.reorder_level
            AND b.current_quantity > 0
            AND b.deleted_at IS NULL
            AND p.deleted_at IS NULL
            GROUP BY p.id, p.product_name, bp.reorder_level, u.unit_symbol, u.is_int
            ORDER BY current_stock ASC
            LIMIT 10
        ";


        $results = self::$db->query($query)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'product_name' => $row['product_name'],
                'current_stock' => (float)$row['current_stock'],
                'reorder_level' => (float)$row['reorder_level'],
                'unit' => $row['unit'],
                'is_int' => (int)$row['is_int']
            ];
        }

        return $formattedResults;
    }

    /**
     * Get stock status
     */
    public function getStockStatus(): array
    {
        $query = "
            SELECT 
                SUM(CASE WHEN total_quantity > max_reorder_level THEN 1 ELSE 0 END) AS in_stock,
                SUM(CASE WHEN total_quantity <= max_reorder_level AND total_quantity > 0 THEN 1 ELSE 0 END) AS low_stock,
                SUM(CASE WHEN total_quantity = 0 OR total_quantity IS NULL THEN 1 ELSE 0 END) AS out_of_stock,
                SUM(total_value) AS total_value
            FROM (
                SELECT 
                    p.id AS product_id,
                    COALESCE(SUM(b.current_quantity), 0) AS total_quantity,
                    MAX(CASE WHEN bp.reorder_level >= 0 THEN bp.reorder_level ELSE 0 END) AS max_reorder_level,
                    COALESCE(SUM(b.current_quantity * b.unit_cost), 0) AS total_value
                FROM product p
                LEFT JOIN branch_product bp ON p.id = bp.product_id
                LEFT JOIN product_batch b ON p.id = b.product_id AND b.branch_id = bp.branch_id
                    AND b.deleted_at IS NULL
                WHERE p.deleted_at IS NULL
                GROUP BY p.id
            ) AS product_summary
        ";

        $results = self::$db->query($query)->fetchAll();

        $formattedResults = [
            'in_stock' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0,
            'total_value' => 'LKR 0.00'
        ];

        if (!empty($results)) {
            $row = $results[0];
            $formattedResults = [
                'in_stock' => (int)$row['in_stock'],
                'low_stock' => (int)$row['low_stock'],
                'out_of_stock' => (int)$row['out_of_stock'],
                'total_value' => 'LKR ' . number_format((float)$row['total_value'], 2)
            ];
        }

        return $formattedResults;
    }

    /**
     * Get recent purchase orders
     */
    public function getRecentPurchaseOrders($startDate, $endDate): array
    {
        $query = "
            SELECT 
                po.reference AS reference,
                s.supplier_name AS supplier,
                po.order_date AS date,
                po.status AS status,
                po.total_amount AS total
            FROM purchase_order po
            JOIN supplier s ON po.supplier_id = s.id
            WHERE po.order_date BETWEEN :startDate AND :endDate
            AND po.deleted_at IS NULL
            ORDER BY po.order_date DESC
            LIMIT 5
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'reference' => $row['reference'],
                'supplier' => $row['supplier'],
                'date' => (new DateTime($row['date']))->format('Y-m-d'),
                'status' => $row['status'],
                'total' => 'LKR ' . number_format((float)$row['total'], 2)
            ];
        }

        return $formattedResults;
    }

    /**
     * Get monthly sales data
     */
    public function getMonthlySalesData($startDate, $endDate): array
    {
        $query = "
            SELECT 
                DATE(s.sale_date) AS date,
                SUM((si.unit_price - si.discount) * si.quantity) AS revenue
            FROM sale s
            JOIN sale_item si ON s.id = si.sale_id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            AND s.status = 'completed'
            AND s.deleted_at IS NULL
            GROUP BY DATE(s.sale_date)
            HAVING SUM((si.unit_price - si.discount) * si.quantity) > 0
            ORDER BY DATE(s.sale_date) ASC
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'date' => (new DateTime($row['date']))->format('M d'),
                'sales' => (float)$row['revenue']
            ];
        }

        return $formattedResults;
    }

    /**
     * Get sales stats for Sales Overview section
     */
    public function getSalesStats($startDate, $endDate): array
    {
        $query = "
            SELECT 
                COUNT(DISTINCT s.id) AS total_orders,
                COALESCE(SUM((si.unit_price - si.discount) * si.quantity), 0) AS total_revenue,
                COALESCE(SUM((si.unit_price - si.discount) * si.quantity) / COUNT(DISTINCT s.id), 0) AS avg_order_value
            FROM sale s
            LEFT JOIN sale_item si ON s.id = si.sale_id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            AND s.status = 'completed'
            AND s.deleted_at IS NULL
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [
            'total_orders' => 0,
            'total_revenue' => 'LKR 0.00',
            'avg_order_value' => 'LKR 0.00'
        ];

        if (!empty($results)) {
            $row = $results[0];
            $formattedResults = [
                'total_orders' => (int)$row['total_orders'],
                'total_revenue' => 'LKR ' . number_format((float)$row['total_revenue'], 2),
                'avg_order_value' => 'LKR ' . number_format((float)$row['avg_order_value'], 2)
            ];
        }

        return $formattedResults;
    }

    /**
     * Get inventory stats for Inventory Status section
     */
    public function getInventoryStats(): array
    {
        $query = "
            SELECT 
                SUM(CASE WHEN total_quantity > max_reorder_level THEN 1 ELSE 0 END) AS in_stock,
                SUM(CASE WHEN total_quantity <= max_reorder_level AND total_quantity > 0 THEN 1 ELSE 0 END) AS low_stock,
                SUM(CASE WHEN total_quantity = 0 OR total_quantity IS NULL THEN 1 ELSE 0 END) AS out_of_stock
            FROM (
                SELECT 
                    p.id AS product_id,
                    COALESCE(SUM(b.current_quantity), 0) AS total_quantity,
                    MAX(CASE WHEN bp.reorder_level >= 0 THEN bp.reorder_level ELSE 0 END) AS max_reorder_level
                FROM product p
                LEFT JOIN branch_product bp ON p.id = bp.product_id
                LEFT JOIN product_batch b ON p.id = b.product_id AND b.branch_id = bp.branch_id
                    AND b.deleted_at IS NULL
                WHERE p.deleted_at IS NULL
                GROUP BY p.id
            ) AS product_summary
        ";

        $results = self::$db->query($query)->fetchAll();

        $formattedResults = [
            'in_stock' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0
        ];

        if (!empty($results)) {
            $row = $results[0];
            $formattedResults = [
                'in_stock' => (int)$row['in_stock'],
                'low_stock' => (int)$row['low_stock'],
                'out_of_stock' => (int)$row['out_of_stock']
            ];
        }

        return $formattedResults;
    }

    /**
     * Get category stats for Product Category Analysis section
     */
    public function getCategoryStats(): array
    {
        $query = "
            SELECT 
                COUNT(DISTINCT c.id) AS category_count,
                COUNT(DISTINCT p.id) AS total_products,
                COUNT(DISTINCT p.id) / COUNT(DISTINCT c.id) AS avg_products_per_category
            FROM category c
            LEFT JOIN product_category pc ON c.id = pc.category_id
            LEFT JOIN product p ON pc.product_id = p.id
            WHERE c.deleted_at IS NULL
            AND (p.deleted_at IS NULL OR p.id IS NULL)
        ";

        $results = self::$db->query($query)->fetchAll();

        $formattedResults = [
            'category_count' => 0,
            'total_products' => 0,
            'avg_products_per_category' => 0.0
        ];

        if (!empty($results)) {
            $row = $results[0];
            $formattedResults = [
                'category_count' => (int)$row['category_count'],
                'total_products' => (int)$row['total_products'],
                'avg_products_per_category' => round((float)$row['avg_products_per_category'], 1)
            ];
        }

        return $formattedResults;
    }

    /**
     * Get profit margin
     */
    public function getProfitMargin($startDate, $endDate): float
    {
        $query = "
            SELECT 
                COALESCE(SUM((si.unit_price - si.discount) * si.quantity), 0) AS revenue,
                COALESCE(SUM(b.unit_cost * si.quantity), 0) AS cost
            FROM sale s
            JOIN sale_item si ON s.id = si.sale_id
            JOIN product_batch b ON si.batch_id = b.id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            AND s.status = 'completed'
            AND s.deleted_at IS NULL
            AND b.deleted_at IS NULL
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $revenue = 0.0;
        $cost = 0.0;

        if (!empty($results)) {
            $row = $results[0];
            $revenue = (float)$row['revenue'];
            $cost = (float)$row['cost'];
        }

        return $revenue > 0 ? (($revenue - $cost) / $revenue) * 100 : 0.0;
    }

    public function getTopProductCombinations(string $startDate, string $endDate, int $limit = 10): array
    {
        $query = "
            SELECT 
                p1.product_name AS product1,
                p2.product_name AS product2,
                COUNT(*) AS frequency
            FROM sale s
            JOIN sale_item si1 ON s.id = si1.sale_id
            JOIN sale_item si2 ON s.id = si2.sale_id AND si1.product_id < si2.product_id
            JOIN product p1 ON si1.product_id = p1.id
            JOIN product p2 ON si2.product_id = p2.id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            AND s.status = 'completed'
            AND s.deleted_at IS NULL
            AND p1.deleted_at IS NULL
            AND p2.deleted_at IS NULL
            GROUP BY p1.id, p2.id, p1.product_name, p2.product_name
            ORDER BY frequency DESC
            LIMIT :limit
        ";

        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate,
            ':limit' => $limit
        ];

        $results = self::$db->query($query, $params)->fetchAll();

        $combinations = [];
        foreach ($results as $row) {
            $combinations[] = [
                'product1' => (string)$row['product1'],
                'product2' => (string)$row['product2'],
                'frequency' => (int)$row['frequency']
            ];
        }

        return $combinations;
    }

    
}