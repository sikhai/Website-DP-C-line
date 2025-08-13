@php
    $edit = !is_null($dataTypeContent->getKey());
    $add = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .panel .mce-panel {
            border-left-color: #fff;
            border-right-color: #fff;
        }

        .panel .mce-toolbar,
        .panel .mce-statusbar {
            padding-left: 20px;
        }

        .panel .mce-edit-area,
        .panel .mce-edit-area iframe,
        .panel .mce-edit-area iframe html {
            padding: 0 10px;
            min-height: 350px;
        }

        .mce-content-body {
            color: #555;
            font-size: 14px;
        }

        .panel.is-fullscreen .mce-statusbar {
            position: absolute;
            bottom: 0;
            width: 100%;
            z-index: 200000;
        }

        .panel.is-fullscreen .mce-tinymce {
            height: 100%;
        }

        .panel.is-fullscreen .mce-edit-area,
        .panel.is-fullscreen .mce-edit-area iframe,
        .panel.is-fullscreen .mce-edit-area iframe html {
            height: 100%;
            position: absolute;
            width: 99%;
            overflow-y: scroll;
            overflow-x: hidden;
            min-height: 100%;
        }

        #add-attribute-btn,
        .setting_attribute {
            margin-bottom: 25px;
        }

        .setting_attribute {
            background-color: #57c7d4 !important;
        }

        #attributes-container .remove-attribute-btn {
            margin-top: 0px;
        }

        .list-group .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
@stop

