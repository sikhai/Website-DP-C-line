<style>
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px; /* Khoảng cách giữa các ảnh */
        margin-bottom: 20px;
    }
    
    .image-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
        background: #f9f9f9;
        max-width: 250px; /* Giới hạn chiều rộng */
    }
    
    .image-item img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
    }
    
    .image-item input {
        width: 100%;
        margin-top: 5px;
    }
    
    .image-item button {
        margin-top: 5px;
    }
    </style>
    
<div class="form-group">
    <label>Hình ảnh và Caption</label>

    @php
        $value = old($row->field, $dataTypeContent->{$row->field} ?? '[]');
        $images = json_decode($value, true);
    @endphp

    <div class="image-preview-container">
        @if ($images)
            @foreach ($images as $index => $image)
                <div class="image-item">
                    <img src="{{ Storage::url($image['path']) }}" style="max-width: 100%; height: auto;">
                    <input type="hidden" name="{{ $row->field }}[existing][{{ $index }}][path]" value="{{ $image['path'] }}">
                    <input type="text" name="{{ $row->field }}[existing][{{ $index }}][caption]" value="{{ $image['caption'] ?? '' }}" class="form-control" placeholder="Nhập caption">
                    <button type="button" class="btn btn-danger remove-image">Xóa</button>
                </div>
            @endforeach
        @endif
    </div>

    <input type="file" name="images[]" multiple class="form-control image-upload mt-2">
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.image-upload').addEventListener('change', function(event) {
        let files = event.target.files;
        let container = document.querySelector('.image-preview-container');

        for (let i = 0; i < files.length; i++) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let div = document.createElement('div');
                div.classList.add('image-item');
                div.innerHTML = `
                    <img src="${e.target.result}" style="max-width: 100px; height: auto;">
                    <input type="hidden" name="new_images[${i}][path]" value="">
                    <input type="text" name="new_images[${i}][caption]" class="form-control" placeholder="Nhập caption">
                    <button type="button" class="btn btn-danger remove-image">Xóa</button>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(files[i]);
        }
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-image')) {
            event.target.parentElement.remove();
        }
    });
});
</script>
