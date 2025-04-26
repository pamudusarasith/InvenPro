<?php

namespace App\Models;

use App\Core\Model;
use DateTime;

class ReportModel extends Model
{
    public function getTopSellingProducts($startDate = null, $endDate = null) {
        
        
        $query = "SELECT p.product_name AS product_name, 
                         SUM(si.quantity) AS quantity, 
                         SUM((si.unit_price - si.discount) * si.quantity) AS revenue
                    FROM sale_item si
                    JOIN product p ON si.product_id = p.id
                    JOIN sale s ON si.sale_id = s.id
                    WHERE s.sale_date BETWEEN :startDate AND :endDate
                    GROUP BY p.id, p.product_name
                    ORDER BY revenue DESC
                    LIMIT 10";
        $params = [':startDate' => $startDate, ':endDate' => $endDate];
        
    
        // Execute the query
        $results = self::$db->query($query, $params)->fetchAll();
    
        // Format the results to match the view's expected structure
        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'product_name' => $row['product_name'],
                'quantity' => (int)$row['quantity'], // Ensure quantity is an integer
                'revenue' => 'LKR ' . number_format($row['revenue'], 2) // Format revenue as "LKR X,XXX.XX"
            ];
        }
    
        return $formattedResults;
    }

    public function getSupplierPerformance($startDate, $endDate): array
    {
        $query = "
            SELECT 
                s.supplier_name AS name,
                AVG(CASE WHEN po.delivery_date <= po.expected_delivery_date THEN 100 ELSE 0 END) AS on_time,
                AVG(poi.quality_rating) AS quality
            FROM purchase_order po
            INNER JOIN supplier s ON po.supplier_id = s.id
            LEFT JOIN purchase_order_item poi ON po.id = poi.po_id
            WHERE po.deleted_at IS NULL
            AND po.order_date BETWEEN :startDate AND :endDate
            GROUP BY s.id, s.supplier_name
            ORDER BY on_time DESC, quality DESC
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
            AND s.sale_date BETWEEN :startDate AND :endDate
            GROUP BY c.id, c.category_name
            ORDER BY revenue DESC
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
                'name' => $row['name'],
                'revenue' => (float)$row['revenue']
            ];
        }

        return $formattedResults;
    }

    public function getSalesData($startDate, $endDate): array
    {
        $query = "
            SELECT 
                s.sale_date AS date,
                SUM((si.unit_price - si.discount) * si.quantity) AS revenue
            FROM sale s
            LEFT JOIN sale_item si ON s.id = si.sale_id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            GROUP BY s.sale_date
            HAVING SUM((si.unit_price - si.discount) * si.quantity) > 0
            ORDER BY s.sale_date ASC
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

    public function getCountAndRevenue($startDate, $endDate): array
    {
        $params = [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ];

        $query = "
            SELECT 
                COUNT(*) AS count,
                SUM((si.unit_price - si.discount) * si.quantity) AS revenue
            FROM sale s
            LEFT JOIN sale_item si ON s.id = si.sale_id
            WHERE s.sale_date BETWEEN :startDate AND :endDate
            HAVING SUM((si.unit_price - si.discount) * si.quantity) > 0
        ";

        $results = self::$db->query($query, $params)->fetchAll();


        $formattedResults = [];
        if (!empty($results)) {
            $row = $results[0]; // Fetch the first row
            $formattedResults = [
                'count' => (int)$row['count'],
                'revenue' => (float)$row['revenue']
            ];
        }
        else {
            $formattedResults = [
                'count' => 0,
                'revenue' => 0.00
            ];
        }

        return $formattedResults;
    }


    public function getCategoryData(): array
    {
        $query = "
            SELECT 
                c.category_name AS name,
                COUNT(DISTINCT p.id) AS product_count,
                COALESCE(SUM((si.unit_price - si.discount) * si.quantity), 0) AS revenue
            FROM category c
            LEFT JOIN product_category pc ON c.id = pc.category_id
            LEFT JOIN product p ON pc.product_id = p.id
            LEFT JOIN sale_item si ON p.id = si.product_id
            LEFT JOIN sale s ON si.sale_id = s.id
            GROUP BY c.id, c.category_name
            ORDER BY revenue DESC
        ";


        $results = self::$db->query($query)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'name' => $row['name'],
                'count' => (int)$row['product_count'],
            ];
        }

        return $formattedResults;
    }

    public function getExpiringBatches($startDate, $endDate){   
        $query = "
            SELECT 
                p.product_name AS product_name,
                b.batch_code AS batch_code,
                b.expiry_date AS expiry_date,
                b.current_quantity AS quantity,
                b.expiry_date - CURDATE() AS days_left,
                u.unit_symbol as unit
            FROM product_batch b
            INNER JOIN product p ON b.product_id = p.id
            INNER JOIN unit u ON p.unit_id = u.id
            WHERE b.expiry_date BETWEEN ? AND ?
            AND b.deleted_at IS NULL
            ORDER BY b.expiry_date ASC
            LIMIT 10
        ";
        $params = [$startDate, $endDate];
        $results = self::$db->query($query, $params)->fetchAll();

        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'product_name' => $row['product_name'],
                'batch_code' => $row['batch_code'],
                'expiry_date' => (new DateTime($row['expiry_date']))->format('Y-m-d'),
                'quantity' => (float)$row['quantity'],
                'days_left' => (int)$row['days_left']
            ];
        }
        return $formattedResults;
    }

    public function getLowStock(): array
    {
        $query = "
            SELECT 
                p.product_name AS product_name,
                SUM(b.current_quantity) AS current_stock,
                bp.reorder_quantity AS reorder_level,
                u.unit_symbol AS unit,
                u.is_int AS is_int
            FROM product_batch b
            LEFT JOIN product p ON b.product_id = p.id
            LEFT JOIN unit u ON p.unit_id = u.id
            LEFT JOIN branch_product bp ON p.id = bp.product_id
            WHERE b.current_quantity <= bp.reorder_quantity
            GROUP BY p.id, p.product_name
            ORDER BY current_stock ASC
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


}
