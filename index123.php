<?php

require_once 'libs/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Page
{
	public $nav;
	public $content;
	public $footer;

	public function __construct($navFile, $contentFile, $footerFile)
	{
		$this->nav = Yaml::parseFile($navFile);
		if (file_exists($contentFile)) {
			$this->content = Yaml::parseFile($contentFile);
		} else {
			$this->content = [];
		}
		$this->footer = Yaml::parseFile($footerFile);
	}

	// 根據語言和頁面名稱獲取 YAML 文件路徑
	public static function getYamlFilePath($baseDir, $lang, $pageName)
	{
		$yamlFile = ".yaml";
		$langDir = "{$baseDir}/{$lang}";
		if (file_exists("{$langDir}/{$pageName}.yaml")) {
			$yamlFile = "{$langDir}/{$pageName}.yaml";
			echo "yaml: ".$yamlFile . "<br>";
			return $yamlFile;
		}
		else{
			// 檢查特定子目錄
			$subDirs = ['news', 'application', 'product'];
			foreach ($subDirs as $subDir) {
				$yamlFile = "{$langDir}/{$subDir}/{$pageName}.yaml";
				if (file_exists($yamlFile)) {
					echo "yaml: ".$yamlFile . "<br>";
					return $yamlFile;
				}
			}
	
			// 檢查 product 子目錄
			$productSubDirs = glob("{$langDir}/product/*", GLOB_ONLYDIR);
			foreach ($productSubDirs as $subDir) {
				$yamlFile = "{$subDir}/{$pageName}.yaml";
				if (file_exists($yamlFile)) {
					echo "yaml: ".$yamlFile . "<br>";
					return $yamlFile;
				}
			}

		}
		echo "yaml: ".$yamlFile . "<br>";
		error_404();

	}

	// 獲取模板文件的路徑（不需要根據語言修改）
	public static function getTemplateFilePath($pageName)
	{
		$subDirs = ['news', 'application', 'product']; // 添加 'news' 和 'application'
		foreach ($subDirs as $subDir) {
			$path = "{$subDir}/{$pageName}.twig";
			if (file_exists("templates/{$path}")) {
				return $path;
			}
		}

		$productSubDirs = glob("templates/product/*", GLOB_ONLYDIR);
		foreach ($productSubDirs as $subDir) {
			$subDirName = basename($subDir);
			$path = "product/{$subDirName}/{$pageName}.twig";
			if (file_exists("templates/{$path}")) {
				return $path;
			}
		}
		return "{$pageName}.twig";
	}
}
function error_404()
{
	// 404 Not Found
	echo "404 Not Found<br>";
	// header("HTTP/1.0 404 Not Found");
	// exit;
}

// Extract the specific parts
$reqLang = "en";      // 'en'
$reqBaseDir = null;  // 'products'
$reqSubDir = null;   // 'linux-arm-industrial-box-computer'
$reqPage = null;     // 'matrix-752'
// Assuming the URI is something like "/jeremy/en/products/matrix-752"
$uri = $_SERVER['REQUEST_URI'];
echo "URI: ".$uri. "<br>";

// Trim the leading slash to avoid an empty element in the array
$trimmedUri = ltrim($uri, '/');

// Split the URI into parts
$parts = explode('/', $trimmedUri);

// Remove the first element ('jeremy') as it's not part of the count 很重要 需視情況修改
array_shift($parts);

// Count the number of paths
$pathCount = count($parts);
echo "pathCount: ".$pathCount. "<br>";
if ($pathCount == 1) {
	echo "parts[0]: ".$parts[0]. "<br>";
	if ($parts[0] == 'en' || $parts[0] == 'tw' || $parts[0] == 'cn') {
		$reqLang = $parts[0];
		$reqPage = 'index';
	}
	else if ($parts[0] == null){
		$reqLang = 'en';
		$reqPage = 'index';
	} else {
		error_404();
	}
}
else if ($pathCount == 2) {
	$reqLang = $parts[0];
	$reqPage = $parts[1];
}
else if ($pathCount == 3) {
	$reqLang = $parts[0];
	$reqBaseDir = $parts[1];
	$reqPage = $parts[2];
}
else if ($pathCount == 4) {
	$reqLang = $parts[0];
	$reqBaseDir = $parts[1];
	$reqSubDir = $parts[2];
	$reqPage = $parts[3];
}
else {
	error_404();
}

// Display the results
echo "Number of paths: $pathCount\n". "<br>";
echo "Language: $reqLang\n". "<br>";
echo "Base Directory: $reqBaseDir\n". "<br>";
echo "Sub Directory: $reqSubDir\n". "<br>";
echo "Page: $reqPage\n". "<br>";

// 初始化 Twig 模板引擎
$loader = new FilesystemLoader(['templates', 'templates/news', 'templates/application', 'templates/product']);
foreach (glob('templates/product/*', GLOB_ONLYDIR) as $productDir) {
	$loader->addPath($productDir);
}
$twig = new Environment($loader, [
	// 'cache' => 'templates/cache',
	'auto_reload' => true
]);

$yamlBaseDir = 'yamls';

// 根據語言和頁面名稱加載對應的 YAML 文件
$navYaml = "{$yamlBaseDir}/{$reqLang}/nav.yaml";
$footerYaml = "{$yamlBaseDir}/{$reqLang}/footer.yaml";
$contentFile = Page::getYamlFilePath($yamlBaseDir, $reqLang, $reqPage);

// 創建頁面對象
$page = new Page($navYaml, $contentFile, $footerYaml);

// 合併導航、內容和 Footer 數據
$context = array_merge($page->nav, $page->content, $page->footer);

// 加載模板並渲染頁面
$templateFile = Page::getTemplateFilePath($reqPage);
$template = $twig->load($templateFile);
$result = $template->render($context);
echo "base dir: ".__DIR__. "<br>";
echo $result;
