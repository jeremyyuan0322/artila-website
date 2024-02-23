<?php
// 指定要搜索的根目錄
$rootDir = 'docs';
// 掃描根目錄下的所有子目錄
$directories = glob($rootDir . '/*', GLOB_ONLYDIR);

foreach ($directories as $dir) {
  // 在每個子目錄中查找符合條件的 PDF 文件
  $txtFiles = glob($dir . '/*_Datasheet.txt');

  foreach ($txtFiles as $txtFile) {
    $lines = file($txtFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // 读取文件行，忽略空行

    // 检查是否有足够的行进行处理
    if (count($lines) >= 2) {
      // 修改第一行和第二行，并添加前缀
      $lines[0] = "description: " . $lines[0];
      $lines[1] = "product: " . $lines[1];

      // 交换第一行和第二行
      list($lines[0], $lines[1]) = array($lines[1], $lines[0]);
    }

    // 遍历行进行进一步处理
    foreach ($lines as $key => &$line) {
      if (strpos($line, 'www.artila.com') !== false) {
        // 删除包含 www.artila.com 的行
        unset($lines[$key]);
      }
    }

    // 写回文件
    file_put_contents($txtFile, implode("\n", $lines));
  }
}

echo "Files processed.";
