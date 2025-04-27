/**
 * InvenPro Notifications JS
 *
 * Handles all notification interactions across the application
 */

document.addEventListener("DOMContentLoaded", function () {
  // Initialize notification components that exist on the page
  initNotificationCard();
  initNavbarNotifications();
  initNotificationsPage();
});

/**
 * Initialize notification navbar dropdown
 */
function initNavbarNotifications() {
  const notificationDropdown = document.getElementById("notifications");
  if (!notificationDropdown) return;

  // Mark individual notification as read when clicked
  document.querySelectorAll(".notification-item").forEach((item) => {
    item.addEventListener("click", function () {
      const notificationId = this.dataset.id;

      // AJAX call to mark notification as read
      fetch("/api/notifications/mark-read", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id: notificationId,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Mark as read in UI
            this.classList.remove("unread");

            // Reduce badge count
            updateNotificationBadge();

            // Handle any action associated with the notification
            handleNotificationAction(this);
          }
        })
        .catch((error) => console.error("Error:", error));
    });
  });

  // Mark all as read button in navbar
  const markAllBtn = document.getElementById("markAllRead");
  if (markAllBtn) {
    markAllBtn.addEventListener("click", function (e) {
      e.stopPropagation(); // Prevent dropdown from closing

      markAllNotificationsAsRead(function () {
        // Mark all as read in UI
        document
          .querySelectorAll(".notification-item.unread")
          .forEach((item) => {
            item.classList.remove("unread");
          });

        // Update badge (remove it)
        document.querySelector(".notification-badge")?.remove();

        // Hide the "mark all read" button
        this.style.display = "none";
      });
    });
  }

  // View all notifications button
  const viewAllBtn = document.querySelector(".view-all-btn");
  if (viewAllBtn) {
    viewAllBtn.addEventListener("click", function () {
      window.location.href = "/notifications";
    });
  }
}

/**
 * Initialize the notifications page
 */
function initNotificationsPage() {
  // Check if we're on the notifications page
  const notificationCardsContainer = document.querySelector(
    ".notification-cards-container"
  );
  if (!notificationCardsContainer) return;

  // Set up filters
  initNotificationFilters();

  // Set up mark as read buttons on notification page
  initMarkAsReadButtons();

  // Set up card interactions
  initNotificationCardInteractions();

  // Set up search and pagination functionality
  setupNotificationSearch();
  setupNotificationPagination();
}

/**
 * Initialize notification cards whether in navbar or main page
 */
function initNotificationCard() {
  // This is a generic initialization that applies to any notification component
  // Currently just initializes the timeAgo values if needed
  document.querySelectorAll("[data-timestamp]").forEach((element) => {
    const timestamp = element.dataset.timestamp;
    if (timestamp) {
      element.textContent = getTimeAgo(timestamp);
    }
  });
}

/**
 * Initialize notification filter buttons
 */
function initNotificationFilters() {
  // Filter buttons functionality
  const filterButtons = document.querySelectorAll(".filter-btn, .filter-chip");
  const notificationCards = document.querySelectorAll(".notification-card");

  if (!filterButtons.length || !notificationCards.length) return;

  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remove active class from all buttons
      filterButtons.forEach((btn) => btn.classList.remove("active"));

      // Add active class to clicked button
      this.classList.add("active");

      const filter = this.dataset.filter;

      // Show/hide cards based on filter
      notificationCards.forEach((card) => {
        switch (filter) {
          case "all":
            card.style.display = "flex";
            break;
          case "unread":
            card.style.display = card.classList.contains("unread")
              ? "flex"
              : "none";
            break;
          case "read":
            card.style.display = !card.classList.contains("unread")
              ? "flex"
              : "none";
            break;
          case "high":
            card.style.display = card.classList.contains("high-priority")
              ? "flex"
              : "none";
            break;
        }
      });

      // Check if any notifications are visible after filtering
      const visibleCount = Array.from(notificationCards).filter(
        (card) => card.style.display === "flex"
      ).length;

      // Add empty state if no notifications match the filter
      const emptyState = document.querySelector(
        ".empty-filtered-notifications"
      );
      if (visibleCount === 0 && !emptyState) {
        const notificationList = document.querySelector(".notification-list");

        const emptyElement = document.createElement("div");
        emptyElement.className =
          "empty-notifications empty-filtered-notifications";
        emptyElement.innerHTML = `
                    <span class="icon large">filter_alt_off</span>
                    <h3>No ${
                      filter.toLowerCase() !== "all" ? filter.toLowerCase() : ""
                    } notifications found</h3>
                    <p>There are no matching notifications for this filter.</p>
                `;

        notificationList.appendChild(emptyElement);
      } else if (visibleCount > 0 && emptyState) {
        emptyState.remove();
      }
    });
  });
}

