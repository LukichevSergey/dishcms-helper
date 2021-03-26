<?
namespace kontur\helpers;

class Xml 
{
    /**
     * @see htmlspecialchars()
     */
    public static function hsc($text, $hsc=true)
    {
        return hsc ? htmlspecialcharsbx($text) : $text;   
    }
    
    /**
     * ConvertCharset
     */
    public static function cc($text, $hsc=true, $from=LANG_CHARSET, $to='windows-1251')
    {
        global $APPLICATION;
        return $APPLICATION->ConvertCharset($fHSC($text, $bHSC), $from, $to);
    }

    /**
     * get attributes string
     */
    public static function a($attrs=array())
    {
        if(!empty($attrs)) {
            $strs=array();
            foreach($attrs as $name=>$val) $strs[]="{$name}=\"{$val}\"";
            return ' '.implode(' ', $strs);
        }
        
        return '';
    }
    
    /**
     * Tag open
     */
    public static function o($tag, $attrs=array(), $newLine=true, $close=false)
    {
        $s="<{$tag}".self::a($attrs);
        if($close) $s.='/';
        $s.='>';
        if($newLine) $s.="\n";
        
        return $s;
    }
    
    /**
     * Tag close
     */
    public static function c($tag)
    {
        return "</{$tag}>\n";
    }
    
    /**
     * Tag
     */
    public static function t($tag, $content, $attrs=array(), $convert=true, $hsc=true)
    {
        if($convert) $content=self::cc($content, $hsc);
        elseif($hsc) $content=self::hsc($content);
        
        return self::o($tag, $attrs, false) . $content . self::c($tag);
    }
    
    public static function head($encoding='windows-1251')
    {
        return '<?xml version="1.0" encoding="'.$encoding.'?>';

    }
}