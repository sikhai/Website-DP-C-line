/**
 * Client-side Image Pre-compressor to WebP
 * Tự động nén ảnh sang định dạng WebP ngay trên trình duyệt trước khi upload,
 * giúp giảm 90% dung lượng, tải lên siêu nhanh và không tốn CPU server.
 */
export async function compressImageToWebp(file, options = {}) {
    // Nếu không phải ảnh (PDF, Excel,...) thì giữ nguyên
    if (!file || !file.type.startsWith('image/')) {
        return file;
    }

    const {
        maxWidth = 1600,
        maxHeight = 1600,
        quality = 0.82,
        minSizeToCompress = 100 * 1024, // 100KB
    } = options;

    // Nếu ảnh đã quá nhỏ (< 100KB) thì không cần nén
    if (file.size <= minSizeToCompress && file.type === 'image/webp') {
        return file;
    }

    return new Promise((resolve) => {
        const objectUrl = URL.createObjectURL(file);
        const img = new Image();
        img.src = objectUrl;

        img.onload = () => {
            URL.revokeObjectURL(objectUrl);
            let { width, height } = img;

            // Tính toán tỷ lệ resize giữ nguyên aspect ratio
            if (width > maxWidth || height > maxHeight) {
                if (width / height > maxWidth / maxHeight) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                } else {
                    width = Math.round((width * maxHeight) / height);
                    height = maxHeight;
                }
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob(
                (blob) => {
                    // Thu hồi bộ nhớ canvas GPU ngay sau khi xuất blob
                    canvas.width = 0;
                    canvas.height = 0;

                    if (!blob) {
                        resolve(file); // Fallback về file gốc nếu trình duyệt lỗi
                        return;
                    }

                    // Nếu file nén lại nặng hơn file gốc (hiếm khi xảy ra), dùng file gốc
                    if (blob.size >= file.size && file.type === 'image/webp') {
                        resolve(file);
                        return;
                    }

                    // Đổi tên đuôi thành .webp
                    const originalName = file.name;
                    const baseName = originalName.substring(0, originalName.lastIndexOf('.')) || originalName;
                    const newFileName = `${baseName}.webp`;

                    const compressedFile = new File([blob], newFileName, {
                        type: 'image/webp',
                        lastModified: Date.now(),
                    });

                    resolve(compressedFile);
                },
                'image/webp',
                quality
            );
        };

        img.onerror = () => {
            URL.revokeObjectURL(objectUrl);
            resolve(file);
        };
    });
}

/**
 * Tự động gắn bộ nén WebP vào input[type="file"]
 * @param {HTMLInputElement} inputElement 
 * @param {Object} options 
 */
export function bindAutoWebpCompression(inputElement, options = {}) {
    if (!inputElement || inputElement.dataset.webpBound) return;
    inputElement.dataset.webpBound = 'true';

    inputElement.addEventListener('change', async (e) => {
        const files = Array.from(e.target.files || []);
        if (files.length === 0) return;

        const dataTransfer = new DataTransfer();
        let changed = false;

        for (const file of files) {
            if (file.type.startsWith('image/')) {
                const compressed = await compressImageToWebp(file, options);
                dataTransfer.items.add(compressed);
                changed = true;
            } else {
                dataTransfer.items.add(file);
            }
        }

        if (changed) {
            inputElement.files = dataTransfer.files;
        }
    });
}

// Global expose cho Blade/vanilla JS nếu cần
if (typeof window !== 'undefined') {
    window.compressImageToWebp = compressImageToWebp;
    window.bindAutoWebpCompression = bindAutoWebpCompression;
}
