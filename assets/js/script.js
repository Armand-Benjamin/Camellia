// // Form validation
// function validateForm(formId) {
//   const form = document.getElementById(formId)
//   const inputs = form.querySelectorAll("input[required], select[required]")
//   let isValid = true

//   inputs.forEach((input) => {
//     const formGroup = input.closest(".form-group")
//     const existingError = formGroup.querySelector(".error-message")

//     if (existingError) {
//       existingError.remove()
//     }

//     if (!input.value.trim()) {
//       input.style.borderColor = "var(--danger-color)"
//       input.style.boxShadow = "0 0 0 3px rgba(231, 76, 60, 0.1)"

//       // Add error message
//       const errorMsg = document.createElement("div")
//       errorMsg.className = "error-message"
//       errorMsg.style.color = "var(--danger-color)"
//       errorMsg.style.fontSize = "0.875rem"
//       errorMsg.style.marginTop = "0.5rem"
//       errorMsg.textContent = `${input.previousElementSibling.textContent.replace(":", "")} is required`
//       formGroup.appendChild(errorMsg)

//       isValid = false
//     } else {
//       input.style.borderColor = "var(--success-color)"
//       input.style.boxShadow = "0 0 0 3px rgba(39, 174, 96, 0.1)"
//     }
//   })

//   return isValid
// }

// // Confirm delete
// function confirmDelete(message) {
//   return confirm(message || "Are you sure you want to delete this item? This action cannot be undone.")
// }

// // Auto-hide alerts
// function initializeAlerts() {
//   const alerts = document.querySelectorAll(".alert")
//   alerts.forEach((alert) => {
//     const closeBtn = document.createElement("button")
//     closeBtn.innerHTML = "×"
//     closeBtn.style.cssText = `
//       position: absolute;
//       top: 0.5rem;
//       right: 1rem;
//       background: none;
//       border: none;
//       font-size: 1.5rem;
//       cursor: pointer;
//       color: inherit;
//       opacity: 0.7;
//       transition: opacity 0.3s;
//     `
//     closeBtn.addEventListener("mouseenter", () => (closeBtn.style.opacity = "1"))
//     closeBtn.addEventListener("mouseleave", () => (closeBtn.style.opacity = "0.7"))
//     closeBtn.addEventListener("click", () => hideAlert(alert))

//     alert.style.position = "relative"
//     alert.appendChild(closeBtn)

//     setTimeout(() => {
//       hideAlert(alert)
//     }, 6000)
//   })
// }

// function hideAlert(alert) {
//   alert.style.transform = "translateX(100%)"
//   alert.style.opacity = "0"
//   setTimeout(() => {
//     if (alert.parentNode) {
//       alert.remove()
//     }
//   }, 300)
// }

// // Search functionality
// function initializeSearch() {
//   const searchInput = document.querySelector(".search-input")
//   if (searchInput) {
//     searchInput.addEventListener("input", (e) => {
//       const query = e.target.value.toLowerCase()
//       // Add your search logic here
//       console.log("Searching for:", query)
//     })
//   }
// }

// // Search table functionality
// function searchTable(inputId, tableId) {
//   const input = document.getElementById(inputId)
//   const table = document.getElementById(tableId)
//   const rows = table.getElementsByTagName("tr")

//   input.addEventListener("keyup", () => {
//     const filter = input.value.toLowerCase()
//     let visibleRows = 0

//     for (let i = 1; i < rows.length; i++) {
//       const row = rows[i]
//       const cells = row.getElementsByTagName("td")
//       let found = false

//       cells.forEach((cell) => {
//         cell.innerHTML = cell.textContent
//       })

//       for (let j = 0; j < cells.length; j++) {
//         const cellText = cells[j].textContent.toLowerCase()
//         if (cellText.includes(filter) && filter.length > 0) {
//           found = true
//           const regex = new RegExp(`(${filter})`, "gi")
//           cells[j].innerHTML = cells[j].textContent.replace(
//             regex,
//             '<mark style="background: var(--accent-light); padding: 0.1em 0.2em; border-radius: 3px;">$1</mark>',
//           )
//         }
//       }

//       if (found || filter.length === 0) {
//         row.style.display = ""
//         visibleRows++
//       } else {
//         row.style.display = "none"
//       }
//     }

//     let noResultsMsg = table.parentNode.querySelector(".no-results")
//     if (visibleRows === 0 && filter.length > 0) {
//       if (!noResultsMsg) {
//         noResultsMsg = document.createElement("div")
//         noResultsMsg.className = "no-results"
//         noResultsMsg.style.cssText = `
//           text-align: center;
//           padding: 2rem;
//           color: var(--text-secondary);
//           font-style: italic;
//         `
//         noResultsMsg.textContent = "No results found for your search."
//         table.parentNode.appendChild(noResultsMsg)
//       }
//       noResultsMsg.style.display = "block"
//     } else if (noResultsMsg) {
//       noResultsMsg.style.display = "none"
//     }
//   })
// }

// // Marks validation
// function validateMarks(input) {
//   const value = Number.parseInt(input.value)
//   const formGroup = input.closest(".form-group")
//   let feedback = formGroup.querySelector(".marks-feedback")

//   if (!feedback) {
//     feedback = document.createElement("div")
//     feedback.className = "marks-feedback"
//     feedback.style.cssText = `
//       margin-top: 0.5rem;
//       font-size: 0.875rem;
//       font-weight: 500;
//       transition: var(--transition);
//     `
//     formGroup.appendChild(feedback)
//   }

