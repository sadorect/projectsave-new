@extends('admin.layouts.app')

@section('title', 'Manage FAQs')

@push('styles')
<style>
    .search-highlight {
        background-color: #fff3cd;
        padding: 1px 2px;
        border-radius: 2px;
    }
    
    .faq-row:hover {
        background-color: #f8f9fa;
    }
    
    .results-info {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .search-stats {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: 1rem;
    }
    
    .advanced-filters {
        background-color: #f8f9fa;
        border-radius: 0.375rem;
        padding: 1rem;
        border: 1px solid #dee2e6;
    }
    
    .filter-active {
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
    
    .search-input-group .btn {
        border-left: none;
    }
    
    .search-input-group .form-control:focus + .btn {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    @media (max-width: 768px) {
        .search-filters .row > div {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>FAQs @if(request('search')) - Search Results @endif</h1>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New FAQ
        </a>
    </div>

    @include('components.alerts')

    <!-- Search and Filter Section -->
    <div class="card mb-4 {{ request()->hasAny(['search', 'status_filter', 'date_from', 'date_to', 'sort']) && request('sort') != 'latest' ? 'filter-active' : '' }}">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-search"></i> Search & Filter FAQs
                @if(request()->hasAny(['search', 'status_filter', 'date_from', 'date_to']) || (request('sort') && request('sort') != 'latest'))
                    <span class="badge bg-info ms-2">Filtered</span>
                @endif
                <button class="btn btn-sm btn-outline-secondary float-end" type="button" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Advanced Filters
                </button>
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.faqs.index') }}" id="search-form">
                <div class="row">
                    <!-- Main Search -->
                    <div class="col-md-6">
                        <div class="input-group search-input-group">
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by title or content... (Ctrl+K)"
                                   id="search-input">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                        @if(request('search'))
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Press <kbd>Esc</kbd> to clear search
                            </small>
                        @endif
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSearch()" title="Clear all filters">
                                <i class="bi bi-x-circle"></i> Clear
                            </button>
                            <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset to default view">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleAdvancedSearch()" title="Ctrl+Shift+F">
                                <i class="bi bi-funnel-fill"></i> Filters
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Filters (Collapsible) -->
                <div class="collapse mt-3" id="searchFilters">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status_filter" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="published" {{ request('status_filter') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ request('status_filter') == 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" 
                                   value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" 
                                   value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title A-Z</option>
                                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                                <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-funnel-fill"></i> Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    @if(request()->hasAny(['search', 'status_filter', 'date_from', 'date_to']))
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Showing {{ $faqs->total() }} result(s) 
            @if(request('search'))
                for "<strong>{{ request('search') }}</strong>"
            @endif
            @if(request('status_filter'))
                with status "<strong>{{ ucfirst(request('status_filter')) }}</strong>"
            @endif
            @if(request('date_from') || request('date_to'))
                @if(request('date_from') && request('date_to'))
                    from {{ request('date_from') }} to {{ request('date_to') }}
                @elseif(request('date_from'))
                    from {{ request('date_from') }}
                @else
                    until {{ request('date_to') }}
                @endif
            @endif
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        Bulk Actions
                        @if($faqs->total() > 0)
                            <span class="search-stats">
                                <small class="text-muted">
                                    ({{ $faqs->total() }} FAQ{{ $faqs->total() != 1 ? 's' : '' }} 
                                    @if($faqs->hasPages())
                                        - Page {{ $faqs->currentPage() }} of {{ $faqs->lastPage() }}
                                    @endif)
                                </small>
                            </span>
                        @endif
                    </h5>
                </div>
                <div class="col-md-6">
                    <form id="bulk-action-form" method="POST" action="{{ route('admin.faqs.bulk-action') }}" class="d-flex gap-2">
                        @csrf
                        <select name="action" class="form-select form-select-sm" id="bulk-action-select" required>
                            <option value="">Select Action</option>
                            <option value="delete">Delete Selected</option>
                            <option value="change_status">Change Status</option>
                        </select>
                        
                        <select name="status" class="form-select form-select-sm d-none" id="status-select">
                            <option value="">Select Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                        
                        <button type="submit" class="btn btn-sm btn-warning" id="apply-bulk-action" disabled>
                            Apply
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($faqs->total() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="20px">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>
                                    Title
                                    @if(request('sort') == 'title_asc')
                                        <i class="bi bi-sort-alpha-down text-primary"></i>
                                    @elseif(request('sort') == 'title_desc')
                                        <i class="bi bi-sort-alpha-down-alt text-primary"></i>
                                    @endif
                                </th>
                                <th>
                                    Status
                                    @if(request('sort') == 'status')
                                        <i class="bi bi-sort-alpha-down text-primary"></i>
                                    @endif
                                </th>
                                <th>
                                    Created
                                    @if(request('sort') == 'latest')
                                        <i class="bi bi-sort-numeric-down text-primary"></i>
                                    @elseif(request('sort') == 'oldest')
                                        <i class="bi bi-sort-numeric-up text-primary"></i>
                                    @endif
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faqs as $faq)
                                <tr class="faq-row">
                                    <td>
                                        <input type="checkbox" name="faq_ids[]" value="{{ $faq->id }}" class="form-check-input faq-checkbox">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ Str::limit($faq->title, 60) }}</strong>
                                        </div>
                                        @if($faq->details)
                                            <small class="text-muted">{{ Str::limit(strip_tags($faq->details), 100) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $faq->status === 'published' ? 'success' : 'warning' }}">
                                            {{ ucfirst($faq->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $faq->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $faq->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this FAQ?')" 
                                                        title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-question-circle display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No FAQs Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status_filter', 'date_from', 'date_to']))
                            No FAQs match your current search criteria. Try adjusting your filters.
                        @else
                            You haven't created any FAQs yet.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status_filter', 'date_from', 'date_to']))
                        <button type="button" class="btn btn-outline-primary" onclick="clearSearch()">
                            <i class="bi bi-arrow-clockwise"></i> Clear Filters
                        </button>
                    @else
                        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Create Your First FAQ
                        </a>
                    @endif
                </div>
            @endif

            @if($faqs->hasPages())
                <div class="mt-4">
                    {{ $faqs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const faqCheckboxes = document.querySelectorAll('.faq-checkbox');
    const bulkActionSelect = document.getElementById('bulk-action-select');
    const statusSelect = document.getElementById('status-select');
    const applyButton = document.getElementById('apply-bulk-action');
    const bulkForm = document.getElementById('bulk-action-form');
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');

    // Search functionality
    let searchTimeout;
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+K or Ctrl+/ to focus search
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === '/')) {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        
        // Escape to clear search
        if (e.key === 'Escape' && searchInput === document.activeElement) {
            if (searchInput.value) {
                searchInput.value = '';
                searchForm.submit();
            } else {
                searchInput.blur();
            }
        }
        
        // Ctrl+Shift+F to toggle advanced filters
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'F') {
            e.preventDefault();
            toggleAdvancedSearch();
        }
    });
    
    // Toggle advanced search
    window.toggleAdvancedSearch = function() {
        const filtersCollapse = document.getElementById('searchFilters');
        const bsCollapse = new bootstrap.Collapse(filtersCollapse, {
            toggle: true
        });
    };
    
    // Auto-search on typing (with debounce)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                searchForm.submit();
            }
        }, 500);
    });

    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            searchForm.submit();
        }
    });

    // Auto-submit on filter changes
    document.querySelectorAll('select[name="status_filter"], select[name="sort"], input[name="date_from"], input[name="date_to"]').forEach(element => {
        element.addEventListener('change', function() {
            searchForm.submit();
        });
    });

    // Clear search function
    window.clearSearch = function() {
        searchInput.value = '';
        document.querySelector('select[name="status_filter"]').value = '';
        document.querySelector('select[name="sort"]').value = 'latest';
        document.querySelector('input[name="date_from"]').value = '';
        document.querySelector('input[name="date_to"]').value = '';
        searchForm.submit();
    };

    // Highlight search terms in results
    if (searchInput.value) {
        highlightSearchTerms(searchInput.value);
    }

    function highlightSearchTerms(searchTerm) {
        if (!searchTerm || searchTerm.length < 2) return;
        
        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        const tableBody = document.querySelector('tbody');
        
        if (tableBody) {
            const walker = document.createTreeWalker(
                tableBody,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );

            const textNodes = [];
            let node;
            while (node = walker.nextNode()) {
                if (node.nodeValue.trim()) {
                    textNodes.push(node);
                }
            }

            textNodes.forEach(textNode => {
                if (regex.test(textNode.nodeValue)) {
                    const highlighted = textNode.nodeValue.replace(regex, '<mark class="search-highlight">$1</mark>');
                    const wrapper = document.createElement('span');
                    wrapper.innerHTML = highlighted;
                    textNode.parentNode.replaceChild(wrapper, textNode);
                }
            });
        }
    }

    // Show search tips
    searchInput.addEventListener('focus', function() {
        if (!this.value && !document.querySelector('.search-tips')) {
            const tips = document.createElement('div');
            tips.className = 'search-tips mt-2';
            tips.innerHTML = `
                <small class="text-muted">
                    <i class="bi bi-lightbulb"></i> 
                    <strong>Search Tips:</strong> Use keywords from FAQ title or content. 
                    Try <kbd>Ctrl+K</kbd> to quickly focus search.
                </small>
            `;
            this.parentNode.parentNode.appendChild(tips);
            
            // Remove tips on blur
            setTimeout(() => {
                this.addEventListener('blur', function() {
                    setTimeout(() => {
                        const tipsEl = document.querySelector('.search-tips');
                        if (tipsEl) tipsEl.remove();
                    }, 200);
                }, { once: true });
            }, 100);
        }
    });

    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            faqCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButton();
        });
    }

    // Individual checkbox functionality
    faqCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.faq-checkbox:checked');
            if (selectAll) {
                selectAll.checked = checkedBoxes.length === faqCheckboxes.length;
                selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < faqCheckboxes.length;
            }
            updateBulkActionButton();
        });
    });

    // Bulk action select functionality
    if (bulkActionSelect) {
        bulkActionSelect.addEventListener('change', function() {
            const needsStatus = this.value === 'change_status';
            
            if (needsStatus && statusSelect) {
                statusSelect.classList.remove('d-none');
                statusSelect.required = true;
            } else if (statusSelect) {
                statusSelect.classList.add('d-none');
                statusSelect.required = false;
                statusSelect.value = '';
            }
            updateBulkActionButton();
        });
    }

    // Status select functionality
    if (statusSelect) {
        statusSelect.addEventListener('change', updateBulkActionButton);
    }

    // Update bulk action button state
    function updateBulkActionButton() {
        if (!applyButton) return;
        
        const selectedFaqs = document.querySelectorAll('.faq-checkbox:checked');
        const actionSelected = bulkActionSelect ? bulkActionSelect.value !== '' : false;
        const statusRequired = bulkActionSelect ? bulkActionSelect.value === 'change_status' : false;
        const statusSelected = !statusRequired || (statusSelect ? statusSelect.value !== '' : false);

        applyButton.disabled = selectedFaqs.length === 0 || !actionSelected || !statusSelected;
        
        // Update button text with count
        if (selectedFaqs.length > 0) {
            applyButton.textContent = `Apply (${selectedFaqs.length})`;
        } else {
            applyButton.textContent = 'Apply';
        }
    }

    // Form submission with confirmation
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const selectedFaqs = document.querySelectorAll('.faq-checkbox:checked');
            const action = bulkActionSelect ? bulkActionSelect.value : '';
            
            if (selectedFaqs.length === 0) {
                e.preventDefault();
                alert('Please select at least one FAQ.');
                return;
            }

            let confirmMessage = `Are you sure you want to ${action.replace('_', ' ')} ${selectedFaqs.length} FAQ(s)?`;
            
            if (action === 'delete') {
                confirmMessage = `Are you sure you want to delete ${selectedFaqs.length} FAQ(s)? This action cannot be undone.`;
            }

            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return;
            }

            // Add selected FAQ IDs to form
            selectedFaqs.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'faq_ids[]';
                input.value = checkbox.value;
                this.appendChild(input);
            });
        });
    }
});
</script>
@endpush

@endsection
