<?php

use App\Services\RBACService;

// Simulate data from controller
// $discounts = [
//   [
//     'id' => 1,
//     'name' => 'Summer Sale',
//     'description' => 'Special discount for summer season',
//     'discount_type' => 'percentage',
//     'application_method' => 'regular',
//     'value' => 15,
//     'start_date' => '2025-04-15 00:00:00',
//     'end_date' => '2025-06-30 23:59:59',
//     'is_active' => 1,
//     'branch_id' => 1,
//     'conditions' => [
//       ['condition_type' => 'min_amount', 'condition_value' => json_encode(['amount' => 5000])],
//     ]
//   ],
//   [
//     'id' => 2,
//     'name' => 'New Customer',
//     'description' => 'Discount for first time customers',
//     'discount_type' => 'fixed',
//     'application_method' => 'coupon',
//     'value' => 500,
//     'start_date' => '2025-01-01 00:00:00',
//     'end_date' => '2025-12-31 23:59:59',
//     'is_active' => 0,
//     'branch_id' => 1,
//     'conditions' => []
//   ],
//   [
//     'id' => 3,
//     'name' => 'Weekday Special',
//     'description' => 'Discount for Monday to Thursday purchases',
//     'discount_type' => 'percentage',
//     'application_method' => 'regular',
//     'value' => 10,
//     'start_date' => '2025-03-01 00:00:00',
//     'end_date' => '2025-05-31 23:59:59',
//     'is_active' => 1,
//     'branch_id' => 1,
//     'conditions' => [
//       ['condition_type' => 'day_of_week', 'condition_value' => json_encode(['days' => [1, 2, 3, 4]])],
//     ]
//   ],
//   [
//     'id' => 4,
//     'name' => 'Loyalty Reward',
//     'description' => 'Discount for customers with loyalty points',
//     'discount_type' => 'percentage',
//     'application_method' => 'regular',
//     'value' => 5,
//     'start_date' => '2025-01-01 00:00:00',
//     'end_date' => null,
//     'is_active' => 1,
//     'branch_id' => 1,
//     'conditions' => [
//       ['condition_type' => 'loyalty_points', 'condition_value' => json_encode(['min_points' => 1000])],
//     ]
//   ],
//   [
//     'id' => 5,
//     'name' => 'Bulk Purchase',
//     'description' => 'Discount for bulk purchases',
//     'discount_type' => 'percentage',
//     'application_method' => 'regular',
//     'value' => 8,
//     'start_date' => '2025-02-01 00:00:00',
//     'end_date' => '2025-12-31 23:59:59',
//     'is_active' => 1,
//     'branch_id' => 1,
//     'conditions' => [
//       ['condition_type' => 'min_quantity', 'condition_value' => json_encode(['quantity' => 15])],
//     ]
//   ],
// ];

$conditionTypes = [
  'min_quantity' => 'Minimum Quantity',
  'min_amount' => 'Minimum Purchase Amount',
  'time_of_day' => 'Time of Day',
  'day_of_week' => 'Day of Week',
  'loyalty_points' => 'Loyalty Points',
];

$daysOfWeek = [
  1 => 'Monday',
  2 => 'Tuesday',
  3 => 'Wednesday',
  4 => 'Thursday',
  5 => 'Friday',
  6 => 'Saturday',
  7 => 'Sunday',
];

$canAddDiscount = RBACService::hasPermission('add_discounts');
$canEditDiscounts = RBACService::hasPermission('edit_discounts');
$canDeleteDiscounts = RBACService::hasPermission('delete_discounts');
?>

