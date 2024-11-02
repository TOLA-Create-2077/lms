// Function to get the status badge class
function getStatusBadgeClass(status) {
    const statusClasses = {
        'អនុញ្ញាត': 'badge bg-success',
        'បដិសេធ': 'badge bg-danger',
        'បោះបង់': 'badge bg-secondary',
        'កំពុងរងចាំ': 'badge bg-warning'
    };

    // Return the corresponding class or a default class if not found
    return statusClasses[status] || 'badge bg-secondary';
}

// Example usage:
// Assuming you have an element where you want to apply the class
document.addEventListener('DOMContentLoaded', () => {
    const statusElement = document.getElementById('status-badge'); // Replace with your element ID
    const status = statusElement.getAttribute('data-status'); // Assuming you use data-status attribute
    statusElement.className = getStatusBadgeClass(status);
});
