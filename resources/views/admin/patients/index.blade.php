@extends('layouts.app')

@section('title', 'Patient Management - Admin Dashboard')

@section('content')
<div class="min-h-screen bg-surface">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gold/10 to-gold-deep/10 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text">Patient Management</h1>
                    <p class="text-muted mt-2">Manage all patient accounts and information</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="btn btn-primary" onclick="createPatient()">
                        <i class="fas fa-plus mr-2"></i>
                        Add Patient
                    </button>
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
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['total_patients']) }}</p>
                            <p class="text-sm text-muted">Total Patients</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-check text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['active_patients']) }}</p>
                            <p class="text-sm text-muted">Active Patients</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['new_patients_this_month']) }}</p>
                            <p class="text-sm text-muted">New This Month</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-birthday-cake text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-text">{{ number_format($stats['avg_age'], 1) }}</p>
                            <p class="text-sm text-muted">Average Age</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients Table -->
        <div class="card feature-card">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-text">Patients</h3>
                    <div class="text-sm text-muted">
                        Showing {{ $patients->firstItem() ?? 0 }} to {{ $patients->lastItem() ?? 0 }} of {{ $patients->total() }} patients
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-muted">Patient</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Contact</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Age/Gender</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Blood Type</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Joined</th>
                                <th class="text-left py-3 px-4 font-medium text-muted">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patients as $patient)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-3 px-4">
                                    <div>
                                        <div class="font-medium text-text">{{ $patient->first_name }} {{ $patient->last_name }}</div>
                                        <div class="text-sm text-muted">@{{ $patient->username }}</div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div>
                                        <div class="text-text">{{ $patient->email }}</div>
                                        <div class="text-sm text-muted">{{ $patient->phone ?? 'No phone' }}</div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div>
                                        <div class="text-text">{{ $patient->age }} years</div>
                                        <div class="text-sm text-muted">{{ ucfirst($patient->gender ?? 'Not specified') }}</div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                        {{ $patient->blood_type ?? 'Not specified' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $patient->status_badge_class }}">
                                        {{ $patient->status_text }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-muted">
                                        {{ $patient->created_at ? $patient->created_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.patients.show', $patient->id) }}"
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.patients.edit', $patient->id) }}"
                                           class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                                           title="Edit Patient">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($patient->is_active)
                                            <button onclick="deactivatePatient({{ $patient->id }})"
                                                    class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300"
                                                    title="Deactivate">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        @else
                                            <button onclick="activatePatient({{ $patient->id }})"
                                                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                                                    title="Activate">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @endif
                                        <button onclick="deletePatient({{ $patient->id }})"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 px-4 text-center text-muted">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p>No patients found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($patients->hasPages())
                <div class="mt-6">
                    {{ $patients->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function createPatient() {
    window.location.href = '{{ route("admin.patients.create") }}';
}

function activatePatient(id) {
    if (confirm('Are you sure you want to activate this patient?')) {
        fetch(`/admin/patients/${id}/activate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Patient activated successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Error activating patient');
            }
        });
    }
}

function deactivatePatient(id) {
    if (confirm('Are you sure you want to deactivate this patient?')) {
        fetch(`/admin/patients/${id}/deactivate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Patient deactivated successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Error deactivating patient');
            }
        });
    }
}

function deletePatient(id) {
    if (confirm('Are you sure you want to delete this patient? This action cannot be undone.')) {
        fetch(`/admin/patients/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Patient deleted successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Error deleting patient');
            }
        });
    }
}
</script>
@endpush
