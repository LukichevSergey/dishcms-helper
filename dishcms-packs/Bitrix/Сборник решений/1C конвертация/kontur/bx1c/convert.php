<?php
/**
 * 1C Convert 
 *
 * \kontur\bx1c\Convert::run( '/upload/1c_catalog/import/' );
 * \kontur\bx1c\Convert::run( '/upload/1c_catalog/import.zip' );
 * \kontur\bx1c\Convert::run( \Bitrix\Main\Application::getDocumentRoot() . '/upload/1c_catalog/import.zip' );
 */
namespace kontur\bx1c;

use Bitrix\Main\Application;
use Bitrix\Main\IO;

class Convert
{
    const ENCODING = 'utf-8';
    
    public $importFilename = 'import.xml';
    public $offersFilename = 'offers.xml';
    
    public $debug = false;
    
    protected $path = null;
    protected $unpackRoot = null;
    protected $unpackPath = null;
    protected $encoding;
    
    protected $unpacked = false;
    
    protected $importContent = '';
    protected $offersContent = '';
    protected $mainContent = '';
    
    protected $importGoodsTagInserted = false;
    protected $offersPackageOffersTagInserted = false;
    
    private $cacheTagNames = array();
    
    /**
     * Run
     * @param string $filename
     * @param string $path
     * @param string $encoding
     * @throws IO\FileNotFoundException
     */
    public static function run( $filename, $path='/upload/1c_catalog', $encoding='utf-8', $debug=false )
    {
        set_time_limit(0);
        
        $converter = new static();
        
        $converter->debug = (bool)$debug;
        
        if ( is_dir( $filename ) ) {
            $converter->setUnpackPath( $filename );
        }
        elseif ( is_dir(Application::getDocumentRoot() . $filename) ) {
            $converter->setUnpackPath( Application::getDocumentRoot() . $filename );
        }
        else {
            if ( !is_file($filename) ) {
                $filename = Application::getDocumentRoot() . $filename;
            }
            
            if ( !is_file($filename) ) {
                throw new IO\FileNotFoundException( $filename );
            }
            
            $converter->unpack( $filename );
        }
        
        $converter->setPath( $path );
        $converter->setEncoding( $encoding );
        $converter->convert();
        
        if ( $converter->isUnpacked() ) {
            $converter->removeUnpack();
        }
    }
    
    public function isUnpacked()
    {
        return $this->unpacked;
    }
    
    public function getImportFilename()
    {
        return $this->getPath() . $this->importFilename;
    }
    
    public function getOffersFilename()
    {
        return $this->getPath() . $this->offersFilename;
    }
    
    /**
     * 
     * @param string|null $filename 
     * @return string
     */
    public function getUnpackPath( $filename=null)
    {
        if( $filename ) {
            $this->setUnpackPath( dirname($filename) . '/' . uniqid('zip') );
        }
        
        return $this->unpackPath;
    }
    
