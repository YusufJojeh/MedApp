@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Specialties</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add, edit, or remove medical specialties</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="openCreateModal()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Specialty
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
            <form method="GET" action="{{ route('admin.settings.specialties') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search specialties..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.settings.specialties') }}"
                       class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Specialties List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Medical Specialties ({{ $specialties->total() }})
                </h2>
            </div>

            @if($specialties->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Specialty
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Icon
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Doctors
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($specialties as $specialty)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $specialty->name_en }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $specialty->name_ar }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                            {{ $specialty->description ?: 'No description' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($specialty->icon)
                                            <i class="{{ $specialty->icon }} text-2xl text-blue-500"></i>
                                        @else
                                            <i class="fas fa-stethoscope text-2xl text-gray-400"></i>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $specialty->doctor_count ?? 0 }} doctors
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="openEditModal({{ $specialty->id }}, '{{ $specialty->name_en }}', '{{ $specialty->name_ar }}', '{{ $specialty->description }}', '{{ $specialty->icon }}')"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteSpecialty({{ $specialty->id }})"
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
                    {{ $specialties->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-stethoscope text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-gray-900 dark:text-white text-lg font-semibold mb-2">No Specialties Found</h3>
                    <p class="text-gray-600 dark:text-gray-400">No medical specialties match your current filters.</p>
                    <button onclick="openCreateModal()"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Specialty
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="specialtyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Add New Specialty</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="specialtyForm">
                <input type="hidden" id="specialtyId" name="id">

                <div class="mb-4">
                    <label for="name_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        English Name *
                    </label>
                    <input type="text"
                           id="name_en"
                           name="name_en"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-4">
                    <label for="name_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Arabic Name *
                    </label>
                    <input type="text"
                           id="name_ar"
                           name="name_ar"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="mb-6">
                    <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Icon Class (FontAwesome)
                    </label>
                    <input type="text"
                           id="icon"
                           name="icon"
                           placeholder="fas fa-stethoscope"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span id="submitText">Create Specialty</span>
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
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Delete Specialty</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this specialty? This action cannot be undone.
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
let deleteSpecialtyId = null;

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Add New Specialty';
    document.getElementById('submitText').textContent = 'Create Specialty';
    document.getElementById('specialtyForm').reset();
    document.getElementById('specialtyId').value = '';
    document.getElementById('specialtyModal').classList.remove('hidden');
}

function openEditModal(id, nameEn, nameAr, description, icon) {
    document.getElementById('modalTitle').textContent = 'Edit Specialty';
    document.getElementById('submitText').textContent = 'Update Specialty';
    document.getElementById('specialtyId').value = id;
    document.getElementById('name_en').value = nameEn;
    document.getElementById('name_ar').value = nameAr;
    document.getElementById('description').value = description;
    document.getElementById('icon').value = icon;
    document.getElementById('specialtyModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('specialtyModal').classList.add('hidden');
}

function deleteSpecialty(id) {
    deleteSpecialtyId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteSpecialtyId = null;
}

function confirmDelete() {
    if (!deleteSpecialtyId) return;

    fetch(`/admin/settings/specialties/${deleteSpecialtyId}`, {
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
        showNotification('Error deleting specialty', 'error');
    })
    .finally(() => {
        closeDeleteModal();
    });
}

document.getElementById('specialtyForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const isEdit = data.id;

    const url = isEdit ? `/admin/settings/specialties/${data.id}` : '/admin/settings/specialties';
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
        showNotification('Error saving specialty', 'error');
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
