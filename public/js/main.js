// Main JavaScript Functions

// Global variables
let currentUser = null;

// Initialize app
document.addEventListener("DOMContentLoaded", function () {
  initDarkMode();
  initTooltips();
});

// Dark Mode
function initDarkMode() {
  const darkModeToggle = document.getElementById("darkModeToggle");
  const isDarkMode = localStorage.getItem("darkMode") === "true";

  if (isDarkMode) {
    document.body.classList.add("dark-mode");
    if (darkModeToggle) darkModeToggle.classList.add("active");
  }

  if (darkModeToggle) {
    darkModeToggle.addEventListener("click", toggleDarkMode);
  }
}

function toggleDarkMode() {
  const body = document.body;
  const toggle = document.getElementById("darkModeToggle");

  body.classList.toggle("dark-mode");
  toggle.classList.toggle("active");

  const isDarkMode = body.classList.contains("dark-mode");
  localStorage.setItem("darkMode", isDarkMode);
}

// Initialize Bootstrap tooltips
function initTooltips() {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

// Show toast notification
function showToast(message, type = "success") {
  const toastContainer =
    document.getElementById("toastContainer") || createToastContainer();

  const toastEl = document.createElement("div");
  toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
  toastEl.setAttribute("role", "alert");
  toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

  toastContainer.appendChild(toastEl);
  const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
  toast.show();

  toastEl.addEventListener("hidden.bs.toast", () => {
    toastEl.remove();
  });
}

function createToastContainer() {
  const container = document.createElement("div");
  container.id = "toastContainer";
  container.className = "toast-container position-fixed top-0 end-0 p-3";
  container.style.zIndex = "9999";
  document.body.appendChild(container);
  return container;
}

// Show loading spinner
function showLoading() {
  const spinner = document.createElement("div");
  spinner.id = "loadingSpinner";
  spinner.className = "spinner-overlay";
  spinner.innerHTML =
    '<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>';
  document.body.appendChild(spinner);
}

function hideLoading() {
  const spinner = document.getElementById("loadingSpinner");
  if (spinner) spinner.remove();
}

// Format currency
function formatCurrency(amount) {
  return (
    "Rs. " +
    parseFloat(amount).toLocaleString("en-LK", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
  );
}

// Format date
function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { year: "numeric", month: "short", day: "numeric" };
  return date.toLocaleDateString("en-IN", options);
}

// Get relative time
function getRelativeTime(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const diffInMs = now - date;
  const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

  if (diffInDays === 0) return "Today";
  if (diffInDays === 1) return "Yesterday";
  if (diffInDays < 7) return `${diffInDays} days ago`;
  if (diffInDays < 30) return `${Math.floor(diffInDays / 7)} weeks ago`;
  if (diffInDays < 365) return `${Math.floor(diffInDays / 30)} months ago`;
  return `${Math.floor(diffInDays / 365)} years ago`;
}

// API Request Helper
async function apiRequest(url, method = "GET", data = null) {
  const options = {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
  };

  if (data && method !== "GET") {
    options.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(url, options);
    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || "Request failed");
    }

    return result;
  } catch (error) {
    console.error("API Error:", error);
    throw error;
  }
}

// File upload helper
async function uploadFile(file, type = "receipts") {
  const formData = new FormData();
  formData.append("file", file);
  formData.append("type", type);

  try {
    const response = await fetch("/seee/api/upload.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || "Upload failed");
    }

    return result;
  } catch (error) {
    console.error("Upload Error:", error);
    throw error;
  }
}

// Sidebar toggle
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const mainContent = document.getElementById("main-content");

  if (sidebar) {
    sidebar.classList.toggle("show");
    sidebar.classList.toggle("collapsed");
  }

  if (mainContent) {
    mainContent.classList.toggle("expanded");
  }
}

// Confirm dialog
function confirmAction(message) {
  return new Promise((resolve) => {
    const result = confirm(message);
    resolve(result);
  });
}

// Debounce function for search
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Export to CSV
function exportToCSV(data, filename) {
  if (!data || data.length === 0) {
    showToast("No data to export", "warning");
    return;
  }

  const csv = convertToCSV(data);
  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  window.URL.revokeObjectURL(url);

  showToast("CSV exported successfully", "success");
}

function convertToCSV(data) {
  if (!data || data.length === 0) return "";

  const headers = Object.keys(data[0]);
  const csvRows = [headers.join(",")];

  for (const row of data) {
    const values = headers.map((header) => {
      let val = row[header];
      // Escape quotes and handle null/undefined
      if (val === null || val === undefined) val = "";
      val = String(val).replace(/"/g, '""');
      return `"${val}"`;
    });
    csvRows.push(values.join(","));
  }

  return csvRows.join("\n");
}

// Export current expenses to CSV
async function exportExpensesToCSV() {
  showLoading();

  try {
    // Fetch all expenses without pagination
    const params = new URLSearchParams({
      ...filters,
      limit: 10000, // Get all records
      page: 1,
    });

    const response = await fetch(`/seee/api/expenses.php?${params.toString()}`);
    const result = await response.json();

    if (result.success && result.data && result.data.length > 0) {
      // Prepare data for CSV
      const csvData = result.data.map((expense) => ({
        Date: expense.expense_date,
        Title: expense.title,
        Category: expense.category_name,
        Amount: expense.amount,
        "Payment Method": expense.payment_method,
        Status: expense.status,
        Description: expense.description || "",
        "Created At": expense.created_at,
      }));

      const filename = `expenses_${new Date().toISOString().split("T")[0]}.csv`;
      exportToCSV(csvData, filename);
    } else {
      showToast("No expenses to export", "warning");
    }
  } catch (error) {
    console.error("Export Error:", error);
    showToast("Failed to export expenses", "danger");
  } finally {
    hideLoading();
  }
}

// Payment method icons
function getPaymentIcon(method) {
  const icons = {
    cash: "fa-money-bill-wave",
    card: "fa-credit-card",
    upi: "fa-mobile-alt",
    bank_transfer: "fa-university",
  };
  return icons[method] || "fa-wallet";
}

// Status badge
function getStatusBadge(status) {
  const badges = {
    approved: "success",
    pending: "warning",
    rejected: "danger",
  };
  return badges[status] || "secondary";
}
