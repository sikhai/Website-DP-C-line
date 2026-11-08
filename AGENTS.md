# Agent Instructions & Architecture Guide - Website DP Celine

Mọi agent / kỹ sư phát triển làm việc trong codebase Website DP Celine (`Website-DP-C-line`) bắt buộc phải đọc và tuân thủ các quy tắc kiến trúc bên dưới trước khi viết hoặc sửa code.

---

## 🖼️ 1. QUY CHUẨN KIẾN TRÚC MEDIA & CLOUDFLARE R2 CDN

### 1.1. Cơ chế Lưu trữ & Phân giải Đường dẫn Ảnh (Zero 404 Mandate)
- **Database (DB)**: Cột `products.image`, `products.images` (JSON), `categories.image`, v.v. **CHỈ LƯU ĐƯỜNG DẪN TƯƠNG ĐỐI (Relative Path)**, ví dụ `products/diva/Diva-102-Beige-crop.webp`.
  - **TUYỆT ĐỐI KHÔNG LƯU FULL URL DOMAIN** (`http://...` hay `https://...`) vào database.
  - Khi chuyển đổi disk hoặc CDN, **không bao giờ sửa dữ liệu trong database**.
- **Tiền tố chuyển vùng lên Cloudflare R2 CDN**:
  Hệ thống quy định danh mục các tiền tố lưu trên Cloudflare R2 (`crm-dpceline-public`, domain `https://cdn.dpceline.com`):
  ```php
  $r2Prefixes = [
      'products',           // Ảnh sản phẩm catalog
      'quotes',             // Báo giá
      'expenses-',          // Chứng từ chi phí
      'print-orders',       // Đơn in
      'project-advances',   // Tạm ứng dự án
      'salary-advances',    // Tạm ứng lương
  ];
  ```
- **Cô lập Asset cục bộ (Local Non-R2 Assets)**:
  Các ảnh hệ thống Voyager như `settings/` (logo website, favicon), `users/` (avatar admin), banner static... hiện vẫn được lưu tại local `storage/app/public/...` và phục vụ qua disk `public` (`/storage/...`). Tuyệt đối không trỏ nhầm lên CDN khi các asset này chưa được đồng bộ lên R2.

### 1.2. Quy tắc Gọi URL Ảnh trong Blade & Code PHP
1. **Helper dùng chung `storage_image_url(?string $path): ?string`**:
   - Được định nghĩa tại [`app/helpers.php`](file:///C:/laragon/www/Website-DP-C-line/app/helpers.php) và tự động nạp qua `AppServiceProvider::register()`.
   - **Bắt buộc dùng `storage_image_url($path)`** cho mọi hiển thị ảnh thông thường trong Blade view.
   - Hàm tự động xử lý:
     - Chuẩn hóa dấu phân cách thư mục: đổi toàn bộ backslash `\` sang slash `/`.
     - Loại bỏ tiền tố thừa `/storage/` hoặc `storage/`.
     - Tự động nhận diện prefix `products/` $\to$ trả về `https://cdn.dpceline.com/{cleanPath}`.
     - Fallback về `http://domain/storage/{cleanPath}` đối với các thư mục local.
2. **Facade `Voyager::image($file, $default = '')`**:
   - Đã được kế thừa và ghi đè thông qua [`App\Services\CustomVoyager`](file:///C:/laragon/www/Website-DP-C-line/app/Services/CustomVoyager.php).
   - Tự động phát hiện prefix `products/` để định tuyến sang CDN, đồng thời fallback về disk Voyager gốc đối với các file khác.
3. **CẤM TUYỆT ĐỐI NỐI CHUỖI HARDCODE TRONG BLADE**:
   - ❌ **CẤM**: `<img src="{{ env('APP_URL') . '/storage/' . $product['image'] }}">`
   - ❌ **CẤM**: `<img src="{{ asset('storage/' . $item->image) }}">`
   - ✅ **BẮT BUỘC**: `<img src="{{ storage_image_url($product['image']) }}">` hoặc `<img src="{{ Voyager::image($item->image) }}">`
   - *Lý do*: Lệnh `env()` trong Blade view sẽ trả về `null` khi server chạy `php artisan config:cache` trên môi trường Production, làm vỡ giao diện website.

---

## ⚡ 2. QUY TRÌNH NÉN ẢNH TỐC ĐỘ CAO (FAST-PATH WEBP)

Hệ thống Website và CRM DP Celine chia sẻ chung chuẩn nén ảnh 2 tầng:

1. **Client-side Compression (Trình duyệt người dùng)**:
   - Module: [`resources/js/shared/image-compressor.js`](file:///C:/laragon/www/Website-DP-C-line/resources/js/shared/image-compressor.js).
   - Tự động nén ảnh về WebP (Max dimension 1920px, Quality 0.82) ngay trên máy khách trước khi gửi lên server.
   - Đảm bảo giải phóng bộ nhớ (No memory leak): gọi `URL.revokeObjectURL()` và reset kích thước canvas `canvas.width = 0; canvas.height = 0;`.
2. **Backend Fast-Path Bypass (Tầng Server PHP)**:
   - File: [`app/Helpers/ImageHelper.php`](file:///C:/laragon/www/Website-DP-C-line/app/Helpers/ImageHelper.php).
   - Kiểm tra 12 bytes magic header `RIFF....WEBP`. Nếu file upload đã là chuẩn WebP, dung lượng $\le 800\text{ KB}$ và không yêu cầu resize đặc biệt $\to$ **Lưu trực tiếp lên R2/disk trong < 3ms**, bỏ qua hoàn toàn bước decode/re-encode nặng nề của GD/Intervention.

---

## 🛡️ 3. QUY TRÌNH DỌN DẸP DISK SERVER PRODUCTION (SAFE PRUNING)

Khi giải phóng dung lượng ổ đĩa local sau khi đã đồng bộ ảnh lên Cloudflare R2:

1. **Parity Check**: Chạy `php artisan products:sync-to-r2` để xác nhận 100% file local đã có bản sao hoàn chỉnh trên R2.
2. **Deploy Code & Clear Cache**:
   ```bash
   git pull origin main
   php artisan config:clear
   php artisan view:clear
   ```
3. **Soft-Delete Simulation (Bắt buộc)**:
   Tuyệt đối **KHÔNG** chạy `rm -rf` ngay lập tức. Đổi tên thư mục sang `products_backup`:
   ```bash
   mv storage/app/public/products storage/app/public/products_backup
   ```
4. **Smoke Test Trình duyệt**:
   Kiểm tra ít nhất 3 trang sản phẩm và trang dịch vụ:
   - DevTools Network tab $\to$ Filter `Img`.
   - 100% ảnh tải từ `cdn.dpceline.com` với HTTP `200 OK`.
   - Không có bất kỳ lỗi 404 nào trong Console và Server Error Log.
5. **Permanent Removal**: Sau 24–48h hoạt động ổn định, xóa thư mục `products_backup` để thu hồi dung lượng đĩa.

---

## 🧪 4. KIỂM THỬ TỰ ĐỘNG (AUTOMATED TESTING MANDATE)

- Mọi thay đổi liên quan đến phân giải đường dẫn ảnh bắt buộc phải vượt qua bộ test:
  ```bash
  php artisan test --filter=StorageImageUrlTest
  php artisan test --filter=MaintenancePageTest
  ```
- File test: [`tests/Unit/StorageImageUrlTest.php`](file:///C:/laragon/www/Website-DP-C-line/tests/Unit/StorageImageUrlTest.php).
- Yêu cầu kiểm thử luôn đạt **100% PASS (0 failure)**.
