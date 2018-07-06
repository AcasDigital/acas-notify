var companies;

Drupal.behaviors.notification_form = {
  attach: function(context, settings) {
    jQuery('#check_employer').click(function() {
      if (!jQuery('#edit-the-full-legal-name-of-the-employer').val()) {
        alert("Enter the full name of your employer");
        jQuery('#edit-the-full-legal-name-of-the-employer').focus();
        return false;
      }
      jQuery('#edit-employers-main-address-address').val('');
      jQuery('#edit-employers-main-address-address-2').val('');
      jQuery('#edit-employers-main-address-city').val('');
      jQuery('#edit-employers-main-address-postal-code').val('');
      jQuery('#employer_message').html('');
      jQuery.ajax({
        url: '/company-house/' + jQuery('#edit-the-full-legal-name-of-the-employer').val(),
        type: "GET",
        dataType: "json",
        cache: false,
        timeout: 60000,
        error: function(XMLHttpRequest, textStatus, errorThrown){

        },
        success: function(data){
          if (!data.length) {
            jQuery('#employer_message').html('<i>' + jQuery('#edit-the-full-legal-name-of-the-employer').val() + '</i> was not found in Companies House. If you think this is wrong, please check the spelling of your employer.');
          }
          if (data.length == 1) {
            jQuery('#edit-the-full-legal-name-of-the-employer').val(data[0].title);
            var address = data[0].address;
            var l1 = "";
            if (address.premises) {
              l1 = address.premises + " ";
            }
            if (address.address_line_1) {
              l1 += address.address_line_1;
            }
            jQuery('#edit-employers-main-address-address').val(l1);
            if (address.address_line_2) {
              jQuery('#edit-employers-main-address-address-2').val(address.address_line_2);
            }
            if (address.locality) {
              jQuery('#edit-employers-main-address-city').val(address.locality);
            }
            if (address.postal_code) {
              jQuery('#edit-employers-main-address-postal-code').val(address.postal_code);
            }
          }else{
            companies = data;
            var html = '<h3>More than one company found for <i>\'' + jQuery('#edit-the-full-legal-name-of-the-employer').val() + '</i>\'. Please select your employer from the list below:</h3>';
            for(var i = 0; i < data.length; i++) {
              var company = data[i];
              html += '<div class="company"><a onclick="populateCompany(' + i + ');">' + company.title + '</a><div class="address">' + company.address_snippet + '</div></div>';
            }
            jQuery('#employer_message').html(html);
          }
        }
      });
      return false;
    });
  }
};

function populateCompany(i) {
  data = companies[i];
  jQuery('#edit-the-full-legal-name-of-the-employer').val(data.title);
  var address = data.address;
  var l1 = "";
  if (address.premises) {
    l1 = address.premises + " ";
  }
  if (address.address_line_1) {
    l1 += address.address_line_1;
  }
  jQuery('#edit-employers-main-address-address').val(l1);
  if (address.address_line_2) {
    jQuery('#edit-employers-main-address-address-2').val(address.address_line_2);
  }
  if (address.locality) {
    jQuery('#edit-employers-main-address-city').val(address.locality);
  }
  if (address.postal_code) {
    jQuery('#edit-employers-main-address-postal-code').val(address.postal_code);
  }
  jQuery('#employer_message').html('');
}