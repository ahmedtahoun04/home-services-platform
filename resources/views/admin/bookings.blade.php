@extends('layouts.admin')

@section('title', 'Manage Bookings - homely')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manage Bookings</h1>
            <p class="text-sm text-slate-500">Review booking requests and move them from Pending to Approved to Completed.</p>
        </div>
        <button id="refreshBtn" type="button"
                class="border border-slate-300 bg-white text-slate-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-slate-50 transition">
            Refresh
        </button>
    </div>

    <!-- Status filter -->
    <div id="tabs" class="flex flex-wrap gap-2 mb-4">
        <button type="button" data-status="" class="tab px-4 py-1.5 text-xs rounded-full border">All</button>
        <button type="button" data-status="Pending" class="tab px-4 py-1.5 text-xs rounded-full border">Pending</button>
        <button type="button" data-status="Approved" class="tab px-4 py-1.5 text-xs rounded-full border">Approved</button>
        <button type="button" data-status="Completed" class="tab px-4 py-1.5 text-xs rounded-full border">Completed</button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Service</th>
                    <th class="px-4 py-3">Date and time</th>
                    <th class="px-4 py-3">Address</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="rows"></tbody>
        </table>
        <div id="empty" class="hidden p-8 text-center text-sm text-slate-500">No bookings found.</div>
        <div id="loading" class="p-8 text-center text-sm text-slate-500">Loading...</div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-4 text-sm text-slate-600">
        <span id="pageInfo"></span>
        <div class="space-x-2">
            <button id="prevBtn" type="button" class="px-3 py-1.5 border border-slate-300 rounded-lg bg-white disabled:opacity-40">Previous</button>
            <button id="nextBtn" type="button" class="px-3 py-1.5 border border-slate-300 rounded-lg bg-white disabled:opacity-40">Next</button>
        </div>
    </div>

    <!-- Details dialog -->
    <div id="modal" class="hidden fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-full overflow-y-auto">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 id="modalTitle" class="text-lg font-bold text-slate-900">Booking details</h2>
                <button type="button" data-action="close" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <div id="modalBody" class="px-6 py-4 space-y-3 text-sm"></div>
            <div id="modalFooter" class="px-6 py-4 border-t border-slate-200 flex justify-end space-x-3"></div>
        </div>
    </div>
@endsection

