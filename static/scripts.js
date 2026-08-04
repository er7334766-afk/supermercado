document.addEventListener('DOMContentLoaded', function () {
  var comingSoon = document.querySelectorAll('[data-action="coming-soon"]');
  comingSoon.forEach(function (button) {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      alert('Funcionalidad en construccion. Esta accion estara disponible pronto.');
    });
  });

  var navigationButtons = document.querySelectorAll('[data-target]');
  navigationButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      var target = button.getAttribute('data-target');
      if (target) {
        event.preventDefault();
        window.location.href = target;
      }
    });
  });
});
