									<h1><?
									$APPLICATION->AddBufferContent(function(){
										global $APPLICATION;
										$h1=KbxCache::get(KbxCache::YEAR, 'h1_'.md5($APPLICATION->GetCurPage()), '/kbxh1');
										if(!$h1) $h1=$APPLICATION->GetPageProperty('h1');
										if(!$h1) $h1=$APPLICATION->getTitle();
										return $h1;
									});
									?></h1>
