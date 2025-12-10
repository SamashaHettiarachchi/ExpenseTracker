// Expenses List JavaScript

let currentPage = 1;
let totalPages = 1;
let filters = {
  search: "",
  category_id: "",
  payment_method: "",
  date_from: "",
  date_to: "",
  order_by: "expense_date",
  order_dir: "DESC",
};

// Initialize Expenses Page
document.addEventListener("DOMContentLoaded", function () {
  loadCategories();
  loadExpenses();
  initializeFilters();
  initializeViewToggle();
});

// Load Categories for Filter
async function loadCategories() {
  try {
    const response = await fetch("/seee/api/categories.php");
    const result = await response.json();

    if (result.success) {
      populateCategoryFilter(result.data);
    }
  } catch (error) {
    console.error("Error loading categories:", error);
  }
}

function populateCategoryFilter(categories) {
  const select = document.getElementById("filterCategory");
  if (!select) return;

  select.innerHTML = '<option value="">All Categories</option>';
  categories.forEach((cat) => {
    select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
  });
}

// Load Expenses with Filters
async function loadExpenses(page = 1) {
  console.log("=== loadExpenses called ===");
  currentPage = page;
  const offset = (page - 1) * 10;

  try {
    showLoading();
    console.log("Loading spinner shown");
  } catch (e) {
    console.error("Error showing loading:", e);
  }

  try {
    const params = new URLSearchParams({
      ...filters,
      limit: 10,
      offset: offset,
    });

    // Remove empty values
    for (let [key, value] of params.entries()) {
      if (!value) params.delete(key);
    }

    const url = `/seee/api/expenses.php?${params.toString()}`;
    console.log("1. Fetching URL:", url);

    const response = await fetch(url, {
      credentials: "same-origin",
    });
    console.log("2. Response received, status:", response.status);

    const text = await response.text();
    console.log("3. Raw response text:", text.substring(0, 200));

    const result = JSON.parse(text);
    console.log("4. Parsed result:", result);

    if (result.success && result.data) {
      const expenses = result.data.expenses || [];
      const total = result.data.total || 0;
      const perPage = result.data.per_page || 10;

      console.log("5. Expenses count:", expenses.length);

      if (expenses.length > 0) {
        console.log("6. First expense:", expenses[0]);
      }

      displayExpenses(expenses);
      updatePagination(total, perPage);

      console.log("7. Display complete");
    } else {
      console.error("API returned error:", result.message);
      const container = document.getElementById("expensesContainer");
      if (container) {
        container.innerHTML =
          '<div class="alert alert-warning m-3">No expenses found. <a href="add-expense.php">Add your first expense</a></div>';
      }
    }
  } catch (error) {
    console.error("CRITICAL ERROR:", error);
    console.error("Error stack:", error.stack);
    const container = document.getElementById("expensesContainer");
    if (container) {
      container.innerHTML =
        '<div class="alert alert-danger m-3">Error loading expenses. Check console for details.</div>';
    }
  } finally {
    console.log("8. Hiding loading spinner");
    try {
      hideLoading();
    } catch (e) {
      console.error("Error hiding loading:", e);
    }
  }
}

// Display Expenses in Table or Card View
function displayExpenses(expenses) {
  const viewMode = localStorage.getItem("expenseView") || "table";

  if (viewMode === "table") {
    displayTableView(expenses);
  } else {
    displayCardView(expenses);
  }
}

