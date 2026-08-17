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
        
        <!-- LEFT COLUMN: TABBED USER MANAGEMENT -->
        <div class="lg:col-span-1 lg:sticky lg:top-6 lg:self-start">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-visible flex flex-col">
                <div class="bg-navy-900 text-white p-4 border-b border-navy-700 rounded-t-2xl">
                    <h2 class="text-md font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-user-shield"></i> Account Management
                    </h2>
                </div>

                <div class="grid grid-cols-3 gap-1 p-2 bg-gray-50 border-b border-gray-100" role="tablist" aria-label="Account lists">
                    <button type="button" class="account-tab rounded-lg px-2 py-2 text-xs font-semibold transition-colors bg-white text-navy-700 shadow-sm" data-tab="pending" role="tab" aria-selected="true" onclick="switchAccountTab('pending')">
                        Pending <span class="ml-1 bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full">{{ $pendingUsers->count() }}</span>
                    </button>
                    <button type="button" class="account-tab rounded-lg px-2 py-2 text-xs font-semibold text-gray-500 transition-colors hover:text-navy-700" data-tab="active" role="tab" aria-selected="false" onclick="switchAccountTab('active')">
                        Active <span class="ml-1 bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">{{ $approvedStaff->count() }}</span>
                    </button>
                    <button type="button" class="account-tab rounded-lg px-2 py-2 text-xs font-semibold text-gray-500 transition-colors hover:text-navy-700" data-tab="revoked" role="tab" aria-selected="false" onclick="switchAccountTab('revoked')">
                        Revoked <span class="ml-1 bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-full">{{ $revokedUsers->count() }}</span>
                    </button>
                </div>

                <div class="account-panel" data-panel="pending" role="tabpanel">
                    @forelse($pendingUsers as $user)
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors last:border-0 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-user-clock"></i></div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-gray-800 truncate">{{ $user->name }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">Requested {{ $user->created_at->diffForHumans() }}</p>
                            </div>
                            <details class="action-dropdown relative shrink-0">
                                <summary class="list-none cursor-pointer w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-white hover:text-navy-700 flex items-center justify-center"><i class="fa-solid fa-ellipsis-vertical"></i></summary>
                                <div class="absolute right-0 mt-1 w-36 bg-white border border-gray-100 rounded-lg shadow-xl z-20 overflow-hidden">
                                    <form action="{{ route('admin.approve', $user->id) }}" method="POST">@csrf<button type="submit" class="w-full px-3 py-2 text-left text-xs text-green-700 hover:bg-green-50"><i class="fa-solid fa-check w-5"></i> Approve</button></form>
                                    <form action="{{ route('admin.decline', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to decline and remove this request?');">@csrf<button type="submit" class="w-full px-3 py-2 text-left text-xs text-red-600 hover:bg-red-50"><i class="fa-solid fa-xmark w-5"></i> Decline</button></form>
                                </div>
                            </details>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400"><i class="fa-solid fa-user-check text-2xl mb-2"></i><p class="text-sm font-semibold text-gray-600">No pending requests</p></div>
                    @endforelse
                </div>

                <div class="account-panel hidden" data-panel="active" role="tabpanel">
                    @forelse($approvedStaff as $staff)
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors last:border-0 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center shrink-0"><i class="fa-solid fa-user-check"></i></div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2"><h3 class="text-sm font-bold text-gray-800 truncate">{{ $staff->name }}</h3>@if($staff->last_seen && \Carbon\Carbon::parse($staff->last_seen)->diffInMinutes(now()) < 15)<span class="w-2 h-2 bg-green-500 rounded-full animate-pulse" title="Online"></span>@endif</div>
                                <p class="text-xs text-gray-500 truncate">{{ $staff->email }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">Last active {{ $staff->last_seen ? \Carbon\Carbon::parse($staff->last_seen)->diffForHumans() : 'never' }}</p>
                            </div>
                            <details class="action-dropdown relative shrink-0">
                                <summary class="list-none cursor-pointer w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-white hover:text-navy-700 flex items-center justify-center"><i class="fa-solid fa-ellipsis-vertical"></i></summary>
                                <div class="absolute right-0 mt-1 w-40 bg-white border border-gray-100 rounded-lg shadow-xl z-20 overflow-hidden">
                                    <button type="button" data-revoke-action="{{ route('admin.revoke', $staff->id) }}" data-staff-name="{{ $staff->name }}" onclick="openRevokeModal(this)" class="w-full px-3 py-2 text-left text-xs text-orange-600 hover:bg-orange-50"><i class="fa-solid fa-ban w-5"></i> Revoke Access</button>
                                </div>
                            </details>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400"><i class="fa-solid fa-users-slash text-2xl mb-2"></i><p class="text-sm font-semibold text-gray-600">No active staff</p></div>
                    @endforelse
                </div>

                <div class="account-panel hidden" data-panel="revoked" role="tabpanel">
                    @forelse($revokedUsers as $user)
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors last:border-0 flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-user-lock"></i></div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-gray-800 truncate">{{ $user->name }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">Revoked {{ $user->revoked_at->diffForHumans() }}</p>
                            </div>
                            <details class="action-dropdown relative shrink-0">
                                <summary class="list-none cursor-pointer w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-white hover:text-navy-700 flex items-center justify-center"><i class="fa-solid fa-ellipsis-vertical"></i></summary>
                                <div class="absolute right-0 mt-1 w-40 bg-white border border-gray-100 rounded-lg shadow-xl z-20 overflow-hidden">
                                    <form action="{{ route('admin.restore', $user->id) }}" method="POST" onsubmit="return confirm('Restore system access for {{ $user->name }}?');">@csrf<button type="submit" class="w-full px-3 py-2 text-left text-xs text-green-700 hover:bg-green-50"><i class="fa-solid fa-rotate-left w-5"></i> Restore Access</button></form>
                                    <form action="{{ route('admin.revoked.delete', $user->id) }}" method="POST" onsubmit="return confirm('Permanently delete the revoked account for {{ $user->name }}? This cannot be undone.');">@csrf @method('DELETE')<button type="submit" class="w-full px-3 py-2 text-left text-xs text-red-600 hover:bg-red-50"><i class="fa-solid fa-trash w-5"></i> Delete Account</button></form>
                                </div>
                            </details>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400"><i class="fa-solid fa-user-check text-2xl mb-2"></i><p class="text-sm font-semibold text-gray-600">No revoked accounts</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SYSTEM LOGS / AUDIT TRAIL -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center gap-3 bg-gray-50">
                    <h2 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-navy-700"></i> Admin & Staff Activity Trail
                    </h2>
                    <span class="text-xs font-medium text-gray-500 bg-white border border-gray-200 px-2 py-1 rounded-md shadow-sm whitespace-nowrap">{{ $systemLogs->count() }} {{ $hasAuditFilters ? 'Matching' : 'Recent' }} Actions</span>
                </div>

                <form id="auditFilterForm" method="GET" action="{{ route('admin.security') }}" class="p-4 border-b border-gray-100 bg-white" aria-label="Filter security activity">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2.5">
                        <div class="sm:col-span-2 xl:col-span-1 relative">
                            <label for="audit-search" class="sr-only">Search activity</label>
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                            <input id="audit-search" type="search" name="search" value="{{ $auditFilters['search'] }}" placeholder="Action, actor, email, or module" class="w-full h-9 pl-8 pr-3 text-xs border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-700/20 focus:border-navy-700">
                        </div>

                        <div>
                            <label for="audit-role" class="sr-only">Actor role</label>
                            <select id="audit-role" name="role" class="w-full h-9 px-3 text-xs border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-700/20 focus:border-navy-700">
                                <option value="">All actors</option>
                                <option value="admin" @selected($auditFilters['role'] === 'admin')>Admins</option>
                                <option value="staff" @selected($auditFilters['role'] === 'staff')>Staff</option>
                                <option value="system" @selected($auditFilters['role'] === 'system')>System / Guest</option>
                            </select>
                        </div>

                        <div>
                            <label for="audit-module" class="sr-only">System module</label>
                            <select id="audit-module" name="module" class="w-full h-9 px-3 text-xs border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-700/20 focus:border-navy-700">
                                <option value="">All modules</option>
                                @foreach($auditModules as $moduleValue => $moduleLabel)
                                    <option value="{{ $moduleValue }}" @selected($auditFilters['module'] === $moduleValue)>{{ $moduleLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="audit-period" class="sr-only">Activity period</label>
                            <select id="audit-period" name="period" class="w-full h-9 px-3 text-xs border border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-700/20 focus:border-navy-700">
                                <option value="">All time</option>
                                <option value="today" @selected($auditFilters['period'] === 'today')>Today</option>
                                <option value="7_days" @selected($auditFilters['period'] === '7_days')>Last 7 days</option>
                                <option value="30_days" @selected($auditFilters['period'] === '30_days')>Last 30 days</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-2">
                        <p class="text-[11px] text-gray-400"><i class="fa-solid fa-bolt mr-1"></i>Filters update automatically.</p>
                        @if($hasAuditFilters)
                            <a href="{{ route('admin.security') }}" class="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                <i class="fa-solid fa-rotate-left"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>
                
                <div class="overflow-x-auto overflow-y-auto flex-1 locked-table-scroll" style="max-height: 800px;">
                    <table class="w-full text-left text-sm text-gray-600 relative">
                        <thead class="text-xs text-gray-500 uppercase bg-white sticky top-0 z-10 shadow-sm border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Timestamp</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Actor</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Action Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($systemLogs as $log)
                                @php
                                    $actorName = $log->causer?->name ?? data_get($log->properties, 'actor_name', 'System / Guest');
                                    $actorRole = strtolower(trim((string) ($log->causer?->role ?? data_get($log->properties, 'actor_role', 'system'))));
                                    $module = data_get($log->properties, 'module');
                                    $roleStyles = match ($actorRole) {
                                        'admin' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        'staff' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $log->created_at->format('M d, Y') }} <br>
                                        <span class="text-gray-400">{{ $log->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        <div class="font-medium text-navy-700">
                                            <i class="fa-solid {{ $actorRole === 'system' ? 'fa-desktop' : 'fa-user-tie' }} mr-1 text-gray-400"></i>
                                            {{ $actorName }}
                                        </div>
                                        <span class="inline-flex mt-1 px-1.5 py-0.5 rounded border text-[10px] font-bold uppercase tracking-wide {{ $roleStyles }}">{{ $actorRole }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800 leading-relaxed">
                                        @if($module)
                                            <span class="inline-flex mr-2 mb-1 px-2 py-0.5 rounded-md bg-navy-50 text-navy-700 text-[10px] font-bold uppercase tracking-wide">{{ $module }}</span>
                                        @endif
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
                                        {{ $hasAuditFilters ? 'No actions match the selected filters.' : 'No system logs recorded yet.' }}
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

<!-- REVOKE ACCESS CONFIRMATION CARD -->
<div id="revokeAccessModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="revokeModalTitle" onclick="handleRevokeBackdrop(event)">
    <div id="revokeModalCard" class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform scale-95 transition-transform duration-200">
        <div class="bg-orange-50 border-b border-orange-100 px-6 py-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-user-lock text-lg"></i>
            </div>
            <div class="flex-1">
                <h2 id="revokeModalTitle" class="text-lg font-bold text-gray-800">Revoke system access?</h2>
                <p class="text-sm text-gray-500 mt-1">This action takes effect immediately.</p>
            </div>
            <button type="button" onclick="closeRevokeModal()" class="text-gray-400 hover:text-gray-600 transition-colors" aria-label="Close confirmation">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="px-6 py-5">
            <p class="text-sm text-gray-600 leading-relaxed">
                Are you sure you want to revoke system access for <span id="revokeStaffName" class="font-bold text-gray-800"></span>?
                They will be logged out immediately and moved to Revoked Accounts.
            </p>
        </div>

        <div class="px-6 pb-6 flex justify-end gap-3">
            <button type="button" onclick="closeRevokeModal()" class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Cancel
            </button>
            <form id="revokeAccessForm" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-ban"></i> Revoke Access
                </button>
            </form>
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

<script>
    function switchAccountTab(tabName) {
        document.querySelectorAll('.account-tab').forEach(tab => {
            const isActive = tab.dataset.tab === tabName;
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            tab.classList.toggle('bg-white', isActive);
            tab.classList.toggle('text-navy-700', isActive);
            tab.classList.toggle('shadow-sm', isActive);
            tab.classList.toggle('text-gray-500', !isActive);
        });

        document.querySelectorAll('.account-panel').forEach(panel => {
            panel.classList.toggle('hidden', panel.dataset.panel !== tabName);
        });

        document.querySelectorAll('.action-dropdown[open]').forEach(menu => menu.removeAttribute('open'));
    }

    const revokeModal = document.getElementById('revokeAccessModal');
    const revokeModalCard = document.getElementById('revokeModalCard');
    const revokeAccessForm = document.getElementById('revokeAccessForm');
    const revokeStaffName = document.getElementById('revokeStaffName');

    function openRevokeModal(button) {
        document.querySelectorAll('.action-dropdown[open]').forEach(menu => menu.removeAttribute('open'));
        revokeAccessForm.action = button.dataset.revokeAction;
        revokeStaffName.textContent = button.dataset.staffName;
        revokeModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            revokeModal.classList.remove('opacity-0');
            revokeModalCard.classList.remove('scale-95');
        });
    }

    function closeRevokeModal() {
        revokeModal.classList.add('opacity-0');
        revokeModalCard.classList.add('scale-95');
        document.body.classList.remove('overflow-hidden');

        setTimeout(() => revokeModal.classList.add('hidden'), 200);
    }

    function handleRevokeBackdrop(event) {
        if (event.target === revokeModal) {
            closeRevokeModal();
        }
    }

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !revokeModal.classList.contains('hidden')) {
            closeRevokeModal();
        }
    });

    document.addEventListener('click', event => {
        document.querySelectorAll('.action-dropdown[open]').forEach(menu => {
            if (!menu.contains(event.target)) {
                menu.removeAttribute('open');
            }
        });
    });

    const auditFilterForm = document.getElementById('auditFilterForm');

    if (auditFilterForm) {
        let searchTimer;
        const auditSearch = document.getElementById('audit-search');

        auditFilterForm.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', () => auditFilterForm.requestSubmit());
        });

        auditSearch?.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => auditFilterForm.requestSubmit(), 450);
        });
    }
</script>
@endsection
