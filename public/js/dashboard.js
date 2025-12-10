// Dashboard JavaScript

let expenseChart = null;

// Initialize Dashboard
document.addEventListener("DOMContentLoaded", function () {
  loadDashboardStats();
  loadRecentExpenses();
});

// Load Dashboard Statistics
async function loadDashboardStats() {
  try {
    const response = await fetch("/seee/api/stats.php");
    const result = await response.json();

    if (result.success) {
      updateStatsCards(result.data);
      renderCategoryChart(result.data.category_breakdown);
      displayRecentExpenses(result.data.recent_expenses);
    } else {
      showToast("Failed to load statistics", "danger");
    }
  } catch (error) {
    console.error("Error loading stats:", error);
    showToast("An error occurred while loading statistics", "danger");
  }
}

// Update Stats Cards
function updateStatsCards(data) {
  // Total Expenses
  const totalExpenses = document.getElementById("totalExpenses");
  if (totalExpenses) {
    totalExpenses.innerHTML = `
            <h3>${data.total_expenses || 0}</h3>
            <p class="mb-0 text-muted">Total: ${formatCurrency(
              data.total_amount || 0
            )}</p>
        `;
  }

  // This Month
  const monthExpenses = document.getElementById("monthExpenses");
  if (monthExpenses) {
    monthExpenses.innerHTML = `
            <h3>${formatCurrency(data.month_amount || 0)}</h3>
            <p class="mb-0 text-muted">${data.month_expenses || 0} expenses</p>
        `;
  }

  // Today
  const todayExpenses = document.getElementById("todayExpenses");
  if (todayExpenses) {
    todayExpenses.innerHTML = `
            <h3>${formatCurrency(data.today_amount || 0)}</h3>
            <p class="mb-0 text-muted">${data.today_expenses || 0} expenses</p>
        `;
  }

  // Top Category
  const topCategory = document.getElementById("topCategory");
  if (topCategory && data.top_category) {
    topCategory.innerHTML = `
            <h3 style="color: ${data.top_category.color}">${
      data.top_category.name
    }</h3>
            <p class="mb-0 text-muted">${formatCurrency(
              data.top_category.total
            )}</p>
        `;
  } else if (topCategory) {
    topCategory.innerHTML = `
            <h3 class="text-muted">No Data</h3>
            <p class="mb-0 text-muted">0 expenses</p>
        `;
  }
}

// Render Category Chart using Chart.js
function renderCategoryChart(categories) {
  const ctx = document.getElementById("categoryChart");
  if (!ctx) return;

  // Filter out categories with no expenses
  const filteredCategories = categories.filter((cat) => cat.total > 0);

  if (filteredCategories.length === 0) {
    ctx.parentElement.innerHTML =
      '<p class="text-center text-muted py-5">No expense data to display</p>';
    return;
  }

  // Destroy existing chart
  if (expenseChart) {
    expenseChart.destroy();
  }

  // Prepare data
  const labels = filteredCategories.map((cat) => cat.name);
  const data = filteredCategories.map((cat) => parseFloat(cat.total));
  const colors = filteredCategories.map((cat) => cat.color);

  // Create chart
  expenseChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: labels,
      datasets: [
        {
          data: data,
          backgroundColor: colors,
          borderWidth: 2,
          borderColor: "#fff",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            padding: 15,
            font: {
              size: 12,
            },
          },
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const label = context.label || "";
              const value = formatCurrency(context.parsed);
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((context.parsed / total) * 100).toFixed(1);
              return `${label}: ${value} (${percentage}%)`;
            },
          },
        },
      },
    },
  });
}

// Display Recent Expenses
function displayRecentExpenses(expenses) {
  const tbody = document.getElementById("recentExpensesBody");
  if (!tbody) return;

  if (!expenses || expenses.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="5" class="text-center text-muted">No recent expenses</td></tr>';
    return;
  }

  tbody.innerHTML = expenses
    .map(
      (expense) => `
        <tr>
            <td>
                <strong>${expense.title}</strong><br>
                <small class="text-muted">${formatDate(
                  expense.expense_date
                )}</small>
            </td>
            <td>
                <span class="badge category-badge" style="background-color: ${
                  expense.category_color
                }">
                    ${expense.category_name}
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
                <a href="expense-details.php?id=${
                  expense.id
                }" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i>
                </a>
            </td>
        </tr>
    `
    )
    .join("");
}

// Load Recent Expenses (separate call for refresh)
async function loadRecentExpenses() {
  try {
    const response = await fetch(
      "/seee/api/expenses.php?limit=5&order_by=created_at&order_dir=DESC"
    );
    const result = await response.json();

    if (result.success && result.data.expenses) {
      displayRecentExpenses(result.data.expenses);
    }
  } catch (error) {
    console.error("Error loading recent expenses:", error);
  }
}

// Refresh Dashboard
function refreshDashboard() {
  showLoading();
  loadDashboardStats().finally(() => hideLoading());
}
