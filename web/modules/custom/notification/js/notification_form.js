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
        jQuery('#feedback-form .feedback-question span').text(jQuery('input[name^="feedback_text"]').val());
      }else if (defaultFeedbackText) {
        jQuery('#feedback-form .feedback-question span').text(defaultFeedbackText);
      }
      // Date tests
      if (jQuery('.when-was-you-last-day-of-work.govuk-webform-elements--wrapper').length) {
        dismissedDate();
        jQuery('.when-was-you-last-day-of-work.govuk-webform-elements--wrapper').find('.govuk-webform-elements-day, .govuk-webform-elements-month, .govuk-webform-elements-year').on('input', dismissedDate);
      }
      //validateDates();
      // Hide buttons if one of several colleagues
      /*
      if (jQuery('.form-item-problem-several-colleagues').length) {
        jQuery('.form-item-problem-several-colleagues input').change(function() {
            if (this.value == 2) {
              jQuery('.form-actions').hide();
            }else{
              jQuery('.form-actions').show();
            }
        });
      }
      */
      // Only show block if preview page
      jQuery('section[id$=notificationpreviewfootermessage').hide();
      jQuery('section[id$=conciliationpreviewfootermessage').hide();
      if (jQuery('form[data-webform-wizard-current-page=webform_preview]').length) {
        jQuery('section[id$=notificationpreviewfootermessage').show();
        jQuery('section[id$=conciliationpreviewfootermessage').show();
      }
      
      // Prevent copy/paste on email fields
      jQuery('.form-email').bind("cut copy paste", function(e) {
        e.preventDefault();
        jQuery('.form-email').bind("contextmenu", function(e) {
          e.preventDefault();
        });
      });
      if (jQuery('.form-item-claimants').length && jQuery('.alert-danger').length) {
        // Move error alert to claimants upload
        // and style
        var alert = jQuery('.alert-danger');
        jQuery(alert).removeClass();
        jQuery(alert).addClass('invalid-feedback');
        jQuery(alert).find('button').remove();
        jQuery('.form-item-claimants label.form-required').replaceWith(alert);
      }
      
      jQuery('.webform-submission-form .webform-button--submit').click(function( ) {
        if (jQuery(this).parent().parent().parent().attr('id') == 'feedback-form') {
          // Prevent feedback webforms having wait dialog
          return;
        }
        var html = '<div id="overlay"></div><div id="modal"><div class="title-wrapper"><h2 class="title">' + jQuery('.webform-submission-form').attr('dialog-title') + '</h2></div>';
        html += '<div class="spinner-wrapper"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>';
        html += '<div class="modal-description">Please wait...</div></div>';
        jQuery('body').append(html);
      });
      // Validate email address
      jQuery('.webform-submission-form .form-email').focusout(function(){
        if (jQuery(this).val()) {
          if (!validateEmail(jQuery(this).val())) {
            jQuery(this).addClass('invalid');
            if (!jQuery(this).parent().find(".invalid-feedback").length) {
              jQuery('<div class="invalid-feedback">Invalid email address</div>').insertBefore(this);
            }else{
              jQuery(this).parent().find(".invalid-feedback").text('Invalid email address');
              jQuery(this).parent().find(".invalid-feedback").show();
            }
          }
        }
      });
      jQuery('.webform-submission-form .form-email').on('input', function() {
        jQuery(this).parent().find('.invalid-feedback').hide();
        jQuery(this).removeClass('invalid');
        jQuery(this).addClass('valid');
      });
      jQuery('<span class="no-file">&nbsp;No file chosen.</span>').insertAfter(jQuery('.webform-document-file .webform-file-button'));
      jQuery('.webform-submission-form .webform-button--next').onFirst('click', function( event ) {
        jQuery('.alert').remove();
        var scrollTo = null;
        if (jQuery('.webform-document-file.required').length) {
          if (jQuery('.webform-document-file.required .form-file').length && !jQuery('.webform-document-file.required .form-file').val()) {
            if (!jQuery('.webform-document-file.required').find(".invalid-feedback").length) {
              jQuery('<div class="invalid-feedback">You must provide a spreadsheet of claimants details.</div>').insertBefore(jQuery('.webform-document-file.required .webform-file-button'));
            }
            scrollTo = jQuery('.webform-document-file.required').parent();
          }
        }
        jQuery('.form-item .form-control').filter(':visible').each( function () {
          if (jQuery(this).hasClass('required')) {
            if (!jQuery(this).val()) {
              jQuery(this).removeClass('valid');
              jQuery(this).addClass('invalid');
              if (!jQuery(this).parent().find(".invalid-feedback").length) {
                jQuery('<div class="invalid-feedback">' + jQuery(this).attr('data-webform-required-error') + '</div>').insertBefore(this);
              }
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
        if (jQuery('.webform-submission-form .webform-email-confirm').length && (jQuery('.webform-submission-form .webform-email-confirm').val() || jQuery('.webform-submission-form .webform-email').val())) {
          if (jQuery('.webform-submission-form .webform-email').val() != jQuery('.webform-submission-form .webform-email-confirm').val()) {
            if (jQuery('.webform-submission-form .webform-email-confirm').parent().find('.invalid-feedback').length) {
              jQuery('.webform-submission-form .webform-email-confirm').parent().find('.invalid-feedback').text('Emails do not match');
              jQuery('.webform-submission-form .webform-email-confirm').parent().find('.invalid-feedback').show();
            }else{
              jQuery('<div class="invalid-feedback">Emails do not match</div>').insertBefore(jQuery('.webform-submission-form .webform-email-confirm'));
            }
            if (!scrollTo) {
              scrollTo = jQuery(jQuery('.webform-submission-form .webform-email-confirm')).parent();
            }
          }
        }
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
        //var url = "https://pce.afd.co.uk/afddata.pce?Serial=821447&Password=thursd4y&Data=Address&Task=Lookup&Fields=List&MaxQuantity=100&Country=UK&Lookup=" + postcode;
        var url = drupalSettings.afd.url + '?si_token=' + drupalSettings.afd.token + '&Lookup=' + postcode;
        jQuery.ajax({
          url: url,
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
            var html = '<select id="addresses"><option value="">-- Select address --</option>';
            for (var i = 0; i < addresses.length; i++) {
              html += '<option value="' + i + '">' + addresses[i] + '</option>';
            }
            html += '</select><br /><br />';
            jQuery('#address_results').html(html);
            jQuery('#addresses').focus();
            // Allow up/down to scroll thru address list without triggering change event.
            jQuery('#addresses').data('activation', 'activated').bind({
              keydown: function(event) {
                if(event.which === 38 || event.which === 40){
                  jQuery(this).data('activation', 'paused');
                }
                if(event.which === 13) {
                  jQuery(this).data('activation', 'activated');
                  jQuery(this).trigger('change');
                }
              },
              click: function() {
                if(jQuery(this).data('activation') === 'paused'){
                  jQuery(this).data('activation', 'activated');
                  jQuery(this).trigger('change');
                }
              },
              change: function() {    
                if(jQuery(this).data('activation') === 'activated'){
                  populateAddress();
                }
              }
          });
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
    }// end once
  }
};

function populateAddress() {
  jQuery('.webform-address--wrapper .form-type-textfield:nth-child(2)').find('input').val('');
  jQuery('.webform-address--wrapper .form-type-textfield:nth-child(3)').find('input').val('');
  jQuery('.webform-address--wrapper .form-type-textfield:nth-child(4)').find('input').val(postcode);
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

function validateDates() {
  jQuery('govuk-webform-elements--wrapper').each( function () {
    var day = jQuery(this).find('.govuk-webform-elements-day').val();
    var month = jQuery(this).find('.when-was-you-last-day-of-work .govuk-webform-elements-month').val();
    var year = jQuery(this).find('.when-was-you-last-day-of-work .govuk-webform-elements-year').val();
    if (!validateDate(day, month, year)) {
      jQuery('<div class="invalid-feedback">Invalid date</div>').insertBefore(jQuery(this).find('.panel-body'));
    }
  });
}

function validateDate(day, month, year) {
  if(year < 1000 || year > 3000 || month === 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 === 0 || (year % 100 !== 0 && year % 4 === 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
}

function validateEmail(email) {
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}