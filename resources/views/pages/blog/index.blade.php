<x-layouts.app>
    
        <!-- Page Header Start -->
        <div class="page-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>From Blog</h2>
                    </div>
                    <div class="col-12">
                        <a href="">Home</a>
                        <a href="">Blog</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page Header End -->
        
        
        <!-- Blog Start -->
        <div class="blog">
            <div class="container">
                <div class="section-header text-center">
                    <p>Our Blog</p>
                    <h2>Latest news & articles directly from our blog</h2>
                </div>
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-lg-4">
                            <div class="blog-item">
                                <div class="blog-img">
                                    @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="Blog Image">
                                    @endif
                                </div>
                                <div class="blog-text">
                                    <h3><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h3>
                                    @if($post->scripture)
                                        <div class="scripture-text mb-2">
                                            <em>{{ $post->scripture }}</em>
                                        </div>
                                    @endif
                                    @if($post->subtitle)
                                        <h5>{{ $post->subtitle }}</h5>
                                    @endif
                                    <p>{!! Str::limit($post->details, 150) !!}</p>
                                </div>
                                <div class="blog-meta">
                                    <p><i class="fa fa-user"></i><a href="">{{ $post->author ?? 'Admin' }}</a></p>
                                    <p><i class="fa fa-comments"></i><a href="">{{ $post->comments_count }} Comments</a></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>        <!-- Blog End -->

</x-layouts.app>
