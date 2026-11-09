<?php
session_start();
require_once 'auth_check.php';
require_once __DIR__ . '/../connection.php';

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <div class="header-title">
            <h1><i class="fas fa-bell" style="color: #4361ee; margin-right: 10px;"></i>Notifications</h1>
            <p>View system alerts and user activity</p>
        </div>
    </div>
    
    <div class="notifications-wrapper">
        <div class="notification-header-actions">
            <div class="notification-filter">
                <button class="filter-btn active">All</button>
                <button class="filter-btn">Unread</button>
                <button class="filter-btn">System</button>
                <button class="filter-btn">Users</button>
            </div>
            
            <div class="notification-actions">
                <button id="markAllRead" class="btn-action">
                    <i class="fas fa-check-circle"></i> Mark all read
                </button>
                <button id="clearAll" class="btn-action btn-action-danger">
                    <i class="fas fa-trash-alt"></i> Clear all
                </button>
            </div>
        </div>
        
        <div class="notification-list" id="notificationList">
            <!-- Loading state -->
            <div class="empty-state">
                <i class="fas fa-spinner fa-spin fa-3x" style="color: #cbd5e1; margin-bottom: 15px;"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
    </div>
</div>

<style>
.notifications-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.notification-header-actions {
    padding: 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    background: #f8fafc;
}

.notification-filter {
    display: flex;
    gap: 10px;
}

.filter-btn {
    padding: 8px 16px;
    border-radius: 20px;
    background-color: white;
    border: 1px solid #e2e8f0;
    color: #64748b;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.filter-btn.active, .filter-btn:hover {
    background-color: #4f46e5;
    border-color: #4f46e5;
    color: white;
}

.notification-actions {
    display: flex;
    gap: 10px;
}

.btn-action {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 8px 15px;
    border-radius: 6px;
    color: #475569;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-action:hover {
    background: #f1f5f9;
    color: #1e293b;
}

.btn-action-danger:hover {
    background: #fef2f2;
    color: #ef4444;
    border-color: #fca5a5;
}

.notification-item {
    padding: 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    gap: 20px;
    transition: background-color 0.2s;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background-color: #f8fafc;
}

.notification-item.unread {
    background-color: #f0fdf4;
    border-left: 4px solid #22c55e;
}

.notification-icon-container {
    min-width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.notification-info {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 5px;
    font-size: 1.05rem;
}

.notification-message {
    color: #475569;
    font-size: 0.95rem;
    margin-bottom: 8px;
    line-height: 1.5;
}

.notification-time {
    color: #94a3b8;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.notification-status {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: transparent;
    margin-top: 15px;
    flex-shrink: 0;
}

.unread .notification-status {
    background-color: #22c55e;
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
    color: #94a3b8;
}

/* Specific icon colors */
.bg-blue { background-color: #e0f2fe; color: #0284c7; }
.bg-green { background-color: #dcfce7; color: #16a34a; }
.bg-yellow { background-color: #fef9c3; color: #ca8a04; }
.bg-purple { background-color: #f3e8ff; color: #9333ea; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to format time difference
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        let interval = Math.floor(seconds / 31536000);
        if (interval > 1) return interval + " years ago";
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) return interval + " months ago";
        interval = Math.floor(seconds / 86400);
        if (interval > 1) return interval + " days ago";
        interval = Math.floor(seconds / 3600);
        if (interval > 1) return interval + " hours ago";
        interval = Math.floor(seconds / 60);
        if (interval > 1) return interval + " mins ago";
        return Math.floor(seconds) + " secs ago";
    }

    // Render function
    function renderNotifications(notifications) {
        const notificationList = document.getElementById('notificationList');
        
        if (!notifications || notifications.length === 0) {
            notificationList.innerHTML = '<div class="empty-state"><i class="far fa-bell-slash fa-4x" style="margin-bottom: 20px; color: #e2e8f0;"></i><h3>All caught up!</h3><p>You have no new notifications.</p></div>';
            return;
        }
        
        let html = '';
        notifications.forEach(n => {
            const isUnread = n.is_read === 0;
            
            // Determine icon and color based on content
            let iconClass = 'fas fa-bell';
            let bgClass = 'bg-blue';
            
            if (n.title.includes('User')) {
                iconClass = 'fas fa-user-plus';
                bgClass = 'bg-green';
            } else if (n.title.includes('Alert')) {
                iconClass = 'fas fa-exclamation-triangle';
                bgClass = 'bg-yellow';
            } else if (n.title.includes('Order') || n.title.includes('Payment')) {
                iconClass = 'fas fa-dollar-sign';
                bgClass = 'bg-purple';
            }
            
            html += `
                <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${n.id}">
                    <div class="notification-icon-container ${bgClass}">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="notification-info">
                        <div class="notification-title">${n.title}</div>
                        <div class="notification-message">${n.message}</div>
                        <div class="notification-time">
                            <i class="far fa-clock"></i>
                            ${timeAgo(n.created_at)}
                        </div>
                    </div>
                    <div class="notification-status"></div>
                </div>
            `;
        });
        
        notificationList.innerHTML = html;
        
        // Attach click handlers to new items
        document.querySelectorAll('.notification-item.unread').forEach(item => {
            item.addEventListener('click', function() {
                this.classList.remove('unread');
                // Simulate AJAX call
                console.log('Marked as read:', this.dataset.id);
            });
        });
    }

    // Simulate fetching data
    function fetchNotifications() {
        const simulatedData = [
            { id: 1, title: "New User Registration", message: "Jane Smith has registered", is_read: 0, created_at: new Date(Date.now() - 3600000).toISOString() },
            { id: 2, title: "Payment Received", message: "Payment of $1500 for Admission ID 25 has been received", is_read: 0, created_at: new Date(Date.now() - 7200000).toISOString() },
            { id: 3, title: "New Appointment Scheduled", message: "Amar has scheduled an appointment", is_read: 1, created_at: new Date(Date.now() - 86400000).toISOString() },
            { id: 4, title: "Server Alert", message: "Database backup completed successfully", is_read: 1, created_at: new Date(Date.now() - 172800000).toISOString() }
        ];
        renderNotifications(simulatedData);
    }

    // Init
    setTimeout(fetchNotifications, 500); // Small delay to show loading state

    // Filter buttons
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Action buttons
    document.getElementById('markAllRead').addEventListener('click', function() {
        document.querySelectorAll('.notification-item').forEach(item => {
            item.classList.remove('unread');
        });
    });

    document.getElementById('clearAll').addEventListener('click', function() {
        if(confirm('Are you sure you want to clear all notifications?')) {
            renderNotifications([]);
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>