/**
 * Initialize mark as read buttons
 */
function initMarkAsReadButtons() {
  // Mark individual notification as read
  document.querySelectorAll(".mark-read, .mark-read-btn").forEach((button) => {
    button.addEventListener("click", function (e) {
      e.stopPropagation();
      const notificationId = this.dataset.id;
      markNotificationAsRead(notificationId, this);
    });
  });

  // Mark all as read button
  const markAllBtn = document.querySelector(
    ".action-btn.mark-all-read, #markAllReadBtn"
  );
  if (markAllBtn) {
    markAllBtn.addEventListener("click", function () {
      markAllNotificationsAsRead();
    });
  }
}

/**
 * Mark a single notification as read
 *
 * @param {number} notificationId - The ID of the notification to mark as read
 * @param {Element} button - The button element that triggered the action
 */
function markNotificationAsRead(notificationId, button) {
  const card = document.querySelector(
    `.notification-card[data-id="${notificationId}"], .notification-item[data-id="${notificationId}"]`
  );

  fetch("/api/notifications/mark-read", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: notificationId }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update UI
        card.classList.remove("unread");
        button.remove(); // Remove the "Mark as read" button

        // Remove "New" badge if it exists
        const newBadge = card.querySelector(".badge.primary");
        if (newBadge) newBadge.remove();

        openPopupWithMessage("Notification marked as read", "success");

        // Check if we should hide the mark all as read button
        updateMarkAllReadButton();

        // Update unread count in filter if on notifications page
        updateUnreadCountInFilter();
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      openPopupWithMessage("Failed to mark notification as read", "error");
    });
}

/**
 * Mark all notifications as read
 *
 * @param {Function} callback - Optional callback function to run after successful operation
 */
function markAllNotificationsAsRead(callback) {
  fetch("/api/notifications/mark-all-read", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update UI
        document
          .querySelectorAll(
            ".notification-card.unread, .notification-item.unread"
          )
          .forEach((card) => {
            card.classList.remove("unread");
          });

        // Remove all "Mark as read" buttons
        document
          .querySelectorAll(".mark-read, .mark-read-btn")
          .forEach((btn) => {
            btn.remove();
          });

        // Remove all "New" badges
        document.querySelectorAll(".badge.primary").forEach((badge) => {
          badge.remove();
        });

        // Hide the "Mark all read" button
        const markAllBtn = document.querySelector(
          ".action-btn.mark-all-read, #markAllReadBtn"
        );
        if (markAllBtn) {
          markAllBtn.style.display = "none";
        }

        // Update unread count in filter to 0 if on notifications page
        updateUnreadCountInFilter(0);

        openPopupWithMessage(
          `${data.count} notification${
            data.count !== 1 ? "s" : ""
          } marked as read`,
          "success"
        );

        // Run callback if provided
        if (typeof callback === "function") {
          callback();
        }
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      openPopupWithMessage("Failed to mark all notifications as read", "error");
    });
}

/**
 * Check if any unread notifications remain and update the mark all button
 */
function updateMarkAllReadButton() {
  const unreadNotifications = document.querySelectorAll(
    ".notification-card.unread, .notification-item.unread"
  );
  const markAllBtn = document.querySelector(
    ".action-btn.mark-all-read, #markAllReadBtn, #markAllRead"
  );

  if (unreadNotifications.length === 0 && markAllBtn) {
    markAllBtn.style.display = "none";
  }
}

/**
 * Update notification badge count in the navbar
 */
function updateNotificationBadge() {
  const badge = document.querySelector(".notification-badge");
  if (badge) {
    const currentCount = parseInt(badge.textContent);
    if (currentCount > 1) {
      badge.textContent = currentCount - 1;
    } else {
      badge.remove();

      // If no more unread notifications, hide mark all read button
      document.getElementById("markAllRead").style.display = "none";
    }
  }
}

/**
 * Update unread count in filter chips on the notifications page
 *
 * @param {number} count - Optional specific count to set
 */
function updateUnreadCountInFilter(count) {
  const unreadFilterChip = document.querySelector(
    '.filter-chip[data-filter="unread"]'
  );
  if (!unreadFilterChip) return;

  const badge = unreadFilterChip.querySelector(".badge");

  if (count === 0 && badge) {
    badge.remove();
  } else if (count && badge) {
    badge.textContent = count;
  } else if (count === undefined && badge) {
    const unreadCount = parseInt(badge.textContent) - 1;
    if (unreadCount > 0) {
      badge.textContent = unreadCount;
    } else {
      badge.remove();
    }
  }
}

/**
 * Handle notification action based on content or metadata
 *
 * @param {Element} notificationElement - The notification element
 */
function handleNotificationAction(notificationElement) {
  // Extract data attributes to determine what action to take
  // For now just redirect to appropriate page based on standard actions

  // You would normally get this from the data attribute
  // For this example we'll infer from the notification content
  const content = notificationElement.textContent.toLowerCase();

  // Find action buttons if they exist
  const actionBtn = notificationElement.querySelector("[data-url]");
  if (actionBtn) {
    window.location.href = actionBtn.dataset.url;
    return;
  }

  // Otherwise infer from content
  if (content.includes("low stock") || content.includes("inventory")) {
    window.location.href = "/inventory";
  } else if (content.includes("order") || content.includes("purchase")) {
    window.location.href = "/purchase-orders";
  }

  // This would be more robust with the metadata we have in our notifications
}

/**
 * Initialize notification card click interactions
 */
function initNotificationCardInteractions() {
  // Handle clicks on notification cards (for navigation)
  document.querySelectorAll(".notification-card").forEach((card) => {
    card.addEventListener("click", function (e) {
      // Don't navigate if clicking on a button or link
      if (e.target.closest("button") || e.target.closest("a")) {
        return;
      }

      const notificationId = this.dataset.id;
      const isUnread = this.classList.contains("unread");

      // If unread, mark as read
      if (isUnread) {
        fetch("/api/notifications/mark-read", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ id: notificationId }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              this.classList.remove("unread");
              const markReadBtn = this.querySelector(
                ".mark-read, .mark-read-btn"
              );
              if (markReadBtn) markReadBtn.remove();

              // Remove new badge
              const newBadge = this.querySelector(".badge.primary");
              if (newBadge) newBadge.remove();

              // Update mark all button visibility
              updateMarkAllReadButton();
            }
          })
          .catch((error) => console.error("Error:", error));
      }

      // Try to find and navigate to the action link if present
      const actionBtn = this.querySelector(".view-action-btn");
      if (actionBtn) {
        window.location.href = actionBtn.dataset.url;
      } else {
        const actionLink = this.querySelector(".notification-actions a");
        if (actionLink) {
          window.location.href = actionLink.href;
        }
      }
    });
  });
}

/**
 * Set up notification search functionality
 */
function setupNotificationSearch() {
  const searchInput = document.getElementById("notificationSearch");
  if (!searchInput) return;

  // Setup search on enter key
  searchInput.addEventListener("keyup", function (event) {
    if (event.key === "Enter") {
      applyFilters();
    }
  });
}

/**
 * Set up notification pagination
 */
function setupNotificationPagination() {
  // Setup pagination if needed
  document.querySelectorAll(".pagination").forEach((pagination) => {
    const currentPage = parseInt(pagination.dataset.page);
    const totalPages = parseInt(pagination.dataset.totalPages);

    if (totalPages > 1) {
      // Insert pagination
      insertPagination(pagination, currentPage, totalPages, (page) => {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set("p", page);
        window.location.href = currentUrl.href;
      });
    }
  });
}

/**
 * Apply filters to notifications
 */
function applyFilters() {
  const searchQuery =
    document.getElementById("notificationSearch")?.value || "";
  const currentFilter =
    document.querySelector(".filter-chip.active")?.dataset.filter || "all";

  const url = new URL(window.location.href);
  searchQuery
    ? url.searchParams.set("q", searchQuery)
    : url.searchParams.delete("q");
  currentFilter && currentFilter !== "all"
    ? url.searchParams.set("filter", currentFilter)
    : url.searchParams.delete("filter");

  window.location.href = url.href;
}

/**
 * Set the active filter
 *
 * @param {string} filter - The filter to activate
 */
function setFilter(filter) {
  document.querySelectorAll(".filter-chip").forEach((chip) => {
    chip.classList.toggle("active", chip.dataset.filter === filter);
  });
  applyFilters();
}

/**
 * Helper function to format time ago
 *
 * @param {string} timestamp - ISO timestamp
 * @returns {string} Formatted time ago string
 */
function getTimeAgo(timestamp) {
  const now = new Date();
  const past = new Date(timestamp);
  const diffMs = now - past;
  const diffSec = Math.floor(diffMs / 1000);
  const diffMin = Math.floor(diffSec / 60);
  const diffHour = Math.floor(diffMin / 60);
  const diffDay = Math.floor(diffHour / 24);

  if (diffSec < 60) {
    return "Just now";
  } else if (diffMin < 60) {
    return `${diffMin} min${diffMin > 1 ? "s" : ""} ago`;
  } else if (diffHour < 24) {
    return `${diffHour} hour${diffHour > 1 ? "s" : ""} ago`;
  } else if (diffDay < 7) {
    return `${diffDay} day${diffDay > 1 ? "s" : ""} ago`;
  } else {
    return past.toLocaleDateString();
  }
}
