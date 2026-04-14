@if(session('success'))
    <div class="alert-neon success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert-neon error">
        {{ session('error') }}
    </div>
@endif

@if(session('mensaje'))
    <div class="alert-neon info">
        {{ session('mensaje') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert-neon error">
        <ul style="margin:0; padding-left:15px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif