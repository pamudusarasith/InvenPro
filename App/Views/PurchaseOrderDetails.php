<?php

use App\Services\RBACService;

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

$canEditOrder = RBACService::hasPermission('edit_purchase_orders');
$canApproveOrder = RBACService::hasPermission('approve_purchase_orders');
$canCancelOrder = RBACService::hasPermission('cancel_purchase_orders');
$canDeleteOrder = RBACService::hasPermission('delete_purchase_orders');
$canReceiveItems = RBACService::hasPermission('receive_purchase_orders');
$canCompleteOrder = RBACService::hasPermission('complete_purchase_orders');
?>

<link rel="stylesheet" href="/css/pages/purchase-order-details.css">

<div class="body">
  <?php App\Core\View::render("Navbar") ?>
  <?php App\Core\View::render("Sidebar") ?>

  <div class="main">
    <!-- Order details header -->
    <div class="card mb-lg row justify-between pad-lg">
      <div class="col gap-md">
        <div class="row gap-md items-center">
          <h1 class=""><?= htmlspecialchars($order['reference']) ?></h1>
          <span class="badge <?= getStatusBadgeClass($order['status']) ?> self-center">
            <?= $order['status'] ?>
          </span>
        </div>

        <div class="row flex-wrap gap-md">
          <div class="row items-center">
            <span class="icon text-info">store</span>
            <span class="text text-secondary"><?= htmlspecialchars($order['supplier_name']) ?></span>
          </div>
          <div class="row flex-wrap items-center">
            <span class="icon text-info">event</span>
            <span class="text text-secondary">Ordered: <?= date('M d, Y', strtotime($order['order_date'])) ?></span>
          </div>
          <div class="row flex-wrap items-center">
            <span class="icon text-info">person</span>
            <span class="text text-secondary">Created by: <?= htmlspecialchars($order['created_by_name']) ?></span>
          </div>
        </div>
      </div>

      <div>
        <?php if ($canEditOrder): ?>
          <div id="edit-actions" class="row gap-md" style="display: none;">
            <button class="btn btn-secondary" onclick="cancelEdit()">
              <span class="icon">close</span>
              Cancel
            </button>
            <button class="btn btn-primary" onclick="saveChanges()">
              <span class="icon">save</span>
              Save
            </button>
          </div>
        <?php endif; ?>

        <div class="dropdown">
          <button class="dropdown-trigger icon-btn" title="More options">
            <span class="icon">more_vert</span>
          </button>
          <div class="dropdown-menu">
            <?php if ($canEditOrder && $order['status'] === 'pending'): ?>
              <button class="dropdown-item" onclick="enableEdit(event)">
                <span class="icon">edit</span>
                Edit Order
              </button>
            <?php endif; ?>

            <?php if ($canApproveOrder && $order['status'] === 'pending'): ?>
              <button class="dropdown-item" onclick="approveOrder()">
                <span class="icon">done</span>
                Approve Order
              </button>
            <?php endif; ?>

            <?php if ($canCancelOrder && $order['status'] === 'pending'): ?>
              <button class="dropdown-item danger" onclick="cancelOrder()">
                <span class="icon">cancel</span>
                Cancel Order
              </button>
            <?php endif; ?>

            <?php if ($canReceiveItems && $order['status'] === 'open'): ?>
              <button class="dropdown-item" onclick="openReceiveItemsDialog()">
                <span class="icon">add</span>
                Add Received Items
              </button>
            <?php endif; ?>

            <?php if ($canCompleteOrder && $order['status'] === 'open'): ?>
              <button class="dropdown-item" onclick="completeOrder()">
                <span class="icon">inventory</span>
                Complete Order
              </button>
            <?php endif; ?>

            <?php if ($canDeleteOrder && in_array($order['status'], ['pending', 'canceled'])): ?>
              <button class="dropdown-item danger" onclick="deleteOrder()">
                <span class="icon">delete</span>
                Delete Order
              </button>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <span class="icon">payments</span>
          <span class="stat-label">Total Amount</span>
        </div>
        <div class="stat-value"><?= $order['total_amount'] ? 'Rs. ' . number_format($order['total_amount'], 2) : 'N/A' ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-header">
          <span class="icon">category</span>
          <span class="stat-label">Items Count</span>
        </div>
        <div class="stat-value"><?= count($order['items']) ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-header">
          <span class="icon">event_available</span>
          <span class="stat-label">Expected Delivery</span>
        </div>
        <div class="stat-value">
          <?= $order['expected_date'] ? date('M d, Y', strtotime($order['expected_date'])) : 'Not specified' ?>
        </div>
      </div>
    </div>

    <div class="tab-nav">
      <button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
      <!-- <button class="tab-btn" onclick="switchTab('timeline')">Timeline</button> -->
    </div>

    <input type="hidden" name="supplier_id" value="<?= $order['supplier_id'] ?>">

    <!-- Order Details Tab -->
    <div id="overview" class="tab-content active">
      <form id="details-form" method="POST" action="/orders/<?= $order['id'] ?>/update">
        <div class="card">
          <h3>Order Information</h3>
          <div class="content form-grid">
            <div class="form-field" style="display: none;">
              <label for="order_reference">Order Reference</label>
              <input id="order_reference" type="text" name="reference" value="<?= htmlspecialchars($order['reference']) ?>" disabled>
            </div>

            <div class="form-field" style="display: none;">
              <label for="expected_date">Expected Delivery Date</label>
              <input id="expected_date" type="date" name="expected_date" value="<?= $order['expected_date'] ?>" disabled>
            </div>

            <div class="form-field span-2">
              <label for="order-items">Order Items</label>
              <div id="order-item-search" class="search-bar mb-md" style="display: none;">
                <span class="icon">search</span>
                <input type="text" placeholder="Search products..." oninput="searchProducts(event)">
                <div class="search-results"></div>
              </div>
              <div class=" table-container">
                <table id="order-items" class="data-table">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Ordered Qty</th>
                      <?php if (in_array($order['status'], ['open', 'completed'])): ?>
                        <th>Received Qty</th>
                        <th>Subtotal</th>
                      <?php endif; ?>
                      <th style="display: none;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                      <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= (
                              $item['is_int']
                              ? intval($item['order_qty'])
                              : number_format($item['order_qty'], 3)
                            ) . ' ' . $item['unit_symbol'] ?></td>
                        <?php if (in_array($order['status'], ['open', 'completed'])): ?>
                          <td><?= (
                                $item['is_int']
                                ? intval($item['received_qty'])
                                : number_format($item['received_qty'], 3)
                              ) . ' ' . $item['unit_symbol'] ?></td>
                          <?php
                          $total = array_reduce($item['batches'], function ($carry, $batch) {
                            return $carry + ($batch['unit_cost'] * $batch['quantity']);
                          }, 0);
                          ?>
                          <td><?= 'Rs. ' . number_format($total, 2) ?></td>
                        <?php endif; ?>
                        <td style="display: none;">
                          <button class="icon-btn danger" title="Remove Item" onclick="removeItem(event)">
                            <span class="icon">delete</span>
                          </button>
                        </td>
                        <input type="hidden" name="items[]" value='{"id": <?= $item['product_id'] ?>, "quantity": <?= $item['order_qty'] ?> }' data-id="<?= $item['product_id'] ?>">
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <?php if ($order['status'] === 'completed'): ?>
                    <tfoot>
                      <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                        <td style="font-weight: bold;">Rs. <?= number_format($order['total_amount'], 2) ?></td>
                      </tr>
                    </tfoot>
                  <?php endif; ?>
                </table>
              </div>
            </div>

            <div class="form-field span-2">
              <label for="order_notes">Notes</label>
              <textarea id="order_notes" name="notes" rows="5" disabled><?= htmlspecialchars($order['notes']) ?></textarea>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Timeline Tab -->
    <div id="timeline" class="tab-content">
      <div class="card">
        <h3>Order Timeline</h3>
        <div class="content">
          <ul class="timeline">
            <li class="timeline-item created">
              <div class="timeline-date">Apr 10, 2025 - 09:30 AM</div>
              <div class="timeline-content">Order Created</div>
              <div class="timeline-meta">by John Manager</div>
            </li>
            <li class="timeline-item pending">
              <div class="timeline-date">Apr 10, 2025 - 09:30 AM</div>
              <div class="timeline-content">Order Submitted for Approval</div>
              <div class="timeline-meta">by John Manager</div>
            </li>
            <li class="timeline-item approved">
              <div class="timeline-date">Apr 11, 2025 - 11:15 AM</div>
              <div class="timeline-content">Order Approved</div>
              <div class="timeline-meta">by Jane Supervisor</div>
            </li>
            <li class="timeline-item received">
              <div class="timeline-date">Apr 12, 2025 - 02:45 PM</div>
              <div class="timeline-content">Order Sent to Supplier</div>
              <div class="timeline-meta">by John Manager</div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if ($canEditOrder): ?>
  <dialog id="add-item-dialog" class="modal">
    <div class="modal-content">
      <div id="add-item-form" class="form-grid">
        <div class="form-field span-2">
          <label for="add-item-qty">Quantity</label>
          <input id="add-item-qty" type="number">
        </div>
        <div class="form-field span-2 add-item-actions">
          <button type="button" class="btn btn-secondary cancel-btn">Cancel</button>
          <button type="button" class="btn btn-primary add-btn">Add</button>
        </div>
      </div>
    </div>
  </dialog>
