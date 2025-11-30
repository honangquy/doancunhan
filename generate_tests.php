<?php

use Illuminate\Support\Str;

// Load CSV
$csvFile = 'test_plan.csv';
if (!file_exists($csvFile)) {
    die("Lỗi: Không tìm thấy file $csvFile ở thư mục gốc.\n");
}

$file = fopen($csvFile, 'r');

// SỬA LỖI: Thêm đầy đủ tham số cho fgetcsv (length, separator, enclosure, escape)
// Bỏ qua dòng tiêu đề
$headers = fgetcsv($file, null, ',', '"', '\\');

$pestTests = [];
$duskTests = [];

echo "Đang đọc file CSV...\n";

// SỬA LỖI: Cập nhật tham số trong vòng lặp
while (($row = fgetcsv($file, null, ',', '"', '\\')) !== false) {
    // Mapping cột dựa trên file CSV của bạn:
    // 0: STT, 1: Mã TC, 2: Tên TC, 3: Route, 4: Mong đợi, 5: Tool

    // Kiểm tra nếu dòng trống hoặc không đủ cột thì bỏ qua
    if (count($row) < 2) continue;

    $tcCode = trim($row[1] ?? '');
    $tcName = trim($row[2] ?? '');
    $route = trim($row[3] ?? '');
    $expect = trim($row[4] ?? '');
    $tool = strtolower(trim($row[5] ?? ''));

    if (empty($tcCode)) continue;

    // Phân loại Module dựa trên Mã TC (VD: TC-AUTH-001 -> Module: Auth)
    // Regex lấy phần chữ giữa TC- và -XXX
    if (preg_match('/TC-([A-Z]+)-/i', $tcCode, $matches)) {
        $module = ucfirst(strtolower($matches[1]));
    } else {
        $module = 'General';
    }

    $data = [
        'code' => $tcCode,
        'name' => $tcName,
        'route' => $route,
        'expect' => $expect
    ];

    // Phân loại Tool
    if (str_contains($tool, 'dusk')) {
        $duskTests[$module][] = $data;
    } else {
        // Mặc định là Pest cho Backend/API
        $pestTests[$module][] = $data;
    }
}
fclose($file);

// --- 1. SINH FILE PEST PHP (tests/Feature) ---
echo "--- Đang tạo Pest Tests ---\n";
if (!is_dir('tests/Feature/Generated')) mkdir('tests/Feature/Generated', 0777, true);

foreach ($pestTests as $module => $tests) {
    $className = $module . 'Test';
    $filePath = "tests/Feature/Generated/{$className}.php";

    $content = "<?php\n\nuse App\Models\User;\nuse function Pest\Laravel\{get, post, put, delete, actingAs};\n\n";
    $content .= "/**\n * TEST MODULE: $module\n * Tự động sinh bởi script.\n */\n\n";

    foreach ($tests as $test) {
        // Escape nháy đơn trong tên để tránh lỗi syntax
        $safeName = str_replace("'", "\'", $test['name']);
        $description = "{$test['code']}: {$safeName}";

        $content .= <<<PHP
test('$description', function () {
    // TODO: Triển khai logic kiểm thử
    // Route: {$test['route']}
    // Mong đợi: {$test['expect']}

    \$response = get('{$test['route']}');

    \$response->assertStatus(200);
})->todo();

PHP;
        $content .= "\n";
    }

    file_put_contents($filePath, $content);
    echo "✔ Đã tạo: $filePath\n";
}

// --- 2. SINH FILE LARAVEL DUSK (tests/Browser) ---
echo "\n--- Đang tạo Dusk Tests ---\n";
if (!is_dir('tests/Browser/Generated')) mkdir('tests/Browser/Generated', 0777, true);

foreach ($duskTests as $module => $tests) {
    $className = $module . 'BrowserTest';
    $filePath = "tests/Browser/Generated/{$className}.php";

    $content = "<?php\n\nnamespace Tests\Browser\Generated;\n\nuse Laravel\Dusk\Browser;\nuse Tests\DuskTestCase;\n\n";
    $content .= "class $className extends DuskTestCase\n{\n";

    foreach ($tests as $test) {
        $funcName = 'test_' . str_replace('-', '_', $test['code']);

        // Xử lý route để tránh lỗi syntax nếu route trống
        $visitRoute = empty($test['route']) ? '/' : $test['route'];
        // Chỉ lấy phần URL path, bỏ method (GET/POST) nếu có
        $visitRoute = preg_replace('/^(GET|POST|PUT|DELETE)\s+/', '', $visitRoute);

        $content .= <<<PHP
    /**
     * {$test['code']}: {$test['name']}
     * Expect: {$test['expect']}
     */
    public function $funcName(): void
    {
        \$this->browse(function (Browser \$browser) {
            \$browser->visit('$visitRoute')
                    ->assertSee('Laravel'); // TODO: Update assertion
        });
    }

PHP;
        $content .= "\n";
    }

    $content .= "}\n";
    file_put_contents($filePath, $content);
    echo "✔ Đã tạo: $filePath\n";
}

echo "\nHOÀN THÀNH! Kiểm tra thư mục tests/Feature/Generated và tests/Browser/Generated.\n";
