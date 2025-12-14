<div class="mx-auto max-w-6xl p-6 space-y-6">
    <h1 class="text-xl font-semibold">لاگ و رخدادها</h1>

    <div class="flex flex-col sm:flex-row sm:items-end gap-3">
        <div class="flex-1">
            <label class="block text-xs text-gray-600 mb-1">جستجو</label>
            <input type="text" wire:model.live="q" class="w-full rounded border-gray-300" placeholder="IP، عنوان آزمون، User-Agent">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">رویداد</label>
            <select wire:model.live="event" class="rounded border-gray-300">
                <option value="all">همه</option>
                <option value="exam_started">شروع آزمون</option>
                <option value="exam_finished">پایان آزمون</option>
                <option value="result_viewed">مشاهده کارنامه (Attempt)</option>
                <option value="result_viewed_session">مشاهده کارنامه (Session)</option>
            </select>
        </div>
    </div>

    @if($logs->count())
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600">
                        <th class="px-3 py-2 text-right">#</th>
                        <th class="px-3 py-2 text-right">زمان</th>
                        <th class="px-3 py-2 text-right">کاربر</th>
                        <th class="px-3 py-2 text-right">رویداد</th>
                        <th class="px-3 py-2 text-right">آزمون</th>
                        <th class="px-3 py-2 text-right">Attempt</th>
                        <th class="px-3 py-2 text-right">IP</th>
                        <th class="px-3 py-2 text-right">جزئیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $row)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $row->id }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">{{ @formatDate($row->created_at, 'Y/m/d H:i') }}</td>
                            <td class="px-3 py-2">{{ $row->user?->name ?? 'مهمان' }}</td>
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
        <div class="rounded border p-4 text-sm text-gray-600">لاگی یافت نشد.</div>
    @endif
</div>
