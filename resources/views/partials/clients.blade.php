<!-- /* clients */ -->
<section class="clients">
    <div class="container">
        <h2 class="title text-center">
            SELECTED CL<span>I</span>ENTS
        </h2>
        <div class="list_clients">
            <h3>
                @foreach ($clients_home as $key => $item)
                @if ( $key == 0 )
                    {{ $item['name'].' ' }}
                @else
                    {{ ' | '.$item['name'] }}
                @endif
                @endforeach
            </h3>
        </div>
    </div>
    <div class="line_list">
    </div>
</section>