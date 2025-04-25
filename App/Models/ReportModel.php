<?php

namespace App\Models;

use App\Core\Model;
use DateTime;

class ReportModel extends Model
{
    public function getSalesReport(string $startDate, string $endDate): array
    {
        $sql = 'SELECT * FROM sales WHERE sale_date BETWEEN ? AND ?';
        $stmt = self::$db->query($sql, [$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    public function getInventoryReport(string $startDate, string $endDate): array
    {
        $sql = 'SELECT * FROM inventory WHERE date BETWEEN ? AND ?';
        $stmt = self::$db->query($sql, [$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    public function filterTimePeriod(string $timePeriod): array
    {
        if ($timePeriod === 'today') {
            $sql = 'SELECT HOUR(sale_date) AS sale_hour, 
                           COALESCE(SUM(amount), 0) AS total_sales 
                    FROM sales 
                    WHERE DATE(sale_date) = CURDATE() 
                    GROUP BY HOUR(sale_date)';
            $stmt = self::$db->query($sql);
            return $stmt->fetchAll();
        } elseif ($timePeriod === 'last_week') {
            $sql = 'SELECT DATE(sale_date) AS sale_day, 
                           COALESCE(SUM(amount), 0) AS total_sales 
                    FROM sales 
                    WHERE sale_date >= CURDATE() - INTERVAL 7 DAY 
                    GROUP BY DATE(sale_date)';
            $stmt = self::$db->query($sql);
            return $stmt->fetchAll();
        } elseif ($timePeriod === 'last_month') {
            $sql = 'SELECT CONCAT(MONTHNAME(sale_date), "-", DAY(sale_date)) AS sale_day, 
                           COALESCE(SUM(amount), 0) AS total_sales 
                    FROM sales 
                    WHERE sale_date >= CURDATE() - INTERVAL 30 DAY 
                    GROUP BY DAY(sale_date)';
            $stmt = self::$db->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as &$result) {
                $result['sale_day'] = substr($result['sale_day'], 0, 3) . '-' . explode('-', $result['sale_day'])[1];
            }
            return $results;
        } elseif ($timePeriod === 'last_year') {
            $sql = 'SELECT LEFT(MONTHNAME(sale_date), 3) AS sale_month, 
                           COALESCE(SUM(amount), 0) AS total_sales 
                    FROM sales 
                    WHERE YEAR(sale_date) = YEAR(CURDATE()) - 1 
                    GROUP BY MONTH(sale_date)';
            $stmt = self::$db->query($sql);
            return $stmt->fetchAll();
        }

        return [];
    }
}