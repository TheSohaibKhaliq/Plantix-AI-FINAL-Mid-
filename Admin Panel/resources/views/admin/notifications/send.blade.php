@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; color: var(--agri-text-muted); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--agri-text-muted);"></i>
                <a href="{{ route('admin.notification') }}" style="text-decoration: none; color: var(--agri-text-muted); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                    Notifications
                </a>
                <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--agri-text-muted);"></i>
                <span style="color: var(--agri-primary); font-size: 13px; font-weight: 600;">Send</span>
            </div>
            <h1 style="font-size: 26px; font-weight: 700; color: var(--agri-primary-dark); margin: 0;"><i class="fa fa-bell text-success me-2"></i>Send Notification</h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card-agri" style="padding: 0; overflow: hidden;">
                    <div style="padding: 24px 28px; border-bottom: 1px solid var(--agri-border); display: flex; align-items: center; justify-content: space-between;">
                        <h2 style="font-size: 18px; font-weight: 800; color: var(--agri-text-heading); margin: 0; display: flex; align-items: center; gap: 12px;">
                            <i class="fa fa-bullhorn" style="color: var(--agri-primary);"></i> Broadcast Notification
                        </h2>
                    </div>
                    <div style="padding: 32px 28px;">
                        <div id="data-table_processing" class="text-center py-3" style="display: none; color: var(--agri-text-muted);">
                            <i class="fa fa-spinner fa-spin me-2"></i> Sending...
                        </div>
                        <div class="error_top alert alert-danger" style="display:none; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; border-radius: 12px; padding: 16px; font-size: 14px; font-weight: 600; margin-bottom: 24px;"></div>
                        <div class="success_top alert alert-success" style="display:none; background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; border-radius: 12px; padding: 16px; font-size: 14px; font-weight: 600; margin-bottom: 24px;"></div>

                        <div class="row">
                            <div class="col-md-10 mx-auto">
                                <div class="mb-4">
                                    <label style="font-size: 12px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 8px; display: block;">Subject</label>
                                    <input type="text" class="form-agri" id="subject" placeholder="Enter notification subject...">
                                </div>

                                <div class="mb-4">
                                    <label style="font-size: 12px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 8px; display: block;">Message</label>
                                    <textarea class="form-agri" rows="5" id="message" placeholder="Enter the notification message..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <label style="font-size: 12px; font-weight: 700; color: var(--agri-text-muted); text-transform: uppercase; margin-bottom: 8px; display: block;">Send To</label>
                                    <select id="role" class="form-agri">
                                        <option value="customer">Customers (Farmers)</option>
                                        <option value="vendor">Vendors</option>
                                        <option value="expert">Experts</option>
                                        <option value="admin">Admins</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 24px 28px; border-top: 1px solid var(--agri-border); display: flex; justify-content: flex-end; gap: 16px; background: #F9FAFB;">
                        <a href="{{ route('admin.notification') }}" class="btn-agri btn-agri-outline" style="text-decoration: none;">
                            <i class="fa fa-undo"></i> Cancel
                        </a>
                        <button type="button" class="btn-agri btn-agri-primary save-form-btn">
                            <i class="fa fa-paper-plane"></i> Send Notification
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $(".save-form-btn").click(function () {
        $(".success_top").hide();
        $(".error_top").hide();

        var subject = $("#subject").val().trim();
        var message = $("#message").val().trim();
        var role    = $("#role").val();

        if (!subject) {
            $(".error_top").show().html("<p>Please enter a subject.</p>");
            window.scrollTo(0, 0);
            return;
        }
        if (!message) {
            $(".error_top").show().html("<p>Please enter a message.</p>");
            window.scrollTo(0, 0);
            return;
        }

        $("#data-table_processing").show();
        $(".save-form-btn").prop('disabled', true);

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: '{{ route("admin.notification.broadcast") }}',
            data: {
                role:    role,
                subject: subject,
                message: message,
                _token:  '{{ csrf_token() }}'
            },
            success: function (response) {
                $("#data-table_processing").hide();
                $(".save-form-btn").prop('disabled', false);
                if (response.success) {
                    $(".success_top").show().html("<p>" + response.message + "</p>");
                    window.scrollTo(0, 0);
                    setTimeout(function () {
                        window.location.href = '{{ route("admin.notification") }}';
                    }, 2500);
                } else {
                    $(".error_top").show().html("<p>" + response.message + "</p>");
                    window.scrollTo(0, 0);
                }
            },
            error: function (xhr) {
                $("#data-table_processing").hide();
                $(".save-form-btn").prop('disabled', false);
                var msg = xhr.responseJSON?.message ?? 'An error occurred. Please try again.';
                $(".error_top").show().html("<p>" + msg + "</p>");
                window.scrollTo(0, 0);
            }
        });
    });
});
</script>
@endsection
