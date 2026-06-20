<form action="{{ $formAction }}" method="POST" onsubmit="btnsubmit.disabled=true; return true;">
    @csrf
    @if (isset($formMethod))
        @method($formMethod)
    @endif

    <div class="row mb-4 align-items-center">
        <div class="col-md-2">
            <label class="form-label mb-md-0 fw-semibold" for="group_name">Role Name <span
                    class="text-danger">*</span></label>
        </div>
        <div class="col-md-10">
            <input type="text" id="group_name" name="group_name"
                value="{{ old('group_name', $group->group_name ?? '') }}"
                class="form-control @error('group_name') is-invalid @enderror"
                placeholder="Example: Super Admin, Finance, Technical Support">
            @error('group_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @php
        $moduleIcons = [
            'System Setting' => 'fa-cogs',
            'Personal' => 'fa-user',
            'Inventory' => 'fa-boxes-stacked',
            'Point Of Sales' => 'fa-cash-register',
            'Presence' => 'fa-fingerprint',
            'Human Resources' => 'fa-users',
            'Dashboard' => 'fa-chart-line',
        ];
    @endphp

    @forelse (collect($page_distincts)->groupBy('module') as $moduleName => $subModules)
        <div class="card mb-4 shadow-sm border border-light-subtle rounded-3 overflow-hidden">
            <div class="card-header bg-light d-flex align-items-center py-3 border-bottom border-light-subtle">
                <h6 class="mb-0 fw-bold text-primary d-flex align-items-center">
                    <i class="fas {{ $moduleIcons[$moduleName] ?? 'fa-folder-open' }} me-2 fs-5"></i>
                    {{ strtoupper($moduleName ?: 'General') }} MODULE
                </h6>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase fw-bold">
                        <tr>
                            <th style="width: 300px;" class="ps-4">SubModule / Page Name</th>
                            <th class="pe-4">Access Permissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subModules as $d)
                            {{-- 🛑 FILTER PROTEKSI: Mencegah data Presence & Human Resource saling tertukar atau tumpang tindih --}}
                            @if ($moduleName === 'Presence' && isset($d->module) && $d->module === 'Human Resource')
                                @continue
                            @endif
                            @if ($moduleName === 'Human Resource' && isset($d->module) && $d->module === 'Presence')
                                @continue
                            @endif

                            <tr>
                                <td class="fw-semibold text-dark ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="checkbox" class="form-check-input row-select-all shadow-sm"
                                            title="Select all permissions for this page">
                                        <span class="text-secondary">{!! str_replace('_', ' ', $d->page_name) !!}</span>
                                    </div>
                                </td>

                                <td class="pe-4">
                                    <div class="d-flex flex-wrap align-items-center gap-3 py-1">

                                        @foreach (['Create', 'Read', 'Update', 'Delete'] as $actionName)
                                            @foreach (collect($pages)->where('page_name', $d->page_name)->where('module', $moduleName)->where('action', $actionName) as $p)
                                                <div
                                                    class="d-flex align-items-center gap-2 bg-light border border-light-subtle rounded px-3 py-1.5 shadow-sm">
                                                    <input type="checkbox"
                                                        class="form-check-input m-0 permission-checkbox"
                                                        id="{!! $p->page_id !!}" name="{!! $p->page_id !!}"
                                                        {!! isset($p->access) && $p->access == 1 ? 'checked' : '' !!}>
                                                    <span
                                                        class="small fw-semibold text-dark text-capitalize ms-1">{{ $actionName }}</span>
                                                </div>
                                            @endforeach
                                        @endforeach

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card mb-4 shadow-sm border border-light-subtle rounded-3 p-5 text-center">
            <div class="text-muted fs-5">
                <i class="fas fa-database mb-2 d-block fs-2 text-secondary"></i>
                No Data Found
            </div>
        </div>
    @endforelse

    <div class="d-flex justify-content-end gap-2 mt-4 action-bar">
        <a href="{{ route('setting.role.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" id="btnsubmit" class="btn btn-primary px-4">
            <i class="fas fa-save me-1"></i> {{ isset($formMethod) ? 'Update Role' : 'Save Role' }}
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateMasterCheckboxState(row) {
            const masterCheckbox = row.querySelector('.row-select-all');
            const childCheckboxes = row.querySelectorAll('.permission-checkbox');

            if (!masterCheckbox || childCheckboxes.length === 0) return;

            const checkedCount = Array.from(childCheckboxes).filter(cb => cb.checked).length;

            if (checkedCount === 0) {
                masterCheckbox.checked = false;
                masterCheckbox.indeterminate = false;
            } else if (checkedCount === childCheckboxes.length) {
                masterCheckbox.checked = true;
                masterCheckbox.indeterminate = false;
            } else {
                masterCheckbox.checked = false;
                masterCheckbox.indeterminate = true;
            }
        }

        document.querySelectorAll('table tbody tr').forEach(function(row) {
            updateMasterCheckboxState(row);
        });

        document.querySelectorAll('.row-select-all').forEach(function(masterCheckbox) {
            masterCheckbox.addEventListener('change', function() {
                const row = masterCheckbox.closest('tr');
                const childCheckboxes = row.querySelectorAll('.permission-checkbox');

                childCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = masterCheckbox.checked;
                });
            });
        });

        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const row = checkbox.closest('tr');
                updateMasterCheckboxState(row);
            });
        });
    });
</script>
