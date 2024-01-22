<?php

$baseDirs = ['cn', 'tw', 'en'];
$productDirs = [
    'product/industrial-iot-controller' => [
        'matrix-310.yaml', 'pac-4000.yaml', 'pac-4010.yaml', 'pac-4070.yaml', 
        'pac-5010.yaml', 'pac-5070.yaml'
    ],
    'product/linux-arm-industrial-box-computer' => [
        'hc-3120.yaml', 'matrix-504.yaml', 'matrix-500.yaml', 'matrix-510.yaml', 
        'matrix-512.yaml', 'matrix-516.yaml', 'matrix-518.yaml', 'matrix-522.yaml'
    ],
    'product/linux-arm-system-on-module' => [
        'm-x6ull.yaml', 'm-m5d35.yaml', 'm-502.yaml', 'm-501.yaml', 'm-501-starter-kit.yaml'
    ],
    'product/remote-io' => [
        'rio-2017.yaml', 'rio-2018.yaml'
    ]
];

$templateDirs = $productDirs;

foreach ($baseDirs as $baseDir) {
    $basePath = "yamls/$baseDir/";
    createFiles($basePath, $productDirs, '.yaml');
}

createFiles('templates/', $templateDirs, '.twig', "{% extends \"skeleton/product.twig\" %}");

function createFiles($basePath, $dirs, $fileExtension, $fileContent = "") {
    foreach ($dirs as $dir => $files) {
        $fullPath = $basePath . $dir;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        foreach ($files as $file) {
            $filePath = $fullPath . '/' . str_replace('.yaml', $fileExtension, $file);
            if (!file_exists($filePath)) {
                file_put_contents($filePath, $fileContent);
            }
        }
    }
}

echo "YAML and Twig files created successfully.";

?>
