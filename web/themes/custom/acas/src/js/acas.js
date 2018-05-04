Drupal.behaviors.acas = {
  attach: function(context, settings) {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      jQuery('body').addClass('ie' + parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))));
    }
    document.body.style.display="block";
  }
};

document.getElementById("menu-primary__icon").addEventListener("click", openNavigation);

function openNavigation() {
  var x = document.getElementById("menu-primary");
  if (x.className === "menu-primary") {
      x.className += " menu-primary--active";
  } else {
      x.className = "menu-primary";
  }
}

var primaryListItems = document.getElementsByClassName("menu-primary__item");

for(var i = 0; i < primaryListItems.length; i++) {
  var primaryLink = primaryListItems[i].querySelectorAll('.menu-primary__link')[0];
  primaryLink.addEventListener("click", function(e) {
    e.preventDefault();
    if(this.parentNode.classList.contains('menu-primary__item--active')) {
      this.parentNode.classList.remove('menu-primary__item--active')
    }
    else {
      this.parentNode.classList.add('menu-primary__item--active')
    }
  });
}

document.getElementById('edit-keys').addEventListener("click", function() {
  document.getElementsByClassName('form--inline')[0].classList.add('active');
});
