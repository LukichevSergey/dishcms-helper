<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class KonturBtnDownloadComponent extends CBitrixComponent
{
	public function RunDownload($arParams)
	{
		global $APPLICATION;

		if(($_REQUEST['d'] == 'Y') && ($_REQUEST['h'] === $this->GetHash($arParams))) {
			$APPLICATION->RestartBuffer(); 

			$file=$_SERVER['DOCUMENT_ROOT'] . $arParams['FILE'];
			if(is_file($file)) {
				if($arParams['UNLINK_ZIP'] == 'Y') {
					$this->unlinkZip($file);
				}
				if($arParams['ZIP'] == 'Y') {
					$file=$this->zip($file);
				}

                if(!empty($arParams['FILENAME'])) {
                    $filename = $arParams['FILENAME'];
                }
                else {
                    $filename=preg_replace('#^.*?/([^/]+)$#', '\\1', $file);
                }

				header_remove(); 
				header("HTTP/1.1 200 OK");
				header("Connection: close");
				header("Content-Type: application/octet-stream");
				header("Accept-Ranges: bytes");
				header("Content-Disposition: Attachment; filename={$filename}");
				header("Content-Length: ".filesize($file));
 
				readfile($file);
				exit;
			}
			header("HTTP/1.0 404 Not Found");
			exit;
		}
	}

	public function GetLink($arParams)
	{
		global $APPLICATION;
		return $APPLICATION->GetCurPageParam('d=Y&h='.$this->GetHash($arParams), array('d','h'));
	}

	public function GetHash($arParams)
	{
		return sha1($arParams['FILE']);
	}

	protected function getZipFileName($filename)
	{
		$pathinfo=pathinfo($filename);
		return $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . date('_d_m_Y') . '.zip';
	}

	protected function getZipLocalName($filename)
	{
		$pathinfo=pathinfo($filename);
		return $pathinfo['filename'] . date('_d_m_Y.') . $pathinfo['extension'];
	}

	protected function unlinkZip($filename)
	{
		$pathinfo=pathinfo($filename);
		$zipFile=$this->getZipFileName($filename);
		array_map(function($file) use ($zipFile, $pathinfo) {
			if($file !== $zipFile) {
				file_put_contents($pathinfo['dirname'].'/zip_unlink.log', $file."\n", FILE_APPEND);
				unlink($file);
			}
		}, glob($pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '_*.zip')); 
	}

	protected function zip($filename)
	{
		$zipFile=$this->getZipFileName($filename);

		if(is_file($zipFile)) {
			return $zipFile;
		}

		$zip=new \ZipArchive();
		if($zip->open($zipFile, \ZipArchive::CREATE)) {
			$zip->addFile($filename, $this->getZipLocalName($filename));
			$zip->close();
		}
		if(file_exists($zipFile)) {
			return $zipFile;
		}

		return $filename;
	}
}