<link rel="stylesheet" href="/css/pages/discounts.css">

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Discounts</h1>
        <p class="subtitle">Manage discounts and promotions for your customers.</p>
      </div>
      <?php if ($canAddDiscount): ?>
        <button class="btn btn-primary" onclick="openCreateDiscountDialog()">
          <span class="icon">add</span>
          New Discount
        </button>
      <?php endif; ?>
    </div>

    <div class="card glass controls">
      <div class="search-bar">
        <span class="icon">search</span>
        <input type="text" id="searchInput" placeholder="Search discounts...">
      </div>

      <div class="filters">
        <select onchange="filterItems()">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>

        <select onchange="filterItems()">
          <option value="">All Types</option>
          <option value="percentage">Percentage</option>
          <option value="fixed">Fixed Amount</option>
        </select>

        <select onchange="filterItems()">
          <option value="">All Application Methods</option>
          <option value="percentage">Regular</option>
          <option value="fixed">Coupon</option>
        </select>

        <div class="date-filter">
          <input type="date" class="date-input" id="fromDate" placeholder="From date" onchange="filterItems()">
          <span class="icon">arrow_forward</span>
          <input type="date" class="date-input" id="toDate" placeholder="To date" onchange="filterItems()">
        </div>
      </div>
    </div>

    <div class="discount-grid">
      <?php foreach ($discounts as $discount): ?>
        <div class="discount-card <?= $discount['is_active'] ? '' : 'inactive' ?>">
          <div class="discount-header">
            <h3><?= htmlspecialchars($discount['name']) ?></h3>
            <?php if ($discount['is_active']): ?>
              <span class="badge success">Active</span>
            <?php else: ?>
              <span class="badge">Inactive</span>
            <?php endif; ?>
          </div>

          <p class="discount-description"><?= htmlspecialchars($discount['description']) ?></p>

          <div class="h-pad-lg mb-md">
            <?php if ($discount['discount_type'] === 'percentage'): ?>
              <span class="text-info text-xl weight-semibold"><?= $discount['value'] ?>%</span> off
            <?php else: ?>
              <span class="text-info text-xl weight-semibold">Rs. <?= number_format($discount['value'], 2) ?></span> off
            <?php endif; ?>
          </div>

          <div class="discount-dates">
            <span class="icon">calendar_today</span>
            <span>
              <?= date('M d, Y', strtotime($discount['start_date'])) ?>
              <?= $discount['end_date'] ? ' - ' . date('M d, Y', strtotime($discount['end_date'])) : ' - No end date' ?>
            </span>
          </div>

          <div class="card-actions">
            <button class="icon-btn secondary" onclick="viewDiscountDetails(<?= $discount['id'] ?>)">
              <span class="icon">visibility</span>
            </button>

            <?php if ($canEditDiscounts): ?>
              <button class="icon-btn edit" onclick="editDiscount(<?= $discount['id'] ?>)" title="Edit discount">
                <span class="icon">edit</span>
              </button>
              <?php if ($discount['is_active']): ?>
                <button class="icon-btn warning" onclick="toggleDiscountStatus(<?= $discount['id'] ?>, 0)" title="Deactivate discount">
                  <span class="icon">pause</span>
                </button>
              <?php else: ?>
                <button class="icon-btn success" onclick="toggleDiscountStatus(<?= $discount['id'] ?>, 1)" title="Activate discount">
                  <span class="icon">play_arrow</span>
                </button>
              <?php endif; ?>
            <?php endif; ?>
            <?php if ($canDeleteDiscounts): ?>
              <button class="icon-btn danger" onclick="deleteDiscount(<?= $discount['id'] ?>)" title="Delete discount">
                <span class="icon">delete</span>
              </button>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Create/Edit Discount Dialog -->
