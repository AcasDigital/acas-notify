var addresses;
var companies;
var defaultFeedbackText = '';
var postcode = '';

Drupal.behaviors.notification_form = {
  attach: function(context, settings) {
    // Had to go old-school by checking for class, jQuery.once was not working for ajax forms
    if (!jQuery('.webform-submission-form-notification').hasClass('notification_form_processed')) {
      jQuery('.webform-submission-form-notification').addClass('notification_form_processed');
      if (jQuery('.webform-submission-form-notification .webform-wizard-pages-link').length) {
        jQuery('.webform-submission-form-notification .webform-wizard-pages-link').html(jQuery('.webform-submission-form-notification .webform-wizard-pages-link').html().replace('Edit', 'Change'));
      }
      // Show how many claimants have been imported. Only reliable way to do this is via a cookie
      if (getCookie('claimants')) {
        jQuery('<div class="total-claimants">Imported ' + getCookie('claimants') + ' claimants.</div>').insertBefore(jQuery('section[data-drupal-selector="edit-your-details"] h2.title'));
        document.cookie = "claimants=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
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
        setTimeout(function() {
            scrollToTop(alert);
        }, 500);
      }
      
      jQuery('.webform-submission-form-notification .webform-button--submit').click(function( ) {
        if (jQuery(this).parent().parent().parent().attr('id') == 'feedback-form') {
          // Prevent feedback webforms having wait dialog
          return;
        }
        var html = '<div id="overlay"></div><div id="modal"><div class="title-wrapper"><h2 class="title">' + jQuery('.webform-submission-form-notification').attr('dialog-title') + '</h2></div>';
        html += '<div class="spinner-wrapper"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>';
        html += '<div class="modal-description">Please wait...</div></div>';
        jQuery('body').append(html);
      });
      // Validate email address
      jQuery('.webform-submission-form-notification .form-email').focusout(function(){
        if (jQuery(this).val()) {
          if (!validateEmail(jQuery(this).val())) {
            jQuery(this).addClass('invalid');
            if (!jQuery(this).parent().find(".invalid-feedback").length) {
              jQuery('<div class="invalid-feedback">Invalid email address</div>').insertBefore(this);
            }else{
              jQuery(this).parent().find(".invalid-feedback").text('Invalid email address');
              jQuery(this).parent().find(".invalid-feedback").show();
            }
            jQuery(this).focus();
          }
        }
      });
      jQuery('.webform-submission-form-notification .form-email').on('input', function() {
        jQuery(this).parent().find('.invalid-feedback').hide();
        jQuery(this).removeClass('invalid');
        jQuery(this).addClass('valid');
      });
      // Add 'No file chosen' to file uploads
      jQuery('<span class="no-file">&nbsp;No file chosen.</span>').insertAfter(jQuery('.webform-document-file .webform-file-button'));
      // Webform telephone ext does not set attributes. Set them here
      jQuery('[data-drupal-selector="edit-acas-claimrepmainphoneno-ext"]').attr('maxlength', '10');
      jQuery('[data-drupal-selector="edit-acas-claimrepmainphoneno-ext"]').attr('size', '10');
      jQuery('[data-drupal-selector="edit-acas-claimrepmainphoneno-ext"]').attr('type', 'tel');
      
      // Remove alert on postcode input
      jQuery('.find-address-wrapper .form-text').on('input', function() {
        jQuery(this).parent().find('.invalid-feedback').hide();
        jQuery(this).removeClass('invalid');
        jQuery(this).addClass('valid');
      });
      
      // **** End of fields config ****
      
      // Next button click
      jQuery('.webform-submission-form-notification .webform-button--next').onFirst('click', function( event ) {
        jQuery('.alert').remove();
        var scrollTo = null;
        var focus = null;
        if (jQuery('.webform-document-file.required').length) {
          if (jQuery('.webform-document-file.required .form-file').length && !jQuery('.webform-document-file.required .form-file').val()) {
            if (!jQuery('.webform-document-file.required').find(".invalid-feedback").length) {
              jQuery('<div class="invalid-feedback">You must provide a spreadsheet of claimants details.</div>').insertBefore(jQuery('.webform-document-file.required .webform-file-button'));
            }
            if (!scrollTo) {
              scrollTo = jQuery('.webform-document-file.required').parent();
            }
            if (!focus) {
              focus = jQuery('.webform-document-file');
            }
          }
        }
        // Validate dates
        jQuery('.govuk-webform-elements--wrapper').each( function () {
          var day = parseInt(jQuery(this).find('.govuk-webform-elements-day').val());
          var month = parseInt(jQuery(this).find('.govuk-webform-elements-month').val());
          var year = parseInt(jQuery(this).find('.govuk-webform-elements-year').val());
          if (day && month && year && !validateDate(day, month, year)) {
            if (!jQuery(this).find(".invalid-feedback").length) {
              jQuery('<div class="invalid-feedback">Invalid date</div>').insertAfter(jQuery(this).find('.panel-body p'));
            }else{
              jQuery(this).find(".invalid-feedback").text('Invalid date');
            }
            if (!scrollTo) {
              scrollTo = jQuery(this).find('.panel-body p');
            }
            if (!focus) {
              focus = jQuery(this).find('.govuk-webform-elements-day');
            }
          }
          if (!jQuery(this).attr('allow_future') && !scrollTo) {
            var d = new Date(year, month, day);
            if (d > new Date()) {
              if (!jQuery(this).find(".invalid-feedback").length) {
                jQuery('<div class="invalid-feedback">Date can\'t be in the future</div>').insertAfter(jQuery(this).find('.panel-body p'));
              }else{
                jQuery(this).find(".invalid-feedback").text('Date can\'t be in the future');
              }
              if (!scrollTo) {
                scrollTo = jQuery(this).find('.panel-body p');
              }
              if (!focus) {
                focus = jQuery(this).find('.govuk-webform-elements-year');
              }
            }
          }
        });
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
              if (!focus) {
                focus = jQuery(this);
              }
            }else{
              jQuery(this).addClass('valid');
            }
          }else{
            jQuery(this).addClass('valid');
          }
        });
        if (jQuery('.webform-submission-form-notification .webform-email-confirm').length && (jQuery('.webform-submission-form-notification .webform-email-confirm').val() || jQuery('.webform-submission-form-notification .webform-email').val())) {
          if (jQuery('.webform-submission-form-notification .webform-email').val() != jQuery('.webform-submission-form-notification .webform-email-confirm').val()) {
            if (jQuery('.webform-submission-form-notification .webform-email-confirm').parent().find('.invalid-feedback').length) {
              jQuery('.webform-submission-form-notification .webform-email-confirm').parent().find('.invalid-feedback').text('Emails do not match');
              jQuery('.webform-submission-form-notification .webform-email-confirm').parent().find('.invalid-feedback').show();
            }else{
              jQuery('<div class="invalid-feedback">Emails do not match</div>').insertBefore(jQuery('.webform-submission-form-notification .webform-email-confirm'));
            }
            if (!scrollTo) {
              scrollTo = jQuery('.webform-submission-form-notification .webform-email-confirm').parent();
            }
            if (!focus) {
              focus = jQuery('.webform-submission-form-notification .webform-email-confirm');
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
        if (focus) {
          jQuery(focus).focus();
        }
        return true;
      });
      jQuery('a.find_address').click(function() {
        if (!jQuery(this).parent().find('.form-text').val()) {
          if (!jQuery(this).parent().find(".invalid-feedback").length) {
            jQuery('<div class="invalid-feedback">Enter a postcode</div>').insertBefore(jQuery(this).parent().find('.form-text'));
          }else {
            jQuery(this).parent().find(".invalid-feedback").show();
          }
          jQuery(jQuery(this).parent().find('.form-text')).removeClass('valid');
          jQuery(jQuery(this).parent().find('.form-text')).addClass('invalid');
          jQuery(this).parent().find('.form-text').focus();
          return false;
        }
        jQuery('#address_results').html(ajaxLoader());
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
    } //end once
    if (jQuery('.form-item-claimants').length && jQuery('.alert-danger').length) {
      // Move error alert to claimants upload
      // and style
      var alert = jQuery('.alert-danger');
      jQuery(alert).removeClass();
      jQuery(alert).addClass('invalid-feedback');
      jQuery(alert).find('button').remove();
      jQuery(alert).find('ul').css('margin', 0);
      jQuery(alert).find('ul').css('list-style', 'none');
      jQuery('.form-item-claimants label.form-required').replaceWith(alert);
      jQuery([document.documentElement, document.body]).animate({
        scrollTop: jQuery(alert).offset().top - 100
      }, 500);
    }
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

function scrollToTop(element) {
  jQuery([document.documentElement, document.body]).animate({
    scrollTop: jQuery(element).offset().top - 100
  }, 500);
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function ajaxLoader() {
  return '<div class="ajax-progress ajax-progress-throbber"><div class="ajax-loader"><div class="ajax-throbber sk-circle"><div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div></div></div></div>';
}