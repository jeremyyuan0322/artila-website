<?php
ob_end_flush();  // 关闭输出缓冲区
require_once 'vendor/autoload.php';

use \ConvertApi\ConvertApi;

// 設置您的 API 密鑰
ConvertApi::setApiSecret('O6ihftvqEaLdQ3ar');

// 指定要搜索的根目錄
$rootDir = 'docs';

// 掃描根目錄下的所有子目錄
$directories = glob($rootDir . '/*' , GLOB_ONLYDIR);

foreach ($directories as $dir) {
    // 在每個子目錄中查找符合條件的 PDF 文件
    $pdfFiles = glob($dir . '/*_Datasheet.pdf');
    
    foreach ($pdfFiles as $pdfFile) {
        // 構建對應的 TXT 文件路徑
        $txtFile = preg_replace('/\.pdf$/i', '.txt', $pdfFile);

        // 檢查 TXT 文件是否已存在
        if (!file_exists($txtFile)) {
            // 如果 TXT 文件不存在，執行轉換操作
            $result = ConvertApi::convert('txt', ['File' => $pdfFile], 'pdf');
            $result->saveFiles($dir);
            echo "The file {$pdfFile} was converted successfully<br>";
        }
        else {
            echo "The file {$pdfFile} was already converted<br>";
        }
    }
}

echo "Conversion finished<br>";
?>
