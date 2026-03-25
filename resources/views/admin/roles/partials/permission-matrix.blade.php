@php
    $selectedPermissions = collect($selectedPermissions ?? [])->map(fn ($permissionId) => (int) $permissionId)->all();
    $matrixId = $formId . '-permission-matrix';
@endphp

<div class="mb-4" id="{{ $matrixId }}" data-permission-matrix>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <label for="{{ $formId }}-permission-search" class="form-label mb-1">Permissions</label>
            <div class="small text-muted">Search, bulk-select, and review the permission matrix by category.</div>
        </div>

        <div class="d-flex gap-2 flex-wrap align-items-center">
            <input type="search" id="{{ $formId }}-permission-search" class="form-control" placeholder="Search permissions" data-permission-search>
            <button type="button" class="btn btn-outline-primary btn-sm" data-select-all>All</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-clear-all>Clear</button>
            <span class="badge bg-light text-dark" data-selected-count></span>
        </div>
    </div>

    @error('permissions')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="row g-3">
        @foreach($permissionGroups as $category => $categoryPermissions)
            @php($categoryKey = $formId . '-category-' . $loop->index)
            <div class="col-xl-4 col-md-6" data-permission-category>
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <h6 class="mb-1">{{ $category }}</h6>
                            <div class="small text-muted">{{ $categoryPermissions->count() }} permissions</div>
                        </div>

                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary" data-category-toggle="{{ $categoryKey }}">All</button>
                            <button type="button" class="btn btn-outline-secondary" data-category-clear="{{ $categoryKey }}">Clear</button>
                        </div>
                    </div>
                    <div class="card-body" data-category-group="{{ $categoryKey }}">
                        @foreach($categoryPermissions as $permission)
                            <div class="form-check permission-matrix-item mb-3" data-permission-item>
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    id="{{ $formId }}-permission-{{ $permission->id }}"
                                    data-permission-checkbox
                                    {{ in_array($permission->id, $selectedPermissions, true) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="{{ $formId }}-permission-{{ $permission->id }}">
                                    <span class="d-block fw-semibold">{{ $permission->name }}</span>
                                    <span class="d-block small text-muted">{{ $permission->slug }}</span>
                                    @if($permission->description)
                                        <span class="d-block small text-muted mt-1">{{ $permission->description }}</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('styles')
    <style>
        [data-permission-search] {
            min-width: 240px;
        }

        .permission-matrix-item.is-hidden {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-permission-matrix]').forEach(function (matrix) {
                if (matrix.dataset.initialized === 'true') {
                    return;
                }

                matrix.dataset.initialized = 'true';

                const searchInput = matrix.querySelector('[data-permission-search]');
                const checkboxes = Array.from(matrix.querySelectorAll('[data-permission-checkbox]'));
                const selectedCount = matrix.querySelector('[data-selected-count]');

                const syncCount = function () {
                    const checked = checkboxes.filter(function (checkbox) {
                        return checkbox.checked;
                    }).length;

                    selectedCount.textContent = checked + ' selected';
                };

                const toggleGroup = function (groupKey, checked) {
                    matrix.querySelectorAll('[data-category-group="' + groupKey + '"] [data-permission-checkbox]').forEach(function (checkbox) {
                        if (checkbox.closest('[data-permission-item]').classList.contains('is-hidden')) {
                            return;
                        }

                        checkbox.checked = checked;
                    });

                    syncCount();
                };

                matrix.querySelector('[data-select-all]').addEventListener('click', function () {
                    checkboxes.forEach(function (checkbox) {
                        if (!checkbox.closest('[data-permission-item]').classList.contains('is-hidden')) {
                            checkbox.checked = true;
                        }
                    });

                    syncCount();
                });

                matrix.querySelector('[data-clear-all]').addEventListener('click', function () {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = false;
                    });

                    syncCount();
                });

                matrix.querySelectorAll('[data-category-toggle]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        toggleGroup(button.dataset.categoryToggle, true);
                    });
                });

                matrix.querySelectorAll('[data-category-clear]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        toggleGroup(button.dataset.categoryClear, false);
                    });
                });

                checkboxes.forEach(function (checkbox) {
                    checkbox.addEventListener('change', syncCount);
                });

                searchInput.addEventListener('input', function () {
                    const term = searchInput.value.trim().toLowerCase();

                    matrix.querySelectorAll('[data-permission-item]').forEach(function (item) {
                        const visible = item.textContent.toLowerCase().includes(term);
                        item.classList.toggle('is-hidden', !visible);
                    });
                });

                syncCount();
            });
        });
    </script>
@endpush