@section('page_title', __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' .
    $dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <div class="page-content container-fluid">
        <form role="form" class="form-edit-add"
            action="{{ $edit ? route('voyager.' . $dataType->slug . '.update', $dataTypeContent->getKey()) : route('voyager.' . $dataType->slug . '.store') }}"
            method="POST" enctype="multipart/form-data">
            <!-- PUT Method if we are editing -->
            @if ($edit)
                {{ method_field('PUT') }}
            @endif

            <!-- CSRF TOKEN -->
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-8">
                    <!-- ### TITLE ### -->
                    <div class="panel">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="voyager-character"></i> {{ __('voyager::product.title') }}
                                <span class="panel-desc"> {{ __('voyager::product.title_sub') }}</span>
                            </h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse"
                                    aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            @include('voyager::multilingual.input-hidden', [
                                '_field_name' => 'product_name',
                                '_field_trans' => get_field_translations($dataTypeContent, 'product_name'),
                            ])
                            <input type="text" required class="form-control" id="name" name="name"
                                placeholder="{{ __('voyager::generic.name') }}"
                                value="{{ $dataTypeContent->getTranslatedAttribute('name', app()->getLocale(), 'en') ?? '' }}">
                        </div>

                    </div>

                    <!-- ### short_description ### -->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{!! __('voyager::product.short_description') !!}</h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse"
                                    aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            @include('voyager::multilingual.input-hidden', [
                                '_field_name' => 'short_description',
                                '_field_trans' => get_field_translations($dataTypeContent, 'short_description'),
                            ])
                            <textarea rows="7" class="form-control" name="short_description">{{ $dataTypeContent->short_description ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- ### CONTENT ### -->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{ __('voyager::product.content') }}</h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-resize-full" data-toggle="panel-fullscreen"
                                    aria-hidden="true"></a>
                            </div>
                        </div>

                        <div class="panel-body">
                            @include('voyager::multilingual.input-hidden', [
                                '_field_name' => 'description',
                                '_field_trans' => get_field_translations($dataTypeContent, 'description'),
                            ])
                            @php
                                $dataTypeRows = $dataType->{$edit ? 'editRows' : 'addRows'};
                                $row = $dataTypeRows->where('field', 'description')->first();
                            @endphp
                            {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                        </div>
                    </div><!-- .panel -->

                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{ __('voyager::product.additional_fields') }}</h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse"
                                    aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="color">{{ __('voyager::product.color') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name' => 'color',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'color'),
                                ])
                                <input required type="text" class="form-control" name="color"
                                    id="color" placeholder="Product color"
                                    value="{{ $dataTypeContent->color ?? '' }}">
                                <span id="product-code-error" style="color: red; display: none;"></span>
                            </div>
                            {{-- <button type="button" class="btn btn-primary"
                                id="add-attribute-btn">{{ __('Add Attribute') }}</button> --}}

                            {{-- <button type="button" class="btn btn-primary setting_attribute" data-toggle="modal"
                                data-target="#setting_attribute">
                                {{ __('voyager::product.setting_attribute') }}
                            </button> --}}

                            {{-- <div id="attributes-container">
                                <!-- Existing attributes -->
                                @if (isset($attributes) && is_array($attributes))
                                    @php
                                        $attribute_id = $dataTypeContent->attributes->last()->id;
                                    @endphp
                                    @if (isset($attribute_id))
                                        <input type="hidden" name="attribute_id" value="{{ $attribute_id }}">
                                    @endif
                                    @foreach ($attributes as $key => $attribute)
                                        <div class="form-group attribute">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" name="attributes[{{ $key }}][name]"
                                                        class="form-control" placeholder="{{ __('Attribute Name') }}"
                                                        value="{{ $attribute['name'] }}">
                                                </div>

                                                <div class="col-md-5">
                                                    @if ($attribute['name'] === 'Supplier')
                                                        <select name="attributes[{{ $key }}][value]"
                                                            class="form-control">
                                                            @foreach ($Suppliers as $supplier)
                                                                <option value="{{ $supplier->name }}"
                                                                    @if ($attribute['value'] == $supplier->name) selected @endif>
                                                                    {{ $supplier->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="text" name="attributes[{{ $key }}][value]"
                                                            class="form-control" placeholder="{{ __('Attribute Value') }}"
                                                            value="{{ $attribute['value'] }}">
                                                    @endif
                                                </div>

                                                <div class="col-md-2">
                                                    <button type="button"
                                                        class="btn btn-danger remove-attribute-btn">{{ __('Remove') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @if (isset($attributes_name))
                                        @foreach ($attributes_name as $key => $name)
                                            <div class="form-group attribute">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <input type="text" name="attributes[{{ $key }}][name]"
                                                            class="form-control" placeholder="{{ __('Attribute Name') }}"
                                                            value="{{ $name }}">
                                                    </div>

                                                    <div class="col-md-5">
                                                        @if ($name === 'Supplier')
                                                            <select name="attributes[{{ $key }}][value]"
                                                                class="form-control" required>
                                                                <option value="">{{ __('Choose Supplier') }}</option>
                                                                @foreach ($Suppliers as $supplier)
                                                                    <option value="{{ $supplier->id }}">
                                                                        {{ $supplier->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <input type="text"
                                                                name="attributes[{{ $key }}][value]"
                                                                class="form-control"
                                                                placeholder="{{ __('Attribute Value') }}" value=""
                                                                required>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-2">
                                                        <button type="button"
                                                            class="btn btn-danger remove-attribute-btn">{{ __('Remove') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            </div> --}}


                            {{-- <div id="setting_attribute" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Nội dung modal -->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">{{ __('voyager::product.setting_attribute') }}</h4>
                                        </div>
                                        <div class="modal-body">
                                            <ul id="attribute-list" class="list-group">
                                                @foreach ($status_attributes as $index => $item)
                                                    <li class="list-group-item">
                                                        <span class="item-name">{{ $item['name'] }}</span>
                                                        <a class="btn {{ $item['status'] == 1 ? 'btn-success' : 'btn-danger' }} btn-sm change-status"
                                                            data-index="{{ $index }}"
                                                            data-status="{{ $item['status'] }}">
                                                            {{ $item['status'] == 1 ? 'Active' : 'Inactive' }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary"
                                                id="saveAttributes">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <!-- ### DETAILS ### -->
                    <div class="panel panel panel-bordered panel-warning">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="icon wb-clipboard"></i>
                                {{ __('voyager::product.details') }}</h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse"
                                    aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="slug">{{ __('voyager::product.slug') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name' => 'slug',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'slug'),
                                ])
                                <input type="text" class="form-control" id="slug" name="slug"
                                    placeholder="slug" {!! isFieldSlugAutoGenerator($dataType, $dataTypeContent, 'slug') !!}
                                    value="{{ $dataTypeContent->slug ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="status">{{ __('voyager::product.status') }}</label>
                                <select class="form-control" name="status">
                                    <option value="PUBLISHED"@if (isset($dataTypeContent->status) && $dataTypeContent->status == 'PUBLISHED') selected="selected" @endif>
                                        {{ __('voyager::product.status_published') }}</option>
                                    <option value="DRAFT"@if (isset($dataTypeContent->status) && $dataTypeContent->status == 'DRAFT') selected="selected" @endif>
                                        {{ __('voyager::product.status_draft') }}</option>
                                    <option value="PENDING"@if (isset($dataTypeContent->status) && $dataTypeContent->status == 'PENDING') selected="selected" @endif>
                                        {{ __('voyager::product.status_pending') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_id">{{ __('voyager::product.category') }}</label>
                                <select class="form-control" name="category_id">
                                    @foreach (App\Models\Design::with('products')->where('is_featured', 1)->get() as $category)
                                        <option
                                            value="{{ $category->id }}"@if (isset($dataTypeContent->category_id) && $dataTypeContent->category_id == $category->id) selected="selected" @endif>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product_code">{{ __('voyager::product.code') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name' => 'product_code',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'product_code'),
                                ])
                                <input required type="text" class="form-control" name="product_code"
                                    id="product_code" placeholder="product code"
                                    value="{{ $dataTypeContent->product_code ?? '' }}">
                                <span id="product-code-error" style="color: red; display: none;"></span>
                            </div>
                            <div class="form-group hidden">
                                <label for="price">{{ __('voyager::product.price') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name' => 'price',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'price'),
                                ])
                                <input type="text" class="form-control" name="price" id="price"
                                    placeholder="Price" value="{{ $dataTypeContent->price ?? 0 }}">
                                <span id="product-code-error" style="color: red; display: none;"></span>
                            </div>
                            <div class="form-group hidden">
                                <label for="meter">{{ __('voyager::product.meter') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name' => 'meter',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'meter'),
                                ])
                                <input type="text" class="form-control" name="meter" id="meter"
                                    placeholder="Price meter" value="{{ $dataTypeContent->meter ?? 0 }}">
                                <span id="product-code-error" style="color: red; display: none;"></span>
                            </div>
                            <div class="form-group hidden">
                                <label for="tax">{{ __('voyager::product.tax') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name' => 'tax',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'tax'),
                                ])
                                <input type="text" class="form-control" name="tax" id="tax"
                                    placeholder="Import tax" value="{{ $dataTypeContent->tax ?? 0 }}">
                                <span id="product-code-error" style="color: red; display: none;"></span>
                            </div>
                            <div class="form-group">
                                <label for="is_featured">{{ __('voyager::generic.featured') }}</label>
                                <input type="checkbox"
                                    name="is_featured"@if (isset($dataTypeContent->is_featured) && $dataTypeContent->is_featured) checked="checked" @endif>
                            </div>
                            <div class="form-group">
                                <label for="is_trending">{{ __('voyager::product.trending') }}</label>
                                <input type="checkbox"
                                    name="is_trending"@if (isset($dataTypeContent->is_trending) && $dataTypeContent->is_trending) checked="checked" @endif>
                            </div>
                            <div class="form-group">
                                <label for="file">{{ __('voyager::product.view_file') }}</label>
                                <input type="file" name="file" id="file">
                                @if (isset($dataTypeContent->file))
                                    @php
                                        $filePath = isset(json_decode($dataTypeContent->file, true)[0])
                                            ? json_decode($dataTypeContent->file, true)[0]
                                            : null;
                                    @endphp
                                    @if (isset($filePath))
                                        <p>Current file: <a href="{{ Storage::url($filePath['download_link']) }}"
                                                download="{{ basename($filePath['original_name']) }}">{{ $filePath['original_name'] }}</a>
                                        </p>
                                    @endif
                                @endif
                                @if ($dataTypeContent->qr_code_path)
                                    <p>Mã QR</p>
                                    <img src="{{ Storage::disk('public')->url($dataTypeContent->qr_code_path) }}"
                                        width="150" alt="QR Code">
                                @endif
                            </div>
                            <div class="form-group">
                                {{-- <label  for="created_at">{{ __('voyager::generic.created_at') }}</label> --}}
                                <input type="hidden" name="created_at"
                                    value="{{ $dataTypeContent->created_at ?? '' }}">
                                <input type="hidden" name="stock_quantity"
                                    value="{{ $dataTypeContent->stock_quantity ?? 0.0 }}">
                            </div>
                        </div>
                    </div>

                    <!-- ### IMAGE ### -->
                    <div class="panel panel-bordered panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="icon wb-image"></i> {{ __('voyager::product.avatar') }}
                            </h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse"
                                    aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            @if (isset($dataTypeContent->image))
                                <img src="{{ filter_var($dataTypeContent->image, FILTER_VALIDATE_URL) ? $dataTypeContent->image : Voyager::image($dataTypeContent->image) }}"
                                    style="width:100%" />
                            @endif
                            <input type="file" name="image">
                        </div>
                    </div>

                    <!-- ### IMAGE ### -->
                    <div class="panel panel-bordered panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="icon wb-image"></i> {{ __('voyager::product.image') }}</h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse"
                                    aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            {{-- @if (isset($dataTypeContent->image))
                                <img src="{{ filter_var($dataTypeContent->image, FILTER_VALIDATE_URL) ? $dataTypeContent->image : Voyager::image( $dataTypeContent->image ) }}" style="width:100%" />
                            @endif
                            <input type="file" name="image"> --}}
                            <div class="form-group">
                                <input type="file" name="images[]" multiple>
                                @if (isset($dataTypeContent->images))
                                    <div id="image-preview">
                                        @foreach (json_decode($dataTypeContent->images, true) as $index => $image)
                                            <div class="image-item"
                                                style="display: inline-block; position: relative; margin: 5px;">
                                                <img src="{{ Storage::disk('public')->url($image) }}"
                                                    style="width: 100px; height: auto;">
                                                <button type="button" class="btn btn-danger btn-sm remove-image"
                                                    data-index="{{ $index }}"
                                                    style="position: absolute; top: 5px; right: 5px;">X</button>
                                                <input type="hidden" name="existing_images[]"
                                                    value="{{ $image }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    <!-- ### SEO CONTENT ### -->
                    <div class="panel panel-bordered panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="icon wb-search"></i>
                                {{ __('voyager::product.seo_content') }}</h3>
                            <div class="panel-actions">
                                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse"
                                    aria-hidden="true"></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            {{-- <div class="form-group">
                                <label for="meta_description">{{ __('voyager::product.meta_description') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name'  => 'meta_description',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'meta_description')
                                ])
                                <textarea class="form-control" name="meta_description">{{ $dataTypeContent->meta_description ?? '' }}</textarea>
                            </div> --}}
                            <div class="form-group">
                                <label for="title">{{ __('voyager::product.seo_title') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name' => 'title',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'title'),
                                ])
                                <input required type="text" class="form-control" name="title"
                                    placeholder="SEO Title" value="{{ $dataTypeContent->title ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="keywords">{{ __('voyager::product.meta_keywords') }}</label>
                                @include('voyager::multilingual.input-hidden', [
                                    '_field_name' => 'keywords',
                                    '_field_trans' => get_field_translations($dataTypeContent, 'keywords'),
                                ])
                                <textarea rows="5" class="form-control" name="keywords">{{ $dataTypeContent->keywords ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @section('submit-buttons')
            <button type="submit" class="btn btn-primary pull-right">
                @if ($edit)
                    {{ __('voyager::product.update') }}
                @else
                    <i class="icon wb-plus-circle"></i> {{ __('voyager::product.new') }}
                @endif
            </button>
        @stop
        @yield('submit-buttons')
    </form>
</div>

<div class="modal fade modal-danger" id="confirm_delete_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}
                </h4>
            </div>

            <div class="modal-body">
                <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                <button type="button" class="btn btn-danger"
                    id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- End Delete File Modal -->
@stop

@section('javascript')
<script>
    var params = {};
    var $file;

    function deleteHandler(tag, isMulti) {
        return function() {
            $file = $(this).siblings(tag);

            params = {
                slug: '{{ $dataType->slug }}',
                filename: $file.data('file-name'),
                id: $file.data('id'),
                field: $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
        };
    }

    $('document').ready(function() {
        $('#slug').slugify();

        $('.toggleswitch').bootstrapToggle();

        //Init datepicker for date fields if data-datepicker attribute defined
        //or if browser does not handle date inputs
        $('.form-group input[type=date]').each(function(idx, elt) {
            if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                elt.type = 'text';
                $(elt).datetimepicker($(elt).data('datepicker'));
            }
        });

        @if ($isModelTranslatable)
            $('.side-body').multilingual({
                "editing": true
            });
        @endif

        $('.side-body input[data-slug-origin]').each(function(i, el) {
            $(el).slugify();
        });

        $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
        $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
        $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
        $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

        $('#confirm_delete').on('click', function() {
            $.post('{{ route('voyager.' . $dataType->slug . '.media.remove') }}', params, function(
                response) {
                if (response &&
                    response.data &&
                    response.data.status &&
                    response.data.status == 200) {

                    toastr.success(response.data.message);
                    $file.parent().fadeOut(300, function() {
                        $(this).remove();
                    })
                } else {
                    toastr.error("Error removing file.");
                }
            });

            $('#confirm_delete_modal').modal('hide');
        });
        $('[data-toggle="tooltip"]').tooltip();
    });

    document.addEventListener('DOMContentLoaded', function() {
        let attributeIndex = Math.max(...Array.from(document.querySelectorAll('[name^="attributes["]'))
            .map(input => parseInt(input.name.match(/\[(\d+)\]/)?.[1] || 0)), 0) + 1;

        // Function to add remove event listener to all remove buttons
        function attachRemoveListeners() {
            document.querySelectorAll('.remove-attribute-btn').forEach(function(button) {
                button.removeEventListener('click', handleRemoveClick);
                button.addEventListener('click', handleRemoveClick);
            });
        }

        // Handle remove button click event
        function handleRemoveClick(event) {
            event.target.closest('.attribute').remove();
        }

        // Initially attach remove listeners to existing buttons
        attachRemoveListeners();

        document.getElementById('add-attribute-btn').addEventListener('click', function() {
            const container = document.getElementById('attributes-container');
            const newField = document.createElement('div');
            newField.classList.add('form-group', 'attribute');
            newField.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <input type="text" name="attributes[${attributeIndex}][name]" class="form-control" placeholder="Attribute Name" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="attributes[${attributeIndex}][value]" class="form-control" placeholder="Attribute Value" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-attribute-btn mt-0">Remove</button>
                </div>
            </div>
        `;
            container.appendChild(newField);
            attributeIndex++;
            attachRemoveListeners(); // Re-attach listeners to include new buttons
        });
    });

    document.querySelectorAll('.remove-image').forEach(function(button) {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            this.parentElement.remove();
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_images[]';
            input.value = index;
            document.getElementById('image-preview').appendChild(input);
        });
    });

    $(document).ready(function() {
        $('#product_code').on('blur', function() {
            var productCode = $(this).val();
            $('#product-code-error').text(productCode + ' already exists.');
            $.ajax({
                url: '{{ route('check.product.code') }}',
                type: 'POST',
                data: {
                    product_code: productCode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.exists) {
                        $('#product-code-error').show();
                        $('#product_code').val('');
                        $('#product_code').focus();
                    } else {
                        $('#product-code-error').hide();
                    }
                }
            });
        });
    });


    // Dữ liệu từ server sẽ được render vào JavaScript thông qua Blade 
    // var items = @_json($status_attributes);

    // Hàm hiển thị các mục vào modal
    function loadAttributes() {
        var attributeList = $('#attribute-list');
        attributeList.empty(); // Xóa danh sách cũ

        items.forEach(function(item, index) {
            var statusLabel = item.status == 1 ? 'Active' : 'Inactive';
            var statusClass = item.status == 1 ? 'btn-success' : 'btn-danger';

            // Thêm phần tử vào modal
            var listItem = `
            <li class="list-group-item">
                <span class="item-name">${item.name}</span>
                <a class="btn ${statusClass} btn-sm change-status" data-index="${index}">
                    ${statusLabel}
                </a>
            </li>
        `;
            attributeList.append(listItem);
        });
    }

    // Khi modal được mở
    $('#setting_attribute').on('show.bs.modal', function() {
        loadAttributes(); // Tải lại danh sách các phần tử vào modal
    });

    // Thay đổi trạng thái khi nhấn vào nút
    $(document).on('click', '.change-status', function() {
        var index = $(this).data('index');
        var newStatus = items[index].status === 1 ? 0 : 1; // Đảo trạng thái
        items[index].status = newStatus;

        // Cập nhật giao diện
        var newLabel = newStatus == 1 ? 'Active' : 'Inactive';
        var newClass = newStatus == 1 ? 'btn-success' : 'btn-danger';
        $(this).text(newLabel).removeClass('btn-success btn-danger').addClass(newClass);
    });

    // Hàm gửi mảng các thay đổi status qua AJAX
    $('#saveAttributes').click(function() {
        // Gửi dữ liệu đã thay đổi
        $.ajax({
            url: '/update-status-attributes', // Địa chỉ route đã định nghĩa trong web.php
            method: 'POST',
            data: {
                items: items, // Mảng items đã thay đổi status
                _token: '{{ csrf_token() }}' // Đừng quên token CSRF để bảo mật
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message); // Thông báo thành công
                    $('#setting_attribute').modal('hide'); // Đóng modal sau khi lưu
                }
            },
            error: function(xhr, status, error) {
                alert('Có lỗi xảy ra khi lưu trạng thái.');
            }
        });
    });
</script>
@stop