    public function setUnpackPath( $path )
    {
        if ( $path === null ) {
            $this->unpackPath = null;
        }
        else {
            $this->unpackPath = rtrim($path, '\\\\/') . '/';
        }
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setPath( $path )
    {
        if ( !is_dir($path) ) {
            $path = Application::getDocumentRoot() . $path;
        }
        
        if ( !is_dir($path) ) {
            throw new IO\InvalidPathException( $path );
        }
        
        $this->path = rtrim($path, '\\\\/') . '/';
    }
    
    public function getEncoding()
    {
        return $this->encoding;
    }
    
    public function setEncoding( $encoding )
    {
        $this->encoding = $encoding;
    }
    
    /**
     * Unpack 1C import archive
     * @param string $filename
     * @throws IO\FileNotOpenedException
     */
    public function unpack( $filename )
    {
        $zip = new \ZipArchive();
        if ($zip->open($filename) === TRUE) {
            $zip->extractTo( $this->getUnpackPath( $filename ) );
            $zip->close();
            $this->unpacked = true;
            $this->unpackRoot = $this->getUnpackPath();
        }
        else {
            throw new IO\FileNotOpenedException( $filename );
        }
    }
    
    public function removeUnpack()
    {
        if ( is_dir($this->unpackRoot) ) {
            return $this->removeDir( $this->unpackRoot );
        }
        
        return false;
    }
    
    /**
     * Convert
     * @throws IO\InvalidPathException
     */
    public function convert()
    {
        if ( !$this->getUnpackPath() || !is_dir($this->getUnpackPath()) ) {
            throw new IO\InvalidPathException( $this->getUnpackPath() );
        }
        
        if( $mainImportFile = $this->findMainImportFile() ) {
            // $xml = simplexml_load_file ( $mainImportFile );
            $content = file_get_contents($this->getUnpackPath() . $mainImportFile);
            if ( preg_match('/ encoding="([^"]+)"/m', $content, $encoding) ) {
                $encodingIn = $encoding[1];
                if ( strtolower($encodingIn) != self::ENCODING ) {
                    $content = $this->convertString( $content, $encodingIn, self::ENCODING );
                }
                
                $this->mainContent = $this->removeXmlTag( $content );                
                
                if ( $commercialinfoTagData = $this->getTagContent('commercialinfo', $this->mainContent) ) {
                    $this->createImportContent( $commercialinfoTagData );
                    $this->createOffersContent( $commercialinfoTagData );
                    
                    // добавление товаров
                    $goodsPath = $this->getUnpackPath() . 'goods/';
                    if ( is_dir($goodsPath) ) {
                        $goodsTagData = $this->getTagContent( 'goods', $this->importContent );
                        $this->importGoodsTagInserted = !empty($goodsTagData);
                        
                        $dirs = $this->getFiles( $goodsPath );
                        if ( !empty($dirs) ) {
                            foreach ($dirs as $dirname) {
                                if ( preg_match('/^\d+$/', $dirname) && is_dir($goodsPath . $dirname) ) {
                                    // добавляем товары, предложения, цены и остатки
                                    $this->appendData( $goodsPath . $dirname );
                                }
                            }
                        }
                    }
                    
                    if ( strtolower($this->getEncoding()) != self::ENCODING ) {
                        $this->importContent = $this->convertString( $this->importContent, self::ENCODING, $this->getEncoding() );
                        $this->offersContent = $this->convertString( $this->offersContent, self::ENCODING, $this->getEncoding() );
                    }
                    
                    file_put_contents( $this->getImportFilename(), $this->importContent );
                    
                    if ($this->offersContent) {
                        file_put_contents( $this->getOffersFilename(), $this->offersContent );
                    }
                    return true;
                }
            }
            
            
        }
        else {
            throw new IO\FileNotFoundException( '' );
        }
        
        throw new IO\IoException( '' );
    }
    
    protected function removeXmlTag( $content )
    {
        return preg_replace('#^.*?<?xml[^>]+?>#i', '', $content);
    }
    
    protected function getTagName( $code )
    {
        if ( !isset($this->cacheTagNames[$code]) ) {
            switch ( $code ) {
                case 'commercialinfo':
                    // "КоммерческаяИнформация" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D09AD0BED0BCD0BCD0B5D180D187D0B5D181D0BAD0B0D18FD098D0BDD184D0BED180D0BCD0B0D186D0B8D18F');
                    break;
                    
                case 'catalog':
                    // Каталог UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D09AD0B0D182D0B0D0BBD0BED0B3');
                    break;
                    
                case 'goods':
                    // "Товары" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D0A2D0BED0B2D0B0D180D18B');
                    break;
                    
                case 'product':
                    // "Товар" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D0A2D0BED0B2D0B0D180');
                    break;
                    
                case 'packageoffers':
                    // "ПакетПредложений" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D09FD0B0D0BAD0B5D182D09FD180D0B5D0B4D0BBD0BED0B6D0B5D0BDD0B8D0B9');
                    break;
                    
                case 'offers':
                    // "Предложения" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D09FD180D0B5D0B4D0BBD0BED0B6D0B5D0BDD0B8D18F');
                    break;
                    
                case 'offer':
                    // "Предложение" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D09FD180D0B5D0B4D0BBD0BED0B6D0B5D0BDD0B8D0B5');
                    break;
                    
                case 'pricetypes':
                    // "ТипыЦен" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D0A2D0B8D0BFD18BD0A6D0B5D0BD');
                    break;
                    
                case 'prices':
                    // "Цены" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D0A6D0B5D0BDD18B');
                    break;
                    
                case 'id':
                    // "Ид" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D098D0B4');
                    break;
                    
                case 'idcatalog':
                    // "ИдКаталога" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D098D0B4D09AD0B0D182D0B0D0BBD0BED0B3D0B0');
                    break;
                    
                case 'idclassificator':
                    // "ИдКлассификатора" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D098D0B4D09AD0BBD0B0D181D181D0B8D184D0B8D0BAD0B0D182D0BED180D0B0');
                    break;
                    
                case 'rests':
                    // "Отстатки" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D09ED182D181D182D0B0D182D0BAD0B8');
                    break;
                    
                case 'groups':
                    // "Группы" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D093D180D183D0BFD0BFD18B');
                    break;
                    
                case 'group':
                    // "Группа" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D093D180D183D0BFD0BFD0B0');
                    break;
                    
                case 'name':
                    // "Наименование" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D09DD0B0D0B8D0BCD0B5D0BDD0BED0B2D0B0D0BDD0B8D0B5');
                    break;
                    
                case 'owner':
                    // "Владелец" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D092D0BBD0B0D0B4D0B5D0BBD0B5D186');
                    break;
                    
                case 'classificator':
                    // "Классификатор" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D09AD0BBD0B0D181D181D0B8D184D0B8D0BAD0B0D182D0BED180');
                    break;
                    
                case 'stores':
                    // "Склады" UTF-8
                    $this->cacheTagNames[$code] = hex2bin('D0A1D0BAD0BBD0B0D0B4D18B');
                    break;
                    
                default: 
                    $this->cacheTagNames[$code] = $code;
            }
        }
        
        return $this->cacheTagNames[$code];
    }
    
    protected function getTagStartPos( &$tagSearchPos, $tag, $content, $isClose=false )
    {
        if ( $tagSearchPos >= mb_strlen($content, self::ENCODING) ) {
            return false;
        }
        
        do {
            $tagSearch = '<' . ($isClose ? '/' : '') . $this->getTagName($tag) . ($isClose ? '>' : '');
            $tagStartPos = mb_strpos($content, $tagSearch, $tagSearchPos, self::ENCODING);
            if ( $tagStartPos !== false ) {
                $tagSearchPos = $tagStartPos + mb_strlen($tagSearch, self::ENCODING);
                $tagNextChar = mb_substr($content, $tagSearchPos, 1, self::ENCODING);
            }
            else {
                $tagNextChar = null;
            }
        }
        while (($tagStartPos !== false) && ($tagNextChar != hex2bin('20')) && ($tagNextChar != hex2bin('3E')) ); // " " - 20, ">" - 3E
        
        return $tagStartPos;
    }
    
    /**
     * Get tag content
     * @param string $tag
     * @param string $content
     * @return string[]|boolean
     */
    protected function getTagContent( $tag, $content, $ungreedy=true )
    {
        $pattern = '^(.*?)(<' . $this->getTagName($tag) . '>)(.*' . ($ungreedy ? '?' : '') . ')(</' . $this->getTagName($tag) . '>)(.*?)$';
        $matches = $this->search($content, $pattern);
        
        if ( empty($matches) ) {
            $pattern = '^(.*?)(<' . $this->getTagName($tag) . '\s[^>]*?>)(.*' . ($ungreedy ? '?' : '') . ')(</' . $this->getTagName($tag) . '>)(.*?)$';
            $matches = $this->search($content, $pattern);
        }
        
        if ( !empty($matches) ) {
            return array(
                'begin' => $matches[0][1],
                'open' => $matches[0][2],
                'content' => $matches[0][3],
                'close' => $matches[0][4],
                'end' => $matches[0][5]
            );
        }
        
        return false;
    }
    
    protected function getTagContents( $tag, $content, $keyTagName=null )
    {
        $contents = array();
        
        $patterns = array(
            '(<' . $this->getTagName($tag) . '>)(.*?)(</' . $this->getTagName($tag) . '>)',
            '(<' . $this->getTagName($tag) . '\s[^>]*?>)(.*?)(</' . $this->getTagName($tag) . '>)'
        );
        
        foreach( $patterns as $pattern ) {
            $matches = $this->search($content, $pattern);
            
            if ( !empty($matches) ) {
                foreach( $matches as $result) {
                    $key=count($contents);
                    if ( $keyTagName ) {
                        $keyTagData = $this->getTagContent($keyTagName, $result[2]);
                        if ( $keyTagData && $keyTagData['content'] ) {
                            $key = $keyTagData['content'];
                        }
                    }
                    $contents[$key] = array(
                        'open' => $result[1],
                        'content' => $result[2],
                        'close' => $result[3]
                    );
                }
            }
        }
        
        return $contents;
    }
    
    protected function search($string, $pattern)
    {
        $matches=array();
        
        mb_regex_encoding( self::ENCODING );
        
        mb_ereg_search_init($string, $pattern, "pr");
        
        $r = mb_ereg_search();
        if($r) {
            $r=mb_ereg_search_getregs();
            do {
                $matches[]=$r;
                $r=mb_ereg_search_regs();
            }
            while($r);
        }
        
        return $matches;
    }
    
    protected function createImportContent( $commercialinfoTagData )
    {
        $this->importContent = '<?xml version="1.0" encoding="'.$this->getEncoding().'" standalone="yes"?>';
        $this->importContent .= $commercialinfoTagData['open'];
        
        
        if ( $classificatorTagData = $this->getTagContent('classificator', $commercialinfoTagData['content']) ) {
            $this->importContent .= $classificatorTagData['open'];
            
            if ( $idTagData = $this->getTagContent('id', $classificatorTagData['content']) ) {
                $this->importContent .= $idTagData['open'] . $idTagData['content'] . $idTagData['close'];
            }
            
            if ( $nameTagData = $this->getTagContent('name', $classificatorTagData['content']) ) {
                $this->importContent .= $nameTagData['open'] . $nameTagData['content'] . $nameTagData['close'];
            }
            
            if ( $ownerTagData = $this->getTagContent('owner', $commercialinfoTagData['content']) ) {
                $this->importContent .= $ownerTagData['open'] 
                    . $ownerTagData['content'] 
                    . $ownerTagData['close'];
            }
            
            if ( $groupsTagData = $this->getTagContent('groups', $classificatorTagData['content'], false) ) {
                $this->importContent .= $groupsTagData['open']
                    . $groupsTagData['content']
                    . $groupsTagData['close'];
            }
            
            $this->importContent .= $classificatorTagData['close'];
        }
        
        if ( $catalogTagData = $this->getTagContent('catalog', $commercialinfoTagData['content']) ) {
            $this->importContent .= $catalogTagData['open']
                    . $catalogTagData['content']
                    . $catalogTagData['close'];
        }
        
        $this->importContent .= $commercialinfoTagData['close'];
    }
    
    protected function createOffersContent( $commercialinfoTagData )
    {
        $this->offersContent = '<?xml version="1.0" encoding="'.$this->getEncoding().'" standalone="yes"?>';
        $this->offersContent .= $commercialinfoTagData['open'];
        $this->offersContent .= $commercialinfoTagData['close'];
    }
    
    protected function appendData( $path )
    {
        $files = $this->getFiles( $path );
        if ( !empty($files) ) {
            foreach( $files as $filename ) {
                if ( preg_match('/^import.*\.xml$/', $filename) ) {
                    $content = file_get_contents($path . '/' . $filename);
                    if ( $goodsTagData = $this->getTagContent( 'goods', $content ) ) {
                        if ($this->debug) {
                            if ( $productTagData = $this->getTagContent( 'product', $content ) ) {
                                $goodsTagData['content'] = $productTagData['open'] . $productTagData['content'] . $productTagData['close'];
                            }
                        }
                        
                        if (!$this->importGoodsTagInserted) {
                            $mainCatalogTagData = $this->getTagContent( 'catalog', $this->importContent );
                            $this->importContent = 
                                $mainCatalogTagData['begin'] 
                                . $mainCatalogTagData['open'] 
                                . $mainCatalogTagData['content'] 
                                . $goodsTagData['open']
                                . $goodsTagData['content']
                                . $goodsTagData['close']
                                . $mainCatalogTagData['close'] 
                                . $mainCatalogTagData['end'];
                            
                            $this->importGoodsTagInserted = true;
                            unset($mainCatalogTagData);
                        }
                        else {
                            $mainGoodsTagData = $this->getTagContent( 'goods', $this->importContent );
                            $this->importContent = $mainGoodsTagData['begin']
                                . $mainGoodsTagData['open']
                                . $mainGoodsTagData['content']
                                . $goodsTagData['content']
                                . $mainGoodsTagData['close']
                                . $mainGoodsTagData['end'];
                            unset($mainGoodsTagData);
                        }
                        unset($goodsTagData);
                    }
                    unset($content);
                }
                elseif ( preg_match('/^(offers|prices|rests).*\.xml$/', $filename) ) {
                    $content = file_get_contents($path . '/' . $filename);
                    $this->updateOffers( $content );
                }
            }
        }
    }
    
    protected function updateOffers($content)
    {
        if ( $packageOffersTagData = $this->getTagContent( 'packageoffers', $content ) ) {
            if (!$this->offersPackageOffersTagInserted) {
                $this->insertPackageOffersTag( $content, $packageOffersTagData );
            }
            
            $mainPackageOffersTagData = $this->getTagContent( 'packageoffers', $this->offersContent );
            if ($offersTagData = $this->getTagContent( 'offers', $content ) ) {
                $mainOffersTagData = $this->getTagContent( 'offers', $mainPackageOffersTagData['content'] );
                if ( $mainOffersTagData ) {
                    $newOffers = array();
                    $mainOffers = $this->getTagContents( 'offer', $mainOffersTagData['content'], 'id' );
                    $offers = $this->getTagContents( 'offer', $offersTagData['content'], 'id' );
                    
                    if ( $mainOffers ) {
                        foreach($mainOffers as $offerId => $offerTagData) {
                            if ( isset($offers[$offerId]) ) {
                                if ( $offerIdTagContent = $this->getTagContent('id', $offers[$offerId]['content']) ) {
                                    $newOffers[$offerId] = $offerTagData['open']
                                        . $offerTagData['content']
                                        . $offerIdTagContent['begin']
                                        . $offerIdTagContent['end']
                                        . $offerTagData['close'];
                                }
                                unset($offers[$offerId]);
                            }
                            else {
                                $newOffers[$offerId] = implode('', $offerTagData);
                            }
                        }
                    }
                    
                    if ( !empty($offers) ) {
                        foreach($offers as $offerId => $offerTagData) {
                            $newOffers[$offerId] = implode('', $offerTagData);
                        }
                    }
                    
                    if ( $this->debug ) {
                        $newOffers = array_slice($newOffers, 0, 3);
                    }
                    
                    $this->offersContent = $mainPackageOffersTagData['begin']
                    . $mainPackageOffersTagData['open']
                        . $mainOffersTagData['begin']
                        . $mainOffersTagData['open']
                        . implode( '', $newOffers )
                        . $mainOffersTagData['close']
                        . $mainOffersTagData['end']
                    . $mainPackageOffersTagData['close']
                    . $mainPackageOffersTagData['end'];
                }
                else {
                    $this->insertOffersTag( $offersTagData, $mainPackageOffersTagData );
                }
            }
        }
    }
    
    protected function insertOffersTag( $offersTagData, $mainPackageOffersTagData=null )
    {
        if ( $mainPackageOffersTagData === null) {
            $mainPackageOffersTagData = $this->getTagContent( 'packageoffers', $this->offersContent );
        }
        
        $this->offersContent = $mainPackageOffersTagData['begin']
            . $mainPackageOffersTagData['open']
            . $mainPackageOffersTagData['content']
            . $offersTagData['open']
            . $offersTagData['content']
            . $offersTagData['close']
            . $mainPackageOffersTagData['close']
            . $mainPackageOffersTagData['end'];
    }
    
    protected function insertPackageOffersTag( $content, $packageOffersTagData=null )
    {
        if ( $packageOffersTagData === null ) {
            $packageOffersTagData = $this->getTagContent( 'packageoffers', $content );
        }
        
        if ( !$this->offersPackageOffersTagInserted && !empty($packageOffersTagData) ) {
            if ( $commercialinfoTagData = $this->getTagContent('commercialinfo', $this->offersContent) ) {
                $this->offersContent =
                    $commercialinfoTagData['begin']
                    . $commercialinfoTagData['open']
                    . $packageOffersTagData['open'];
                
                foreach( array('id', 'idcatalog', 'idclassificator', 'name') as $tagName ) {
                    if ( $tagData = $this->getTagContent($tagName, $packageOffersTagData['content']) ) {
                        $this->offersContent .= $tagData['open'] . $tagData['content'] . $tagData['close'];
                    }
                }
                
                if ( $ownerTagData = $this->getTagContent('owner', $this->importContent) ) {
                    $this->offersContent .= $ownerTagData['open']
                        . $ownerTagData['content']
                        . $ownerTagData['close'];
                }
                
                if ( $priceTypesTagData = $this->getTagContent( 'pricetypes', $this->mainContent ) ) {
                    $this->offersContent .= $priceTypesTagData['open']
                        . $priceTypesTagData['content']
                        . $priceTypesTagData['close'];
                }
                
                if ( $storesTagData = $this->getTagContent( 'stores', $this->mainContent ) ) {
                    $this->offersContent .= $storesTagData['open']
                        . $storesTagData['content']
                        . $storesTagData['close'];
                }
                        
                $this->offersContent .= $packageOffersTagData['close']
                    . $commercialinfoTagData['close']
                    . $commercialinfoTagData['end'];
            }
            
            $this->offersPackageOffersTagInserted = true;
            
            return true;
        }
        
        return false;
    }
    
    protected function convertString( $string, $charsetIn, $charsetOut )
    {
        return iconv( $charsetIn, $charsetOut . '//TRANSLIT', $string );
    }
    
    /**
     *
     * @param string|null $path 
     * @return boolean|mixed
     */
    protected function findMainImportFile( $path=null )
    {
        if ( $path === null ) {
            $path = $this->getUnpackPath();
        }
        
        $files = $this->getFiles( $path );
        
        if ( empty($files) ) {
            return false;
        }
        
        $imports = preg_grep('#^import(.*)\.xml$#', $files);
        if( !empty($imports) ) {
            return array_shift($imports);
        }
        
        foreach( $files as $entry ) {
            if ( is_dir($path . '/' . $entry) ) {
                $this->setUnpackPath(null);
                if ( $filename = $this->findMainImportFile( $path . '/' . $entry ) ) {
                    if ( !$this->getUnpackPath() ) {
                        $this->setUnpackPath( $path . '/' . $entry );
                    }
                    return $filename;
                }
            }
        }
        
        return false;
    }
    
    protected function getFiles( $path )
    {
        $files = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $files[] = $entry;
                }
            }
        }
        return $files;
    }
    
    protected function removeDir( $path )
    {
        if( $dir = opendir($path) ) {
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    $full = $path . '/' . $file;
                    if ( is_dir($full) ) {
                        $this->removeDir($full);
                    }
                    else {
                        unlink($full);
                    }
                }
            }
            closedir($dir);
            rmdir($path);
            
            return true;
        }
        
        return false;
    }
}
