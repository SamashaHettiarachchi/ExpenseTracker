// Authentication JavaScript

// Login Form Handler
document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  if (loginForm) {
    loginForm.addEventListener("submit", handleLogin);
  }

  if (registerForm) {
    registerForm.addEventListener("submit", handleRegister);
  }
});

// Handle Login
async function handleLogin(e) {
  e.preventDefault();

  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const remember = document.getElementById("remember")?.checked || false;

  // Clear previous errors
  clearErrors();

  // Validate
  let isValid = true;

  if (!email) {
    showFieldError("email", "Email is required");
    isValid = false;
  }

  if (!password) {
    showFieldError("password", "Password is required");
    isValid = false;
  }

  if (!isValid) return;

  // Show loading
  const btn = e.target.querySelector('button[type="submit"]');
  const originalText = btn.innerHTML;
  btn.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span>Logging in...';
  btn.disabled = true;

  try {
    const response = await fetch("/seee/api/login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email, password, remember }),
    });

    const result = await response.json();

    if (result.success) {
      showToast("Login successful! Redirecting...", "success");
      setTimeout(() => {
        window.location.href = result.data.redirect;
      }, 1000);
    } else {
      showToast(result.message, "danger");
      btn.innerHTML = originalText;
      btn.disabled = false;
    }
  } catch (error) {
    showToast("An error occurred. Please try again.", "danger");
    btn.innerHTML = originalText;
    btn.disabled = false;
  }
}

// Handle Register
async function handleRegister(e) {
  e.preventDefault();

  const name = document.getElementById("name").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm_password").value;

  // Clear previous errors
  clearErrors();

  // Validate
  let isValid = true;

  if (!name) {
    showFieldError("name", "Name is required");
    isValid = false;
  }

  if (!email) {
    showFieldError("email", "Email is required");
    isValid = false;
  } else if (!isValidEmail(email)) {
    showFieldError("email", "Invalid email format");
    isValid = false;
  }

  if (!password) {
    showFieldError("password", "Password is required");
    isValid = false;
  } else if (password.length < 6) {
    showFieldError("password", "Password must be at least 6 characters");
    isValid = false;
  }

  if (password !== confirmPassword) {
    showFieldError("confirm_password", "Passwords do not match");
    isValid = false;
  }

  if (!isValid) return;

  // Show loading
  const btn = e.target.querySelector('button[type="submit"]');
  const originalText = btn.innerHTML;
  btn.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span>Creating account...';
  btn.disabled = true;

  try {
    const response = await fetch("/seee/api/register.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        name,
        email,
        password,
        confirm_password: confirmPassword,
      }),
    });

    const result = await response.json();

    if (result.success) {
      showToast("Registration successful! Redirecting...", "success");
      setTimeout(() => {
        window.location.href = result.data.redirect;
      }, 1000);
    } else {
      if (result.errors) {
        for (const [field, message] of Object.entries(result.errors)) {
          showFieldError(field, message);
        }
      } else {
        showToast(result.message, "danger");
      }
      btn.innerHTML = originalText;
      btn.disabled = false;
    }
  } catch (error) {
    showToast("An error occurred. Please try again.", "danger");
    btn.innerHTML = originalText;
    btn.disabled = false;
  }
}

// Show field error
function showFieldError(fieldId, message) {
  const field = document.getElementById(fieldId);
  if (field) {
    field.classList.add("is-invalid");

    let feedback = field.nextElementSibling;
    if (!feedback || !feedback.classList.contains("invalid-feedback")) {
      feedback = document.createElement("div");
      feedback.className = "invalid-feedback";
      field.parentNode.appendChild(feedback);
    }
    feedback.textContent = message;
  }
}

// Clear all errors
function clearErrors() {
  document.querySelectorAll(".is-invalid").forEach((el) => {
    el.classList.remove("is-invalid");
  });
  document.querySelectorAll(".invalid-feedback").forEach((el) => {
    el.remove();
  });
}

// Email validation
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}
