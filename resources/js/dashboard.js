// Dashboard-specific JavaScript functionality
import { initializeThemeToggle, initializeMobileMenu } from './app.js';

// Dashboard initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeThemeToggle();
    initializeMobileMenu();
    initializeDashboardCharts();
    initializeRealTimeUpdates();
    initializeInteractiveElements();
    initializeDataTables();
    initializeSearchFunctionality();
});

// Dashboard Charts
function initializeDashboardCharts() {
    // Check if Chart.js is available
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded. Skipping chart initialization.');
        return;
    }

    // Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart');
    if (appointmentsCtx) {
        new Chart(appointmentsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Upcoming', 'Cancelled', 'Pending'],
                datasets: [{
                    data: [65, 20, 10, 5],
                    backgroundColor: [
                        '#10B981', // Green
                        '#3B82F6', // Blue
                        '#EF4444', // Red
                        '#F59E0B'  // Yellow
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            }
        });
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 35000, 32000, 40000, 38000, 45000],
                    borderColor: '#F0C24B',
                    backgroundColor: 'rgba(240, 194, 75, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Patient Growth Chart
    const patientCtx = document.getElementById('patientGrowthChart');
    if (patientCtx) {
        new Chart(patientCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Patients',
                    data: [45, 52, 38, 67, 58, 72],
                    backgroundColor: '#8B5CF6',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Real-time Updates
function initializeRealTimeUpdates() {
    // Simulate real-time data updates
    setInterval(() => {
        updateLiveStats();
    }, 30000); // Update every 30 seconds

    // Update appointment statuses
    setInterval(() => {
        updateAppointmentStatuses();
    }, 60000); // Update every minute
}

function updateLiveStats() {
    // Update statistics cards with animation
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        const valueElement = card.querySelector('.stat-value');
        if (valueElement) {
            const currentValue = parseInt(valueElement.textContent.replace(/,/g, ''));
            const newValue = currentValue + Math.floor(Math.random() * 5);

            // Animate the number change
            animateNumber(valueElement, currentValue, newValue);
        }
    });
}

function animateNumber(element, start, end) {
    const duration = 1000;
    const startTime = performance.now();

    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        const current = Math.floor(start + (end - start) * progress);
        element.textContent = current.toLocaleString();

        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }

    requestAnimationFrame(updateNumber);
}

function updateAppointmentStatuses() {
    // Update appointment status badges
    const statusBadges = document.querySelectorAll('.status-badge');
    statusBadges.forEach(badge => {
        const statuses = ['Confirmed', 'In Progress', 'Completed', 'Cancelled'];
        const randomStatus = statuses[Math.floor(Math.random() * statuses.length)];

        // Only update occasionally to avoid too much change
        if (Math.random() < 0.1) {
            updateStatusBadge(badge, randomStatus);
        }
    });
}

function updateStatusBadge(badge, status) {
    const statusClasses = {
        'Confirmed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'In Progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'Completed': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        'Cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    };

    badge.className = `px-2 py-1 rounded-full text-xs font-medium ${statusClasses[status]}`;
    badge.textContent = status;
}

// Interactive Elements
function initializeInteractiveElements() {
    // Quick action buttons
    const quickActions = document.querySelectorAll('.quick-action');
    quickActions.forEach(action => {
        action.addEventListener('click', function(e) {
            e.preventDefault();

            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);

            // Handle different action types
            const actionType = this.dataset.action;
            handleQuickAction(actionType);
        });
    });

    // Card hover effects
    const cards = document.querySelectorAll('.card, .feature-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.1)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
        });
    });
}

function handleQuickAction(actionType) {
    switch(actionType) {
        case 'add-doctor':
            showNotification('Opening Add Doctor form...', 'info');
            break;
        case 'manage-clinics':
            showNotification('Opening Clinic Management...', 'info');
            break;
        case 'view-reports':
            showNotification('Loading Reports...', 'info');
            break;
        case 'settings':
            showNotification('Opening Settings...', 'info');
            break;
        case 'book-appointment':
            showNotification('Opening Appointment Booking...', 'info');
            break;
        case 'ai-assistant':
            showAIModal();
            break;
        default:
            showNotification('Action not implemented yet', 'warning');
    }
}

// Data Tables
function initializeDataTables() {
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        // Add sorting functionality
        const headers = table.querySelectorAll('th[data-sortable]');
        headers.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.dataset.column;
                const currentOrder = this.dataset.order || 'asc';
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                // Update header
                this.dataset.order = newOrder;

                // Sort table
                sortTable(table, column, newOrder);
            });
        });
    });
}

function sortTable(table, column, order) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
        const aValue = a.querySelector(`[data-${column}]`).textContent;
        const bValue = b.querySelector(`[data-${column}]`).textContent;

        if (order === 'asc') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });

    // Reorder rows
    rows.forEach(row => tbody.appendChild(row));
}

// Search Functionality
function initializeSearchFunctionality() {
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', debounce(function() {
            const searchTerm = this.value.toLowerCase();
            const table = this.closest('.card').querySelector('.data-table');

            if (table) {
                filterTable(table, searchTerm);
            }
        }, 300));
    });
}

function filterTable(table, searchTerm) {
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Utility Functions
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

function showNotification(message, type = 'info') {
    if (window.showNotification) {
        window.showNotification(message, type);
    } else {
        console.log(`${type.toUpperCase()}: ${message}`);
    }
}

function showAIModal() {
    if (window.showAIModal) {
        window.showAIModal();
    } else {
        showNotification('AI Assistant feature coming soon!', 'info');
    }
}

// Export functions for use in other modules
export {
    initializeDashboardCharts,
    initializeRealTimeUpdates,
    initializeInteractiveElements,
    initializeDataTables,
    initializeSearchFunctionality
};
