@if(session('success'))
    <div class="alert-success mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-error mb-4">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert-error mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif
