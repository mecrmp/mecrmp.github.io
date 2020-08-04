<fieldset><legend>Поиск и сортировка</legend>
	<form id="SerchForm">
	<table id="tableForm" style="width: 100%;">
	<tr>
	<td>Ищем</td>
	<td>
		<input type="text" size="35" id="query" value="" />
		в
		<select name="search_in" id="search_in">
			<option value="0">Названии</option>
			<option value="1">Описании</option>
		</select>
	</td>
	</tr>
	<tr>
	<td>Категория</td>
	<td>
	<select name="category" id="category_id">
		<option value="0">Любая категория</option>
		<option value="16">Зарубежные фильмы</option>
		<option value="15">Наши фильмы</option>
		<option value="11">Научно-популярные фильмы</option>
		<option value="14">Сериалы </option>
		<option value="5">Телевизор</option>
		<option value="6">Мультипликация </option>
		<option value="9">Аниме </option>
		<option value="12">Музыка</option>
		<option value="7">Игры </option>
		<option value="8">Софт </option>
		<option value="2">Спорт и Здоровье</option>
		<option value="1">Юмор</option>
		<option value="3">Хозяйство и Быт</option>
		<option value="10">Книги </option>
		<option value="13">Другое</option></select>
	</td>
	</tr>
	<tr>
	<td>Упорядочить по</td>
	<td>
	<select id="sort_id">
		<option value="0">дате добавления</option>
		<option value="1">названию</option>
		<option value="2">релевантности</option>
	</select>
	по
	<label><input type="radio" name="s_ad" value="1"  />возрастанию</label>
	<label><input type="radio" name="s_ad" value="0"  checked="checked"  />убыванию</label>
	</td>
	</tr>

	<tr>
	<td>
	<input type="submit" value="Поехали"/>
	</td>
	</tr>


	</table>
	</form>
	
</fieldset>
<h1><div id="eror" style="height: 30px;"></div></h1>



		<script type="text/javascript">
		$(document).ready(function() {
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
var searchUrl = getUrlParameter('search');
var searchCat = getUrlParameter('cat');	
var searchFull = getUrlParameter('full');//	
       //alert(searchUrl);
	   
	   if (typeof searchUrl !== "undefined") {
      $( "#query" ).val( function( query, val ) {
    return val + searchUrl;
    });
    }
	 if (typeof searchCat !== "undefined") {
	 //alert(searchCat);
	 $("#category_id").val(searchCat).change();
    }
	 if (typeof searchFull !== "undefined") {
	 //alert(searchCat);
	 $("#search_in").val(searchFull).change();
    }
	 
	 
	  // $('#query').val($('#query').val() + 'more text');
		$('#search_in').change(function(){	
			   $("#SerchForm").submit();
			   });
		$('#category_id').change(function(){
                 $("#SerchForm").submit();
			   });
		$('#sort_id').change(function(){
                 $("#SerchForm").submit();
			   });	
        $("#SerchForm").on("change", "input[name=s_ad]", function(){
               $("#SerchForm").submit();
          });
			   
		var $select_category_id = $('#category_id');	   
		var $select_search_in = $('#search_in');
		var $select_sort_id = $('#sort_id');
		
		//var $select_search_in = $('#search_in');
		//var $custom = $('#custom');
		$("#SerchForm").submit(function(event) {
			event.preventDefault();
			 var value_category_id = $select_category_id.val();
			 var value_search_in = $select_search_in.val();
			 var value_sort_id = $select_sort_id.val();
			 var value_s_ad = $("input:radio[name ='s_ad']:checked").val();
			 var text_category_id = $("#category_id option:selected").text();
			 var text_search_in = $("#search_in option:selected").text();
			 var response_data_img =('<img src="/templates/Default/images/load_sersh.gif" alt="Загрузка" style="display: block; margin: auto;" >');
             var response_data =('<img src="/templates/Default/images/ok_enable.png" alt="OK" style="width: 16px; height: 16px;" > Результат поиска по категории "'+text_category_id+'", искать в "'+text_search_in+'":');
               $('#eror').html(response_data_img);
				//alert(text_search_in);
			$.post('/engine/ajax/search_advanced.php', {'sad':value_s_ad,'sort':value_sort_id,'cat':value_category_id,'full':value_search_in,'query':$('#query').val()},
					function(data) { 
					        $('.gai').remove();
							$('#eror').html(response_data);
							$('.gai2').after(data);
							
					});
				
			});
			});
			
	</script>
<div style="min-height: 300px;">
<table width="100%" style="border-spacing: 0px;">
<tr class="backgr">
<td width="10px"><img src="{THEME}/images/ic24.gif" />
</td>
<td colspan="2">Название</td>
<td width="1px">Размер</td>
<td width="1px">Пиры</td>
</tr>
<tr class="gai2" width="100%"></tr>
</table>
</div>