<dialog id="discountDialog" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>New Discount</h2>
      <button class="close-btn" aria-label="Close" onclick="closeDiscountDialog()">
        <span class="icon">close</span>
      </button>
    </div>

    <form id="discountForm" action="/discounts/new" method="POST">
      <div class="form-grid">
        <div class="form-field span-2">
          <label for="discountName">Discount Name *</label>
          <input type="text" id="discountName" name="name" required>
        </div>

        <div class="form-field span-2">
          <label for="discountDescription">Description</label>
          <textarea id="discountDescription" name="description" rows="3"></textarea>
        </div>

        <div class="form-field">
          <label for="discountType">Discount Type *</label>
          <select id="discountType" name="discount_type" required>
            <option value="percentage">Percentage (%)</option>
            <option value="fixed">Fixed Amount (Rs.)</option>
          </select>
        </div>

        <div class="form-field">
          <label for="discountValue">Value *</label>
          <input type="number" id="discountValue" name="value" min="0" step="0.01" required>
        </div>

        <div class="form-field">
          <label for="applicationMethod">Application Method *</label>
          <select id="applicationMethod" name="application_method" required onchange="toggleCouponSection(this)">
            <option value="regular">Regular (Automatic)</option>
            <option value="coupon">Coupon-based</option>
          </select>
        </div>

        <div class="form-field"> </div>

        <div class="form-field">
          <label for="startDate">Start Date *</label>
          <input type="date" id="startDate" name="start_date" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-field">
          <label for="endDate">End Date</label>
          <input type="date" id="endDate" name="end_date">
          <small>Leave empty for no end date</small>
        </div>

        <!-- Coupons Section - Only visible when coupon-based is selected -->
        <div id="couponsSection" class="form-field span-2" style="display: none;">
          <div class="section-title">
            <h3>Coupons</h3>
            <button type="button" class="btn" onclick="addCoupon()">
              <span class="icon">add</span>
              Add Coupon
            </button>
          </div>

          <div id="couponsContainer" class="coupons-container">
            <!-- Coupon items will be added dynamically -->
          </div>
        </div>

        <div class="form-field span-2">
          <div class="section-title">
            <h3>Discount Conditions</h3>
            <button type="button" class="btn" onclick="addCondition()">
              <span class="icon">add</span>
              Add Condition
            </button>
          </div>

          <div id="conditionsContainer" class="conditions-container">
            <!-- Condition templates will be added dynamically -->
          </div>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeDiscountDialog()">
          Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <span class="icon">save</span>
          Save Discount
        </button>
      </div>
    </form>
  </div>
</dialog>

<!-- Discount Details Dialog -->
<dialog id="discountDetailsDialog" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Discount Details</h2>
      <button class="close-btn" aria-label="Close" onclick="closeDiscountDetailsDialog()">
        <span class="icon">close</span>
      </button>
    </div>


    <div class="detail-header">
      <h3 id="detail-name"></h3>
      <div id="detail-status"></div>
    </div>

    <p id="detail-description" class="detail-description"></p>

    <div class="detail-info">
      <div class="detail-row">
        <span class="label">Type:</span>
        <span id="detail-type" class="value"></span>
      </div>
      <div class="detail-row">
        <span class="label">Value:</span>
        <span id="detail-value" class="value"></span>
      </div>
      <div class="detail-row">
        <span class="label">Application:</span>
        <span id="detail-application" class="value"></span>
      </div>
      <div class="detail-row">
        <span class="label">Start Date:</span>
        <span id="detail-start-date" class="value"></span>
      </div>
      <div class="detail-row">
        <span class="label">End Date:</span>
        <span id="detail-end-date" class="value"></span>
      </div>
    </div>

    <div id="coupon-section" class="coupon-section">
      <h4>Coupons</h4>
      <div id="coupon-list" class="coupon-list"></div>
    </div>

    <div id="conditions-section" class="conditions-section details-section">
      <h4>Conditions</h4>
      <ul id="conditions-list" class="conditions-list"></ul>
    </div>
    <div class="form-actions">
      <button type="button" class="btn btn-secondary" onclick="closeDiscountDetailsDialog()">
        Close
      </button>
      <?php if ($canEditDiscounts): ?>
        <button type="button" class="btn btn-primary" id="edit-from-details-btn">
          <span class="icon">edit</span>
          Edit
        </button>
      <?php endif; ?>
    </div>
  </div>
</dialog>

