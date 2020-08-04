function b64EncodeUnicode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
    }));
}

$(document).on("click", "#down", function (event) {
        let url = '//multidownloader.site/encode.php';
        let vaLue = $(this).attr("data-down_id");
		$.ajax({
	type: 'POST',
        url: url,
        data: { 'nameid': vaLue},
        success: function (data) {
                increment = data ;
			if (increment == 0) {
            alert('Ошибка. Данные не отправлены. Повторите через несколько минут');
			}else{
				$(".modal_Down").remove()
				$("body").append('<div class="modal_Down" title="Скачивание файла" style="display:none;" ><b>После скачивания торрента просим оставить свой комментарий.</b><br /><br /><div id="timer_num"><progress id="progressbar" style="width: 100%;" value="0" max="100"></progress></div>')
				encode_increment = b64EncodeUnicode('//multidownloader.site/id/' + increment);
				setTimeout(function() {
                $("#imag_aj").css("display","none");
                $('#timer_num').html('Cсылка на скачивания файла: <a href=/engine/go.php?url=' + encode_increment +' target="_blank">Скачать</a>');
                }, 6000);
			   $(function(){
               $('.modal_Down').dialog({
               autoOpen: true,
               show: 'fade',
               hide: 'fade',
               width: 520,
               buttons: {
               "Закрыть окно" : function() {
                   $(this).remove();
               },
           }
   
       });
   });
		     	}
				$(document).ready(function() {
	var progressbar = $('#progressbar'),
		max = progressbar.attr('max'),
		time = (1000/max)*5,	
	    value = progressbar.val();

	var loading = function() {
	    value += 1;
	    addValue = progressbar.val(value);
	    
	    $('.progress-value').html(value + '%');

	    if (value == max) {
	        clearInterval(animate);			           
	    }
	};

	var animate = setInterval(function() {
	    loading();
	}, time);
});
			   
             },
            error: function(response) {
                alert( "Ошибка! Повторите попытку позже." );
            }
           });
    });
