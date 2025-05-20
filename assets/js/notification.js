document.addEventListener("DOMContentLoaded", function () {
    const notificationBell = document.querySelector(".nav-notif");
    const notificationCount = document.getElementById("notification-count");
    const notificationContainer = document.querySelector(".list-group");

    function fetchNotifications() {
        axios.get("./notification/get_notifications.php") // Update with actual path
            .then(response => {
                const data = response.data;
                notificationContainer.innerHTML = "";

                if (data.unread_count > 0) {
                    notificationCount.textContent = data.unread_count;
                    notificationCount.style.display = "flex";
                } else {
                    notificationCount.style.display = "none";
                }

                if (data.notifications.length > 0) {
                    data.notifications.forEach(n => {
                        let notificationHTML = `
                            <div class="alert alert-primary alert-dismissible fade show d-flex align-items-center justify-content-start" role="alert" id="notification">
                                ${n.file_path ? `<img class="fade show" src="../${n.file_path}" width="35" height="35">` : `<img class="fade show" src="/images/default-notif.png" width="35" height="35">`}
                                <strong>${n.name}</strong> ${n.action} a file.<br>
                            </div>`;
                        notificationContainer.innerHTML += notificationHTML;
                    });
                } else {
                    notificationContainer.innerHTML = `<div id="no-notifications" style="text-align:center; margin-top:10px;">No notifications</div>`;
                }
            })
            .catch(error => console.error("Error fetching notifications:", error));
    }

    function markNotificationsAsRead() {
        axios.post("./notification/read_notifications.php")
            .then(() => {
                fetchNotifications();
            })
            .catch(error => console.error("Error marking notifications as read:", error));
    }

    notificationBell.addEventListener("click", function () {
        markNotificationsAsRead();
    });

    fetchNotifications();
});