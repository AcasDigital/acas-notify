var addresses;
var companies;
var defaultFeedbackText = '';
var postcode = '';

Drupal.behaviors.notification_form = {
  attach: function(context, settings) {
    if (jQuery(context).find('.webform-submission-form').once('notification_form').length > 0) {
      if (jQuery('.webform-submission-form .webform-wizard-pages-link').length) {
        jQuery('.webform-submission-form .webform-wizard-pages-link').html(jQuery('.webform-submission-form .webform-wizard-pages-link').html().replace('Edit', 'Change'));
      }
      //Feedback text
      if (jQuery('#feedback-form .left-wrapper .text').hasClass('default')) {
        defaultFeedbackText = jQuery('#feedback-form .left-wrapper .text').text();
        jQuery('#feedback-form .left-wrapper .text').removeClass('default');
      }
      if (jQuery('input[name^="feedback_text"]').length) {
        jQuery('#feedback-form .left-wrapper .text').text(jQuery('input[name^="feedback_text"]').val());
      }else if (defaultFeedbackText) {
        jQuery('#feedback-form .left-wrapper .text').text(defaultFeedbackText);
      }
      // Date tests
      if (jQuery('.when-was-you-last-day-of-work.govuk-webform-elements--wrapper').length) {
        dismissedDate();
        jQuery('.when-was-you-last-day-of-work.govuk-webform-elements--wrapper').find('.govuk-webform-elements-day, .govuk-webform-elements-month, .govuk-webform-elements-year').on('input', dismissedDate);
      }
      
      if (jQuery('.form-item-problem-several-colleagues').length) {
        jQuery('.form-item-problem-several-colleagues input').change(function() {
            if (this.value == 2) {
              jQuery('.form-actions').hide();
            }else{
              jQuery('.form-actions').show();
            }
        });
      }
      jQuery('.webform-submission-form .webform-button--submit').click(function( event ) {
        if (jQuery(this).parent().parent().parent().attr('id') == 'feedback-form') {
          // Prevent feedback webforms having wait dialog
          return;
        }
        var html = '<div id="overlay"></div><div id="modal"><div class="title-wrapper"><span class="title">Sending your notification to Acas</span></div>';
        html += '<div class="spinner-wrapper"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>';
        html += '<div class="modal-description">Please wait...</div></div>';
        jQuery('body').append(html);
      });
      jQuery('.webform-submission-form .webform-button--next').onFirst('click', function( event ) {
        jQuery('.alert').remove();
        var scrollTo = null;
        jQuery('.form-item .form-control').filter(':visible').each( function () {
          if (jQuery(this).hasClass('required')) {
            if (!jQuery(this).val()) {
              jQuery(this).removeClass('valid');
              jQuery(this).addClass('invalid');
              var e = jQuery(this).parent().find('.invalid-feedback');
              jQuery(e).text(jQuery(this).attr('data-webform-required-error'));
              jQuery(e).show();
              jQuery(this).on('input', function() {
                if (!jQuery(this).val()) {
                  jQuery(this).parent().find('.invalid-feedback').show();
                  jQuery(this).addClass('invalid');
                  jQuery(this).removeClass('valid');
                }else{
                  jQuery(this).parent().find('.invalid-feedback').hide();
                  jQuery(this).removeClass('invalid');
                  jQuery(this).addClass('valid');
                }
              });
              if (!scrollTo) {
                scrollTo = jQuery(this).parent();
              }
            }else{
              jQuery(this).addClass('valid');
            }
          }else{
            jQuery(this).addClass('valid');
          }
        });
        if (scrollTo) {
          jQuery(scrollTo).find('.form-control').focus();
          jQuery([document.documentElement, document.body]).animate({
            scrollTop: jQuery(scrollTo).offset().top - 100
          }, 500);
          event.stopImmediatePropagation();
          return false;
        }
        return true;
      });
      jQuery('a.find_address').click(function() {
        if (!jQuery(this).parent().find('.form-text').val()) {
          alert("Enter a postcode");
          jQuery(this).parent().find('.form-text').focus();
          return false;
        }
        postcode = jQuery(this).parent().find('.form-text').val();
        jQuery(this).parent().find('.form-text').val('');
        jQuery.ajax({
          url: 'https://pce.afd.co.uk/afddata.pce?Serial=' + drupalSettings.afd.serial + '&Password=' + drupalSettings.afd.password + '&Data=Address&Task=Lookup&Fields=List&MaxQuantity=100&Country=UK&Lookup=' + postcode,
          type: "GET",
          dataType: "xml",
          cache: false,
          timeout: 60000,
          error: function(XMLHttpRequest, textStatus, errorThrown){
            if (XMLHttpRequest.status == 400) {
              jQuery('#address_results').html('<div class="red">Your postcode is not valid.</div>');
            }else if (XMLHttpRequest.status == 404) {
              jQuery('#address_results').html('<div class="red">No addresses could be found for this postcode.</div>');
            }else {
              jQuery('#address_results').html('<div class="red">An error has occured. Please complete your address manually.</div>');
            }
          },
          success: function(xml){
            addresses = [];
            var item = jQuery(xml).find("Item");
            jQuery(item).each(function() {
              postcode = jQuery(this).find('Postcode').text();
              addresses.push(jQuery(this).find('List').text().replace(postcode, '').trim());
              if (!jQuery('.webform-address--wrapper .form-type-textfield:last-child').find('input').val()) {
                jQuery('.webform-address--wrapper .form-type-textfield:last-child').find('input').val(postcode);
                jQuery('a.find_address').parent().find('.form-text').val(postcode);
              }
            });
            if (!addresses.length) {
              jQuery('#address_results').html('<div class="red">No addresses could be found for this postcode.</div>');
              return;
            }
            var html = '<select id="addresses" onchange="populateAddress();"><option value="">-- Select address --</option>';
            for (var i = 0; i < addresses.length; i++) {
              html += '<option value="' + i + '">' + addresses[i] + '</option>';
            }
            html += '</select><br /><br />';
            jQuery('#address_results').html(html);
          }
        });
        return false;
      });
        
      jQuery('#check_employer').click(function() {
        if (!jQuery('#edit-employer-name').val()) {
          alert("Enter the full name of your employer");
          jQuery('#edit-employer-name').focus();
          return false;
        }
        jQuery('#edit-employer-address-address').val('');
        jQuery('#edit-employer-address-address-2').val('');
        jQuery('#edit-employer-address-city').val('');
        jQuery('#edit-employer-address-postal-code').val('');
        jQuery('#employer_message').html('');
        jQuery.ajax({
          url: '/company-house/' + jQuery('#edit-employer-name').val(),
          type: "GET",
          dataType: "json",
          cache: false,
          timeout: 60000,
          error: function(XMLHttpRequest, textStatus, errorThrown){
  
          },
          success: function(data){
            if (!data.length) {
              jQuery('#employer_message').html('<i>' + jQuery('#edit-employer-name').val() + '</i> was not found in Companies House. If you think this is wrong, please check the spelling of your employer.');
            }
            if (data.length == 1) {
              jQuery('#edit-employer-name').val(data[0].title);
              var address = data[0].address;
              var l1 = "";
              if (address.premises) {
                l1 = address.premises + " ";
              }
              if (address.address_line_1) {
                l1 += address.address_line_1;
              }
              jQuery('#edit-employer-address-address').val(l1);
              checkValidate(jQuery('#edit-employer-address-address'));
              if (address.address_line_2) {
                jQuery('#edit-employer-address-address-2').val(address.address_line_2);
              }
              if (address.locality) {
                jQuery('#edit-employer-address-city').val(address.locality);
                checkValidate(jQuery('#edit-employer-address-city'));
              }
              if (address.postal_code) {
                jQuery('#edit-employer-address-postal-code').val(address.postal_code);
                checkValidate(jQuery('#edit-employer-address-postal-code'));
              }
            }else{
              companies = data;
              var html = '<h3>More than one company found for <i>\'' + jQuery('#edit-employer-name').val() + '</i>\'. Please select your employer from the list below:</h3>';
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
    };// end once
  }
};

function populateAddress() {
  jQuery('.webform-address--wrapper .form-type-textfield:nth-child(2)').find('input').val('');
  jQuery('.webform-address--wrapper .form-type-textfield:nth-child(3)').find('input').val('');
  var address = addresses[jQuery('#addresses').val()].replace(/ ,/g, '').split(',');
  jQuery('.webform-address--wrapper .form-type-textfield:nth-child(3)').find('input').val(address.pop().trim());
  checkValidate(jQuery('.webform-address--wrapper .form-type-textfield:nth-child(3)').find('input'));
  if (address.length > 2) {
    jQuery('.webform-address--wrapper .form-type-textfield:nth-child(1)').find('input').val(address[0] + ', ' + address[1].trim());
    checkValidate(jQuery('.webform-address--wrapper .form-type-textfield:nth-child(1)').find('input'));
    jQuery('.webform-address--wrapper .form-type-textfield:nth-child(2)').find('input').val(address[2].trim());
  }else{
    jQuery('.webform-address--wrapper .form-type-textfield:nth-child(1)').find('input').val(address[0].trim());
    checkValidate(jQuery('.webform-address--wrapper .form-type-textfield:nth-child(1)').find('input'));
    jQuery('.webform-address--wrapper .form-type-textfield:nth-child(2)').find('input').val(address[1].trim());
  }
  checkValidate(jQuery('.webform-address--wrapper .form-type-textfield:last-child').find('input'));
  jQuery('#address_results').html('');
}

function populateCompany(i) {
  data = companies[i];
  jQuery('#edit-employer-name').val(data.title);
  var address = data.address;
  var l1 = "";
  if (address.premises) {
    l1 = address.premises + " ";
  }
  if (address.address_line_1) {
    l1 += address.address_line_1;
  }
  jQuery('#edit-employer-address-address').val(l1);
  checkValidate(jQuery('#edit-employer-address-address'));
  if (address.address_line_2) {
    jQuery('#edit-employer-address-address-2').val(address.address_line_2);
  }
  if (address.locality) {
    jQuery('#edit-employer-address-city').val(address.locality);
    checkValidate(jQuery('#edit-employer-address-city'));
  }
  if (address.postal_code) {
    jQuery('#edit-employer-address-postal-code').val(address.postal_code);
    checkValidate(jQuery('#edit-employer-address-postal-code'));
  }
  jQuery('#employer_message').html('');
}

function checkValidate(element) {
  if (jQuery(element).hasClass('invalid')) {
    jQuery(element).parent().find('.invalid-feedback').hide();
    jQuery(element).removeClass('invalid');
    jQuery(element).addClass('valid');
  }
}

function dismissedDate() {
  var day = jQuery('.when-was-you-last-day-of-work .govuk-webform-elements-day').val();
  var month = jQuery('.when-was-you-last-day-of-work .govuk-webform-elements-month').val();
  var year = jQuery('.when-was-you-last-day-of-work .govuk-webform-elements-year').val();
  if (day && month && year.length == 4) {
    if (moment().diff(moment(day + '/' + month +'/' + year, 'DD/MM/YYYY'), 'days') >= 90) {
      jQuery('.last-day-of-work-out-of-time-text').slideDown();
    }else{
      jQuery('.last-day-of-work-out-of-time-text').slideUp();
    }
  }else{
    jQuery('.last-day-of-work-out-of-time-text').hide();
  }
}