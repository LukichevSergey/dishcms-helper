------------------------------------------------
Подключить конфигурацию и добавить пункт меню
------------------------------------------------
/protected/config/crud.php
'price_section'=>'application.config.crud.price_section',
'price_subsection'=>'application.config.crud.price_subsection',

/protected/modules/admin/config/menu.php
use crud\components\helpers\HCrud;

HCrud::getMenuItems(Y::controller(), 'price_section', 'crud/index', true)

------------------------------------------------
Отображение списка разделов и элементов
------------------------------------------------
<?
if($sections=PriceSection::model()->activly()->scopeSort('price_sections')->findAll()):
	?><div class="pricelist__block"><?
	foreach($sections as $section):
		if($pricelists=PriceSubSection::model()->activly()->scopeSort('price_subsections', $section->id)->wcolumns(['section_id'=>$section->id])->findAll()):
		?><div class="pricelist__section" data-id="<?=$section->id?>">
			<div class="pricelist__section-heading" onclick="$(this).siblings('.pricelist__table').toggle();"><?=$section->title?></div>
			<? foreach($pricelists as $pricelist): ?>
			<div class="pricelist__table">
				<div class="pricelist__table-heading" onclick="$(this).siblings('.pricelist__table-content').toggle();"><?=$pricelist->title?></div>
				<div class="pricelist__table-content"><?=$pricelist->text?></div>
			</div>
			<? endforeach; ?>
		</div>
		<?
		endif;
	endforeach;
	?></div><?
endif;
?>

------------------------------------------------
СТИЛИ LESS
------------------------------------------------

.pricelist__block {
	.pricelist__section {
		position: relative;
		z-index: 10;
		
		.pricelist__section-heading {
			position: relative;
			z-index: 20;
			
		    font-weight: bold;
		    font-size: 1.5em;
		    background: #ccc;
		    padding: 5px;
		    margin: 5px 0;
			
			&:hover {
			    opacity: 0.7;
			    cursor: pointer;
			}
		}
		.pricelist__table {
		    margin-left: 20px;
		    display: none;
		
			.pricelist__table-heading {
			    font-weight: bold;
			    font-size: 1.5em;
			    background: #f0f0f0;
			    border: 1px solid #ccc;
			    padding: 5px;
			    margin: 5px 0;
				
				&:hover {
				    opacity: 0.7;
				    cursor: pointer;
				}
			}
	
			.pricelist__table-content {
				display: none;
				
				table {
					width: 100%;
					tr {
						
						td {
						    border: 1px solid #f0f0f0;
						    
							&:first-child {
							    border-left: 0;
							}
							&:last-child {
							    border-right: 0;
							}
						}
							
						&:first-child {
						    background: #4a7fc3;
						    color: #fff;
						    font-weight: bold;
							
							td {
							    border: 1px solid #fff;
							}
						}
					}
				}
			}
		}
	}
}
