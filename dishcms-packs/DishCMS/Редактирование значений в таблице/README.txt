Пример

В контроллере
public function actionUpdateAddProductItem()
{
	$ajax=HAjax::start();
	if($id=(int)$_POST['id']) {
		if($model=AddProducts::model()->findByPk($id)) {
			try {
	    		if($name=$_POST['name']) {
	    			$value=isset($_POST['value']) ? $_POST['value'] : '';
	    			$model->$name=$value;
	    			if($model->save()) {
	    				$ajax->data['value']=$model->$name;
	    				$ajax->success=true;
	    			}
	    			else {
	    				$ajax->errors=$model->getErrors();
	    			}
	    		}
			}
			catch(\Exception $e) {
				
			}
		}
	}

	$ajax->end();
}

В шаблоне

<table class="table" id="productAttrTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Цена</th>
          <th>Заголовок</th>
          <th>Длина</th>
          <th>Высота</th>
          <th>Ширина</th>
          <th>Глубина</th>
          <th> </th>
        </tr>
      </thead>
      <tbody>
        <?foreach($model->productAttr as $attrd):?>
        <tr data-id="<?= $attrd->id; ?>">
          <td><?=$attrd->id?></td>
          <td data-editable="price"><?=$attrd->price?></td>
          <td data-editable="title"><?=$attrd->title?></td>
          <td data-editable="length"><?=$attrd->length?></td>
          <td data-editable="height"><?=$attrd->height?></td>
          <td data-editable="width"><?=$attrd->width?></td>
          <td data-editable="depth"><?=$attrd->depth?></td>
          <td><a class="delitem" data-id="<?=$attrd->id?>" href="#">Удалить</a></td>
        </tr>
        <?endforeach;?>
      </tbody>
</table>

<script type="text/javascript">
      $(document).ready(function(){
        $('.delitem').on('click', function(){
          $(this).parents('tr').remove();
          $.ajax({
            url: "/cp/shop/removeItem/" + $(this).data('id'),

          })
            .done(function(data) {
            });
          return false;
        });
        
        var attributeValues=[];
        function closeEditables(activeId, activeIdx) {
        	$("#productAttrTable tbody tr[data-id] td[data-editable] input").each(function(){
        		let id=$(this).parents("tr:first").data("id");
        		let idx=$(this).parents("td:first").data("editable");
        		if(((id != activeId) || ((id == activeId) && (idx != activeIdx))) && (typeof(attributeValues[id])!="undefined") && (typeof(attributeValues[id][idx])!="undefined")) {
        			$(this).parent().text(attributeValues[id][idx]);
        		}
        	});
        }
        $(document).on("click", "#productAttrTable tbody tr[data-id] td[data-editable]", function(e){
        	var $t=$(e.target).closest("td");
        	if(!$t.find("input").length) {
        		var id=$t.parent().data("id"), v=$t.text();
        		closeEditables(id, $t.data("editable"));
        		if(typeof(attributeValues[id])=="undefined") attributeValues[id]=[];
        		attributeValues[id][$t.data("editable")]=v;
        		$t.html($.parseHTML('<input type="text" data-id="' + id + '" /><span class="btn-save btn btn-success btn-xs inline"><i class="btn-save glyphicon glyphicon-ok"></i></span>'));
        		$t.find("input").val(v);
        	}
        });
        $(document).on("click", "#productAttrTable tbody tr[data-id] td[data-editable] .btn-save", function(e){
        	var $t=$(e.target).closest("td");
        	var $btn=$(e.target).closest(".btn-save");
        	if($btn.hasClass("disabled")) return false;
        	var $inp=$t.find("input");
        	$btn.addClass("disabled");
        	if($inp.length) {
        		$.post(
        			"/cp/shop/updateAddProductItem", 
        			{id:$t.parent().data("id"), name: $t.data("editable"), value: $inp.val()}, 
        			function(response){
        				if(response.success) {
        					$inp.removeClass("error");
        					$t.text(response.data.value);
        					$t.addClass("bg-success");
        					setTimeout(function(){$t.removeClass("bg-success")}, 500);
        				}
        				else {
        					$inp.addClass("error");
        				}
        				$btn.removeClass("disabled");
        			},
        			"json"
        		);
        	}
        });
      });
    </script>
    <style>
    	#productAttrTable tbody tr[data-id] td {
    		white-space: nowrap;
    	}
    	#productAttrTable tbody tr[data-id] td[data-editable]:hover {
    		cursor: pointer;
    		border: 1px solid #069 !important;
    	}
    	#productAttrTable tbody tr[data-id] td[data-editable] input {
    	    width: 85%;
    	    min-width: 50px;
		    padding: 2px 3px;
		    border-radius: 3px;
		    border: 1px solid #ccc;
		    display: inline-block;
    	}
    	#productAttrTable tbody tr[data-id] td[data-editable] span {
    		margin-left: 5px;
    	}
    </style>