//   if (isNaN(value) || value < 0 || value > 100) {
//     input.style.borderColor = "var(--danger-color)"
//     input.style.boxShadow = "0 0 0 3px rgba(231, 76, 60, 0.1)"
//     feedback.style.color = "var(--danger-color)"
//     feedback.textContent = "Marks must be between 0 and 100"
//     input.focus()
//     return false
//   } else {
//     input.style.borderColor = "var(--success-color)"
//     input.style.boxShadow = "0 0 0 3px rgba(39, 174, 96, 0.1)"
//     feedback.style.color = "var(--success-color)"

//     let grade = "F"
//     if (value >= 80) grade = "A (Excellent)"
//     else if (value >= 70) grade = "B (Good)"
//     else if (value >= 60) grade = "C (Average)"
//     else if (value >= 50) grade = "D (Below Average)"
//     else grade = "F (Fail)"

//     feedback.textContent = `Grade: ${grade}`
//     return true
//   }
// }

// // Loading states
// function addLoadingState(button, text = "Loading...") {
//   const originalText = button.textContent
//   button.disabled = true
//   button.innerHTML = `<span class="loading"></span> ${text}`

//   return () => {
//     button.disabled = false
//     button.textContent = originalText
//   }
// }

// // Sidebar functionality
// document.addEventListener("DOMContentLoaded", () => {
//   const sidebar = document.querySelector(".sidebar")
//   const sidebarToggle = document.querySelector(".sidebar-toggle")
//   const mobileMenuBtn = document.querySelector(".mobile-menu-btn")
//   const mobileOverlay = document.querySelector(".mobile-overlay")

//   // Desktop sidebar toggle
//   if (sidebarToggle) {
//     sidebarToggle.addEventListener("click", () => {
//       sidebar.classList.toggle("collapsed")
//       localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"))
//     })
//   }

//   // Mobile menu toggle
//   if (mobileMenuBtn) {
//     mobileMenuBtn.addEventListener("click", () => {
//       sidebar.classList.toggle("open")
//       mobileOverlay.classList.toggle("active")
//       document.body.style.overflow = sidebar.classList.contains("open") ? "hidden" : ""
//     })
//   }

//   // Close mobile menu when clicking overlay
//   if (mobileOverlay) {
//     mobileOverlay.addEventListener("click", () => {
//       sidebar.classList.remove("open")
//       mobileOverlay.classList.remove("active")
//       document.body.style.overflow = ""
//     })
//   }

//   // Restore sidebar state
//   const sidebarCollapsed = localStorage.getItem("sidebarCollapsed") === "true"
//   if (sidebarCollapsed && sidebar) {
//     sidebar.classList.add("collapsed")
//   }

//   // Set active navigation item
//   const currentPath = window.location.pathname
//   const navItems = document.querySelectorAll(".nav-item")
//   navItems.forEach((item) => {
//     const href = item.getAttribute("href")
//     if (href && currentPath.includes(href.replace("./", ""))) {
//       item.classList.add("active")
//     }
//   })

//   initializeSearch()
//   initializeAlerts()

//   // Form submission handling
//   const forms = document.querySelectorAll("form")
//   forms.forEach((form) => {
//     form.addEventListener("submit", (e) => {
//       const submitBtn = form.querySelector('button[type="submit"]')
//       if (submitBtn) {
//         const removeLoading = addLoadingState(submitBtn, "Processing...")
//         setTimeout(removeLoading, 3000)
//       }
//     })
//   })

//   // Keyboard shortcuts
//   document.addEventListener("keydown", (e) => {
//     if ((e.ctrlKey || e.metaKey) && e.key === "k") {
//       e.preventDefault()
//       const searchInput = document.querySelector(".search-input")
//       if (searchInput) {
//         searchInput.focus()
//       }
//     }

//     if (e.key === "Escape") {
//       const alerts = document.querySelectorAll(".alert")
//       alerts.forEach((alert) => hideAlert(alert))
//     }
//   })
// })

// // Table sorting
// function makeSortable(tableId) {
//   const table = document.getElementById(tableId)
//   if (!table) return

//   const headers = table.querySelectorAll("th")
//   headers.forEach((header, index) => {
//     header.style.cursor = "pointer"
//     header.style.userSelect = "none"
//     header.addEventListener("click", () => sortTable(table, index))
//   })
// }

// function sortTable(table, columnIndex) {
//   const tbody = table.querySelector("tbody")
//   const rows = Array.from(tbody.querySelectorAll("tr"))

//   const isNumeric = rows.every((row) => {
//     const cell = row.cells[columnIndex]
//     return cell && !isNaN(Number.parseFloat(cell.textContent.trim()))
//   })

//   rows.sort((a, b) => {
//     const aVal = a.cells[columnIndex].textContent.trim()
//     const bVal = b.cells[columnIndex].textContent.trim()

//     if (isNumeric) {
//       return Number.parseFloat(bVal) - Number.parseFloat(aVal)
//     } else {
//       return aVal.localeCompare(bVal)
//     }
//   })

//   rows.forEach((row) => tbody.appendChild(row))
// }

// // Initialize sortable tables
// document.addEventListener("DOMContentLoaded", () => {
//   const tables = document.querySelectorAll(".table")
//   tables.forEach((table) => {
//     if (table.id) {
//       makeSortable(table.id)
//     }
//   })
// })
