<div class="mx-auto max-w-6xl p-6 space-y-6">
    <h1 class="text-xl font-semibold">Logs & Events</h1>

    <div class="flex flex-col sm:flex-row sm:items-end gap-3">
        <div class="flex-1">
            <label class="block text-xs text-gray-600 mb-1">Search</label>
            <input type="text" wire:model.live="q" class="w-full rounded border-gray-300" placeholder="IP, Exam Title, User-Agent">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Event</label>
            <select wire:model.live="event" class="rounded border-gray-300">
                <option value="all">All</option>
                <option value="exam_started">Exam Started</option>
                <option value="exam_finished">Exam Finished</option>
                <option value="result_viewed">View Result (Attempt)</option>
                <option value="result_viewed_session">View Result (Session)</option>
            </select>
        </div>
    </div>

    @if($logs->count())
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="px-3 py-2 text-left">#</th>
                        <th class="px-3 py-2 text-left">Time</th>
                        <th class="px-3 py-2 text-left">User</th>
                        <th class="px-3 py-2 text-left">Event</th>
                        <th class="px-3 py-2 text-left">Exam</th>
                        <th class="px-3 py-2 text-left">Attempt</th>
                        <th class="px-3 py-2 text-left">IP</th>
                        <th class="px-3 py-2 text-left">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $row)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $row->id }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('Y/m/d H:i') : '' }}</td>
                            <td class="px-3 py-2">{{ $row->user?->name ?? 'Guest' }}</td>
                            <td class="px-3 py-2">{{ $row->event }}</td>
                            <td class="px-3 py-2">{{ $row->exam?->title ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $row->attempt_id ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $row->ip ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-600">
                                <pre class="text-xs whitespace-pre-wrap">{{ json_encode($row->meta, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @else
        <div class="rounded border p-4 text-sm text-gray-600">No logs found.</div>
    @endif
</div>
