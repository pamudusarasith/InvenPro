<?php

use App\Services\RBACService;

// Get current filters from request
$currentStatus = $_GET['status'] ?? '';
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';
$searchQuery = $_GET['q'] ?? '';

// Parse other query parameters
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$itemsPerPage = isset($_GET['ipp']) ? intval($_GET['ipp']) : 10;

// Helper function to generate a unique PO number
function generateOrderReference()
{
  return 'PO-' . date('Ymd') . '-' . substr(uniqid(), -5);
}

// Helper function to get badge class based on status
function getStatusBadgeClass($status)
{
  switch ($status) {
    case 'pending':
      return 'warning';
    case 'open':
      return 'accent';
    case 'completed':
      return 'success';
    case 'canceled':
      return 'danger';
    default:
      return 'secondary';
  }
}

$canCreateOrder = RBACService::hasPermission('create_purchase_order');
?>

<link rel="stylesheet" href="/css/pages/purchase-orders.css">

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Purchase Orders</h1>
        <p class="subtitle">Manage purchase orders across all suppliers</p>
      </div>

      <div class="header-actions">
        <?php if ($canCreateOrder): ?>
          <button class="btn btn-primary" onclick="openCreateOrderModal()">
            <span class="icon">add</span>
            Create Order
          </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Filters and search controls -->
    <div class="card glass controls">
      <div class="search-bar-with-btn">
        <span class="icon">search</span>
        <input type="text" id="orderSearch" placeholder="Search by PO number or supplier name..."
          value="<?= htmlspecialchars($searchQuery) ?>">
        <button class="icon-btn" onclick="applyFilters()">
          <span class="icon">search</span>
        </button>
      </div>

      <div class="filters">
        <select id="statusFilter" onchange="applyFilters()">
          <option value="" <?= $currentStatus === '' ? 'selected' : '' ?>>All Statuses</option>
          <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="open" <?= $currentStatus === 'open' ? 'selected' : '' ?>>Open</option>
          <option value="completed" <?= $currentStatus === 'completed' ? 'selected' : '' ?>>Completed</option>
          <option value="canceled" <?= $currentStatus === 'canceled' ? 'selected' : '' ?>>Canceled</option>
        </select>

        <div class="date-filter">
          <input type="date" class="date-input" id="fromDate" placeholder="From date"
            value="<?= $fromDate ?>" onchange="applyFilters()">
          <span class="icon">arrow_forward</span>
          <input type="date" class="date-input" id="toDate" placeholder="To date"
            value="<?= $toDate ?>" onchange="applyFilters()">
        </div>
      </div>
    </div>

    <!-- Table view of purchase orders -->
    <div class="table-container">
      <table class="data-table clickable">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Supplier</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Total Amount</th>
            <th>Items</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="7" style="text-align: center;">No purchase orders found</td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <tr onclick="window.location.href='/orders/<?= $order['id'] ?>'">
                <td><?= htmlspecialchars($order['reference']) ?></td>
                <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                <td>
                  <span class="badge <?= getStatusBadgeClass($order['status']) ?>">
                    <?= ucfirst($order['status']) ?>
                  </span>
                </td>
                <td><?= $order['total_amount'] ? "Rs." . number_format($order['total_amount'], 2) : "N/A" ?></td>
                <td><?= $order['items'] ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <!-- Pagination controls -->
      <div class="pagination-controls">
        <div class="items-per-page">
          <span>Show:</span>
          <select class="items-select" onchange="changeItemsPerPage(this.value)">
            <option value="10" <?= $itemsPerPage === 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $itemsPerPage === 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $itemsPerPage === 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $itemsPerPage === 100 ? 'selected' : '' ?>>100</option>
          </select>
          <span>entries</span>
        </div>

        <div class="pagination" data-page="<?= $page ?>" data-total-pages="<?= $totalPages ?>">
          <!-- Pagination will be generated here by JavaScript -->
        </div>
      </div>
    </div>
  </div>
</div>