<?php endif; ?>

<?php if ($canReceiveItems): ?>
  <dialog id="receive-items-dialog" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Receive Items: <?= htmlspecialchars($order['reference']) ?></h2>
        <button class="close-btn" onclick="closeReceiveItemsDialog()">
          <span class="icon">close</span>
        </button>
      </div>
      <form id="receive-items-form" method="POST" action="/orders/<?= $order['id'] ?>/receive">
        <div class="receive-items-container">
          <?php foreach ($order['items'] as $item): ?>
            <div class="card glass">
              <div class="product-header" onclick="toggleProductCard(event)">
                <div class="col gap-sm">
                  <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                  <div class="row gap-md">
                    <span class="badge accent">Ordered: <?= ($item['is_int'] ? intval($item['order_qty']) : number_format($item['order_qty'], 3)) . ' ' . $item['unit_symbol'] ?></span>
                    <span class="badge" id="received-qty-badge-<?= $item['product_id'] ?>">Received: <?= (
                                                                                                        $item['is_int']
                                                                                                        ? intval($item['received_qty'])
                                                                                                        : number_format($item['received_qty'], 3)
                                                                                                      ) . ' ' . $item['unit_symbol'] ?></span>
                  </div>
                </div>
                <span class="icon toggle-icon">expand_more</span>
              </div>

              <div class="product-content" data-product-id="<?= $item['product_id'] ?>" style="display: none;">
                <!-- Batches container -->
                <div class="batches-container">
                  <?php foreach ($item['batches'] as $idx => $batch): ?>
                    <div class="batch-card mb-sm">
                      <div class="batch-header">
                        <h4>Batch #<span class="batch-number"><?= $idx ?></span></h4>
                        <button type="button" class="icon-btn danger" title="Remove Batch" onclick="removeBatch(event)">
                          <span class="icon">delete</span>
                        </button>
                      </div>
                      <div class="form-grid">
                        <input type="hidden" name="batches[BATCH_INDEX][product_id]" value="<?= $item['product_id'] ?>">
                        <div class="form-field">
                          <label>Batch Code *</label>
                          <input type="text"
                            name="batches[BATCH_INDEX][batch_code]"
                            value="<?= $batch['batch_code'] ?>"
                            placeholder="BAT-001"
                            required>
                        </div>
                        <div class="form-field">
                          <label>Quantity *</label>
                          <input type="number"
                            name="batches[BATCH_INDEX][received_qty]"
                            value="<?= $item['is_int']
                                      ? intval($batch['quantity'])
                                      : number_format($batch['quantity'], 3) ?>"
                            min="0"
                            step="<?= $item['is_int'] ? '1' : '0.001' ?>"
                            placeholder="0"
                            required
                            onchange="updateTotalReceivedQty(<?= $item['product_id'] ?>)">
                        </div>
                        <div class="form-field">
                          <label>Unit Cost *</label>
                          <input type="number"
                            name="batches[BATCH_INDEX][unit_cost]"
                            value="<?= $batch['unit_cost'] ?>"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            required>
                        </div>
                        <div class="form-field">
                          <label>Selling Price *</label>
                          <input type="number"
                            name="batches[BATCH_INDEX][unit_price]"
                            value="<?= $batch['unit_price'] ?>"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            required>
                        </div>
                        <div class="form-field">
                          <label>Manufacturing Date</label>
                          <input type="date"
                            name="batches[BATCH_INDEX][manufactured_date]"
                            value="<?= $batch['manufactured_date'] ?>">
                        </div>
                        <div class="form-field">
                          <label>Expiry Date</label>
                          <input type="date"
                            name="batches[BATCH_INDEX][expiry_date]"
                            value="<?= $batch['expiry_date'] ?>">
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <!-- Template for a new batch (hidden) -->
                <template>
                  <div class="batch-card mb-sm">
                    <div class="batch-header">
                      <h4>Batch #<span class="batch-number"></span></h4>
                      <button type="button" class="icon-btn danger" title="Remove Batch" onclick="removeBatch(event)">
                        <span class="icon">delete</span>
                      </button>
                    </div>
                    <div class="form-grid">
                      <input type="hidden" name="batches[BATCH_INDEX][product_id]" value="">
                      <div class="form-field">
                        <label>Batch Code *</label>
                        <input type="text"
                          name="batches[BATCH_INDEX][batch_code]"
                          placeholder="BAT-001"
                          required>
                      </div>
                      <div class="form-field">
                        <label>Quantity *</label>
                        <input type="number"
                          name="batches[BATCH_INDEX][received_qty]"
                          min="0"
                          step="<?= $item['is_int'] ? '1' : '0.001' ?>"
                          placeholder="0"
                          required
                          onchange="updateTotalReceivedQty(<?= $item['product_id'] ?>)">
                      </div>
                      <div class="form-field">
                        <label>Unit Cost *</label>
                        <input type="number"
                          name="batches[BATCH_INDEX][unit_cost]"
                          min="0"
                          step="0.01"
                          placeholder="0.00"
                          required>
                      </div>
                      <div class="form-field">
                        <label>Selling Price *</label>
                        <input type="number"
                          name="batches[BATCH_INDEX][unit_price]"
                          min="0"
                          step="0.01"
                          placeholder="0.00"
                          required>
                      </div>
                      <div class="form-field">
                        <label>Manufacturing Date</label>
                        <input type="date"
                          name="batches[BATCH_INDEX][manufactured_date]">
                      </div>
                      <div class="form-field">
                        <label>Expiry Date</label>
                        <input type="date"
                          name="batches[BATCH_INDEX][expiry_date]">
                      </div>
                    </div>
                  </div>
                </template>

                <!-- Add new batch button -->
                <div class="row justify-center v-pad-md">
                  <button type="button" class="btn btn-primary" onclick="addNewBatch(event, <?= $item['product_id'] ?>, '<?= $item['is_int'] ? '1' : '0.001' ?>')">
                    <span class="icon">add</span>
                    Add Batch
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="closeReceiveItemsDialog()">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary" onclick="submitReceiveItems(event)">
            <span class="icon">done</span>
            Receive Items
          </button>
        </div>
      </form>
    </div>
  </dialog>
