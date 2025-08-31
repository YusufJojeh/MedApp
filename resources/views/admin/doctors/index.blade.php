@extends('layouts.app')

@section('title', 'Doctor Management - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Doctor Management</h1>
                    <p class="text-muted mt-2">Manage healthcare professionals</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Add Doctor
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card feature-card" data-aos="fade-up">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_doctors']) }}</p>
                            <p class="text-sm text-muted">Total Doctors</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['active_doctors']) }}</p>
                            <p class="text-sm text-muted">Active Doctors</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['pending_doctors']) }}</p>
                            <p class="text-sm text-muted">Pending Approval</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['avg_rating'], 1) }}</p>
                            <p class="text-sm text-muted">Average Rating</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <button class="btn btn-outline" onclick="exportDoctors()">
                <i class="fas fa-download mr-2"></i>
                Export Doctors
            </button>
            <button class="btn btn-outline" onclick="bulkActions()">
                <i class="fas fa-tasks mr-2"></i>
                Bulk Actions
            </button>
            <button class="btn btn-outline" onclick="importDoctors()">
                <i class="fas fa-upload mr-2"></i>
                Import Doctors
            </button>
        </div>

        <!-- Filters -->
        <div class="card feature-card mb-8" data-aos="fade-up">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Search doctors..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Specialty</label>
                        <select id="specialtyFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Specialties</option>
                            @foreach($specialties as $specialty)
                                <option value="{{ $specialty->id }}">{{ $specialty->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Status</label>
                        <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-2">Rating</label>
                        <select id="ratingFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent bg-white dark:bg-gray-800 text-text">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4+ Stars</option>
                            <option value="3">3+ Stars</option>
                            <option value="2">2+ Stars</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="btn btn-primary w-full" onclick="filterDoctors()">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctors Table -->
        <div class="card feature-card" data-aos="fade-up">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-text">All Doctors</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted">Showing {{ $doctors->count() }} doctors</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-text">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-gold focus:ring-gold">
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-text">Doctor</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Specialty</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Rating</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Fee</th>
                                <th class="text-left py-3 px-4 font-medium text-text">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($doctors as $doctor)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-4 px-4">
                                        <input type="checkbox" class="doctor-checkbox rounded border-gray-300 text-gold focus:ring-gold" value="{{ $doctor->id }}">
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-gold to-gold-deep rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user-md text-white"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-text">Dr. {{ $doctor->name }}</p>
                                                <p class="text-sm text-muted">{{ $doctor->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            <p class="font-medium text-text">{{ $doctor->specialty_name }}</p>
                                            <p class="text-sm text-muted">{{ $doctor->experience_years }} years exp.</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 text-xs rounded-full {{ $doctor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($doctor->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="flex items-center space-x-1 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-sm {{ $i <= $doctor->rating ? 'text-gold' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-text">{{ number_format($doctor->rating, 1) }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            <p class="text-sm text-text">${{ number_format($doctor->consultation_fee, 2) }}</p>
                                            <p class="text-xs text-muted">Consultation</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.doctors.show', $doctor->id) }}" class="btn btn-sm btn-outline">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if($doctor->status == 'active')
                                                <button class="btn btn-sm btn-warning" onclick="deactivateDoctor({{ $doctor->id }})">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-success" onclick="activateDoctor({{ $doctor->id }})">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif

                                            <button class="btn btn-sm btn-danger" onclick="deleteDoctor({{ $doctor->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-muted">
                                        <i class="fas fa-user-md text-4xl mb-4"></i>
                                        <p>No doctors found</p>
                                        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary mt-4">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add Your First Doctor
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($doctors->hasPages())
                    <div class="mt-6">
                        {{ $doctors->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>




@endsection

@push('scripts')
<script>


    // Filter doctors
    function filterDoctors() {
        const search = document.getElementById('searchInput').value;
        const specialty = document.getElementById('specialtyFilter').value;
        const status = document.getElementById('statusFilter').value;
        const rating = document.getElementById('ratingFilter').value;

        let url = '{{ route("admin.doctors.index") }}?';
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (specialty) url += `specialty=${specialty}&`;
        if (status) url += `status=${status}&`;
        if (rating) url += `rating=${rating}&`;

        window.location.href = url;
    }







    // Verify doctor
    function verifyDoctor(doctorId) {
        if (!confirm('Are you sure you want to verify this doctor?')) {
            return;
        }

        fetch(`/admin/doctors/${doctorId}/verify`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Doctor verified successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification('Error verifying doctor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error verifying doctor', 'error');
        });
    }

    // Unverify doctor
    function unverifyDoctor(doctorId) {
        if (!confirm('Are you sure you want to unverify this doctor?')) {
            return;
        }

        fetch(`/admin/doctors/${doctorId}/unverify`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Doctor unverified successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification('Error unverifying doctor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error unverifying doctor', 'error');
        });
    }

    // Activate doctor
    function activateDoctor(doctorId) {
        if (!confirm('Are you sure you want to activate this doctor?')) {
            return;
        }

        fetch(`/admin/doctors/${doctorId}/activate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Doctor activated successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification('Error activating doctor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error activating doctor', 'error');
        });
    }

    // Deactivate doctor
    function deactivateDoctor(doctorId) {
        if (!confirm('Are you sure you want to deactivate this doctor?')) {
            return;
        }

        fetch(`/admin/doctors/${doctorId}/deactivate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Doctor deactivated successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification('Error deactivating doctor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deactivating doctor', 'error');
        });
    }

    // Delete doctor
    function deleteDoctor(doctorId) {
        if (!confirm('Are you sure you want to delete this doctor? This action cannot be undone.')) {
            return;
        }

        fetch(`/admin/doctors/${doctorId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Doctor deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification('Error deleting doctor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting doctor', 'error');
        });
    }

    // Close modals
    function closeModal() {
        document.getElementById('doctorModal').classList.add('hidden');
    }

    function closeDoctorFormModal() {
        document.getElementById('doctorFormModal').classList.add('hidden');
    }

    // Export doctors
    function exportDoctors() {
        const search = document.getElementById('searchInput').value;
        const specialty = document.getElementById('specialtyFilter').value;
        const status = document.getElementById('statusFilter').value;
        const rating = document.getElementById('ratingFilter').value;

        let url = '{{ route("admin.doctors.export") }}?';
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (specialty) url += `specialty=${specialty}&`;
        if (status) url += `status=${status}&`;
        if (rating) url += `rating=${rating}&`;

        window.location.href = url;
    }

    // Bulk actions
    function bulkActions() {
        const selectedDoctors = document.querySelectorAll('.doctor-checkbox:checked');
        if (selectedDoctors.length === 0) {
            showNotification('Please select doctors to perform bulk actions', 'warning');
            return;
        }

        const action = prompt('Enter action (verify/unverify/delete):').toLowerCase();
        const doctorIds = Array.from(selectedDoctors).map(cb => cb.value);

        if (['verify', 'unverify', 'delete'].includes(action)) {
            if (confirm(`Are you sure you want to ${action} ${doctorIds.length} doctors?`)) {
                fetch('{{ route("admin.doctors.bulk-action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        action: action,
                        doctor_ids: doctorIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(`Bulk ${action} completed successfully`, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showNotification('Error performing bulk action', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error performing bulk action', 'error');
                });
            }
        } else {
            showNotification('Invalid action specified', 'error');
        }
    }

    // Import doctors
    function importDoctors() {
        showNotification('Import functionality coming soon', 'info');
    }

    // Select all doctors
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.doctor-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple notification - you can enhance this with a proper notification system
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(`${type.toUpperCase()}: ${message}`);
    }

    // Initialize filters on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('search')) {
            document.getElementById('searchInput').value = urlParams.get('search');
        }
        if (urlParams.get('specialty')) {
            document.getElementById('specialtyFilter').value = urlParams.get('specialty');
        }
        if (urlParams.get('status')) {
            document.getElementById('statusFilter').value = urlParams.get('status');
        }
        if (urlParams.get('rating')) {
            document.getElementById('ratingFilter').value = urlParams.get('rating');
        }
    });
</script>
@endpush
