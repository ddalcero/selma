$(document).ready(function() {
	$('form').on('submit', function() {
		var form = $(this);
		$('.modal').modal('hide');

		var title_attr = form.attr('data-modal-title');
		if( typeof title_attr !== 'undefined' && title_attr !== false && title_attr != '' ) {
			var title = form.attr('data-modal-title');
		} else {
			var title = 'Enviando datos';
		}

		var body_attr = form.attr('data-modal-body')
		if( typeof body_attr !== 'undefined' && body_attr !== false && title_attr != '' ) {
			var body = form.attr('data-modal-body');
		} else {
			var body = 'Por favor espera mientras se envían los datos...';
		}
		showModal(title, body);
	});
});

$(function(){
  $('[data-method]').append(function(){
    return "\n"+
    "<form action='"+$(this).attr('href')+"' method='POST' style='display:none'>\n"+
    "   <input type='hidden' name='_method' value='"+$(this).attr('data-method')+"'>\n"+
    "</form>\n"
  })
  .removeAttr('href')
  .attr('style','cursor:pointer;')
  .attr('onclick','$(this).find("form").submit();');
});

function showModal(title, body) {
	$('#generic_loading_modal_title').html(title);
	$('#generic_loading_modal_body').html(body);
	$('#generic_loading_modal').modal({
		backdrop: 'static',
		keyboard: false
	});
}

function closeModal() {
	$('#generic_loading_modal').modal('hide');
}

function ajaxCall(url, data, success) {
	$.ajax({
		dataType: "json",
		type: "POST",
		url: url,
		data: data,
	}).done(function(msg) {
		return success(msg);
	});
}

function ajaxGet(url, success) {
	$.ajax({
		dataType: "json",
		type: "GET",
		url: url,
	}).done(function(msg) {
		return success(msg);
	});
}

function regExQuote(str) {
    return str.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
}

function arrayRemove(array, from, to) {
  var rest = array.slice((to || from) + 1 || array.length);
  array.length = from < 0 ? array.length + from : from;
  return array.push.apply(array, rest);
};

function _NFL (number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
	s = '',
	toFixedFix = function (n, prec) {
		var k = Math.pow(10, prec);
		return '' + Math.round(n * k) / k;
	};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}
