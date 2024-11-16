<x-layouts.app>
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>{{ ucfirst($partnerType) }} Force Application</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
      @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show">
              {{ session('success') }}
              <button type="button" class="close" data-dismiss="alert">×</button>
          </div>
      @endif
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <form id="partnerForm" method="POST" action="{{ route('partners.store', $partnerType) }}">
                    @csrf
                    @include('partners.partials.personal-info')
                    @include('partners.partials.spiritual-background')
                    @include('partners.partials.leadership')
                    @include('partners.partials.commitment')
                    
                    <div class="g-recaptcha mb-3" 
                    data-sitekey="{{ config('services.recaptcha.site_key') }}">
               </div>
                    <div class="form-group text-center mt-4">
                        <button type="submit" class="btn btn-custom">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