<?php if ($canCreateOrder): ?>
  <!-- Create Order Modal Dialog -->
  <dialog id="createOrderModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Create Purchase Order</h2>
        <button class="close-btn" onclick="closeCreateOrderModal()">
          <span class="icon">close</span>
        </button>
      </div>

      <form id="createOrderForm" method="POST" action="/orders/new">
        <div class="form-grid">
          <div class="form-field span-2">
            <label for="order-reference">Order Reference *</label>
            <input type="text" id="order-reference" name="reference" value="<?= generateOrderReference() ?>" required>
          </div>

          <div class="form-field span-2">
            <label for="order-supplier">Supplier *</label>
            <div id="order-supplier" class="search-bar">
              <span class="icon">search</span>
              <input type="text" placeholder="Search Suppliers...">
              <div class="search-results"></div>
            </div>
          </div>

          <div class="form-field span-2" style="display: none;">
            <div id="supplier_details" class="supplier-info">
              <div class="supplier-info-item">
                <span class="icon">business</span>
                <span id="supplier_name"></span>
              </div>
              <div class="supplier-info-item">
                <span class="icon">person</span>
                <span id="contact_person"></span>
              </div>
              <div class="supplier-info-item">
                <span class="icon">email</span>
                <span id="supplier_email"></span>
              </div>
              <div class="supplier-info-item">
                <span class="icon">phone</span>
                <span id="supplier_phone"></span>
              </div>
            </div>
            <input type="hidden" id="supplier_id" name="supplier_id">
          </div>

          <div class="form-field">
            <label for="order_date">Order Date*</label>
            <input type="date" id="order_date" name="order_date" value="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="form-field">
            <label for="expected_date">Expected Delivery Date</label>
            <input type="date" id="expected_date" name="expected_date">
          </div>

          <div class="form-field span-2">
            <label for="order-items">Order Items *</label>
            <div id="order-items" class="search-bar">
              <span class="icon">search</span>
              <input type="text" placeholder="Search products..." oninput="searchProducts(event)">
              <div class="search-results"></div>
            </div>
          </div>

          <div id="add-item-form" class="form-field span-2 card glass" style="display: none;">
            <div class="form-grid">
              <div class="form-field span-2">
                <label for="order-item-qty">Quantity</label>
                <input id="order-item-qty" type="number">
              </div>
              <div class="form-field span-2 add-item-actions">
                <button type="button" class="btn btn-secondary cancel-btn">Cancel</button>
                <button type="button" class="btn btn-primary add-btn">Add</button>
              </div>
            </div>
          </div>

          <div class="form-field span-2 table-container">
            <table id="order-items-tbl" class="data-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="form-field span-2">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Add any notes or special instructions for this order"></textarea>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="closeCreateOrderModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Order</button>
        </div>
      </form>
    </div>
  </dialog>
<?php endif; ?>