// Table View
function displayTableView(expenses) {
  const container = document.getElementById("expensesContainer");

  if (!container) return;

  container.innerHTML = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="expensesTableBody"></tbody>
            </table>
        </div>
    `;

  const newTbody = document.getElementById("expensesTableBody");

  if (!expenses || expenses.length === 0) {
    newTbody.innerHTML =
      '<tr><td colspan="7" class="text-center py-5 text-muted">No expenses found</td></tr>';
    return;
  }

  newTbody.innerHTML = expenses
    .map(
      (expense) => `
        <tr>
            <td>${formatDate(expense.expense_date)}</td>
            <td>
                <strong>${expense.title}</strong>
                ${
                  expense.description
                    ? `<br><small class="text-muted">${expense.description.substring(
                        0,
                        50
                      )}...</small>`
                    : ""
                }
            </td>
            <td>
                <span class="badge category-badge" style="background-color: ${
                  expense.category_color
                }">
                    <i class="fas ${expense.category_icon} me-1"></i>${
        expense.category_name
      }
                </span>
            </td>
            <td><strong>${formatCurrency(expense.amount)}</strong></td>
            <td>
                <i class="fas ${getPaymentIcon(
                  expense.payment_method
                )} me-1"></i>
                ${expense.payment_method.replace("_", " ").toUpperCase()}
            </td>
            <td>
                <span class="badge bg-${getStatusBadge(expense.status)}">${
        expense.status
      }</span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="expense-details.php?id=${
                      expense.id
                    }" class="btn btn-outline-primary" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="edit-expense.php?id=${
                      expense.id
                    }" class="btn btn-outline-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="deleteExpense(${
                      expense.id
                    })" class="btn btn-outline-danger" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `
    )
    .join("");
}

// Card View
function displayCardView(expenses) {
  const container = document.getElementById("expensesContainer");
  if (!container) return;

  if (!expenses || expenses.length === 0) {
    container.innerHTML =
      '<div class="text-center py-5 text-muted">No expenses found</div>';
    return;
  }

  container.innerHTML = '<div class="row g-3" id="expenseCards"></div>';
  const cardsContainer = document.getElementById("expenseCards");

  cardsContainer.innerHTML = expenses
    .map(
      (expense) => `
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card expense-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">${expense.title}</h6>
                        <span class="badge bg-${getStatusBadge(
                          expense.status
                        )}">${expense.status}</span>
                    </div>
                    
                    <p class="text-muted small mb-3">${
                      expense.description || "No description"
                    }</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge category-badge" style="background-color: ${
                          expense.category_color
                        }">
                            <i class="fas ${expense.category_icon} me-1"></i>${
        expense.category_name
      }
                        </span>
                        <strong class="text-primary">${formatCurrency(
                          expense.amount
                        )}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                        <span><i class="fas ${getPaymentIcon(
                          expense.payment_method
                        )} me-1"></i>${expense.payment_method}</span>
                        <span><i class="fas fa-calendar me-1"></i>${formatDate(
                          expense.expense_date
                        )}</span>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="expense-details.php?id=${
                          expense.id
                        }" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        <a href="edit-expense.php?id=${
                          expense.id
                        }" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteExpense(${
                          expense.id
                        })" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `
    )
    .join("");
}

