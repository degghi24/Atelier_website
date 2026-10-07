/* Atelier Tang - main.js
   Comportamento: menu mobile e validazione lato client del form.
   Se JavaScript non e' disponibile il sito resta pienamente utilizzabile. */

(function () {
  'use strict';

  // Segnala al CSS che JavaScript e' attivo
  document.documentElement.classList.add('js');

  /* ---------- Menu mobile ---------- */
  var toggle = document.querySelector('.nav-toggle');
  var menu = document.getElementById('menu-principale');

  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var aperto = menu.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', aperto ? 'true' : 'false');
    });

    // Chiude il menu dopo il click su un link o con il tasto Esc
    menu.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') {
        menu.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('is-open')) {
        menu.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
      }
    });
  }

  /* ---------- Validazione del form ---------- */
  var form = document.getElementById('form-prenotazione');
  if (!form) { return; }

  function errore(campo) {
    return document.getElementById('err-' + campo.id);
  }

  function messaggio(campo) {
    var v = campo.value.trim();

    if (campo.type === 'checkbox') {
      return campo.checked ? '' : 'Devi acconsentire al trattamento dei dati.';
    }
    if (campo.required && v === '') {
      return campo.tagName === 'SELECT' ? 'Scegli un servizio.' : 'Questo campo è obbligatorio.';
    }
    if (campo.id === 'nome' && v.length < 2) {
      return 'Inserisci almeno 2 caratteri.';
    }
    if (campo.id === 'nome' && !/^[A-Za-zÀ-ÖØ-öø-ÿ' .-]+$/.test(v)) {
      return 'Il nome può contenere solo lettere, spazi, apostrofi e trattini.';
    }
    if (campo.id === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) {
      return 'Inserisci un indirizzo email valido, ad esempio nome@dominio.it.';
    }
    if (campo.id === 'telefono' && v !== '' && !/^[0-9+ ]{6,20}$/.test(v)) {
      return 'Usa da 6 a 20 caratteri tra numeri, spazi e +.';
    }
    if (campo.id === 'data' && v !== '') {
      var oggi = new Date();
      oggi.setHours(0, 0, 0, 0);
      if (new Date(v) < oggi) { return 'Scegli una data a partire da oggi.'; }
    }
    if (campo.id === 'messaggio' && (v.length < 10 || v.length > 1000)) {
      return 'Il messaggio deve avere da 10 a 1000 caratteri.';
    }
    return '';
  }

  function controlla(campo) {
    var testo = messaggio(campo);
    var box = errore(campo);
    if (box) { box.textContent = testo; }
    if (testo) {
      campo.setAttribute('aria-invalid', 'true');
    } else {
      campo.removeAttribute('aria-invalid');
    }
    return testo === '';
  }

  var campi = form.querySelectorAll('input:not([type="hidden"]), select, textarea');

  Array.prototype.forEach.call(campi, function (campo) {
    campo.addEventListener('blur', function () { controlla(campo); });
    // Dopo un errore, ricontrolla mentre l'utente corregge
    campo.addEventListener('input', function () {
      if (campo.getAttribute('aria-invalid') === 'true') { controlla(campo); }
    });
  });

  form.addEventListener('submit', function (e) {
    var primoErrore = null;
    Array.prototype.forEach.call(campi, function (campo) {
      if (!controlla(campo) && !primoErrore) { primoErrore = campo; }
    });
    if (primoErrore) {
      e.preventDefault();
      primoErrore.focus();
    }
  });

  form.addEventListener('reset', function () {
    Array.prototype.forEach.call(campi, function (campo) {
      campo.removeAttribute('aria-invalid');
      var box = errore(campo);
      if (box) { box.textContent = ''; }
    });
  });
})();