<?php

use App\Services\RBACService;

// Get current filters from request
$currentStatus = $_GET['status'] ?? 'all';
$currentSupplier = $_GET['supplier'] ?? 'all';
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

// Parse other query parameters
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$perPage = isset($_GET['ipp']) ? intval($_GET['ipp']) : 10;
$view = $_GET['view'] ?? 'table';

// Helper function to generate a unique PO number
function generateOrderReference()
{
  return 'PO-' . date('Ymd') . '-' . substr(uniqid(), -5);
}

?>

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <div class="card glass page-header">
      <div class="header-content">
        <h1>Purchase Orders</h1>
        <p class="subtitle">Manage purchase orders across all suppliers</p>
      </div>

      <?php if (RBACService::hasPermission('create_purchase_order')): ?>
        <div class="header-actions">
          <button class="btn btn-primary" onclick="openCreateOrderModal()">
            <span class="icon">add</span>
            Create Order
          </button>
        </div>
      <?php endif; ?>
    </div>

    <!-- Filters and search controls -->
    <div class="card glass controls">
      <div class="search-bar">
        <span class="icon">search</span>
        <input type="text" id="orderSearch" placeholder="Search by PO number or supplier name...">
      </div>

      <div class="filters">
        <select id="statusFilter" onchange="applyFilters()">
          <option value="all" <?= $currentStatus === 'all' ? 'selected' : '' ?>>All Statuses</option>
          <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
          <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="approved" <?= $currentStatus === 'approved' ? 'selected' : '' ?>>Approved</option>
          <option value="ordered" <?= $currentStatus === 'ordered' ? 'selected' : '' ?>>Ordered</option>
          <option value="received" <?= $currentStatus === 'received' ? 'selected' : '' ?>>Received</option>
          <option value="cancelled" <?= $currentStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
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
                  <span class="badge order-status status-<?= $order['status'] ?>">
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
            <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $perPage === 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100</option>
          </select>
          <span>entries</span>
        </div>

        <div class="pagination">
          <!-- Previous button -->
          <button class="page-btn" <?= $page <= 1 ? 'disabled' : '' ?>
            onclick="goToPage(<?= $page - 1 ?>)">
            <span class="icon">navigate_before</span>
          </button>

          <!-- Page numbers -->
          <div class="page-numbers">
            <?php
            $totalPages = ceil($totalOrders / $perPage);
            $startPage = max(1, min($page - 2, $totalPages - 4));
            $endPage = min($totalPages, max($page + 2, 5));

            if ($startPage > 1) {
              echo '<button class="page-number" onclick="goToPage(1)">1</button>';
              if ($startPage > 2) {
                echo '<span class="page-dots">...</span>';
              }
            }

            for ($i = $startPage; $i <= $endPage; $i++) {
              echo '<button class="page-number ' . ($page === $i ? 'active' : '') . '" onclick="goToPage(' . $i . ')">' . $i . '</button>';
            }

            if ($endPage < $totalPages) {
              if ($endPage < $totalPages - 1) {
                echo '<span class="page-dots">...</span>';
              }
              echo '<button class="page-number" onclick="goToPage(' . $totalPages . ')">' . $totalPages . '</button>';
            }
            ?>
          </div>

          <!-- Next button -->
          <button class="page-btn" <?= $page >= $totalPages ? 'disabled' : '' ?>
            onclick="goToPage(<?= $page + 1 ?>)">
            <span class="icon">navigate_next</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

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

<script>
  class OrderForm {
    constructor(formElement) {
      this.form = formElement;
      this.supplierSearch = {
        query: "",
        results: [],
        page: 0,
      };
      this.supplier = null;
      this.productSearch = {
        query: "",
        results: [],
        page: 0,
      };
      this.items = new Map();
    }

    init() {
      this.form
        .querySelector("#order-supplier input")
        .addEventListener("input", (e) => this.searchSuppliers(e.target.value));
      this.form
        .querySelector("#order-supplier input")
        .addEventListener("focusin", (e) => this.searchSuppliers(e.target.value));
      this.form
        .querySelector("#order-supplier input")
        .addEventListener("focusout", () => {
          setTimeout(() => {
            this.form.querySelector("#order-supplier .search-results").innerHTML =
              "";
          }, 200);
        });
      this.form
        .querySelector("#order-supplier .search-results")
        .addEventListener("scrollend", (e) =>
          this.searchSuppliers(
            this.supplierSearch.query,
            this.supplierSearch.page + 1
          )
        );

      this.form
        .querySelector("#order-items input")
        .addEventListener("input", (e) => this.searchProducts(e.target.value));
      this.form
        .querySelector("#order-items input")
        .addEventListener("focusin", (e) => this.searchProducts(e.target.value));
      this.form
        .querySelector("#order-items input")
        .addEventListener("focusout", () => {
          setTimeout(() => {
            this.form.querySelector("#order-items .search-results").innerHTML =
              "";
          }, 200);
        });
      this.form
        .querySelector("#order-items .search-results")
        .addEventListener("scrollend", (e) =>
          this.searchProducts(
            this.productSearch.query,
            this.productSearch.page + 1
          )
        );

      this.form.onsubmit = (e) => this.submit(e);

      document.querySelector("#add-item-form .cancel-btn").onclick =
        this.cancelAddItemForm;

      this.renderItems();
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

      this.supplierSearch.results = [];
      this.supplierSearch.query = "";
      this.supplierSearch.page = 0;
    }

    renderSupplierSearchResults() {
      const resultsContainer = this.form.querySelector(
        "#order-supplier .search-results"
      );
      resultsContainer.innerHTML = "";

      this.supplierSearch.results.forEach((supplier) => {
        const supplierElement = document.createElement("div");
        supplierElement.classList.add("search-result");
        supplierElement.textContent = supplier.supplier_name;
        supplierElement.addEventListener("click", () =>
          this.selectSupplier(supplier)
        );

        resultsContainer.appendChild(supplierElement);
      });
    }

    async searchSuppliers(query, page = 1) {
      const response = await fetch(
        `/api/suppliers/search?q=${query}&p=${page}&ipp=5`
      );
      const data = await response.json();

      if (!data.success) {
        console.error("Failed to search suppliers:", data.error);
        return;
      }

      if (data.data.length === 0) return;

      if (document.querySelector("#order-supplier input").value !== query) return;

      if (query !== this.supplierSearch.query && page === 1) {
        this.supplierSearch.query = query;
        this.supplierSearch.results = data.data;
        this.supplierSearch.page = 1;
      } else if (
        query === this.supplierSearch.query &&
        page > this.supplierSearch.page
      ) {
        this.supplierSearch.results = this.supplierSearch.results.concat(
          data.data
        );
        this.supplierSearch.page = page;
      }

      this.renderSupplierSearchResults();
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

      console.log(this.items);
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
        input.name = "order_items[]";
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

      document.getElementById("items_count").textContent =
        this.items.size.toString();
    }

    renderProductSearchResults() {
      const resultsContainer = this.form.querySelector(
        "#order-items .search-results"
      );
      resultsContainer.innerHTML = "";

      if (this.productSearch.results.length === 0) {
        const elem = document.createElement("div");
        elem.classList.add("search-result");
        elem.textContent = "No results";
        resultsContainer.appendChild(elem);
        return;
      }

      this.productSearch.results.forEach((product) => {
        const productElement = document.createElement("div");
        productElement.classList.add("search-result");
        productElement.textContent = product.product_name;
        productElement.addEventListener("click", () =>
          this.showAddItemForm(product)
        );

        resultsContainer.appendChild(productElement);
      });
    }

    async searchProducts(query, page = 1) {
      if (!this.supplier) return;
      const response = await fetch(
        `/api/suppliers/${this.supplier.id}/products/search?q=${query}&p=${page}&ipp=5`
      );
      const data = await response.json();

      if (!data.success) {
        console.error("Failed to search products:", data.error);
        return;
      }

      if (data.data.length === 0) return;

      if (document.querySelector("#order-items input").value !== query) return;

      if (query !== this.productSearch.query && page === 1) {
        this.productSearch.query = query;
        this.productSearch.results = data.data;
        this.productSearch.page = 1;
      } else if (
        query === this.productSearch.query &&
        page > this.productSearch.page
      ) {
        this.productSearch.results = this.productSearch.results.concat(data.data);
        this.productSearch.page = page;
      }

      this.renderProductSearchResults();
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

  // Modify the existing event listener to include the modal initialization
  document.addEventListener("DOMContentLoaded", function() {
    const createOrderForm = document.getElementById("createOrderForm");
    const orderForm = new OrderForm(createOrderForm);
    orderForm.init();
  });

  // Pagination functions
  function goToPage(page) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set("p", page);
    window.location.href = currentUrl.href;
  }

  function changeItemsPerPage(perPage) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set("ipp", perPage);
    currentUrl.searchParams.set("p", 1);
    window.location.href = currentUrl.href;
  }

  function openCreateOrderModal() {
    const modal = document.getElementById("createOrderModal");
    modal.showModal();
  }

  // Function to close create order modal
  function closeCreateOrderModal() {
    const modal = document.getElementById("createOrderModal");
    modal.close();
  }
</script>