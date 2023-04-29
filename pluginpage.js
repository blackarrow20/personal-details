var decodeEntities = (function() {
  // this prevents any overhead from creating the object each time
  var element = document.createElement('div');

  function decodeHTMLEntities (str) {
    if(str && typeof str === 'string') {
      // strip script/html tags
      str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
      str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
      element.innerHTML = str;
      str = element.textContent;
      element.textContent = '';
    }

    return str;
  }

  return decodeHTMLEntities;
})();

function fillTextboxesWithSelectedData(index) {
	jQuery("#first_name").val(decodeEntities(jQuery("#row_"+String(index)).find("td")[2].innerHTML));
	jQuery("#last_name").val(decodeEntities(jQuery("#row_"+String(index)).find("td")[3].innerHTML));
	jQuery("#email").val(decodeEntities(jQuery("#row_"+String(index)).find("td")[4].innerHTML));
	jQuery("#address").val(decodeEntities(jQuery("#row_"+String(index)).find("td")[5].innerHTML));	
	jQuery("#mobile").val(decodeEntities(jQuery("#row_"+String(index)).find("td")[6].innerHTML));
}

function selectRow(index) {	
	jQuery("#index").val(index); // To remember selection index after submit
	
	// Select the radio button
	jQuery('input[id^="radio_"]').attr("checked", false);
	jQuery("#radio_"+String(index)).attr("checked", true);
	
	// Highlight the whole row
	jQuery('tr[id^="row_"]').attr("class", "dataRow basic");
	jQuery("#row_"+String(index)).attr("class", "dataRow highlighted");	
}

function clearFields() {
	jQuery("#first_name").val("");
	jQuery("#last_name").val("");
	jQuery("#address").val("");
	jQuery("#email").val("");
	jQuery("#mobile").val("");
}