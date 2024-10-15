@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-collection.css') }}">
@endsection

<section class="table-products position-relative">
    <div class="row">
        <div class="col-4" style="margin:27px 70px 0px">
            <p id="sum-products">{{ count($products) }} Products, {{ count($designs) }} designs</p>
        </div>
    </div>
    <div class="container">
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-alaska.jpg" alt="" loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Alaska</p>
                <p id="collection-quantity">(15)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-capsule.jpg" alt="" loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Capsule</p>
                <p id="collection-quantity">(15)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-deluxe.jpg" alt="" loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Deluxe</p>
                <p id="collection-quantity">(8)</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-iconic.jpg" alt="" loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Iconic</p>
                <p id="collection-quantity">(13)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-copenhague.png" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Copenhague</p>
                <p id="collection-quantity">(10)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-mantilla.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Manitlla</p>
                <p id="collection-quantity">(7)</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-marmont.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Marmont</p>
                <p id="collection-quantity">(11)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-naturalstory.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Natural Story</p>
                <p id="collection-quantity">(10)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-nero.jpg" alt="" loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Nero</p>
                <p id="collection-quantity">(9)</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-porthouse.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Porthouse</p>
                <p id="collection-quantity">(14)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-sunlight.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Sunlight</p>
                <p id="collection-quantity">(10)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-twirler.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Twirler</p>
                <p id="collection-quantity">(17)</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-ultraweave.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Ultra Weave</p>
                <p id="collection-quantity">(14)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-woolskin.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Woolskin</p>
                <p id="collection-quantity">(18)</p>
            </div>
        </div>
        <div class="fabric-item">
            <img class="img w-100" src="images/acacia-collection/collection-twirler.jpg" alt=""
                loading="lazy">
            <div class="d-flex pt-4 m-0" id="block-collection-lable">
                <p id="collection-name">Twirler</p>
                <p id="collection-quantity">(17)</p>
            </div>
        </div>
    </div>

    <div class="button-showmore">
        <button type="button" class="btn btn-primary" id="btn-showmore">
            SHOW MORE PRODUCTS
        </button>
    </div>
</section>

