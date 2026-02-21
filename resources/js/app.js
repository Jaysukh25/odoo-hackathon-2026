import './bootstrap';

// FleetFlow JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            if (mainContent) {
                mainContent.classList.toggle('expanded');
            }
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768 && sidebar && sidebarToggle) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                if (mainContent) {
                    mainContent.classList.remove('expanded');
                }
            }
        }
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('input[type="text"][id*="Search"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableId = e.target.id.replace('Search', 'TableBody');
            const tableBody = document.getElementById(tableId);
            
            if (tableBody) {
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }
        });
    });

    // Auto-refresh dashboard data every 30 seconds
    const dashboardElement = document.querySelector('[data-dashboard="true"]');
    if (dashboardElement) {
        setInterval(function() {
            // Refresh dashboard data
            fetch('/dashboard/refresh', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update dashboard elements
                updateDashboardElements(data);
            })
            .catch(error => {
                console.error('Error refreshing dashboard:', error);
            });
        }, 30000); // 30 seconds
    }

    // Form validation
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Confirm actions
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (message && !confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Auto-calculate costs
    const costCalculators = document.querySelectorAll('[data-calculate="cost"]');
    costCalculators.forEach(calculator => {
        calculator.addEventListener('input', function() {
            const liters = parseFloat(document.getElementById('liters')?.value || 0);
            const costPerLiter = parseFloat(document.getElementById('cost_per_liter')?.value || 0);
            const totalCost = liters * costPerLiter;
            const costField = document.getElementById('cost');
            if (costField) {
                costField.value = totalCost.toFixed(2);
            }
        });
    });

    // Status toggle switches
    const toggleSwitches = document.querySelectorAll('[data-toggle="status"]');
    toggleSwitches.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const url = this.getAttribute('data-url');
            const status = this.checked;
            
            if (url) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Status updated successfully', 'success');
                    } else {
                        showNotification('Error updating status', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error toggling status:', error);
                    showNotification('Error updating status', 'error');
                });
            }
        });
    });

    // Chart initialization helper
    window.initChart = function(ctx, type, data, options = {}) {
        if (typeof Chart !== 'undefined') {
            return new Chart(ctx, {
                type: type,
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    ...options
                }
            });
        }
        return null;
    };

    // Notification system
    window.showNotification = function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    };

    // Update dashboard elements
    function updateDashboardElements(data) {
        // Update KPI cards
        if (data.activeVehicles !== undefined) {
            const activeVehiclesElement = document.querySelector('[data-kpi="active-vehicles"]');
            if (activeVehiclesElement) {
                activeVehiclesElement.textContent = data.activeVehicles;
            }
        }
        
        if (data.tripsToday !== undefined) {
            const tripsTodayElement = document.querySelector('[data-kpi="trips-today"]');
            if (tripsTodayElement) {
                tripsTodayElement.textContent = data.tripsToday;
            }
        }
        
        if (data.vehiclesInShop !== undefined) {
            const vehiclesInShopElement = document.querySelector('[data-kpi="vehicles-in-shop"]');
            if (vehiclesInShopElement) {
                vehiclesInShopElement.textContent = data.vehiclesInShop;
            }
        }
        
        if (data.monthlyOperationalCost !== undefined) {
            const monthlyCostElement = document.querySelector('[data-kpi="monthly-cost"]');
            if (monthlyCostElement) {
                monthlyCostElement.textContent = '$' + data.monthlyOperationalCost.toLocaleString();
            }
        }
    }

    // Export functionality
    window.exportData = function(url, filename = 'export.csv') {
        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'text/csv',
            }
        })
        .then(response => response.blob())
        .then(blob => {
            const downloadUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(downloadUrl);
            a.remove();
        })
        .catch(error => {
            console.error('Error exporting data:', error);
            showNotification('Error exporting data', 'error');
        });
    }

    // Print functionality
    window.printTable = function(tableId) {
        const table = document.getElementById(tableId);
        if (table) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Table</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            table { width: 100%; border-collapse: collapse; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                            th { background-color: #f2f2f2; }
                        </style>
                    </head>
                    <body>
                        ${table.outerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    };

    // Initialize datepickers if available
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Set max date to today for past dates
        if (input.hasAttribute('data-max-date-today')) {
            input.max = new Date().toISOString().split('T')[0];
        }
        
        // Set min date to today for future dates
        if (input.hasAttribute('data-min-date-today')) {
            input.min = new Date().toISOString().split('T')[0];
        }
    });

    // Number formatting helpers
    window.formatNumber = function(num, decimals = 2) {
        return parseFloat(num).toFixed(decimals);
    };

    window.formatCurrency = function(num, currency = '$') {
        return currency + parseFloat(num).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    // Loading states
    window.setLoading = function(element, loading = true) {
        if (loading) {
            element.classList.add('loading');
            element.disabled = true;
        } else {
            element.classList.remove('loading');
            element.disabled = false;
        }
    };

    // Initialize tooltips on dynamically added content
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        const tooltips = node.querySelectorAll('[data-bs-toggle="tooltip"]');
                        tooltips.forEach(function(tooltip) {
                            new bootstrap.Tooltip(tooltip);
                        });
                    }
                });
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    console.log('FleetFlow initialized successfully');
});
