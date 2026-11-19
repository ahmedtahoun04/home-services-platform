@extends('layouts.admin')

@section('title', 'Manage Services - homely')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manage Services</h1>
            <p class="text-sm text-slate-500">Add, edit, price and hide the services customers can book.</p>
        </div>
        <button id="addBtn" type="button"
                class="bg-tealPrimary hover:bg-tealHover text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            Add service
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 mb-4 flex flex-col sm:flex-row gap-3">
        <input id="search" type="text" placeholder="Search by name or description"
               class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
        <input id="category" type="text" placeholder="Category (exact name)"
               class="sm:w-60 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Rating</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="rows"></tbody>
        </table>
        <div id="empty" class="hidden p-8 text-center text-sm text-slate-500">No services found.</div>
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

    <!-- Add / Edit dialog -->
    <div id="modal" class="hidden fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-full overflow-y-auto">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 id="modalTitle" class="text-lg font-bold text-slate-900">Add service</h2>
            </div>

            <form id="serviceForm" class="px-6 py-4 space-y-4" novalidate>
                <div>
                    <label for="f_name" class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                    <input id="f_name" type="text" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                    <p class="text-xs text-red-600 mt-1" data-error="name"></p>
                </div>

                <div>
                    <label for="f_category" class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <input id="f_category" type="text" placeholder="Plumbing, Electrical, Cleaning..."
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                    <p class="text-xs text-red-600 mt-1" data-error="category"></p>
                </div>

                <div>
                    <label for="f_description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea id="f_description" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary"></textarea>
                    <p class="text-xs text-red-600 mt-1" data-error="description"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="f_price" class="block text-sm font-medium text-slate-700 mb-1">Price ($)</label>
                        <input id="f_price" type="number" min="0" step="0.01" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                        <p class="text-xs text-red-600 mt-1" data-error="price"></p>
                    </div>
                    <div>
                        <label for="f_rating" class="block text-sm font-medium text-slate-700 mb-1">Rating (0 to 5)</label>
                        <input id="f_rating" type="number" min="0" max="5" step="0.1" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-tealPrimary">
                        <p class="text-xs text-red-600 mt-1" data-error="rating"></p>
                    </div>
                </div>

                <label class="flex items-center space-x-2 text-sm text-slate-700">
                    <input id="f_active" type="checkbox" class="rounded border-slate-300" checked>
                    <span>Active (visible to customers)</span>
                </label>

                <div class="flex justify-end space-x-3 pt-2">
                    <button id="cancelBtn" type="button" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</button>
                    <button id="saveBtn" type="submit" class="bg-tealPrimary hover:bg-tealHover text-white text-sm font-semibold px-5 py-2 rounded-lg transition">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@verbatim
