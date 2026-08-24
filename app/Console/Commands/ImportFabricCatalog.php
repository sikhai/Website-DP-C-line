<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use ZipArchive;

use App\Models\Design;
use App\Models\Product;
use App\Models\Collection;
use App\Models\Currency;

class ImportFabricCatalog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:fabric-catalog 
                            {--file= : Path to the Excel file (defaults to storage/app/imports/NHẬP LIỆU _VER 01.xlsx)}
                            {--sheet=all : Specific sheet to import (all, designs, nitas, ashley-wilde)}
                            {--clean-orphaned : Scan and clean orphaned product images not in database}
                            {--dry-run : Validate and simulate import without saving to database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Designs and Products from Excel (Design Fabric, Nitas, Ashley Wilde) with WebP image conversion and validation';

    /**
     * Lookup cache for collections
     */
    protected array $collectionMap = [];

    /**
     * Lookup cache for currencies
     */
    protected array $currencyRateMap = [];

    /**
     * Design lookup map: lowercase slug/name/dp_design/code => Design model or simulated array
     */
    protected array $designMap = [];

    /**
     * Set of existing product codes in DB (lowercase)
     */
    protected array $existingProductCodes = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('clean-orphaned')) {
            $this->cleanOrphanedImages();
            return Command::SUCCESS;
        }

        $filePath = $this->option('file');
        if (empty($filePath)) {
            $defaultProjectStorage = storage_path('app/imports/NHẬP LIỆU _VER 01.xlsx');
            if (file_exists($defaultProjectStorage)) {
                $filePath = $defaultProjectStorage;
            } else {
                $filePath = 'D:\DP\NHẬP LIỆU _VER 01.xlsx';
            }
        }

        $sheetOption = strtolower($this->option('sheet'));
        $dryRun = $this->option('dry-run');

        $this->info("=================================================================");
        $this->info("🚀 IMPORT FABRIC CATALOG & PRODUCTS");
        $this->info("   File: " . $filePath);
        $this->info("   Sheet Option: " . $sheetOption);
        $this->info("   Mode: " . ($dryRun ? "🔍 DRY-RUN (Validation only, no DB writes)" : "💾 LIVE IMPORT"));
        $this->info("=================================================================\n");

        if (!file_exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return Command::FAILURE;
        }

        // Initialize Lookups
        $this->initLookups();

        // 1. GIAI ĐOẠN 1: Import Design Fabric
        if (in_array($sheetOption, ['all', 'designs', 'design-fabric', 'design_fabric'])) {
            $this->importDesignFabric($filePath, $dryRun);
            // Refresh design map after import
            $this->refreshDesignMap();
        }

        // 2. GIAI ĐOẠN 2: Import Nitas Products
        if (in_array($sheetOption, ['all', 'nitas'])) {
            $this->importProductsSheet($filePath, 'Nitas', 'drawing2.xml', $dryRun);
        }

        // 3. GIAI ĐOẠN 3: Import Ashley Wilde Products
        if (in_array($sheetOption, ['all', 'ashley-wilde', 'ashley_wilde', 'ashley'])) {
            $this->importProductsSheet($filePath, 'Ashley Wilde', 'drawing1.xml', $dryRun);
        }

        $this->info("\n=================================================================");
        $this->info("🎉 TOÀN BỘ TIẾN TRÌNH IMPORT ĐÃ HOÀN TẤT!");
        $this->info("=================================================================");

        return Command::SUCCESS;
    }

    /**
     * Initialize lookup caches
     */
    protected function initLookups(): void
    {
        // 1. Collections lookup
        $this->collectionMap['NITAS'] = Collection::where('slug', 'nitas')->orWhere('name', 'Nitas')->value('id') ?? 12;
        $this->collectionMap['ASHLEY WILDE'] = Collection::where('slug', 'ashley-wilde')->orWhere('name', 'Ashley Wilde')->value('id') ?? 44;

        // 2. Currencies lookup
        $currencies = Currency::all();
        foreach ($currencies as $c) {
            $rate = (float) $c->exchange_rate;
            if ($rate > 0) {
                $this->currencyRateMap[(int) $rate] = $c->id;
            }
            if ($c->code === 'USD') $this->currencyRateMap['USD'] = $c->id;
            if ($c->code === 'GBP') $this->currencyRateMap['GBP'] = $c->id;
            if ($c->code === 'EUR') $this->currencyRateMap['EUR'] = $c->id;
            if ($c->code === 'VND') $this->currencyRateMap['VND'] = $c->id;
        }

        // Default fallbacks
        $this->currencyRateMap[28000] = $this->currencyRateMap[28000] ?? 1;
        $this->currencyRateMap[39000] = $this->currencyRateMap[39000] ?? 2;
        $this->currencyRateMap[36000] = $this->currencyRateMap[36000] ?? 2;
        $this->currencyRateMap[33000] = $this->currencyRateMap[33000] ?? 3;
        $this->currencyRateMap[1] = $this->currencyRateMap[1] ?? 4;

        // 3. Existing product codes in DB
        $this->existingProductCodes = Product::pluck('product_code')
            ->filter()
            ->map(fn($c) => strtolower(trim($c)))
            ->flip()
            ->toArray();

        // 4. Design map
        $this->refreshDesignMap();
    }

    /**
     * Refresh design lookup map from database
     */
    protected function refreshDesignMap(): void
    {
        $designs = Design::all();

        foreach ($designs as $d) {
            $this->registerDesignInMap($d);
        }
    }

    /**
     * Helper to index a design under all its possible aliases
     */
    protected function registerDesignInMap($designObj): void
    {
        $name = is_object($designObj) ? ($designObj->name ?? '') : ($designObj['name'] ?? '');
        $slug = is_object($designObj) ? ($designObj->slug ?? '') : ($designObj['slug'] ?? '');
        
        $aliases = [$name, $slug, Str::slug($name)];

        $attrs = [];
        if ($designObj instanceof \Illuminate\Database\Eloquent\Model) {
            $rawAttrs = $designObj->getRawOriginal('attributes');
            if ($rawAttrs) {
                $attrs = is_array($rawAttrs) ? $rawAttrs : (json_decode($rawAttrs, true) ?: []);
            }
        } elseif (is_object($designObj) && isset($designObj->attributes)) {
            $attrs = is_array($designObj->attributes) ? $designObj->attributes : (json_decode($designObj->attributes, true) ?: []);
        } elseif (is_array($designObj) && isset($designObj['attributes'])) {
            $attrs = is_array($designObj['attributes']) ? $designObj['attributes'] : (json_decode($designObj['attributes'], true) ?: []);
        }

        foreach (['Design', 'Dp Céline Design', 'Keyword', 'design_code'] as $key) {
            if (!empty($attrs[$key])) {
                $aliases[] = $attrs[$key];
                $aliases[] = Str::slug($attrs[$key]);
            }
        }

        foreach ($aliases as $alias) {
            $clean = strtolower(trim((string)$alias));
            if ($clean !== '') {
                $this->designMap[$clean] = $designObj;
            }
        }
    }

    /**
     * Giai đoạn 1: Import Sheet Design Fabric
     */
    protected function importDesignFabric(string $filePath, bool $dryRun): void
    {
        $this->info("\n--- [GIAI ĐOẠN 1] Bắt đầu xử lý Sheet: Design Fabric ---");

        $rows = $this->readSheetRows($filePath, 'Design Fabric');
        if (empty($rows)) {
            $this->warn("Sheet 'Design Fabric' rỗng hoặc không tìm thấy!");
            return;
        }

        $header = $rows[0];
        $dataRows = array_slice($rows, 1);

        $this->info("Tìm thấy " . count($dataRows) . " dòng trong sheet Design Fabric.");

        $attributeColumns = [
            'Supplier', 'Collection', 'Design', 'Dp Céline Collection', 'Dp Céline Design',
            'Composition', 'Roll Width', 'Roll length', 'MOQ', 'Keyword', 'Usage',
            'FR treatment', 'SR treatment', 'Repeat', 'Weight (g/m2)', 'Abrasion',
            'Tensile Strength', 'Tear Growth Resistance', 'Seam Slippage Resistance', 'Pilling',
            'Color Fastness to Rubbing', 'Color Fastness to Artifficial Light',
            'Color Fastness to Sea Water', 'Color Fastness to Chlorine Water',
            'Snagging Test', 'Water Repellent', 'Anti Bacteria', 'status'
        ];

        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        $bar = $this->output->createProgressBar(count($dataRows));
        $bar->start();

        foreach ($dataRows as $r) {
            $bar->advance();

            // Row validation
            $dCode = trim((string)($r[1] ?? ''));
            $supplier = trim((string)($r[5] ?? ''));
            $designName = trim((string)($r[7] ?? ''));
            $dpDesign = trim((string)($r[9] ?? ''));

            if ($dCode === '' && $dpDesign === '' && $designName === '') {
                $skippedCount++;
                continue;
            }

            $primaryName = $dpDesign !== '' ? $dpDesign : ($dCode !== '' ? $dCode : $designName);
            $slug = Str::slug($primaryName);

            // Determine Collection parent_id
            $supUpper = strtoupper($supplier);
            $parentId = $this->collectionMap['ASHLEY WILDE'];
            if (str_contains($supUpper, 'NITAS')) {
                $parentId = $this->collectionMap['NITAS'];
            }

            // Numeric values
            $currencyRate = $this->sanitizeDecimal($r[2] ?? null, 28000);
            $price = $this->sanitizeDecimal($r[3] ?? null, 0);
            $importTax = $this->sanitizeDecimal($r[4] ?? null, 1.12);

            // Determine currency_id
            $currencyId = $this->currencyRateMap[(int) $currencyRate] ?? (str_contains($supUpper, 'ASHLEY') ? 2 : 1);

            // Build Attributes JSON (chỉ lưu các thuộc tính có giá trị thực tế, bỏ qua thuộc tính trống)
            $attributesMap = [];
            if ($dCode !== '') {
                $attributesMap['design_code'] = $dCode;
            }

            foreach ($header as $colIdx => $colName) {
                if ($colName && in_array($colName, $attributeColumns)) {
                    $val = isset($r[$colIdx]) ? trim((string)$r[$colIdx]) : '';
                    if ($val !== '') {
                        $attributesMap[$colName] = $val;
                    }
                }
            }

            if ($dryRun) {
                // Register in memory map for simulation
                $simulatedDesign = (object)[
                    'id' => 999900 + $importedCount,
                    'name' => $primaryName,
                    'slug' => $slug,
                    'price' => $price,
                    'import_tax' => $importTax,
                    'attributes' => $attributesMap
                ];
                $this->registerDesignInMap($simulatedDesign);
                $this->registerDesignInMap(['name' => $dCode, 'slug' => Str::slug($dCode), 'attributes' => $attributesMap]);
                $this->registerDesignInMap(['name' => $designName, 'slug' => Str::slug($designName), 'attributes' => $attributesMap]);

                $importedCount++;
                continue;
            }

            // Save to DB
            $design = Design::where('slug', $slug)
                ->orWhere(function ($q) use ($primaryName, $parentId) {
                    $q->where('name', $primaryName)->where('parent_id', $parentId);
                })
                ->first();

            $isNew = false;
            if (!$design) {
                $design = new Design();
                $isNew = true;
            }

            $design->parent_id = $parentId;
            $design->name = $primaryName;
            $design->slug = $slug;
            $design->price = $price;
            $design->currency_rate = $currencyRate;
            $design->import_tax = $importTax;
            $design->currency_id = $currencyId;
            $design->attributes = json_encode($attributesMap, JSON_UNESCAPED_UNICODE);
            $design->is_featured = 1;
            $design->is_trending = 0;
            $design->save();

            // Register all aliases in memory map
            $this->registerDesignInMap($design);
            $this->registerDesignInMap(['name' => $dCode, 'slug' => Str::slug($dCode), 'id' => $design->id, 'price' => $price, 'import_tax' => $importTax]);
            $this->registerDesignInMap(['name' => $designName, 'slug' => Str::slug($designName), 'id' => $design->id, 'price' => $price, 'import_tax' => $importTax]);

            if ($isNew) {
                $importedCount++;
            } else {
                $updatedCount++;
            }
        }

        $bar->finish();
        $this->newLine();

        $this->table(
            ['Sheet', 'Mới thêm', 'Cập nhật', 'Bỏ qua (Trống)', 'Trạng thái'],
            [['Design Fabric', $importedCount, $updatedCount, $skippedCount, $dryRun ? 'DRY-RUN (Simulated)' : 'SUCCESS']]
        );
    }

    /**
     * Giai đoạn 2 & 3: Import Sheet Products (Nitas / Ashley Wilde)
     */
    protected function importProductsSheet(string $filePath, string $sheetName, string $drawingXmlFile, bool $dryRun): void
    {
        $this->info("\n--- Bắt đầu xử lý Sheet Sản phẩm: {$sheetName} ---");

        $rows = $this->readSheetRows($filePath, $sheetName);
        if (empty($rows)) {
            $this->warn("Sheet '{$sheetName}' rỗng hoặc không tìm thấy!");
            return;
        }

        $dataRows = array_slice($rows, 1);
        $totalRows = count($dataRows);
        $this->info("Tổng số dòng dữ liệu: {$totalRows}");

        // 1. Trích xuất bản đồ hình ảnh nhúng từ OpenXML Drawing
        $this->info("Đang ánh xạ hình ảnh nhúng từ OpenXML Drawing ({$drawingXmlFile})...");
        $drawingImageMap = $this->extractDrawingImageMap($filePath, $sheetName, $drawingXmlFile);
        $this->info("Đã ánh xạ " . count($drawingImageMap) . " hình ảnh nhúng vào các dòng.");

        // 2. Mở file Zip để sẵn sàng stream ảnh
        $zip = new ZipArchive();
        $zipOpen = $zip->open($filePath);
        if ($zipOpen !== true) {
            $this->error("Không thể mở file Excel dạng ZipArchive!");
            return;
        }

        $importedCount = 0;
        $skippedMissingDesign = [];
        $skippedDuplicateProductCode = [];
        $skippedEmptyRow = 0;

        $bar = $this->output->createProgressBar($totalRows);
        $bar->start();

        foreach ($dataRows as $zeroIdx => $r) {
            $bar->advance();
            $rowNum = $zeroIdx + 2; // 1-indexed row in Excel (Header is row 1)

            $name = trim((string)($r[0] ?? ''));
            $productCode = trim((string)($r[1] ?? ''));
            $designCode = trim((string)($r[2] ?? ''));
            $color = trim((string)($r[3] ?? ''));

            if ($productCode === '' && $name === '' && $designCode === '') {
                $skippedEmptyRow++;
                continue;
            }

            // ==========================================
            // VALIDATE 1: Chỉ tồn tại design_code thì mới tiếp tục
            // ==========================================
            $dKey = strtolower($designCode);
            $dKeySlug = Str::slug($designCode);
            $dKeyName = strtolower($name);
            $dKeyNameSlug = Str::slug($name);

            $design = $this->designMap[$dKey]
                ?? $this->designMap[$dKeySlug]
                ?? $this->designMap[$dKeyName]
                ?? $this->designMap[$dKeyNameSlug]
                ?? null;

            if (!$design) {
                $skippedMissingDesign[] = [
                    'row' => $rowNum,
                    'name' => $name,
                    'product_code' => $productCode,
                    'design_code' => $designCode
                ];
                continue;
            }

            // ==========================================
            // VALIDATE 2: Product_code không trùng mới pass
            // ==========================================
            $pKey = strtolower($productCode);
            if (isset($this->existingProductCodes[$pKey])) {
                $skippedDuplicateProductCode[] = [
                    'row' => $rowNum,
                    'product_code' => $productCode,
                    'reason' => 'Đã tồn tại trong DB hoặc trùng lặp trong file'
                ];
                continue;
            }

            // Đánh dấu mã này đã được xử lý để tránh trùng trong cùng 1 file
            $this->existingProductCodes[$pKey] = true;

            // ==========================================
            // XỬ LÝ HÌNH ẢNH: Trích xuất & Convert sang WebP theo product_code
            // ==========================================
            $imagePath = null;
            if (isset($drawingImageMap[$rowNum])) {
                $zipImgPath = $drawingImageMap[$rowNum];
                if (!$dryRun) {
                    $imgBinary = $zip->getFromName($zipImgPath);
                    if ($imgBinary) {
                        $imagePath = $this->processAndSaveWebpImage($imgBinary, $productCode, 'main_');
                    }
                } else {
                    $imagePath = 'products/main_' . Str::slug($productCode) . '.webp';
                }
            }

            if ($dryRun) {
                $importedCount++;
                continue;
            }

            // ==========================================
            // LƯU PRODUCT VÀO DATABASE
            // ==========================================
            $productTitle = $name . ($color !== '' ? " {$color}" : '');
            $productSlug = Str::slug($productCode !== '' ? $productCode : $productTitle);

            // Đảm bảo slug là duy nhất
            $slugBase = $productSlug;
            $counter = 1;
            while (Product::where('slug', $productSlug)->exists()) {
                $productSlug = $slugBase . '-' . $counter;
                $counter++;
            }

            $designId = is_object($design) ? $design->id : ($design['id'] ?? null);
            $designPrice = is_object($design) ? ($design->price ?? 0) : ($design['price'] ?? 0);
            $designTax = is_object($design) ? ($design->import_tax ?? 0) : ($design['import_tax'] ?? 0);

            $product = new Product();
            $product->name = $productTitle;
            $product->title = $productTitle;
            $product->product_code = $productCode;
            $product->color = $color;
            $product->slug = $productSlug;
            $product->category_id = $designId;
            $product->price = $designPrice;
            $product->tax = $designTax;
            $product->stock_quantity = 0.00;
            $product->meter = 0.00;
            $product->status = 'PUBLISHED';
            $product->is_featured = 1;
            $product->is_trending = '0';
            $product->image = $imagePath;
            $product->save();

            // ProductObserver sẽ tự động kích hoạt tạo QR Code PNG tại storage/app/public/qrcodes/

            $importedCount++;
        }

        $zip->close();
        $bar->finish();
        $this->newLine(2);

        // Báo cáo tổng kết Sheet
        $this->table(
            ['Chỉ số', 'Số lượng', 'Ghi chú'],
            [
                ['Tổng số dòng trong Sheet', $totalRows, ''],
                ['Import thành công', $importedCount, $dryRun ? 'Đạt chuẩn (DRY-RUN)' : 'Đã lưu DB & chuyển đổi WebP'],
                ['Bỏ qua do THIẾU Design', count($skippedMissingDesign), 'Không tìm thấy design_code trong hệ thống'],
                ['Bỏ qua do TRÙNG Product Code', count($skippedDuplicateProductCode), 'Đã có trong DB hoặc trùng lặp'],
                ['Dòng rỗng', $skippedEmptyRow, ''],
            ]
        );

        if (!empty($skippedMissingDesign)) {
            $this->warn("\n⚠️ Danh sách dòng bị bỏ qua do thiếu Design (Tối đa 10 dòng đầu):");
            $sampleMissing = array_slice($skippedMissingDesign, 0, 10);
            $this->table(['Row', 'Name', 'Product Code', 'Missing Design Code'], $sampleMissing);
            if (count($skippedMissingDesign) > 10) {
                $this->warn("... và còn " . (count($skippedMissingDesign) - 10) . " dòng khác.");
            }
        }

        if (!empty($skippedDuplicateProductCode)) {
            $this->warn("\n⚠️ Danh sách dòng bị bỏ qua do trùng Product Code (Tối đa 10 dòng đầu):");
            $sampleDup = array_slice($skippedDuplicateProductCode, 0, 10);
            $this->table(['Row', 'Product Code', 'Lý do'], $sampleDup);
        }
    }

    /**
     * Convert and save image to WebP format with deterministic naming (main_{product_code}.webp)
     */
    protected function processAndSaveWebpImage(string $imgBinary, string $productCode, string $prefix = 'main_'): ?string
    {
        try {
            $codeSlug = Str::slug($productCode);
            $filename = ($prefix ?: 'main_') . ($codeSlug ?: uniqid()) . '.webp';

            $webpImage = Image::make($imgBinary)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 50);

            $path = 'products/' . $filename;
            Storage::disk('public')->put($path, (string) $webpImage);

            return $path;
        } catch (\Throwable $e) {
            $this->error("Lỗi chuyển đổi WebP cho mã {$productCode}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Scan and remove orphaned product images in storage that are not referenced in the database
     */
    protected function cleanOrphanedImages(): void
    {
        $this->info("=================================================================");
        $this->info("🧹 DỌN DẸP ẢNH MỒ CÔI (ORPHANED IMAGES)");
        $this->info("=================================================================");

        $this->info("Đang quét toàn bộ ảnh đang được liên kết trong Database (Products, Designs, Collections)...");
        
        $activeImages = [];

        // 1. Product single & multiple images
        foreach (Product::whereNotNull('image')->pluck('image')->filter() as $img) {
            $activeImages[trim($img)] = true;
        }
        foreach (Product::whereNotNull('images')->pluck('images')->filter() as $imgs) {
            $decoded = is_array($imgs) ? $imgs : json_decode($imgs, true);
            if (is_array($decoded)) {
                foreach ($decoded as $img) {
                    if ($img) $activeImages[trim($img)] = true;
                }
            }
        }

        // 2. Design images
        foreach (Design::whereNotNull('images')->pluck('images')->filter() as $imgs) {
            $decoded = is_array($imgs) ? $imgs : json_decode($imgs, true);
            if (is_array($decoded)) {
                foreach ($decoded as $img) {
                    if ($img) $activeImages[trim($img)] = true;
                }
            }
        }

        // 3. Collection images
        foreach (Collection::whereNotNull('image')->pluck('image')->filter() as $img) {
            $activeImages[trim($img)] = true;
        }

        $this->info("Tìm thấy " . count($activeImages) . " file ảnh đang được dùng hợp lệ trong Database.");

        $allFiles = Storage::disk('public')->files('products');
        $this->info("Tìm thấy " . count($allFiles) . " file ảnh trong thư mục storage/app/public/products/.");

        $deletedCount = 0;
        $bar = $this->output->createProgressBar(count($allFiles));
        $bar->start();

        foreach ($allFiles as $file) {
            $bar->advance();
            // Nếu file này KHÔNG có trong bất kỳ bảng nào của Database -> mới xóa
            if (!isset($activeImages[$file])) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("🎉 Đã dọn dẹp thành công {$deletedCount} file ảnh mồ côi (ảnh rác)!");
    }

    /**
     * Parse drawing XML and relationships to map Excel row to image file path in zip
     */
    protected function extractDrawingImageMap(string $filePath, string $sheetName, string $drawingXmlFile): array
    {
        $rowToImage = [];
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return $rowToImage;
        }

        $drawingPath = 'xl/drawings/' . $drawingXmlFile;
        $drawingRelsPath = 'xl/drawings/_rels/' . $drawingXmlFile . '.rels';

        if (!$zip->locateName($drawingPath) || !$zip->locateName($drawingRelsPath)) {
            $zip->close();
            return $rowToImage;
        }

        // 1. Read Drawing Rels
        $relsXmlStr = $zip->getFromName($drawingRelsPath);
        $relsXml = simplexml_load_string($relsXmlStr);
        $relMap = [];
        foreach ($relsXml->Relationship as $rel) {
            $rId = (string) $rel['Id'];
            $target = (string) $rel['Target'];
            $targetPath = str_replace('../', 'xl/', $target);
            $relMap[$rId] = $targetPath;
        }

        // 2. Read Drawing XML
        $drawXmlStr = $zip->getFromName($drawingPath);
        $drawXml = simplexml_load_string($drawXmlStr);
        $namespaces = $drawXml->getNamespaces(true);
        $drawXml->registerXPathNamespace('xdr', $namespaces['xdr'] ?? 'http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing');
        $drawXml->registerXPathNamespace('a', $namespaces['a'] ?? 'http://schemas.openxmlformats.org/drawingml/2006/main');

        foreach ($drawXml->xpath('//xdr:twoCellAnchor | //xdr:oneCellAnchor') as $anchor) {
            $from = $anchor->xpath('xdr:from');
            $blip = $anchor->xpath('.//a:blip');

            if (!empty($from) && !empty($blip)) {
                $fromRow = (int) $from[0]->xpath('xdr:row')[0];
                $blipAttrs = $blip[0]->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                $embedId = (string) ($blipAttrs['embed'] ?? '');

                if ($embedId && isset($relMap[$embedId])) {
                    $rowNum = $fromRow + 1; // 1-indexed Excel row
                    $rowToImage[$rowNum] = $relMap[$embedId];
                }
            }
        }

        $zip->close();
        return $rowToImage;
    }

    /**
     * Fast & memory-safe sheet reader via ZipArchive + SimpleXML
     */
    protected function readSheetRows(string $filePath, string $sheetName): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Get sheet rId from workbook.xml
        $wbXml = simplexml_load_string($zip->getFromName('xl/workbook.xml'));
        $wbRels = simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels'));

        $relMap = [];
        foreach ($wbRels->Relationship as $rel) {
            $relMap[(string)$rel['Id']] = (string)$rel['Target'];
        }

        $sheetTarget = null;
        foreach ($wbXml->sheets->sheet as $s) {
            if ((string)$s['name'] === $sheetName) {
                $rId = (string)$s->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                $sheetTarget = 'xl/' . $relMap[$rId];
                break;
            }
        }

        if (!$sheetTarget || !$zip->locateName($sheetTarget)) {
            $zip->close();
            return [];
        }

        // 2. Read shared strings
        $sharedStrings = [];
        if ($zip->locateName('xl/sharedStrings.xml')) {
            $sstXml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
            foreach ($sstXml->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string)$si->t;
                } elseif (isset($si->r)) {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string)$r->t;
                    }
                    $sharedStrings[] = $text;
                } else {
                    $sharedStrings[] = '';
                }
            }
        }

        // 3. Read sheet XML
        $sheetXmlStr = $zip->getFromName($sheetTarget);
        $zip->close();

        $sheetXml = simplexml_load_string($sheetXmlStr);
        $rows = [];

        foreach ($sheetXml->sheetData->row as $row) {
            $rowNum = (int)$row['r'];
            $rowData = [];
            $maxColIdx = 0;

            foreach ($row->c as $cell) {
                $cellRef = (string)$cell['r'];
                preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                $colLetters = $matches[1] ?? 'A';
                $colIdx = $this->columnLettersToNumber($colLetters) - 1;

                $val = (string)$cell->v;
                $type = (string)$cell['t'];

                if ($type === 's') {
                    $cellValue = $sharedStrings[(int)$val] ?? '';
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $cellValue = (string)$cell->is->t;
                } else {
                    $cellValue = $val;
                }

                $rowData[$colIdx] = $cellValue;
                if ($colIdx > $maxColIdx) {
                    $maxColIdx = $colIdx;
                }
            }

            // Fill nulls for missing column keys
            $completeRow = [];
            for ($i = 0; $i <= $maxColIdx; $i++) {
                $completeRow[$i] = $rowData[$i] ?? null;
            }

            $rows[] = $completeRow;
        }

        return $rows;
    }

    /**
     * Convert Excel column letters (A, B, ..., Z, AA, AB) to 1-indexed column number
     */
    protected function columnLettersToNumber(string $letters): int
    {
        $num = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord(strtoupper($letters[$i])) - ord('A') + 1);
        }
        return $num;
    }

    /**
     * Sanitize decimal string to float
     */
    protected function sanitizeDecimal($value, float $default = 0.0): float
    {
        if ($value === null || $value === '' || $value === 'N/A') {
            return $default;
        }
        $clean = preg_replace('/[^0-9.]/', '', (string)$value);
        return is_numeric($clean) ? (float)$clean : $default;
    }
}
