<?php
session_start();
$pageTitle = "Teacher Dashboard";
require_once '../config/database.php';


include './includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/user_navbar.css">

<?php 
include './includes/navbar.php';
include './includes/sidebar.php';
?>

<main role="main" class="main-content">
            
    <!--For Notification header naman ito-->
    <div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="defaultModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="list-group list-group-flush my-n3">
                    <div class="col-12 mb-4">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="notification">
                        <img class="fade show" src="../assets/images/unified-lgu-logo.png" width="35" height="35">
                        <strong style="font-size:12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></strong> 
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="removeNotification()">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    </div>

                <div id="no-notifications" style="display: none; text-align:center; margin-top:10px;">
                    No notifications
                </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-block" onclick="clearAllNotifications()">Clear All</button>
            </div>
            </div>
        </div>
    </div>


    <!-- Page Content Here -->
    <div class="container-fluid py-3">
        <div class="welcome-section">
            <h3 class="mb-0">Notifications</h3>
        </div>

        <div class="container-fluid px-4 mt-5 ">
            <ul class="list-group">
                {% for notif in page_obj %}
                <div class="d-flex mt-3">
                    <div style="width: 150px; height: 100px; ">
                        <img src="{{ notif.file_path }}" class="img-fluid" style="width: 150px; height: 100px; object-fit: contain;" alt="">
                    </div>
                    <li class="list-group-item">
                        <strong>{{ notif.name }}</strong> {{ notif.action }} a file <br>
                        <small>{{ notif.date_created }}</small>
                    </li>

                </div>
                {% empty %}
                <li class="list-group-item text-center">No notifications found.</li>
                {% endfor %}
            </ul>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center mt-3">
                {% if page_obj.has_previous %}
                    <li class="page-item"><a class="page-link" href="?page=1">First</a></li>
                    <li class="page-item"><a class="page-link" href="?page={{ page_obj.previous_page_number }}">Previous</a></li>
                {% endif %}

                <li class="page-item active"><span class="page-link">{{ page_obj.number }}</span></li>

                {% if page_obj.has_next %}
                    <li class="page-item"><a class="page-link" href="?page={{ page_obj.next_page_number }}">Next</a></li>
                    <li class="page-item"><a class="page-link" href="?page={{ page_obj.paginator.num_pages }}">Last</a></li>
                {% endif %}
                </ul>
            </nav>
        </div>
    </div>
</main>

<?php
include './includes/footer.php';

?>