<!-- Condition Template (hidden, used for cloning) -->
<template id="conditionTemplate">
  <div class="condition-card">
    <div class="condition-header">
      <select class="condition-type" name="conditions[INDEX][condition_type]" required onchange="updateConditionFields(this)">
        <option value="">Select Condition Type</option>
        <?php foreach ($conditionTypes as $value => $label): ?>
          <option value="<?= $value ?>"><?= $label ?></option>
        <?php endforeach; ?>
      </select>
      <button type="button" class="icon-btn danger" onclick="removeCondition(this)" title="Remove condition">
        <span class="icon">delete</span>
      </button>
    </div>
    <div class="condition-body">
      <!-- Condition fields will be injected here based on selected type -->
    </div>
  </div>
</template>

<!-- Coupon Item Template -->
<template id="couponTemplate">
  <div class="coupon-item">
    <div class="coupon-field">
      <div class="form-field">
        <div class="coupon-code-field">
          <input type="text" name="coupons[INDEX][code]" placeholder="Coupon code" required>
          <button type="button" class="btn" onclick="generateCouponCode(this)">Generate</button>
        </div>
      </div>
      <div class="form-field coupon-status">
        <div class="toggle-switch">
          <input type="checkbox" id="coupon_active_INDEX" name="coupons[INDEX][is_active]" checked>
          <label for="coupon_active_INDEX"></label>
          <span class="toggle-label">Active</span>
        </div>
      </div>
      <button type="button" class="icon-btn danger" onclick="removeCoupon(this)" title="Remove coupon">
        <span class="icon">delete</span>
      </button>
    </div>
  </div>
</template>

<!-- Minimum Quantity Condition Fields -->
<template id="min_quantity_template">
  <div class="form-grid">
    <div class="form-field span-2">
      <label for="order-items">Product *</label>
      <div id="order-items" class="search-bar">
        <span class="icon">search</span>
        <input type="text" placeholder="Search products..." oninput="searchProducts(event)">
        <div class="search-results"></div>
      </div>
      <div class="selected-product">
        <span class="selected-product-name">Product Name</span>
        <button type="button" class="icon-btn danger" onclick="removeSelectedProduct(this)">
          <span class="icon">delete</span>
        </button>
        <input type="hidden" name="conditions[INDEX][condition_value][product_id]" value="PRODUCT_ID">
      </div>
    </div>
    <div class="form-field span-2">
      <label>Minimum Quantity *</label>
      <input type="number" name="conditions[INDEX][condition_value][min_quantity]" min="1" required>
    </div>
  </div>
</template>

<!-- Minimum Amount Condition Fields -->
<template id="min_amount_template">
  <div class="form-field">
    <label>Minimum Purchase Amount (Rs.)</label>
    <input type="number" name="conditions[INDEX][condition_value][min_amount]" min="0" step="0.01" required>
  </div>
</template>

<!-- Time of Day Condition Fields -->
<template id="time_of_day_template">
  <div class="form-grid">
    <div class="form-field">
      <label>Start Time</label>
      <input type="time" name="conditions[INDEX][condition_value][start_time]" required>
    </div>
    <div class="form-field">
      <label>End Time</label>
      <input type="time" name="conditions[INDEX][condition_value][end_time]" required>
    </div>
  </div>
</template>

<!-- Day of Week Condition Fields -->
<template id="day_of_week_template">
  <div class="form-field">
    <label>Select Days</label>
    <div class="checkbox-grid">
      <?php foreach ($daysOfWeek as $value => $day): ?>
        <div class="checkbox-item">
          <input type="checkbox" id="day_INDEX_<?= $value ?>" name="conditions[INDEX][condition_value][days][]" value="<?= $value ?>">
          <span><?= $day ?></label>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</template>

<!-- Loyalty Points Condition Fields -->
<template id="loyalty_points_template">
  <div class="form-field">
    <label>Minimum Loyalty Points</label>
    <input type="number" name="conditions[INDEX][condition_value][min_points]" min="0" required>
  </div>
</template>

<script>
  const discounts = <?= json_encode($discounts) ?>;
</script>
<script src="/js/discounts.js"></script>