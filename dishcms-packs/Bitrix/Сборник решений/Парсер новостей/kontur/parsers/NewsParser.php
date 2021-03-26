<?php
/**
 * if(isset($_GET['addnewsparsetagent'])) { \kontur\parsers\NewsParser::addAgent(ИдентификаторИнфоблока); }
 */

namespace kontur\parsers;

use Bitrix\Main\Loader;

Loader::includeModule("iblock");

class NewsParser
{
	public $url = 'https://www.motul.com/ru/ru/news';
	
	public $hashPropertyName = 'SOURCE_HASH';
	public $sourcePropertyName = 'SOURCE';
	
	public $debug = false;
	
	public $iblockId;
	
	protected $data = [];
	
	public static function addAgent($iblockId, $duration=3600)
	{
		\CAgent::AddAgent(
		    "kontur\parsers\NewsParser::run({$iblockId},1);", // имя функции
    		"main", // идентификатор модуля
    		"N", // агент не критичен к кол-ву запусков
    		$duration, // интервал запуска - 1 сутки
    		"", // дата первой проверки на запуск
    		"Y", // агент активен
    		"", // дата первого запуска
		    30
		);
	}
	
	public static function log()
	{
		file_put_contents(dirname(__FILE__).'/log.log', "\n\n".date('d.m.Y H:i:s')."\n\n".var_export(func_get_args(), true), FILE_APPEND);
	}
	
	public static function run($iblockId, $isAgent=false, $debug=false)
	{
		if (empty($iblockId)) {
			return false;
		}
		
		$parser = new self;
		$parser->debug = $debug;
		$parser->iblockId = (int)$iblockId;
		
		$parser->parse();
		
		if( $isAgent ) {
			return "kontur\parsers\NewsParser::run({$iblockId},1);";
		}
	}
	
	public function parse()
	{ 
		$html = file_get_contents($this->url);
		
		$blocks = $this->getEregMatches('<div[^>]+news-item-racing[^>]+>.*?<span[^>]+news-item-date[^>]+>[^<]+</span>.*?</div>.*?</div>.*?</div>', $html, 0);
		
		if (!empty($blocks)) {
			foreach($blocks as $block) {
				$parts = $this->getEregMatches('<figure[^>]+background-image:url\(&#39;(.*?)&#39;[^>]+></figure>.*?<h2>.*?<a[^>]+href="([^"]+)">([^<]+)</a>.*?<p>([^<]+)</p>.*?<span[^>]+news-item-date[^>]+>([^<]+)</span>', $block);
				
				$detailUrl = $this->strip($parts[0][2]);
				$hash = $this->getHash($detailUrl);
				
				if ( !$this->exists('PROPERTY_'.$this->hashPropertyName, $hash) ) {
					$this->data[] = [
						'hash' => $hash,
						'title' => $this->strip($parts[0][3]),
						'detail_url' => $detailUrl,
						'preview_picture' => $this->getImage($parts[0][1]),
						'date' => $this->strip($parts[0][5]),
						'preview_text' => $this->strip($parts[0][4]),
						'detail_text' => $this->getDetailText($detailUrl),
						'source' => $this->url
					];
				}
			}
			
			$this->save();
		}
	}
	
	protected function getHash($detailUrl)
	{
		return md5($detailUrl);
	}
	
	protected function save()
	{
		foreach($this->data as $data) {
				
			$fields = [
				'IBLOCK_ID' => $this->iblockId,
				'ACTIVE' => 'Y',
				'NAME' => $data['title'],
				'DATE_ACTIVE_FROM' => $data['date'],
				'PREVIEW_TEXT' => $data['preview_text'],
				'DETAIL_TEXT' => $data['detail_text'],
				'DETAIL_TEXT_TYPE' => 'html',
				'PROPERTY_VALUES' => [
					$this->hashPropertyName => $data['hash'],
					$this->sourcePropertyName => $data['source']
				]
			];
			
			if( is_file($data['preview_picture']) ) {
				$fields['PREVIEW_PICTURE'] = \CFile::MakeFileArray($data['preview_picture']);
			}
			
			$el = new \CIBlockElement;
			$el->Add($fields);
			
			if (is_file($data['preview_picture'])) {
				unlink($data['preview_picture']);
			}
		}
	}
	
	protected function exists($propertyName, $compareValue)
	{
		$rs = \CIBlockElement::GetList([], [$propertyName=>$compareValue, 'IBLOCK_ID'=>$this->iblockId], false, false, ['ID']);
		
		return (bool)$rs->Fetch();
	}
	
	protected function getDetailText($detailUrl)
	{
		$html = file_get_contents($detailUrl);
		
		$blocks = $this->getEregMatches('<div[^>]+pad-large[^>]+>(.*?)<div[^>]+class="social_networks">', $html);
		
		return $blocks[0][1];
	}
	
	protected function getImage($src, $forcy=false)
	{
		$src = $this->strip($src);
		
		if($src) {
			$src = mb_ereg_replace("\?\d+$", '', $src);
			$ext = mb_ereg_replace("^.*\.([^.]+)$", '\1', $src);
			
			$filename = dirname(__FILE__) . '/upload_images/' . md5($src) . '.' . strtolower($ext);
			
			if($forcy || !file_exists($filename)) {
				file_put_contents($filename, file_get_contents($src));
			}
			
			return $filename;
		}
		
		return null;
	}
	
	protected function strip($text, $striptags=true)
	{
		$text = trim(mb_ereg_replace("\r|\n", '', $text));
		if($striptags) {
			$text = strip_tags($text);
		}
		return $text;
	}
	
	/**
     * Получение результат совпадений в тексте по шаблону.
     * @param string $pattern 
     * @param string $text 
     * @return array
     */
    protected function getEregMatches($pattern, $text, $index=false)
    {
        $result=[];

        mb_regex_encoding("UTF-8");
        mb_ereg_search_init($text, $pattern, 'msr');

        $r = mb_ereg_search();
        if($r) {
            $r=mb_ereg_search_getregs(); //get first result 

            do {
                $result[]= ($index !== false) ? $r[$index] : $r;
                $r=mb_ereg_search_regs();//get next result
            }
            while($r);
        }
        return $result;
    }
}
