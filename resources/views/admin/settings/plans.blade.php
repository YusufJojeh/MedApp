@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Subscription Plans</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Configure plans for doctors and patients</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="openCreateModal()"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Plan
                    </button>
                    <a href="{{ route('admin.settings.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('admin.settings.plans') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search plans..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <select name="audience"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Audiences</option>
                    <option value="doctors" {{ request('audience') === 'doctors' ? 'selected' : '' }}>Doctors</option>
                    <option value="patients" {{ request('audience') === 'patients' ? 'selected' : '' }}>Patients</option>
                </select>
                <button type="submit"
                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>
                @if(request('search') || request('audience'))
                    <a href="{{ route('admin.settings.plans') }}"
                       class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Plans List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Subscription Plans ({{ $plans->total() }})
                </h2>
            </div>

            @if($plans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Plan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Audience
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Features
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($plans as $plan)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $plan->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $plan->slug }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $plan->audience === 'doctors' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                            {{ ucfirst($plan->audience) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            ${{ number_format($plan->price, 2) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $plan->billing_cycle }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $plan->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($plan->is_popular)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Popular
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $plan->feature_count ?? 0 }} features
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="openEditModal({{ $plan->id }})"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePlan({{ $plan->id }})"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $plans->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-crown text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-gray-900 dark:text-white text-lg font-semibold mb-2">No Plans Found</h3>
                    <p class="text-gray-600 dark:text-gray-400">No subscription plans match your current filters.</p>
                    <button onclick="openCreateModal()"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Plan
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="planModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Add New Plan</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="planForm">
                <input type="hidden" id="planId" name="id">

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Plan Name *
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-4">
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Slug *
                    </label>
                    <input type="text"
                           id="slug"
                           name="slug"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-4">
                    <label for="audience" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Audience *
                    </label>
                    <select id="audience"
                            name="audience"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Audience</option>
                        <option value="doctors">Doctors</option>
                        <option value="patients">Patients</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Price *
                    </label>
                    <input type="number"
                           id="price"
                           name="price"
                           step="0.01"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-4">
                    <label for="billing_cycle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Billing Cycle *
                    </label>
                    <select id="billing_cycle"
                            name="billing_cycle"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Billing Cycle</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="lifetime">Lifetime</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox"
                               id="is_active"
                               name="is_active"
                               value="1"
                               class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Plan</span>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox"
                               id="is_popular"
                               name="is_popular"
                               value="1"
                               class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Popular Plan</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        <span id="submitText">Create Plan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Delete Plan</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this plan? This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <button onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmDelete()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deletePlanId = null;

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Add New Plan';
    document.getElementById('submitText').textContent = 'Create Plan';
    document.getElementById('planForm').reset();
    document.getElementById('planId').value = '';
    document.getElementById('planModal').classList.remove('hidden');
}

function openEditModal(id) {
    // This would need to be implemented to fetch plan data
    document.getElementById('modalTitle').textContent = 'Edit Plan';
    document.getElementById('submitText').textContent = 'Update Plan';
    document.getElementById('planId').value = id;
    document.getElementById('planModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('planModal').classList.add('hidden');
}

function deletePlan(id) {
    deletePlanId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deletePlanId = null;
}

function confirmDelete() {
    if (!deletePlanId) return;

    fetch(`/admin/settings/plans/${deletePlanId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error deleting plan', 'error');
    })
    .finally(() => {
        closeDeleteModal();
    });
}

document.getElementById('planForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const isEdit = data.id;

    const url = isEdit ? `/admin/settings/plans/${data.id}` : '/admin/settings/plans';
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving plan', 'error');
    });
});

function showNotification(message, type) {
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else {
        alert(message);
    }
}
</script>
@endsection
