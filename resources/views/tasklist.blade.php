<div class="table-responsive">
    <table class="table table-bordered align-middle mb-0">
        <thead class="table-light text-center d-none d-md-table-header-group">
            <tr>
                <th style="min-width: 150px;">Title</th>
                <th style="min-width: 250px;">Description</th>
                <th style="min-width: 120px;">Status</th>
                <th style="min-width: 100px;">Priority</th>
                <th style="min-width: 200px;">Action</th>
            </tr>
        </thead>
        <tbody id="task-list">
            @forelse($data as $index => $row)
                <tr data-id="{{ $row->id }}" class="bg-white shadow-sm mb-2 rounded d-block d-md-table-row">
                    <td data-label="Title">{{ $row->title }}</td>
                    <td data-label="Description">{{ $row->description }}</td>
                    <td data-label="Status" class="text-center">
                        @if($row->is_completed)
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-warning text-dark">Incomplete</span>
                        @endif
                    </td>
                    <td data-label="Priority" class="text-center">
                        @php
                            $priority = $row->position == 1 ? 'High' : ($row->position == 2 ? 'Medium' : 'Low');
                            $priorityClass = $row->position == 1 ? 'bg-danger' : ($row->position == 2 ? 'bg-warning text-dark' : 'bg-success');
                        @endphp
                        <span class="badge {{ $priorityClass }}">{{ $priority }}</span>
                    </td>
                    <td data-label="Action" class="text-center">
                        <div class="d-flex justify-content-center flex-wrap gap-2">
                            @if(!$row->is_completed)
                                <button type="button" class="btn btn-sm btn-primary task-edit" data-id="{{ $row->id }}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary task-toggle" data-id="{{ $row->id }}">
                                    Mark Complete
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-danger task-delete" data-id="{{ $row->id }}">
                                    Delete
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary task-toggle" data-id="{{ $row->id }}">
                                    Mark Incomplete
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No tasks found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="my-pagination mt-3 d-flex justify-content-center">
    {{ $data->links('pagination::bootstrap-5') }}
</div>

<style>
    /* Responsive card-style table for mobile */
    @media (max-width: 768px) {
        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 0.75rem;
            background: #fff;
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

        /* Adjust badges and buttons */
        .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }

        .d-flex.flex-wrap.gap-2 {
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    /* Table hover effect for desktop */
    @media (min-width: 769px) {
        #task-list tr:hover {
            background-color: #f8f9fa;
        }
    }
</style>
