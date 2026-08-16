@extends('layouts.app')

@section('title', 'Security Hub - Ken\'s Marketing')
@section('header_title', 'System Security & Audit Hub')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Toast Notifications -->
    @if(session('success'))
        <div class="p-4 bg-white border border-green-100 rounded-xl shadow-sm flex items-center gap-3 mb-6">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-600 bg-green-50 rounded-lg">
                <i class="fa-solid fa-check"></i>
            </div>
            <span class="text-sm font-medium text-gray-700">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- LEFT COLUMN: USER MANAGEMENT (Pending & Active) -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- 1. PENDING APPROVALS BOX -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="bg-navy-900 text-white p-4 border-b border-navy-700 flex justify-between items-center">
                    <h2 class="text-md font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-user-shield"></i> Pending Approvals
                    </h2>
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">
                        {{ $pendingUsers->count() }}
                    </span>
                </div>
                
                <div class="p-0 bg-white overflow-y-auto locked-table-scroll" style="max-height: 350px;">
                    @forelse($pendingUsers as $user)
                        <div class="p-5 border-b border-gray-100 hover:bg-gray-50 transition-colors last:border-0">
                            <div class="flex flex-col gap-1 mb-4">
                                <h3 class="text-sm font-bold text-gray-800">{{ $user->name }}</h3>
                                <p class="text-xs text-gray-500 font-medium"><i class="fa-regular fa-envelope mr-1"></i> {{ $user->email }}</p>
                                <p class="text-xs text-gray-400 mt-1">Requested: {{ $user->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.approve', $user->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1 shadow-sm">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.decline', $user->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to decline and remove this request?');">
                                    @csrf
                                    <button type="submit" class="w-full bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1 shadow-sm">
                                        <i class="fa-solid fa-xmark"></i> Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center flex flex-col items-center justify-center text-gray-500">
                            <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                                <i class="fa-solid fa-user-check text-gray-400 text-lg"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">No pending requests</p>
                            <p class="text-xs text-gray-400 mt-1">All queues clear.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. ACTIVE STAFF BOX -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="bg-white p-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-users-viewfinder text-navy-700"></i> Active Staff
                    </h2>
                    <span class="bg-blue-50 text-blue-700 border border-blue-100 text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">
                        {{ $approvedStaff->count() }} Total
                    </span>
                </div>
                
                <div class="p-0 bg-white overflow-y-auto locked-table-scroll" style="max-height: 400px;">
                    @forelse($approvedStaff as $staff)
                        <div class="p-5 border-b border-gray-100 hover:bg-gray-50 transition-colors last:border-0">
                            <div class="flex flex-col gap-1 mb-4">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-sm font-bold text-gray-800">{{ $staff->name }}</h3>
                                    
                                    <!-- Online / Offline Status -->
                                    @if($staff->last_seen && \Carbon\Carbon::parse($staff->last_seen)->diffInMinutes(now()) < 15)
                                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-green-600 uppercase tracking-wide bg-green-50 px-2 py-0.5 rounded border border-green-100">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Online
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide bg-gray-50 px-2 py-0.5 rounded border border-gray-100">
                                            Offline
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 font-medium"><i class="fa-regular fa-envelope mr-1"></i> {{ $staff->email }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    <i class="fa-regular fa-clock mr-1"></i> Last active: {{ $staff->last_seen ? \Carbon\Carbon::parse($staff->last_seen)->diffForHumans() : 'Never logged in' }}
                                </p>
                            </div>
                            
                            <!-- Revoke Button -->
                            <div>
                                <form action="{{ route('admin.revoke', $staff->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke system access for {{ $staff->name }}? They will be logged out immediately and moved back to the pending queue.');">
                                    @csrf
                                    <button type="submit" class="w-full bg-white border border-gray-200 text-orange-600 hover:bg-orange-50 hover:border-orange-200 text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1 shadow-sm">
                                        <i class="fa-solid fa-ban"></i> Revoke Access
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center flex flex-col items-center justify-center text-gray-500">
                            <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                                <i class="fa-solid fa-users-slash text-gray-400 text-lg"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">No active staff</p>
                            <p class="text-xs text-gray-400 mt-1">Approved users will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SYSTEM LOGS / AUDIT TRAIL -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-navy-700"></i> Live System Audit Trail
                    </h2>
                    <span class="text-xs font-medium text-gray-500 bg-white border border-gray-200 px-2 py-1 rounded-md shadow-sm">Last 50 Actions</span>
                </div>
                
                <div class="overflow-x-auto overflow-y-auto flex-1 locked-table-scroll" style="max-height: 800px;">
                    <table class="w-full text-left text-sm text-gray-600 relative">
                        <thead class="text-xs text-gray-500 uppercase bg-white sticky top-0 z-10 shadow-sm border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Timestamp</th>
                                <th scope="col" class="px-6 py-4 font-semibold">User</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Action Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($systemLogs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $log->created_at->format('M d, Y') }} <br>
                                        <span class="text-gray-400">{{ $log->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-navy-700 text-xs">
                                        @if($log->causer)
                                            <i class="fa-solid fa-user-tie mr-1 text-gray-400"></i> {{ $log->causer->name }}
                                        @else
                                            <i class="fa-solid fa-desktop mr-1 text-gray-400"></i> System / Guest
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800 leading-relaxed">
                                        @if(str_contains(strtolower($log->description), 'alert') || str_contains(strtolower($log->description), 'blocked') || str_contains(strtolower($log->description), 'failed') || str_contains(strtolower($log->description), 'revoked'))
                                            <span class="text-red-600 font-medium"><i class="fa-solid fa-shield-halved mr-1 text-xs"></i> {{ $log->description }}</span>
                                        @elseif(str_contains(strtolower($log->description), 'request') || str_contains(strtolower($log->description), 'pending'))
                                            <span class="text-orange-600 font-medium"><i class="fa-solid fa-bell mr-1 text-xs"></i> {{ $log->description }}</span>
                                        @else
                                            {{ $log->description }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-400 text-sm font-medium border-t border-gray-50">
                                        No system logs recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Custom Scrollbar to match your existing UI */
    .locked-table-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .locked-table-scroll::-webkit-scrollbar-track { background: transparent; }
    .locked-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .locked-table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection