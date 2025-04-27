<?php

namespace App\Models;

use App\Core\Model;

class SaleModel extends Model
{
  /**
   * Get sales on today
   * @return array
   */
  public function getSalesCardData(): array
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND branch_id = ?';
    $sql = "
      SELECT
        SUM(CASE WHEN DATE(sale_date) = CURDATE() THEN total ELSE 0 END) AS today_sales,
        SUM(CASE WHEN DATE(sale_date) = CURDATE() - INTERVAL 1 DAY THEN total ELSE 0 END) AS yesterday_sales
      FROM sale
      WHERE deleted_at IS NULL $branchCondition
    ";

    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    return self::$db->query($sql, $params)->fetch();
  }

  public function createSale(array $data): void
  {
    try {
      self::$db->beginTransaction();
      $sql = '
      INSERT INTO sale (
        branch_id,
        customer_id,
        user_id,
        subtotal,
        discount,
        total,
        payment_method,
        notes)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ';

      self::$db->query($sql, [
        $data['branch_id'],
        $data['customer_id'],
        $data['user_id'],
        $data['subtotal'],
        $data['discount'],
        $data['total'],
        $data['payment_method'],
        $data['notes'],
      ]);

      $saleId = self::$db->lastInsertId();

      foreach ($data['items'] as $item) {
        $sql = '
          UPDATE product_batch
          SET
            current_quantity = current_quantity - ?
          WHERE
            id = ?
        ';

        self::$db->query($sql, [$item['quantity'], $item['batch_id']]);

        $sql = '
          INSERT INTO sale_item (
            sale_id,
            product_id,
            batch_id,
            quantity,
            unit_price)
          VALUES (?, ?, ?, ?, ?)
        ';

        self::$db->query($sql, [
          $saleId,
          $item['product_id'],
          $item['batch_id'],
          $item['quantity'],
          $item['unit_price'],
        ]);
      }

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }

  /**
   * Get total revenue for the past month from today
   * @return int
   */
  public function getMonthlyRevenue(): int
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND branch_id = ?';
    $sql = "
      SELECT SUM(total) AS total_revenue
      FROM sale
      WHERE DATE(sale_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        AND deleted_at IS NULL $branchCondition AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
    ";

    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    return self::$db->query($sql, $params)->fetchColumn() ?: 0;

  }

  /**
   * Get sales trend data for the past 12 months
   * @param string|null $fromDate Start date filter (YYYY-MM-DD)
   * @param string|null $toDate End date filter (YYYY-MM-DD)
   * @param string|null $branchId Branch ID filter
   * @param string $period Period type (daily, weekly, monthly, yearly)
   * @return array Labels and data for sales trend chart
   */
  public function getSalesTrend(?string $fromDate, ?string $toDate, ?string $branchId, string $period = 'monthly'): array
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND s.branch_id = ?';
    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    
    if ($branchId && $_SESSION['user']['branch_id'] == 1) {
      $branchCondition .= ' AND s.branch_id = ?';
      $params[] = $branchId;
    }

    $dateCondition = '';
    if ($fromDate && $toDate) {
      $dateCondition = 'AND DATE(s.sale_date) BETWEEN ? AND ?';
      $params[] = $fromDate;
      $params[] = $toDate;
    }

    switch ($period) {
      case 'daily':
        $groupBy = "DATE(s.sale_date)";
        $dateFormat = "DATE_FORMAT(s.sale_date, '%Y-%m-%d')";
        break;
      case 'weekly':
        $groupBy = "YEARWEEK(s.sale_date, 1)";
        $dateFormat = "DATE_FORMAT(s.sale_date, '%Y-W%U')";
        break;
      case 'yearly':
        $groupBy = "YEAR(s.sale_date)";
        $dateFormat = "DATE_FORMAT(s.sale_date, '%Y')";
        break;
      case 'monthly':
      default:
        $groupBy = "DATE_FORMAT(s.sale_date, '%Y-%m')";
        $dateFormat = "DATE_FORMAT(s.sale_date, '%b')";
        break;
    }

    $sql = "
      SELECT 
        $dateFormat AS label,
        SUM(s.total) AS total_sales
      FROM sale s
      WHERE s.deleted_at IS NULL $branchCondition $dateCondition
      GROUP BY $groupBy
      ORDER BY s.sale_date ASC
    ";

    $results = self::$db->query($sql, $params)->fetchAll();

    $labels = [];
    $data = [];
    foreach ($results as $row) {
      $labels[] = $row['label'];
      $data[] = (float) $row['total_sales'];
    }

    return [
      'labels' => $labels,
      'data' => $data
    ];
  }

  /**
   * Get payment method distribution
   * @param string|null $fromDate Start date filter (YYYY-MM-DD)
   * @param string|null $toDate End date filter (YYYY-MM-DD)
   * @param string|null $branchId Branch ID filter
   * @return array Labels and data for payment method chart
   */
  public function getPaymentMethodDistribution(?string $fromDate, ?string $toDate, ?string $branchId): array
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND s.branch_id = ?';
    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    
    if ($branchId && $_SESSION['user']['branch_id'] == 1) {
      $branchCondition .= ' AND s.branch_id = ?';
      $params[] = $branchId;
    }

    $dateCondition = '';
    if ($fromDate && $toDate) {
      $dateCondition = 'AND DATE(s.sale_date) BETWEEN ? AND ?';
      $params[] = $fromDate;
      $params[] = $toDate;
    }

    $sql = "
      SELECT 
        s.payment_method AS label,
        COUNT(*) AS count,
        (COUNT(*) * 100.0 / SUM(COUNT(*)) OVER ()) AS percentage
      FROM sale s
      WHERE s.deleted_at IS NULL $branchCondition $dateCondition
      GROUP BY s.payment_method
    ";

    $results = self::$db->query($sql, $params)->fetchAll();

    $labels = [];
    $data = [];
    foreach ($results as $row) {
      $labels[] = ucfirst($row['label']);
      $data[] = round($row['percentage'], 2);
    }

    return [
      'labels' => $labels ?: ['Cash', 'Card'],
      'data' => $data ?: [0, 0]
    ];
  }

  /**
   * Get sales by branch
   * @param string|null $fromDate Start date filter (YYYY-MM-DD)
   * @param string|null $toDate End date filter (YYYY-MM-DD)
   * @param string|null $branchId Branch ID filter
   * @return array Branch sales data
   */
  public function getSalesByBranch(?string $fromDate, ?string $toDate, ?string $branchId): array
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND s.branch_id = ?';
    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    
    if ($branchId && $_SESSION['user']['branch_id'] == 1) {
      $branchCondition .= ' AND s.branch_id = ?';
      $params[] = $branchId;
    }

    $dateCondition = '';
    if ($fromDate && $toDate) {
      $dateCondition = 'AND DATE(s.sale_date) BETWEEN ? AND ?';
      $params[] = $fromDate;
      $params[] = $toDate;
    }

    $sql = "
      SELECT 
        b.branch_name AS name,
        COUNT(s.id) AS sales,
        SUM(s.total) AS revenue
      FROM sale s
      JOIN branch b ON s.branch_id = b.id
      WHERE s.deleted_at IS NULL $branchCondition $dateCondition
      GROUP BY s.branch_id, b.branch_name
      ORDER BY revenue DESC
    ";

    return self::$db->query($sql, $params)->fetchAll();
  }

  /**
   * Get top selling products
   * @param string|null $fromDate Start date filter (YYYY-MM-DD)
   * @param string|null $toDate End date filter (YYYY-MM-DD)
   * @param string|null $branchId Branch ID filter
   * @param int $limit Number of products to return
   * @return array Top selling products data
   */
  public function getTopSellingProducts(?string $fromDate, ?string $toDate, ?string $branchId, int $limit = 5): array
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND s.branch_id = ?';
    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    
    if ($branchId && $_SESSION['user']['branch_id'] == 1) {
      $branchCondition .= ' AND s.branch_id = ?';
      $params[] = $branchId;
    }

    $dateCondition = '';
    if ($fromDate && $toDate) {
      $dateCondition = 'AND DATE(s.sale_date) BETWEEN ? AND ?';
      $params[] = $fromDate;
      $params[] = $toDate;
    }

    $sql = "
      SELECT 
        p.product_name AS name,
        SUM(si.quantity) AS quantity,
        SUM(si.quantity * si.unit_price) AS revenue
      FROM sale s
      JOIN sale_item si ON s.id = si.sale_id
      JOIN product p ON si.product_id = p.id
      WHERE s.deleted_at IS NULL $branchCondition $dateCondition
      GROUP BY p.id, p.product_name
      ORDER BY quantity DESC
      LIMIT ?
    ";

    $params[] = $limit;
    return self::$db->query($sql, $params)->fetchAll();
  }

  /**
   * Get recent sales
   * @param string|null $fromDate Start date filter (YYYY-MM-DD)
   * @param string|null $toDate End date filter (YYYY-MM-DD)
   * @param string|null $branchId Branch ID filter
   * @param string|null $status Status filter
   * @param string|null $paymentMethod Payment method filter
   * @param string|null $searchQuery Search query for customer name
   * @param int $limit Number of sales to return
   * @return array Recent sales data
   */
  public function getRecentSales(
    ?string $fromDate,
    ?string $toDate,
    ?string $branchId,
    ?string $status,
    ?string $paymentMethod,
    ?string $searchQuery,
    int $limit = 5
  ): array {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND s.branch_id = ?';
    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    
    if ($branchId && $_SESSION['user']['branch_id'] == 1) {
      $branchCondition .= ' AND s.branch_id = ?';
      $params[] = $branchId;
    }

    $dateCondition = '';
    if ($fromDate && $toDate) {
      $dateCondition = 'AND DATE(s.sale_date) BETWEEN ? AND ?';
      $params[] = $fromDate;
      $params[] = $toDate;
    }

    $statusCondition = $status ? 'AND s.status = ?' : '';
    if ($status) {
      $params[] = $status;
    }

    $paymentCondition = $paymentMethod ? 'AND s.payment_method = ?' : '';
    if ($paymentMethod) {
      $params[] = $paymentMethod;
    }

    $searchCondition = $searchQuery ? 'AND c.name LIKE ?' : '';
    if ($searchQuery) {
      $params[] = "%$searchQuery%";
    }

    $sql = "
      SELECT 
        s.id,
        s.branch_id,
        b.branch_name AS branch_name,
        s.customer_id,
        c.first_name AS customer_name,
        s.sale_date,
        s.subtotal,
        s.discount,
        s.total,
        s.payment_method,
        s.status,
        COUNT(si.id) AS items,
        u.display_name AS user_name
      FROM sale s
      JOIN branch b ON s.branch_id = b.id
      LEFT JOIN customer c ON s.customer_id = c.id
      LEFT JOIN user u ON s.user_id = u.id
      LEFT JOIN sale_item si ON s.id = si.sale_id
      WHERE s.deleted_at IS NULL $branchCondition $dateCondition $statusCondition $paymentCondition $searchCondition
      GROUP BY s.id
      ORDER BY s.sale_date DESC
      LIMIT ?
    ";

    $params[] = $limit;
    return self::$db->query($sql, $params)->fetchAll();
  }

  /**
   * Get total sales count
   * @param string|null $fromDate Start date filter (YYYY-MM-DD)
   * @param string|null $toDate End date filter (YYYY-MM-DD)
   * @param string|null $branchId Branch ID filter
   * @return int Total number of sales
   */
  public function getTotalSalesCount(?string $fromDate, ?string $toDate, ?string $branchId): int
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND branch_id = ?';
    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    
    if ($branchId && $_SESSION['user']['branch_id'] == 1) {
      $branchCondition .= ' AND branch_id = ?';
      $params[] = $branchId;
    }

    $dateCondition = '';
    if ($fromDate && $toDate) {
      $dateCondition = 'AND DATE(sale_date) BETWEEN ? AND ?';
      $params[] = $fromDate;
      $params[] = $toDate;
    }

    $sql = "
      SELECT COUNT(*) AS total_sales
      FROM sale
      WHERE deleted_at IS NULL $branchCondition $dateCondition
    ";

    return self::$db->query($sql, $params)->fetchColumn() ?: 0;
  }

  /**
   * Get sales summary for today, this week, and this month
   * @param string|null $branchId Branch ID filter
   * @return array Sales summary data
   */
  public function getSalesSummary(?string $branchId): array
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND branch_id = ?';
    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    
    if ($branchId && $_SESSION['user']['branch_id'] == 1) {
      $branchCondition .= ' AND branch_id = ?';
      $params[] = $branchId;
    }

    $sql = "
      SELECT
        SUM(CASE WHEN DATE(sale_date) = CURDATE() THEN total ELSE 0 END) AS today_sales,
        SUM(CASE WHEN YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1) THEN total ELSE 0 END) AS this_week_sales,
        SUM(CASE WHEN DATE(sale_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) THEN total ELSE 0 END) AS this_month_sales
      FROM sale
      WHERE deleted_at IS NULL $branchCondition
    ";

    return self::$db->query($sql, $params)->fetch();
  }

}
