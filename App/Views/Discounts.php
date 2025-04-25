<?php

use App\Services\RBACService;

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

$page = $_GET['p'] ?? 1;
$itemsPerPage = $_GET['ipp'] ?? 10;
$searchQuery = $_GET['q'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$typeFilter = $_GET['type'] ?? '';
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';

$canAddDiscount = RBACService::hasPermission('add_discounts');
$canEditDiscounts = RBACService::hasPermission('edit_discounts');
$canDeleteDiscounts = RBACService::hasPermission('delete_discounts');
$canActivateDiscounts = RBACService::hasPermission('activate_discounts');
$canDeactivateDiscounts = RBACService::hasPermission('deactivate_discounts');
$canViewDiscounts = RBACService::hasPermission('view_discounts');
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
      <div class="search-bar-with-btn">
        <span class="icon">search</span>
        <input type="text" id="discountSearch" placeholder="Search by name or description" onkeydown="if (event.key === 'Enter') applyFilters()"
          value="<?= htmlspecialchars($searchQuery) ?>" autocomplete="off">
        <button class="icon-btn" onclick="applyFilters()">
          <span class="icon">search</span>
        </button>
      </div>

      <div class="filters">
        <select id="statusFilter" onchange="applyFilters()">
          <option value="" <?= $statusFilter === '' ? 'selected' : '' ?>>All Statuses</option>
          <option value="1" <?= $statusFilter === '1' ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= $statusFilter === '0' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <select id="typeFilter" onchange="applyFilters()">
          <option value="" <?= $typeFilter === '' ? 'selected' : '' ?>>All Types</option>
          <option value="percentage" <?= $typeFilter === 'percentage' ? 'selected' : '' ?>>Percentage</option>
          <option value="fixed" <?= $typeFilter === 'fixed' ? 'selected' : '' ?>>Fixed Amount</option>
        </select>

        <div class="date-filter">
          <input type="date" class="date-input" id="fromDate" placeholder="From date" onchange="applyFilters()" value="<?= $fromDate ?>">
          <span class="icon">arrow_forward</span>
          <input type="date" class="date-input" id="toDate" placeholder="To date" onchange="applyFilters()" value="<?= $toDate ?>">
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
            <?php endif; ?>

            <?php if ($canDeactivateDiscounts && $discount['is_active']): ?>
              <button class="icon-btn warning" onclick="toggleDiscountStatus(<?= $discount['id'] ?>, 0)" title="Deactivate discount">
                <span class="icon">pause</span>
              </button>
            <?php endif; ?>

            <?php if ($canActivateDiscounts && !$discount['is_active']): ?>
              <button class="icon-btn success" onclick="toggleDiscountStatus(<?= $discount['id'] ?>, 1)" title="Activate discount">
                <span class="icon">play_arrow</span>
              </button>
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
    <div class="pagination-controls card mt-lg">
      <div class="items-per-page">
        <span>Show:</span>
        <select class="items-select" onchange="changeItemsPerPage(this.value)">
          <option value="10" <?= $itemsPerPage == 10 ? 'selected' : '' ?>>10</option>
          <option value="25" <?= $itemsPerPage == 25 ? 'selected' : '' ?>>25</option>
          <option value="50" <?= $itemsPerPage == 50 ? 'selected' : '' ?>>50</option>
          <option value="100" <?= $itemsPerPage == 100 ? 'selected' : '' ?>>100</option>
        </select>
        <span>entries</span>
      </div>

      <div class="pagination" data-page="<?= $page ?>" data-total-pages="<?= $totalPages ?>">
        <!-- Pagination will be generated here by JavaScript -->
      </div>
    </div>
  </div>
</div>

<!-- Create/Edit Discount Dialog -->
<?php if ($canAddDiscount || $canEditDiscounts): ?>
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
            <label for="startDate">Start Date *</label>
            <input type="date" id="startDate" name="start_date" value="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="form-field">
            <label for="endDate">End Date</label>
            <input type="date" id="endDate" name="end_date">
            <small>Leave empty for no end date</small>
          </div>

          <div class="form-field span-2">
            <div class="toggle-switch">
              <input type="checkbox" id="isCombinable" name="is_combinable">
              <label for="isCombinable"></label>
              <span class="toggle-label">Can be combined with other discounts</span>
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
<?php endif; ?>

<!-- Discount Details Dialog -->
<?php if ($canViewDiscounts): ?>
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
          <span class="label">Start Date:</span>
          <span id="detail-start-date" class="value"></span>
        </div>
        <div class="detail-row">
          <span class="label">End Date:</span>
          <span id="detail-end-date" class="value"></span>
        </div>
        <div class="detail-row">
          <span class="label">Combinable:</span>
          <span id="detail-combinable" class="value"></span>
        </div>
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
<?php endif; ?>

<!-- Templates for condition types - Only include if user can add or edit discounts -->
<?php if ($canAddDiscount || $canEditDiscounts): ?>
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

  <!-- Minimum Quantity Condition Fields -->
  <template id="min_quantity_template">
    <div class="form-grid">
      <div class="form-field span-2">
        <label>Product *</label>
        <div class="search-bar">
          <span class="icon">search</span>
          <input type="text" placeholder="Search products..." oninput="searchProducts(event)">
          <div class="search-results"></div>
        </div>
        <div class="selected-product" style="display: none;">
          <span class="selected-product-name"></span>
          <input type="hidden" name="conditions[INDEX][condition_value][product_id]">
          <input type="hidden" name="conditions[INDEX][condition_value][product_name]">
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
<?php endif; ?>

<script>
  const discounts = <?= json_encode($discounts) ?>;
</script>
<script src="/js/search.js"></script>
<script src="/js/discounts.js"></script>