<script>
(function () {
    const byId = function (id) { return document.getElementById(id); };
    const state = { page: 1, search: '', category: '' };
    let cache = [];
    let editingId = null;
    let searchTimer = null;

    const fields = ['name', 'category', 'description', 'price', 'rating'];

    // ---------- Loading and drawing the table ----------

    async function load() {
        byId('loading').classList.remove('hidden');

        const params = new URLSearchParams();
        if (state.search) { params.set('search', state.search); }
        if (state.category) { params.set('category', state.category); }
        params.set('page', state.page);

        const res = await adminApi('/admin/services?' + params.toString());
        byId('loading').classList.add('hidden');

        if (!res.ok) {
            adminToast((res.data && res.data.message) || 'Could not load the services.', 'error');
            return;
        }

        const list = res.data.data || [];
        const meta = res.data.meta || {};

        // After deleting the last item of a page, go back one page.
        if (list.length === 0 && state.page > 1) {
            state.page -= 1;
            return load();
        }

        draw(list, meta);
    }

    function badge(active) {
        return active
            ? '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Active</span>'
            : '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-600">Inactive</span>';
    }

    function rowHtml(s) {
        const btn = 'px-2 py-1 text-xs font-semibold rounded-md border ';
        return '<tr class="border-t border-slate-100">'
            + '<td class="px-4 py-3 font-medium text-slate-900">' + adminEsc(s.name) + '</td>'
            + '<td class="px-4 py-3 text-slate-600">' + adminEsc(s.category) + '</td>'
            + '<td class="px-4 py-3">$' + Number(s.price).toFixed(2) + '</td>'
            + '<td class="px-4 py-3">' + Number(s.rating).toFixed(1) + '</td>'
            + '<td class="px-4 py-3">' + badge(s.is_active) + '</td>'
            + '<td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">'
            + '<button type="button" data-action="edit" data-id="' + s.id + '" class="' + btn + 'border-slate-300 text-slate-700 hover:bg-slate-50">Edit</button>'
            + '<button type="button" data-action="toggle" data-id="' + s.id + '" class="' + btn + 'border-slate-300 text-slate-700 hover:bg-slate-50">' + (s.is_active ? 'Deactivate' : 'Activate') + '</button>'
            + '<button type="button" data-action="delete" data-id="' + s.id + '" class="' + btn + 'border-red-200 text-red-600 hover:bg-red-50">Delete</button>'
            + '</td>'
            + '</tr>';
    }

    function draw(list, meta) {
        cache = list;
        byId('rows').innerHTML = list.map(rowHtml).join('');
        byId('empty').classList.toggle('hidden', list.length > 0);

        const current = meta.current_page || 1;
        const last = meta.last_page || 1;
        byId('pageInfo').textContent = 'Page ' + current + ' of ' + last + ' (' + (meta.total || list.length) + ' services)';
        byId('prevBtn').disabled = current <= 1;
        byId('nextBtn').disabled = current >= last;
    }

    // ---------- Add / Edit dialog ----------

    function clearErrors() {
        document.querySelectorAll('[data-error]').forEach(function (p) { p.textContent = ''; });
    }

    function showErrors(errors) {
        Object.keys(errors).forEach(function (field) {
            const p = document.querySelector('[data-error="' + field + '"]');
            if (p) { p.textContent = errors[field][0]; }
        });
    }

    function openModal(service) {
        clearErrors();
        editingId = service ? service.id : null;
        byId('modalTitle').textContent = service ? 'Edit service' : 'Add service';
        byId('f_name').value = service ? service.name : '';
        byId('f_category').value = service ? service.category : '';
        byId('f_description').value = service && service.description ? service.description : '';
        byId('f_price').value = service ? service.price : '';
        byId('f_rating').value = service ? service.rating : '';
        byId('f_active').checked = service ? !!service.is_active : true;
        byId('modal').classList.remove('hidden');
        byId('f_name').focus();
    }

    function closeModal() {
        byId('modal').classList.add('hidden');
    }

    byId('serviceForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        const rating = byId('f_rating').value.trim();
        const price = byId('f_price').value.trim();

        // The rating cannot be null on the server, so an empty rating is sent as 0.
        const payload = {
            name: byId('f_name').value.trim(),
            category: byId('f_category').value.trim(),
            description: byId('f_description').value.trim(),
            price: price === '' ? '' : Number(price),
            rating: rating === '' ? 0 : Number(rating),
            is_active: byId('f_active').checked
        };

        const saveBtn = byId('saveBtn');
        saveBtn.disabled = true;

        const res = editingId
            ? await adminApi('/admin/services/' + editingId, { method: 'PUT', body: payload })
            : await adminApi('/admin/services', { method: 'POST', body: payload });

        saveBtn.disabled = false;

        if (res.ok) {
            closeModal();
            adminToast((res.data && res.data.message) || 'Saved.');
            load();
        } else if (res.status === 422 && res.data && res.data.errors) {
            showErrors(res.data.errors);
        } else {
            adminToast((res.data && res.data.message) || 'Something went wrong.', 'error');
        }
    });

    // ---------- Row actions (edit, activate/deactivate, delete) ----------

    byId('rows').addEventListener('click', async function (e) {
        const button = e.target.closest('button[data-action]');
        if (!button) { return; }

        const id = Number(button.dataset.id);
        const service = cache.find(function (s) { return s.id === id; });
        if (!service) { return; }

        if (button.dataset.action === 'edit') {
            openModal(service);
        }

        if (button.dataset.action === 'toggle') {
            const res = await adminApi('/admin/services/' + id, { method: 'PATCH', body: { is_active: !service.is_active } });
            if (res.ok) {
                adminToast((res.data && res.data.message) || 'Updated.');
                load();
            } else {
                adminToast((res.data && res.data.message) || 'Could not update the service.', 'error');
            }
        }

        if (button.dataset.action === 'delete') {
            if (!window.confirm('Delete "' + service.name + '"? This cannot be undone.')) { return; }

            const res = await adminApi('/admin/services/' + id, { method: 'DELETE' });
            if (res.ok) {
                adminToast('Service deleted.');
                load();
            } else {
                // 409: the service has bookings, the message tells the admin to deactivate it.
                adminToast((res.data && res.data.message) || 'Could not delete the service.', 'error');
            }
        }
    });

    // ---------- Filters, pagination, buttons ----------

    byId('search').addEventListener('input', function (e) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            state.search = e.target.value.trim();
            state.page = 1;
            load();
        }, 300);
    });

    byId('category').addEventListener('input', function (e) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            state.category = e.target.value.trim();
            state.page = 1;
            load();
        }, 300);
    });

    byId('prevBtn').addEventListener('click', function () { state.page -= 1; load(); });
    byId('nextBtn').addEventListener('click', function () { state.page += 1; load(); });
    byId('addBtn').addEventListener('click', function () { openModal(null); });
    byId('cancelBtn').addEventListener('click', closeModal);
    byId('modal').addEventListener('click', function (e) { if (e.target === byId('modal')) { closeModal(); } });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeModal(); } });

    load();
})();
</script>
@endverbatim
@endpush
