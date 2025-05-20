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

            <div class="modal-body" style="overflow-y: scroll;">
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
        </div>
    </div>
</div>