// Update Pagination
function updatePagination(total, perPage) {
  totalPages = Math.ceil(total / perPage);

  const pagination = document.getElementById("pagination");
  if (!pagination) return;

  if (totalPages <= 1) {
    pagination.innerHTML = "";
    return;
  }

  let html = '<ul class="pagination justify-content-center">';

  // Previous
  html += `<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
        <a class="page-link" href="#" onclick="loadExpenses(${
          currentPage - 1
        }); return false;">Previous</a>
    </li>`;

  // Pages
  for (let i = 1; i <= totalPages; i++) {
    if (
      i === 1 ||
      i === totalPages ||
      (i >= currentPage - 2 && i <= currentPage + 2)
    ) {
      html += `<li class="page-item ${i === currentPage ? "active" : ""}">
                <a class="page-link" href="#" onclick="loadExpenses(${i}); return false;">${i}</a>
            </li>`;
    } else if (i === currentPage - 3 || i === currentPage + 3) {
      html +=
        '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }
  }

  // Next
  html += `<li class="page-item ${
    currentPage === totalPages ? "disabled" : ""
  }">
        <a class="page-link" href="#" onclick="loadExpenses(${
          currentPage + 1
        }); return false;">Next</a>
    </li>`;

  html += "</ul>";
  pagination.innerHTML = html;
}

// Initialize Filters
function initializeFilters() {
  const searchInput = document.getElementById("searchExpense");
  if (searchInput) {
    searchInput.addEventListener(
      "input",
      debounce(function (e) {
        filters.search = e.target.value;
        loadExpenses(1);
      }, 500)
    );
  }

  const filterCategory = document.getElementById("filterCategory");
  if (filterCategory) {
    filterCategory.addEventListener("change", function (e) {
      filters.category_id = e.target.value;
      loadExpenses(1);
    });
  }

  const filterPayment = document.getElementById("filterPayment");
  if (filterPayment) {
    filterPayment.addEventListener("change", function (e) {
      filters.payment_method = e.target.value;
      loadExpenses(1);
    });
  }

  const filterDateFrom = document.getElementById("filterDateFrom");
  if (filterDateFrom) {
    filterDateFrom.addEventListener("change", function (e) {
      filters.date_from = e.target.value;
      loadExpenses(1);
    });
  }

  const filterDateTo = document.getElementById("filterDateTo");
  if (filterDateTo) {
    filterDateTo.addEventListener("change", function (e) {
      filters.date_to = e.target.value;
      loadExpenses(1);
    });
  }
}

// Clear Filters
function clearFilters() {
  filters = {
    search: "",
    category_id: "",
    payment_method: "",
    date_from: "",
    date_to: "",
    order_by: "expense_date",
    order_dir: "DESC",
  };

  document.getElementById("searchExpense").value = "";
  document.getElementById("filterCategory").value = "";
  document.getElementById("filterPayment").value = "";
  document.getElementById("filterDateFrom").value = "";
  document.getElementById("filterDateTo").value = "";

  loadExpenses(1);
}

// View Toggle
function initializeViewToggle() {
  const viewMode = localStorage.getItem("expenseView") || "table";
  updateViewButtons(viewMode);
}

function toggleView(view) {
  localStorage.setItem("expenseView", view);
  updateViewButtons(view);
  loadExpenses(currentPage);
}

function updateViewButtons(view) {
  const tableBtn = document.getElementById("viewTable");
  const cardBtn = document.getElementById("viewCards");

  if (tableBtn && cardBtn) {
    if (view === "table") {
      tableBtn.classList.add("active");
      cardBtn.classList.remove("active");
    } else {
      cardBtn.classList.add("active");
      tableBtn.classList.remove("active");
    }
  }
}

// Delete Expense
async function deleteExpense(id) {
  const confirmed = await confirmAction(
    "Are you sure you want to delete this expense?"
  );
  if (!confirmed) return;

  showLoading();

  try {
    const response = await fetch(`/seee/api/expenses.php?id=${id}`, {
      method: "DELETE",
    });

    const result = await response.json();

    if (result.success) {
      showToast("Expense deleted successfully", "success");
      loadExpenses(currentPage);
    } else {
      showToast(result.message, "danger");
    }
  } catch (error) {
    console.error("Error deleting expense:", error);
    showToast("An error occurred while deleting expense", "danger");
  } finally {
    hideLoading();
  }
}

// Export to CSV
async function exportExpenses() {
  showLoading();

  try {
    const params = new URLSearchParams(filters);
    params.set("limit", 1000); // Get all results

    const response = await fetch(`/seee/api/expenses.php?${params.toString()}`);
    const result = await response.json();

    if (result.success) {
      const data = result.data.expenses.map((exp) => ({
        Date: exp.expense_date,
        Title: exp.title,
        Category: exp.category_name,
        Amount: exp.amount,
        Payment: exp.payment_method,
        Status: exp.status,
        Description: exp.description || "",
      }));

      exportToCSV(
        data,
        `expenses_${new Date().toISOString().split("T")[0]}.csv`
      );
      showToast("Expenses exported successfully", "success");
    }
  } catch (error) {
    console.error("Error exporting expenses:", error);
    showToast("Failed to export expenses", "danger");
  } finally {
    hideLoading();
  }
}
