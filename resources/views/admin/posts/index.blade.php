@extends('admin.layouts.app')

@section('title', 'Manage Posts')

@push('styles')
<style>
    .search-highlight {
        background-color: #fff3cd;
        padding: 1px 2px;
        border-radius: 2px;
    }
    
    .post-row:hover {
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
        <h1>Posts @if(request('search')) - Search Results @endif</h1>
        <div class="d-flex gap-2">
            @if(auth()->user()?->hasPermission('manage-ai-image-settings'))
                <a href="{{ route('admin.ai-images.settings.edit') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-stars"></i> AI Settings
                </a>
            @endif
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Post
            </a>
        </div>
    </div>

    @include('components.alerts')

    @if(auth()->user()?->hasPermission('manage-ai-image-settings'))
        <div class="card mb-4 border-primary-subtle">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-sliders"></i> AI Featured Image Settings</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="fw-semibold mb-1">Review requirement is currently {{ !empty($aiSettings['require_approval']) ? 'ON' : 'OFF' }}</div>
                        <div class="text-muted small">Default provider: {{ $aiSettings['default_provider'] }} · Default preset: {{ $aiSettings['default_preset'] }}</div>
                        <div class="text-muted small">Use the AI Settings page to toggle review and add provider API keys.</div>
                    </div>
                    <a href="{{ route('admin.ai-images.settings.edit') }}" class="btn btn-primary">
                        <i class="bi bi-stars"></i> Open AI Settings
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($aiSettings['require_approval']))
        <div class="alert {{ $pendingAiReviewCount > 0 ? 'alert-warning' : 'alert-light border' }} d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="fw-semibold">AI review queue</div>
                @if($pendingAiReviewCount > 0)
                    <div>You have {{ $pendingAiReviewCount }} AI-generated {{ $pendingAiReviewCount === 1 ? 'post image' : 'post images' }} waiting for review.</div>
                @else
                    <div>No AI-generated post images are currently waiting for review.</div>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.posts.index', array_merge(request()->query(), ['image_source_filter' => 'ai_candidate', 'ai_approval_status_filter' => 'pending'])) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-stars"></i> View Pending Reviews
                </a>
            </div>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="card mb-4 {{ request()->hasAny(['search', 'category_filter', 'author_filter', 'date_from', 'date_to', 'sort', 'image_source_filter', 'ai_generation_status_filter', 'ai_approval_status_filter']) && request('sort') != 'latest' ? 'filter-active' : '' }}">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-search"></i> Search & Filter Posts
                @if(request()->hasAny(['search', 'category_filter', 'author_filter', 'date_from', 'date_to', 'image_source_filter', 'ai_generation_status_filter', 'ai_approval_status_filter']) || (request('sort') && request('sort') != 'latest'))
                    <span class="badge bg-info ms-2">Filtered</span>
                @endif
                <button class="btn btn-sm btn-outline-secondary float-end" type="button" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Advanced Filters
                </button>
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.posts.index') }}" id="search-form">
                <div class="row">
                    <!-- Main Search -->
                    <div class="col-md-6">
                        <div class="input-group search-input-group">
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by title, content, author, scripture... (Ctrl+K)"
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
                            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset to default view">
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
                            <label class="form-label">Category</label>
                            <select name="category_filter" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_filter') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Author</label>
                            <select name="author_filter" class="form-select form-select-sm">
                                <option value="">All Authors</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author }}" {{ request('author_filter') == $author ? 'selected' : '' }}>
                                        {{ $author }}
                                    </option>
                                @endforeach
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
                        
                        <div class="col-md-2">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title A-Z</option>
                                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                                <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>Author</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Image Source</label>
                            <select name="image_source_filter" class="form-select form-select-sm">
                                <option value="">Any Source</option>
                                @foreach($imageSourceOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('image_source_filter') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">AI Generation Status</label>
                            <select name="ai_generation_status_filter" class="form-select form-select-sm">
                                <option value="">Any Generation Status</option>
                                @foreach($aiGenerationStatusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('ai_generation_status_filter') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">AI Review Status</label>
                            <select name="ai_approval_status_filter" class="form-select form-select-sm">
                                <option value="">Any Review Status</option>
                                @foreach($aiApprovalStatusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('ai_approval_status_filter') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-funnel-fill"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    @if(request()->hasAny(['search', 'category_filter', 'author_filter', 'date_from', 'date_to', 'image_source_filter', 'ai_generation_status_filter', 'ai_approval_status_filter']))
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Showing {{ $posts->total() }} result(s) 
            @if(request('search'))
                for "<strong>{{ request('search') }}</strong>"
            @endif
            @if(request('category_filter'))
                in category "<strong>{{ $categories->find(request('category_filter'))->name ?? 'Unknown' }}</strong>"
            @endif
            @if(request('author_filter'))
                by author "<strong>{{ request('author_filter') }}</strong>"
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
            @if(request('image_source_filter'))
                with source <strong>{{ $imageSourceOptions[request('image_source_filter')] ?? request('image_source_filter') }}</strong>
            @endif
            @if(request('ai_generation_status_filter'))
                with generation status <strong>{{ $aiGenerationStatusOptions[request('ai_generation_status_filter')] ?? request('ai_generation_status_filter') }}</strong>
            @endif
            @if(request('ai_approval_status_filter'))
                with review status <strong>{{ $aiApprovalStatusOptions[request('ai_approval_status_filter')] ?? request('ai_approval_status_filter') }}</strong>
            @endif
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        Bulk Actions
                        @if($posts->total() > 0)
                            <span class="search-stats">
                                <small class="text-muted">
                                    ({{ $posts->total() }} post{{ $posts->total() != 1 ? 's' : '' }} 
                                    @if($posts->hasPages())
                                        - Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}
                                    @endif)
                                </small>
                            </span>
                        @endif
                    </h5>
                </div>
                <div class="col-md-6">
                    <form id="bulk-action-form" method="POST" action="{{ route('admin.posts.bulk-action') }}" class="d-flex gap-2">
                        @csrf
                        <select name="action" class="form-select form-select-sm" id="bulk-action-select" required>
                            <option value="">Select Action</option>
                            <option value="delete">Delete Selected</option>
                            <option value="change_category">Change Category</option>
                            <option value="add_category">Add Category</option>
                            <option value="remove_category">Remove Category</option>
                        </select>
                        
                        <select name="category_id" class="form-select form-select-sm d-none" id="category-select">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="btn btn-sm btn-warning" id="apply-bulk-action" disabled>
                            Apply
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($posts->total() > 0)
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
                                    Author
                                    @if(request('sort') == 'author')
                                        <i class="bi bi-sort-alpha-down text-primary"></i>
                                    @endif
                                </th>
                                <th>Categories</th>
                                <th>Tags</th>
                                <th>Image Status</th>
                                <th>
                                    Published
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
                            @foreach($posts as $post)
                                <tr class="post-row">
                                    <td>
                                        <input type="checkbox" name="post_ids[]" value="{{ $post->id }}" class="form-check-input post-checkbox">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ Str::limit($post->title, 50) }}</strong>
                                            @if($post->subtitle)
                                                <br><small class="text-muted">{{ Str::limit($post->subtitle, 60) }}</small>
                                            @endif
                                        </div>
                                        @if($post->scripture)
                                            <small class="text-info">
                                                <i class="bi bi-book"></i> {{ Str::limit($post->scripture, 30) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($post->author)
                                            <span class="badge bg-light text-dark">{{ $post->author }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @forelse($post->categories as $category)
                                            <span class="badge bg-primary me-1">{{ $category->name }}</span>
                                        @empty
                                            <span class="text-muted">No categories</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse($post->tags as $tag)
                                            <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                                        @empty
                                            <span class="text-muted">No tags</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($post->featured_image_source === 'manual')
                                                <span class="badge bg-dark-subtle text-dark">Manual</span>
                                            @elseif($post->featured_image_source === 'ai')
                                                <span class="badge bg-primary">AI Live</span>
                                            @elseif($post->featured_image_candidate_path)
                                                <span class="badge bg-warning text-dark">AI Candidate</span>
                                            @else
                                                <span class="badge bg-light text-dark border">None</span>
                                            @endif

                                            @if($post->featured_image_generation_status)
                                                <span class="badge {{ $post->featured_image_generation_status === 'failed' ? 'bg-danger' : ($post->featured_image_generation_status === 'processing' ? 'bg-info text-dark' : 'bg-secondary') }}">
                                                    {{ ucfirst($post->featured_image_generation_status) }}
                                                </span>
                                            @endif

                                            @if($post->featured_image_approval_status)
                                                <span class="badge {{ $post->featured_image_approval_status === 'approved' ? 'bg-success' : ($post->featured_image_approval_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $post->featured_image_approval_status)) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($post->published_at)
                                            <div>{{ $post->published_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $post->published_at->format('H:i') }}</small>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if($post->featured_image_candidate_path && $post->featured_image_approval_status === 'pending')
                                                <a href="{{ route('admin.posts.edit', $post) }}#ai-review" class="btn btn-sm btn-warning" title="Review AI Candidate">
                                                    <i class="bi bi-stars"></i> Review AI
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this post?')" 
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
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No Posts Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'category_filter', 'author_filter', 'date_from', 'date_to', 'image_source_filter', 'ai_generation_status_filter', 'ai_approval_status_filter']))
                            No posts match your current search criteria. Try adjusting your filters.
                        @else
                            You haven't created any posts yet.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'category_filter', 'author_filter', 'date_from', 'date_to', 'image_source_filter', 'ai_generation_status_filter', 'ai_approval_status_filter']))
                        <button type="button" class="btn btn-outline-primary" onclick="clearSearch()">
                            <i class="bi bi-arrow-clockwise"></i> Clear Filters
                        </button>
                    @else
                        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Create Your First Post
                        </a>
                    @endif
                </div>
            @endif

            @if($posts->hasPages())
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const postCheckboxes = document.querySelectorAll('.post-checkbox');
    const bulkActionSelect = document.getElementById('bulk-action-select');
    const categorySelect = document.getElementById('category-select');
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
    document.querySelectorAll('select[name="category_filter"], select[name="author_filter"], select[name="sort"], select[name="image_source_filter"], select[name="ai_generation_status_filter"], select[name="ai_approval_status_filter"], input[name="date_from"], input[name="date_to"]').forEach(element => {
        element.addEventListener('change', function() {
            searchForm.submit();
        });
    });

    // Clear search function
    window.clearSearch = function() {
        searchInput.value = '';
        document.querySelector('select[name="category_filter"]').value = '';
        document.querySelector('select[name="author_filter"]').value = '';
        document.querySelector('select[name="sort"]').value = 'latest';
        document.querySelector('select[name="image_source_filter"]').value = '';
        document.querySelector('select[name="ai_generation_status_filter"]').value = '';
        document.querySelector('select[name="ai_approval_status_filter"]').value = '';
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
                    <strong>Search Tips:</strong> Use keywords from title, content, author, or scripture. 
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
            postCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButton();
        });
    }

    // Individual checkbox functionality
    postCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.post-checkbox:checked');
            if (selectAll) {
                selectAll.checked = checkedBoxes.length === postCheckboxes.length;
                selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < postCheckboxes.length;
            }
            updateBulkActionButton();
        });
    });

    // Bulk action select functionality
    if (bulkActionSelect) {
        bulkActionSelect.addEventListener('change', function() {
            const needsCategory = ['change_category', 'add_category', 'remove_category'].includes(this.value);
            
            if (needsCategory && categorySelect) {
                categorySelect.classList.remove('d-none');
                categorySelect.required = true;
            } else if (categorySelect) {
                categorySelect.classList.add('d-none');
                categorySelect.required = false;
                categorySelect.value = '';
            }
            updateBulkActionButton();
        });
    }

    // Category select functionality
    if (categorySelect) {
        categorySelect.addEventListener('change', updateBulkActionButton);
    }

    // Update bulk action button state
    function updateBulkActionButton() {
        if (!applyButton) return;
        
        const selectedPosts = document.querySelectorAll('.post-checkbox:checked');
        const actionSelected = bulkActionSelect ? bulkActionSelect.value !== '' : false;
        const categoryRequired = bulkActionSelect ? ['change_category', 'add_category', 'remove_category'].includes(bulkActionSelect.value) : false;
        const categorySelected = !categoryRequired || (categorySelect ? categorySelect.value !== '' : false);

        applyButton.disabled = selectedPosts.length === 0 || !actionSelected || !categorySelected;
        
        // Update button text with count
        if (selectedPosts.length > 0) {
            applyButton.textContent = `Apply (${selectedPosts.length})`;
        } else {
            applyButton.textContent = 'Apply';
        }
    }

    // Form submission with confirmation
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const selectedPosts = document.querySelectorAll('.post-checkbox:checked');
            const action = bulkActionSelect ? bulkActionSelect.value : '';
            
            if (selectedPosts.length === 0) {
                e.preventDefault();
                alert('Please select at least one post.');
                return;
            }

            let confirmMessage = `Are you sure you want to ${action.replace('_', ' ')} ${selectedPosts.length} post(s)?`;
            
            if (action === 'delete') {
                confirmMessage = `Are you sure you want to delete ${selectedPosts.length} post(s)? This action cannot be undone.`;
            }

            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return;
            }

            // Add selected post IDs to form
            selectedPosts.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'post_ids[]';
                input.value = checkbox.value;
                this.appendChild(input);
            });
        });
    }
});
</script>
@endpush

@endsection