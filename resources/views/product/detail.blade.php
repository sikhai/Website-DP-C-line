@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-detail.css') }}">
@endsection

<!-- detail item -->
<section class="detail-fabric">
    <div class="container" style="margin-top: 90px;">
        <div class="row img-detail">
            <div class="col-10 main-view">
                <img id="main-image" src="{{ env('APP_URL') . '/storage/' . $product['image'] }}"
                    onclick="switchImage(this)" alt="{{ $product['name'] }}">
            </div>
            <div class="col-2 small-view">
                <div class="current-selected">
                    <img src="{{ env('APP_URL') . '/storage/' . $product['image'] }}" onclick="switchImage(this)"
                        alt="{{ $product['name'] }}">
                </div>
                @foreach (json_decode($product['images'] ?? '[]') as $item)
                    <div class="next-selected">
                        <img src="{{ Voyager::image($item) }}" onclick="switchImage(this)" alt="{{ $product['name'] }}">
                    </div>
                @endforeach

            </div>
        </div>

        <div class="row mt-3 lable-general">
            <div class="d-flex directory">
                <a class="back-directory" href="/products">All products</a>
                <a class="back-directory" style="cursor: default">/</a>
                @if ($collection)
                    <a class="back-directory" href="/{{ $collection->slug }}">{{ $collection->name }}</a>
                    <a class="back-directory" style="cursor: default">/</a>
                    <a class="back-directory" href="/collection">Collection</a>
                @endif
                @if ( isset($product->category->name) )
                    <a class="back-directory" style="cursor: default">/</a>
                    <a class="back-directory" href="{{ $product->category->slug }}">{{ $product->category->name }}</a>
                    <a class="back-directory" style="cursor: default">/</a>
                @endif
                <a class="current-directory">{{ $product['name'] }}</a>
            </div>
            <div class="d-flex mt-5 lable-fabric">
                <div id="code">{{ $product['product_code'] }}</div>
                <div id="name">{{ $product['name'] }}</div>
            </div>

            <button type="button" class="btn btn-secondary">{{ isset($product->category->name) ? $product->category->name : '' }}</button>
        </div>
        <div class="row p-0" style="margin-top: 80px;">
            <div class="download">
                <div class="d-flex download-item">
                    <img src="images/file-download.svg" alt="">
                    <div id="name">
                        <p style="margin-top: -3px;">TECHNICAL</p>
                        <p style="margin-top: -15px;">DOCUMENTS</p>
                    </div>
                </div>
                <div class="d-flex mt-3 download-item">
                    <img src="images/file-download.svg" alt="">
                    <div id="name">
                        <p>HD IMAGES</p>
                    </div>
                </div>
            </div>
            <div class="p-0 left-description">
                <div class="content">
                    <div class="title">
                        <p>About Collection</p>
                    </div>
                    <div class="description">
                        <p>Hacienda - Irregularity of the tie&dye thread, color vibration</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Use</p>
                    </div>
                    <div class="description">
                        <p>Curtain, decorative seat</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Pattern Repeat</p>
                    </div>
                    <div class="description">
                        <p>No repeat</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Sales Unit</p>
                    </div>
                    <div class="description">
                        <p>Mètre linéaire / MT</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Dimensions</p>
                    </div>
                    <div class="description">
                        <p>Useful width 140cm (55")</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Weight</p>
                    </div>
                    <div class="description">
                        <p>16oz per sq.yd</p>
                    </div>
                </div>
            </div>
            <div class="p-0 left-description">
                <div class="content">
                    <div class="title">
                        <p>UV Lightfastness</p>
                    </div>
                    <div class="description">
                        <p>7 on a scale of 8</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>+ Products</p>
                    </div>
                    <div class="description">
                        <p>Irregular yarns, very good light resistance</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Martindale</p>
                    </div>
                    <div class="description">
                        <p>16 000 T</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Composition</p>
                    </div>
                    <div class="description">
                        <p>45% VI 19% CO 12% PC 11% LI 6% PA 4% WO 3% PL</p>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Care</p>
                    </div>
                    <div class="description">
                        <div class="d-flex" style="gap: 22px;">
                            <img src="images/icon-mayui.svg" alt="">
                            <img src="images/icon-nowash.svg" alt="">
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="title">
                        <p>Environment Standard</p>
                    </div>
                    <div class="description">
                        <div class="d-flex mt-3" style="gap: 18px;">
                            <img src="images/GRS_Grey.png" alt="">
                            <img src="images/OekoTex_100RecycledPolyester_2111231_noQR_Grey_RGB.png" alt="">
                            <img src="images/ISO_14001_grey_RGB.png" alt="">
                            <img src="images/ECO_grey50.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- /* tab-general-fabric collection design */ -->

<section class="table-products position-relative">
    <div class="row">
        <div class="col-4" style="margin:27px 70px 0px">
            <p id="lable-samecollection">In the same collection</p>
        </div>
    </div>
    <div class="container mt-5">
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/amigo01.jpg" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Amigo</p>
            <p class="pt-2 m-0" id="design-code">Amigo - 01-Walnut</p>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/amigo02.jpg" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Amigo</p>
            <p class="pt-2 m-0" id="design-code">Amigo - 02-Mushroom</p>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/amigo03.jpg" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Amigo</p>
            <p class="pt-2 m-0" id="design-code">Amigo - 03-Taupe</p>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/amigo04.jpg" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Amigo</p>
            <p class="pt-2 m-0" id="design-code">Amigo - 04-Pecan</p>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/amigo08.jpg" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Amigo</p>
            <p class="pt-2 m-0" id="design-code">Amigo - 08-Plum</p>
        </div>
    </div>

    <div class="container">
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/VELLUTO-1-160x160.webp" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Velluto</p>
            <p class="pt-2 m-0" id="design-code">Velluto - 01-Cream</p>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/VELLUTO-3-160x160.webp" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Velluto</p>
            <p class="pt-2 m-0" id="design-code">Velluto - 03-Mika</p>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/VELLUTO-9-160x160.webp" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Velluto</p>
            <p class="pt-2 m-0" id="design-code">Velluto - 09-Olive</p>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/Velluto-33-Rust-160x160.webp" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Velluto</p>
            <p class="pt-2 m-0" id="design-code">Velluto - 33-Rust</p>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/DPC/Velluto-36-Peacock-160x160.webp" alt="" loading="lazy">
            <p class="pt-2 m-0" id="design-name">Velluto</p>
            <p class="pt-2 m-0" id="design-code">Velluto - 36-Peacock</p>
        </div>
    </div>

    <!-- <div class="button-showmore">
        <button type="button" class="btn btn-primary" id="btn-showmore">
            SHOW MORE PRODUCTS
        </button>
    </div> -->
</section>