<?php endif; ?>

<script src="/js/search.js"></script>
<script>
  class PurchaseOrderDetails {
    constructor() {
      <?php if ($canEditOrder): ?>
        window.enableEdit = this.enableEdit.bind(this);
        window.cancelEdit = this.cancelEdit.bind(this);
        window.saveChanges = this.saveChanges.bind(this);
        window.removeItem = this.removeItem.bind(this);
      <?php endif; ?>
      <?php if ($canApproveOrder): ?>
        window.approveOrder = this.approveOrder.bind(this);
      <?php endif; ?>
      <?php if ($canCancelOrder): ?>
        window.cancelOrder = this.cancelOrder.bind(this);
      <?php endif; ?>
      <?php if ($canCompleteOrder): ?>
        window.completeOrder = this.completeOrder.bind(this);
      <?php endif; ?>
      <?php if ($canDeleteOrder): ?>
        window.deleteOrder = this.deleteOrder.bind(this);
      <?php endif; ?>
      <?php if ($canReceiveItems): ?>
        window.openReceiveItemsDialog = this.openReceiveItemsDialog.bind(this);
        window.closeReceiveItemsDialog = this.closeReceiveItemsDialog.bind(this);
        window.toggleProductCard = this.toggleProductCard.bind(this);
        window.addNewBatch = this.addNewBatch.bind(this);
        window.removeBatch = this.removeBatch.bind(this);
        window.updateTotalReceivedQty = this.updateTotalReceivedQty.bind(this);
        window.submitReceiveItems = this.submitReceiveItems.bind(this);
      <?php endif; ?>
    }

    init() {
      <?php if ($canEditOrder): ?>
        let supplierId = document.querySelector("input[name='supplier_id']").value;
        this.productSearch = new SearchHandler({
          apiEndpoint: `/api/suppliers/${supplierId}/products/search`,
          inputElement: document.querySelector("#order-item-search input"),
          resultsContainer: document.querySelector(
            "#order-item-search .search-results"
          ),
          itemsPerPage: 5,
          renderResultItem: (product) => {
            const element = document.createElement("div");
            element.classList.add("search-result");
            element.textContent = product.product_name;
            return element;
          },
          onSelect: (product) => this.showAddItemDialog(product),
        });
      <?php endif; ?>
    }

    <?php if ($canEditOrder): ?>
      enableEdit(e) {
        e.target.closest(".dropdown").style.display = "none";
        document.getElementById("edit-actions").style.display = "flex";
        document
          .querySelectorAll(".form-field :is(input, select, textarea)")
          .forEach((input) => {
            input.disabled = false;
          });
        document
          .querySelectorAll(".form-field[style*='display'")
          .forEach((field) => {
            field.style.display = "flex";
          });

        document.getElementById("order-item-search").style.display = "flex";
        document.querySelector("#order-items th:last-child").style.display = "";
        document
          .querySelectorAll("#order-items tbody tr td[style*='display']")
          .forEach((td) => {
            td.style.display = "";
          });
      }

      cancelEdit() {
        if (
          confirm(
            "Are you sure you want to cancel? Any unsaved changes will be lost."
          )
        ) {
          window.location.reload();
        }
      }

      saveChanges() {
        if (!confirm("Are you sure you want to save these changes?")) {
          return;
        }
        const form = document.getElementById("details-form");
        form.submit();
      }

      removeItem(e) {
        e.preventDefault();
        e.target.closest("tr").remove();
      }

      showAddItemDialog(product) {
        if (
          document.querySelector(
            `#order-items tbody tr input[data-id="${product.id}"]`
          )
        ) {
          openPopupWithMessage("Item already added to the order", "error");
          return;
        }
        const dialog = document.getElementById("add-item-dialog");
        dialog.querySelector(".add-btn").onclick = () => {
          this.addItemToOrder(product);
          dialog.close();
        };
        dialog.querySelector(".cancel-btn").onclick = () => {
          dialog.close();
        };
        dialog.showModal();
      }

      addItemToOrder(product) {
        let quantity = parseFloat(document.querySelector("#add-item-qty").value);
        if (product.is_int) {
          quantity = Math.floor(quantity);
        } else {
          quantity = quantity.toFixed(3);
        }
        if (quantity <= 0) {
          openPopupWithMessage("Please enter a valid quantity", "error");
          return;
        }
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${product.product_name}</td>
            <td>${quantity} ${product.unit_symbol}</td>
            <td>
                <button type="button" class="icon-btn danger" onclick="removeItem(event)">
                    <span class="icon">delete</span>
                </button>
            </td>
            <input type="hidden" name="items[]" value='{"id": ${product.id}, "quantity": ${quantity} }' data-id="${product.id}">
        `;
        document.querySelector("#order-items tbody").appendChild(tr);
        document.querySelector("#order-item-search input").value = "";
        this.productSearch.clear();
      }
    <?php endif; ?>

    <?php if ($canApproveOrder): ?>
      approveOrder() {
        if (confirm("Are you sure you want to approve this order?")) {
          window.location = window.location.pathname + "/approve";
        }
      }
    <?php endif; ?>

    <?php if ($canCancelOrder): ?>
      cancelOrder() {
        if (confirm("Are you sure you want to cancel this order?")) {
          window.location = window.location.pathname + "/cancel";
        }
      }
    <?php endif; ?>

    <?php if ($canCompleteOrder): ?>
      completeOrder() {
        if (confirm("Are you sure you want to complete this order?")) {
          window.location = window.location.pathname + "/complete";
        }
      }
    <?php endif; ?>

    <?php if ($canDeleteOrder): ?>
      deleteOrder() {
        if (confirm("Are you sure you want to delete this order?")) {
          window.location = window.location.pathname + "/delete";
        }
      }
    <?php endif; ?>

    <?php if ($canReceiveItems): ?>
      openReceiveItemsDialog() {
        const dialog = document.getElementById("receive-items-dialog");
        dialog.showModal();
      }

      closeReceiveItemsDialog() {
        const dialog = document.getElementById("receive-items-dialog");
        dialog.close();
      }

      // Toggle product card expansion
      toggleProductCard(e) {
        let target = e.target;
        if (!e.target.classList.contains("product-header")) {
          target = e.target.closest(".product-header");
        }
        target.classList.toggle("open");
        const content = target.nextElementSibling;

        if (content.style.display === "none") {
          content.style.display = "block";
        } else {
          content.style.display = "none";
        }
      }

      // Add a new batch for a product
      addNewBatch(e) {
        const productContent = e.target.closest(".product-content");
        const container = productContent.querySelector(".batches-container");
        const template = productContent.querySelector("template");
        const batches = container.querySelectorAll(".batch-card");
        const nextBatchNumber = batches.length + 1;

        // Clone the template
        const batchNode = template.content.cloneNode(true);

        // Update batch number
        batchNode.querySelector(".batch-number").textContent = nextBatchNumber;

        batchNode.querySelector("input[name*='product_id']").value =
          productContent.dataset.productId;

        // Append the new batch
        container.appendChild(batchNode);
      }

      // Remove a batch
      removeBatch(e) {
        const productContent = e.target.closest(".product-content");
        const container = productContent.querySelector(".batches-container");
        const batchElement = e.target.closest(".batch-card");
        batchElement.remove();

        // Renumber the batches
        container.querySelectorAll(".batch-card").forEach((batch, index) => {
          batch.querySelector(".batch-number").textContent = index + 1;
        });

        // Update the received quantity badge
        this.updateTotalReceivedQty(productId);
      }

      // Update the total received quantity for a product
      updateTotalReceivedQty(productId) {
        const container = document.getElementById(`batches-container-${productId}`);
        const quantityInputs = container.querySelectorAll(
          'input[name*="received_qty"]'
        );
        let total = 0;

        quantityInputs.forEach((input) => {
          const value = parseFloat(input.value || 0);
          if (!isNaN(value)) {
            total += value;
          }
        });

        // Update the badge
        const badge = document.getElementById(`received-qty-badge-${productId}`);
        const unitSymbol = badge.textContent.split(" ").pop();
        badge.textContent = `Received: ${total} ${unitSymbol}`;

        // Update class based on comparison with ordered quantity
        const orderedText = document.querySelector(
          `.product-meta .badge.accent[id*="${productId}"]`
        ).textContent;
        const orderedQty = parseFloat(orderedText.replace("Ordered: ", ""));

        if (total === 0) {
          badge.className = "badge";
        } else if (total < orderedQty) {
          badge.className = "badge warning";
        } else if (total === orderedQty) {
          badge.className = "badge success";
        } else {
          badge.className = "badge danger";
        }
      }

      submitReceiveItems(e) {
        const form = document.getElementById("receive-items-form");
        form.querySelectorAll(".batch-card").forEach((batch, index) => {
          batch.querySelectorAll("input").forEach((input) => {
            let name = input.name;
            input.name = name.replace("BATCH_INDEX", index);
          });
        });
      }
    <?php endif; ?>
  }

  document.addEventListener("DOMContentLoaded", () => {
    const purchaseOrderDetails = new PurchaseOrderDetails();
    purchaseOrderDetails.init();
  });
</script>