<script src="/js/search.js"></script>
<script>
  <?php if ($canCreateOrder): ?>
    class OrderForm {
      constructor(formElement) {
        this.form = formElement;
        this.supplier = null;
        this.items = new Map();

        // Initialize search handlers after DOM is loaded
        this.initSearchHandlers();
      }

      init() {
        this.form.onsubmit = (e) => this.submit(e);
        document.querySelector("#add-item-form .cancel-btn").onclick =
          this.cancelAddItemForm;
        this.renderItems();
      }

      initSearchHandlers() {
        // Supplier search handler
        this.supplierSearch = new SearchHandler({
          apiEndpoint: "/api/suppliers/search",
          inputElement: this.form.querySelector("#order-supplier input"),
          resultsContainer: this.form.querySelector(
            "#order-supplier .search-results"
          ),
          itemsPerPage: 5,
          renderResultItem: (supplier) => {
            const element = document.createElement("div");
            element.classList.add("search-result");
            element.textContent = supplier.supplier_name;
            return element;
          },
          onSelect: (supplier) => this.selectSupplier(supplier),
        });

        // Products search handler
        this.productSearch = new SearchHandler({
          apiEndpoint: "/api/suppliers/{supplier_id}/products/search",
          inputElement: this.form.querySelector("#order-items input"),
          resultsContainer: this.form.querySelector("#order-items .search-results"),
          itemsPerPage: 5,
          renderResultItem: (product) => {
            const element = document.createElement("div");
            element.classList.add("search-result");
            element.textContent = product.product_name;
            return element;
          },
          onSelect: (product) => this.showAddItemForm(product),
        });
      }

      selectSupplier(supplier) {
        this.supplier = supplier;

        this.form
          .querySelector("#supplier_details")
          .closest(".form-field").style.display = "flex";
        this.form.querySelector("#supplier_name").textContent =
          supplier.supplier_name;
        this.form.querySelector("#contact_person").textContent =
          supplier.contact_person;
        this.form.querySelector("#supplier_email").textContent = supplier.email;
        this.form.querySelector("#supplier_phone").textContent = supplier.phone;
        this.form.querySelector("#supplier_id").value = supplier.id;

        // Update product search with current supplier ID
        this.productSearch.updateParams({
          supplier_id: supplier.id,
        });
        this.productSearch.apiEndpoint = `/api/suppliers/${supplier.id}/products/search`;
      }

      addItem(product) {
        const form = document.getElementById("add-item-form");
        const quantity = form.querySelector("#order-item-qty").value;
        if (!quantity || quantity <= 0) {
          alert("Please enter a valid quantity");
          return;
        }

        this.items.set(product.id, {
          ...product,
          quantity,
        });

        form.querySelectorAll("input").forEach((elem) => (elem.value = ""));
        form.style.display = "none";

        this.renderItems();
      }

      cancelAddItemForm() {
        const form = document.getElementById("add-item-form");
        form.querySelectorAll("input").forEach((elem) => (elem.value = ""));
        form.style.display = "none";
      }

      showAddItemForm(product) {
        if (this.items.has(product.id)) {
          alert("Item already added");
          return;
        }

        const form = document.getElementById("add-item-form");
        form.style.display = "flex";
        form.querySelector(".add-btn").onclick = () => this.addItem(product);
      }

      renderItems() {
        const table = document.getElementById("order-items-tbl");
        const tbody = table.querySelector("tbody");
        if (this.items.size === 0) {
          tbody.innerHTML = `
        <tr class="table-empty-msg">
          <td colspan="3" style="text-align: center;">No items added</td>
        </tr>
      `;
          return;
        }

        tbody.innerHTML = "";
        this.items.forEach((product) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
        <td>${product.product_name}</td>
        <td>${product.quantity}</td>
      `;
          const removeBtn = document.createElement("button");
          removeBtn.classList.add("icon-btn", "danger");
          removeBtn.innerHTML = "<span class='icon'>delete</span>";
          removeBtn.onclick = () => {
            this.items.delete(product.id);
            this.renderItems();
          };

          const input = document.createElement("input");
          input.type = "hidden";
          input.name = "items[]";
          input.value = JSON.stringify({
            id: product.id,
            quantity: product.quantity,
          });

          const td = document.createElement("td");
          td.appendChild(removeBtn);
          td.appendChild(input);
          tr.appendChild(td);
          tbody.appendChild(tr);
        });

        if (document.getElementById("items_count")) {
          document.getElementById("items_count").textContent =
            this.items.size.toString();
        }
      }

      submit(e) {
        if (this.supplier === null) {
          e.preventDefault();
          alert("Please select a supplier");
        } else if (this.items.size === 0) {
          e.preventDefault();
          alert("Please add at least one item");
        }
      }
    }

    function openCreateOrderModal() {
      const modal = document.getElementById("createOrderModal");
      modal.showModal();
    }

    function closeCreateOrderModal() {
      const modal = document.getElementById("createOrderModal");
      modal.close();
    }

    document.addEventListener("DOMContentLoaded", function() {
      const createOrderForm = document.getElementById("createOrderForm");
      const orderForm = new OrderForm(createOrderForm);
      orderForm.init();
    });
  <?php endif; ?>

  function applyFilters() {
    const status = document.getElementById("statusFilter").value;
    const fromDate = document.getElementById("fromDate").value;
    const toDate = document.getElementById("toDate").value;
    const searchQuery = document.getElementById("orderSearch").value;

    const url = new URL(window.location.href);
    status
      ?
      url.searchParams.set("status", status) :
      url.searchParams.delete("status");
    fromDate
      ?
      url.searchParams.set("from", fromDate) :
      url.searchParams.delete("from");
    toDate ? url.searchParams.set("to", toDate) : url.searchParams.delete("to");
    searchQuery
      ?
      url.searchParams.set("q", searchQuery) :
      url.searchParams.delete("q");

    window.location.href = url.href;
  }

  document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".pagination").forEach((pagination) => {
      const currentPage = parseInt(pagination.dataset.page);
      const totalPages = parseInt(pagination.dataset.totalPages);

      // Insert pagination
      insertPagination(pagination, currentPage, totalPages, (page) => {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set("p", page);
        window.location.href = currentUrl.href;
      });
    });

    document.getElementById("orderSearch").addEventListener("keyup", function(event) {
      if (event.key === "Enter") {
        applyFilters();
      }
    });
  });
</script>