@push('scripts')
@verbatim
<script>
(function () {
    const byId = function (id) { return document.getElementById(id); };
    const state = { page: 1, status: '' };
    let cache = [];

    // The only allowed moves (the server enforces the same rule).
    const NEXT = { Pending: 'Approved', Approved: 'Completed' };
    const ACTION_LABEL = { Pending: 'Approve', Approved: 'Mark completed' };

    const BADGE = {
        Pending: 'bg-amber-100 text-amber-700',
        Approved: 'bg-sky-100 text-sky-700',
        Completed: 'bg-emerald-100 text-emerald-700'
    };

    function badge(status) {
        const style = BADGE[status] || 'bg-slate-200 text-slate-600';
        return '<span class="px-2 py-0.5 rounded-full text-xs font-semibold ' + style + '">' + adminEsc(status) + '</span>';
    }

    function advanceButton(b, extraClass) {
        const next = NEXT[b.status];
        if (!next) { return ''; }
        return '<button type="button" data-action="advance" data-id="' + b.id + '" class="'
            + 'px-3 py-1.5 text-xs font-semibold rounded-md bg-tealPrimary text-white hover:bg-tealHover ' + (extraClass || '') + '">'
            + ACTION_LABEL[b.status] + '</button>';
    }

    // ---------- Filter tabs ----------

    function paintTabs() {
        document.querySelectorAll('#tabs .tab').forEach(function (tab) {
            const active = tab.dataset.status === state.status;
            tab.className = 'tab px-4 py-1.5 text-xs rounded-full border '
                + (active
                    ? 'bg-tealPrimary text-white border-tealPrimary font-semibold'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100');
        });
    }

    // ---------- Loading and drawing the table ----------

    async function load() {
        byId('loading').classList.remove('hidden');

        const params = new URLSearchParams();
        if (state.status) { params.set('status', state.status); }
        params.set('page', state.page);

        const res = await adminApi('/admin/bookings?' + params.toString());
        byId('loading').classList.add('hidden');

        if (!res.ok) {
            adminToast(adminErrorText(res), 'error');
            return;
        }

        const list = res.data.data || [];
        const meta = res.data.meta || {};

        if (list.length === 0 && state.page > 1) {
            state.page -= 1;
            return load();
        }

        draw(list, meta);
    }

    function rowHtml(b) {
        const customer = b.user
            ? '<div class="font-medium text-slate-900">' + adminEsc(b.user.name) + '</div>'
                + '<div class="text-xs text-slate-500">' + adminEsc(b.user.email) + '</div>'
            : '<span class="text-slate-400">Unknown</span>';

        const service = b.service
            ? adminEsc(b.service.name)
            : '<span class="text-slate-400">Removed service</span>';

        return '<tr class="border-t border-slate-100">'
            + '<td class="px-4 py-3 text-slate-500">' + b.id + '</td>'
            + '<td class="px-4 py-3">' + customer + '</td>'
            + '<td class="px-4 py-3 text-slate-700">' + service + '</td>'
            + '<td class="px-4 py-3">' + adminEsc(b.booking_date)
                + '<div class="text-xs text-slate-500">' + adminEsc(b.booking_time) + '</div></td>'
            + '<td class="px-4 py-3 text-slate-600 max-w-xs truncate">' + adminEsc(b.address || '-') + '</td>'
            + '<td class="px-4 py-3">' + badge(b.status) + '</td>'
            + '<td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">'
                + '<button type="button" data-action="details" data-id="' + b.id + '" '
                + 'class="px-3 py-1.5 text-xs font-semibold rounded-md border border-slate-300 text-slate-700 hover:bg-slate-50">Details</button>'
                + advanceButton(b)
            + '</td>'
            + '</tr>';
    }

    function draw(list, meta) {
        cache = list;
        byId('rows').innerHTML = list.map(rowHtml).join('');
        byId('empty').classList.toggle('hidden', list.length > 0);

        const current = meta.current_page || 1;
        const last = meta.last_page || 1;
        byId('pageInfo').textContent = 'Page ' + current + ' of ' + last + ' (' + (meta.total || list.length) + ' bookings)';
        byId('prevBtn').disabled = current <= 1;
        byId('nextBtn').disabled = current >= last;
    }

    // ---------- Details dialog ----------

    function line(label, valueHtml) {
        return '<div class="flex justify-between gap-4">'
            + '<span class="text-slate-500 shrink-0">' + label + '</span>'
            + '<span class="text-slate-900 text-right break-words min-w-0">' + valueHtml + '</span>'
            + '</div>';
    }

    function openDetails(b) {
        const service = b.service
            ? adminEsc(b.service.name) + ' <span class="text-slate-500">(' + adminEsc(b.service.category) + ', $' + Number(b.service.price).toFixed(2) + ')</span>'
            : '<span class="text-slate-400">Removed service</span>';

        const customer = b.user
            ? adminEsc(b.user.name) + ' <span class="text-slate-500">(' + adminEsc(b.user.email) + ')</span>'
            : '<span class="text-slate-400">Unknown</span>';

        const created = b.created_at ? adminEsc(new Date(b.created_at).toLocaleString()) : '-';

        byId('modalTitle').textContent = 'Booking #' + b.id;
        byId('modalBody').innerHTML = ''
            + line('Status', badge(b.status))
            + line('Service', service)
            + line('Customer', customer)
            + line('Date', adminEsc(b.booking_date))
            + line('Time', adminEsc(b.booking_time))
            + line('Address', adminEsc(b.address || '-'))
            + line('Notes', adminEsc(b.notes || '-'))
            + line('Requested on', created);

        byId('modalFooter').innerHTML =
            '<button type="button" data-action="close" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">Close</button>'
            + advanceButton(b, 'py-2 px-4 text-sm');

        byId('modal').classList.remove('hidden');
    }

    function closeModal() {
        byId('modal').classList.add('hidden');
    }

    // ---------- Actions (details, change status, close) ----------

    document.addEventListener('click', async function (e) {
        const button = e.target.closest('[data-action]');
        if (!button) { return; }

        const action = button.dataset.action;

        if (action === 'close') {
            closeModal();
            return;
        }

        const id = Number(button.dataset.id);
        const booking = cache.find(function (b) { return b.id === id; });
        if (!booking) { return; }

        if (action === 'details') {
            openDetails(booking);
        }

        if (action === 'advance') {
            const next = NEXT[booking.status];
            if (!next) { return; }

            const question = 'Change booking #' + id + ' from ' + booking.status + ' to ' + next + '?';
            if (!window.confirm(question)) { return; }

            button.disabled = true;
            const res = await adminApi('/admin/bookings/' + id + '/status', {
                method: 'PATCH',
                body: { status: next }
            });

            if (res.ok) {
                adminToast((res.data && res.data.message) || 'Status updated.');
            } else {
                // 422: not an allowed change, 403/401: permission, 404: removed meanwhile.
                adminToast(adminErrorText(res), 'error');
            }

            closeModal();
            load(); // always reload so the table matches the server
        }
    });

    byId('tabs').addEventListener('click', function (e) {
        const tab = e.target.closest('.tab');
        if (!tab) { return; }
        state.status = tab.dataset.status;
        state.page = 1;
        paintTabs();
        load();
    });

    byId('prevBtn').addEventListener('click', function () { state.page -= 1; load(); });
    byId('nextBtn').addEventListener('click', function () { state.page += 1; load(); });
    byId('refreshBtn').addEventListener('click', load);
    byId('modal').addEventListener('click', function (e) { if (e.target === byId('modal')) { closeModal(); } });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeModal(); } });

    paintTabs();
    load();
})();
</script>
@endverbatim
@endpush
