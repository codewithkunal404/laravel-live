<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery & jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

    <style>
        body {
            padding-bottom: 80px;
        }

        #task-list tr {
            cursor: move;
        }

        /* Toast container */
        #toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1055;
            max-width: 90%;
        }

        @media (max-width: 576px) {
            #toast-container {
                left: 50%;
                right: auto;
                transform: translateX(-50%);
            }

            .toast {
                width: 100%;
                max-width: 350px;
                font-size: 0.9rem;
            }

            /* Form responsive adjustments */
            #taskform .col-md-3,
            #taskform .col-md-4,
            #taskform .col-md-2 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            #taskform button {
                margin-top: 0.5rem;
            }
        }

        /* Table responsiveness for mobile */
        @media (max-width: 768px) {
            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                padding: 0.75rem;
                background: #fff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border: none;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                text-transform: capitalize;
            }

            .d-flex.flex-wrap.gap-2 {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body class="p-3">

    <div class="container">
        <h2 class="mb-3 text-center text-md-start">Task Management</h2>

        <!-- Task Form -->
        <form id="taskform" class="row g-2 mb-3">
            @csrf
            <input type="hidden" id="task_id" name="task_id">
            <div class="col-md-3">
                <input type="text" id="title" name="title" class="form-control" placeholder="Task Title">
            </div>
            <div class="col-md-4">
                <input type="text" id="description" name="description" class="form-control"
                    placeholder="Task Description">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </div>
        </form>

        <!-- Filters -->
        <div class="row mb-3 g-2">
            <div class="col-md-4 col-12">
                <input type="text" id="search" class="form-control" placeholder="Search Task">
            </div>
            <div class="col-md-3 col-12">
                <select id="task_status" class="form-select">
                    <option value="-1">All Tasks</option>
                    <option value="1">Completed</option>
                    <option value="0">Incompleted</option>
                </select>
            </div>
        </div>

        <!-- Task List -->
        <div id="containerForm" class="table-responsive"></div>
    </div>

    <!-- Toast container -->
    <div id="toast-container"></div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-4">
        <div class="container">
            <small>Made by Kunal Chaudhary © 2025. All rights reserved.</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // Toast helper
            function showToast(message, type = 'success') {
                const toastId = 'toast-' + Date.now();
                const toastHtml = `
                    <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>`;
                $('#toast-container').append(toastHtml);
                const toastEl = document.getElementById(toastId);
                const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
                bsToast.show();
                toastEl.addEventListener('hidden.bs.toast', () => $(toastEl).remove());
            }

            // Load tasks
            function loadData(search = null, status = null, page = 1) {
                let url = "{{ route('tasks.list') }}";
                let params = [];
                if (search) params.push("filter=" + encodeURIComponent(search));
                if (status != null && status != -1) params.push("task_status=" + encodeURIComponent(status));
                if (page) params.push("page=" + page);
                if (params.length > 0) url += "?" + params.join("&");

                $("#containerForm").load(url, function () {
                    initSortable();
                });
                $("#taskform")[0].reset();
                $('#task_id').val("");
            }

            // Initialize drag & drop
            function initSortable() {
                let tbody = $("#task-list");
                if (!tbody.length) return;

                tbody.sortable({
                    helper: function (e, ui) {
                        ui.children().each(function () { $(this).width($(this).width()); });
                        return ui;
                    },
                    cursor: "move",
                    update: function () {
                        let order = [];
                        tbody.find("tr").each(function (index) {
                            order.push({ id: $(this).data("id"), position: index + 1 });
                        });

                        $.post("{{ route('tasks.reorder') }}", {
                            _token: $("meta[name='csrf-token']").attr("content"),
                            order: order
                        }, function (response) {
                            showToast(response.msg, 'success');
                            loadData();
                        }).fail(function () {
                            showToast("Error updating order", 'danger');
                            loadData();
                        });
                    }
                });
                tbody.find("tr").css("cursor", "move");
            }

            // Initial load
            loadData();

            // Pagination
            $(document).on("click", ".my-pagination a", function (e) {
                e.preventDefault();
                let page = new URL($(this).attr("href")).searchParams.get("page");
                loadData($("#search").val(), $("#task_status").val(), page);
            });

            // Search & filter
            $("#search").on("keyup", function () { loadData($(this).val(), $("#task_status").val()); });
            $("#task_status").on("change", function () { loadData($("#search").val(), $(this).val()); });

            // Add / Edit task
            $("#taskform").on("submit", function (e) {
                e.preventDefault();
                $.post("{{ route('tasks.store') }}", $(this).serialize(), function (res) {
                    showToast(res.msg, 'success');
                    loadData();
                }).fail(function (xhr) {
                    showToast("Error: " + xhr.responseText, 'danger');
                });
            });

            // Delete task
            $(document).on("click", ".task-delete", function (e) {
                e.preventDefault();
                let id = $(this).data("id");
                $.post("{{ route('tasks.delete') }}", { id: id }, function () {
                    showToast("Task deleted", 'success');
                    loadData();
                });
            });

            // Toggle completion
            $(document).on("click", ".task-toggle", function (e) {
                e.preventDefault();
                $.post("{{ route('tasks.toggle') }}", { id: $(this).data("id") }, function () {
                    showToast("Task status updated", 'success');
                    loadData();
                });
            });

            // Edit task with scroll
            $(document).on("click", ".task-edit", function (e) {
                e.preventDefault();
                let id = $(this).data("id");
                $.post("{{ route('tasks.edit') }}", { id: id }, function (res) {
                    $("#task_id").val(res.id);
                    $("#title").val(res.title);
                    $("#description").val(res.description);
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                });
            });

        });
    </script>

</body>
</html>
