<div class="table-responsive">
    <table class="table table-bordered align-middle mb-0">
        <thead class="table-light text-center">
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
                <tr data-id="{{ $row->id }}">
                    <td>{{ $row->title }}</td>
                    <td>{{ $row->description }}</td>
                    <td class="text-center">
                        @if($row->is_completed)
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-warning text-dark">Incomplete</span>
                        @endif
                    </td>

                    {{-- Priority --}}
                    <td class="text-center">
                        @php
                            if ($row->position == 1)
                                $priority = 'High';
                            elseif ($row->position == 2)
                                $priority = 'Medium';
                            else
                                $priority = 'Low';
                        @endphp
                        <span class="badge 
                            {{ $row->position == 1 ? 'bg-danger' : ($row->position == 2 ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $priority }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
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
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }
        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 0.5rem;
        }
        .table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem;
        }
        .table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            text-transform: capitalize;
        }
    }
</style>
