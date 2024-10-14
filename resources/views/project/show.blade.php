@extends('layouts.main')

@section('content')
    <div class="container">
        <h1>Our Projects</h1>
        
        @foreach ($projects as $project)
            <div class="project">
                <h2>{{ $project->project_name }}</h2>
                <p>{{ $project->description }}</p>

                <h3>Products</h3>
                <ul>
                    @foreach ($project->products as $product)
                        <li>{{ $product->name }}</li>
                    @endforeach
                </ul>

                <h3>Images</h3>
                <div>
                    @php
                        $images = json_decode($project->images);
                    @endphp
                    @if ($images)
                        @foreach ($images as $image)
                            <img src="{{ Voyager::image($image) }}" alt="Project Image" style="width: 150px; height: auto;">
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection
