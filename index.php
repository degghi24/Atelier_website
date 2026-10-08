<?php
declare(strict_types=1);
session_start();

/* Token CSRF per il form di prenotazione */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/* Dati restituiti da php/prenota.php dopo il controllo lato server */
$flash  = $_SESSION['flash']  ?? null;   // ['tipo' => 'ok'|'errore', 'testo' => '...']
$errori = $_SESSION['errori'] ?? [];     // ['nome' => 'messaggio', ...]
$vecchi = $_SESSION['vecchi'] ?? [];     // valori gia' inseriti dall'utente
unset($_SESSION['flash'], $_SESSION['errori'], $_SESSION['vecchi']);

$loggato = isset($_SESSION['utente']);

/* Funzioni di supporto: escape dell'output e attributi del form */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function val(array $v, string $k): string
{
    return e($v[$k] ?? '');
}
function err(array $errori, string $k): string
{
    return isset($errori[$k]) ? e($errori[$k]) : '';
}
function invalido(array $errori, string $k): string
{
    return isset($errori[$k]) ? ' aria-invalid="true"' : '';
}

/* Contenuti ripetuti */
$servizi = [
    'abito'       => ['Abiti e giacche su misura',      'Completi, giacche e pantaloni disegnati sulla tua figura, con rifiniture e fodere a scelta.'],
    'camicia'     => ['Camicie su misura',              'Colli, polsini e tessuti scelti insieme. Vestibilità precisa, cuciture pulite.'],
    'cerimonia'   => ['Abiti da cerimonia e da sposo',  'Per matrimoni ed eventi importanti, con tempi pianificati e più prove abito.'],
    'donna'       => ['Abiti da donna',                 'Tailleur, abiti e pantaloni su misura, dal modello al capo finito.'],
    'riparazione' => ['Modifiche e riparazioni',        'Orli, strette, cambi di cerniera e rinnovo di capi che vuoi continuare a indossare.'],
    'tessuti'     => ['Consulenza sui tessuti',         'Ti aiutiamo a scegliere peso, trama e colore in base a stagione, uso e budget.'],
];
$passi = [
    ['Consulenza',         'Ci racconti cosa ti serve e quando. Guardiamo insieme modelli e campioni di tessuto.'],
    ['Misure e modello',   'Prendiamo le misure e disegniamo il cartamodello personale.'],
    ['Prima prova',        'Il capo viene imbastito e provato addosso per correggere linee e proporzioni.'],
    ['Finitura',           'Dopo le ultime correzioni cuciamo a mano le rifiniture e stiriamo il capo.'],
    ['Consegna',           'Ritiri il capo finito. Per ogni ritocco successivo siamo a disposizione.'],
];
$realizzazioni = [
    ['swatch-lana',   'Giacca in lana grigia'],
    ['swatch-cotone', 'Camicia in cotone azzurro'],
    ['swatch-notte',  'Abito da sposo blu notte'],
    ['swatch-lino',   'Tailleur in lino'],
];
$faq = [
    ['Quanto tempo serve per un abito su misura?',            'In media da quattro a sei settimane, con due o tre prove. Per cerimonie ed eventi conviene contattarci con anticipo.'],
    ['Quanto costa un capo su misura?',                       'Il prezzo dipende da modello e tessuto. Dopo la consulenza ricevi un preventivo chiaro, senza impegno.'],
    ['Fate anche riparazioni di capi non cuciti da voi?',     'Sì. Ci occupiamo di modifiche e riparazioni su capi di qualsiasi provenienza.'],
    ['Serve l\'appuntamento?',                                'Sì, lavoriamo su appuntamento per dedicarti tutto il tempo necessario.'],
];
$orari = [
    ['Lunedì',            'Chiuso'],
    ['Martedì - Venerdì', '9.30 - 12.30 e 15.00 - 19.00'],
    ['Sabato',            '9.30 - 13.00'],
    ['Domenica',          'Chiuso'],
];

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Atelier Tang | Sartoria artigianale su misura a Padova</title>
  <meta name="description" content="Atelier Tang a Padova: sartoria artigianale su misura per abiti, giacche, camicie e abiti da cerimonia. Prenota una consulenza e una prova." />
  <meta name="keywords" content="sartoria Padova, abiti su misura, sartoria artigianale, abito da sposo, riparazioni sartoriali, atelier Padova" />
  <link rel="stylesheet" href="src/css/style.css" />
  <script src="src/js/main.js" defer="defer"></script>
</head>

<body id="top">

  <a class="skip-link" href="#contenuto">Vai al contenuto principale</a>

  <header class="site-header">
    <div class="container header-inner">
      <a class="logo" href="index.php" aria-label="Atelier Tang, torna alla home">
        <span class="logo-name">Atelier Tang</span>
        <span class="logo-sub">Sartoria artigianale, Padova</span>
      </a>

      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menu-principale">Menu</button>

      <nav id="menu-principale" class="main-nav" aria-label="Navigazione principale">
        <ul>
          <li><a href="index.php" aria-current="page">Home</a></li>
          <li><a href="#atelier">L'atelier</a></li>
          <li><a href="#servizi">Servizi</a></li>
          <li><a href="#processo">Come lavoriamo</a></li>
          <li><a href="#realizzazioni">Realizzazioni</a></li>
          <li><a href="#prenota">Prenota</a></li>
          <li><a href="#dove">Dove siamo</a></li>
<?php if ($loggato): ?>
          <li><a class="nav-login" href="area.php">Le mie richieste</a></li>
          <li><a href="logout.php">Esci</a></li>
<?php else: ?>
          <li><a class="nav-login" href="login.php">Area riservata</a></li>
<?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <main id="contenuto">

    <section class="hero" aria-labelledby="titolo-hero">
      <div class="container hero-inner">
        <div class="hero-text">
          <h1 id="titolo-hero">Atelier Tang, sartoria artigianale a Padova</h1>
          <p class="hero-lead">
            Ogni capo nasce da una misura, da un tessuto scelto con cura e da molte ore di lavoro a mano.
            Abiti, giacche, camicie e capi da cerimonia cuciti su di te, nel cuore di Padova.
          </p>
          <p class="hero-actions">
            <a class="btn btn-primary" href="#prenota">Prenota una consulenza</a>
            <a class="btn btn-secondary" href="#servizi">Scopri i servizi</a>
          </p>
        </div>
        <figure class="hero-figure">
          <span class="hero-cloth" role="img" aria-label="Campione di tessuto gessato blu notte con una linea di gesso diagonale"></span>
          <figcaption>Gessato blu notte, segnato con il gesso del sarto.</figcaption>
        </figure>
      </div>
    </section>

    <section id="atelier" class="section" aria-labelledby="titolo-atelier">
      <div class="container two-col">
        <div>
          <h2 id="titolo-atelier">L'atelier</h2>
          <p>
            Atelier Tang è una piccola sartoria artigianale di Padova. Lavoriamo su appuntamento,
            con pochi clienti alla volta, perché ogni capo richiede tempo, ascolto e attenzione ai dettagli.
          </p>
          <p>
            Selezioniamo lane, lini, sete e cotoni da tessitori italiani ed europei. Il modello viene disegnato
            sulle tue misure, imbastito e provato più volte, fino a quando veste come deve.
          </p>
          <ul class="values">
            <li>Taglio e cucitura eseguiti a mano</li>
            <li>Tessuti di qualità, anche a campione</li>
            <li>Modifiche e riparazioni su capi di ogni provenienza</li>
          </ul>
        </div>
        <figure class="atelier-figure">
          <span class="fabric-table" role="img" aria-label="Campione di lino naturale dalla trama visibile"></span>
          <figcaption>Lino naturale, uno dei tessuti della nostra campionatura.</figcaption>
        </figure>
      </div>
    </section>

    <section id="servizi" class="section section-alt" aria-labelledby="titolo-servizi">
      <div class="container">
        <h2 id="titolo-servizi">I nostri servizi</h2>
        <p class="section-intro">Dal capo completamente nuovo alla piccola riparazione: ti seguiamo in ogni fase.</p>
        <div class="grid services-grid">
<?php foreach ($servizi as $s): ?>
          <article class="service">
            <h3><?= e($s[0]) ?></h3>
            <p><?= e($s[1]) ?></p>
          </article>
<?php endforeach; ?>
        </div>
      </div>
    </section>

    <section id="processo" class="section" aria-labelledby="titolo-processo">
      <div class="container">
        <h2 id="titolo-processo">Come lavoriamo</h2>
        <ol class="steps">
<?php foreach ($passi as $p): ?>
          <li>
            <h3><?= e($p[0]) ?></h3>
            <p><?= e($p[1]) ?></p>
          </li>
<?php endforeach; ?>
        </ol>
      </div>
    </section>

    <section id="realizzazioni" class="section section-alt" aria-labelledby="titolo-realizzazioni">
      <div class="container">
        <h2 id="titolo-realizzazioni">Alcune realizzazioni</h2>
        <div class="grid gallery">
<?php foreach ($realizzazioni as $r): ?>
          <figure>
            <span class="swatch <?= e($r[0]) ?>" aria-hidden="true"></span>
            <figcaption><?= e($r[1]) ?></figcaption>
          </figure>
<?php endforeach; ?>
        </div>
      </div>
    </section>
<!--

    <section class="section" aria-labelledby="titolo-recensioni">
      <div class="container">
        <h2 id="titolo-recensioni">Cosa dicono i clienti</h2>
        <div class="grid reviews">
          <blockquote>
            <p>Il mio abito da sposo è stato provato tre volte. Alla fine mi stava come un guanto.</p>
            <footer>Marco, Padova</footer>
          </blockquote>
          <blockquote>
            <p>Ho portato una giacca di mio padre e mi è stata riadattata con grande rispetto per l'originale.</p>
            <footer>Chiara, Abano Terme</footer>
          </blockquote>
          <blockquote>
            <p>Competenza e puntualità. Le camicie su misura sono diventate la mia divisa da lavoro.</p>
            <footer>Luca, Vicenza</footer>
          </blockquote>
        </div>
        <p class="note-link">
          Hai già lavorato con noi? <a href="recensioni.php">Leggi o scrivi una recensione</a>.
        </p>
      </div>
    </section>
-->
    <section id="prenota" class="section section-alt" aria-labelledby="titolo-prenota">
      <div class="container two-col">
        <div>
          <h2 id="titolo-prenota">Prenota una consulenza</h2>
          <p>
            Compila il modulo: ti ricontatteremo entro due giorni lavorativi per fissare l'appuntamento.
            I campi contrassegnati con asterisco sono obbligatori.
          </p>
          <p>
            Dopo l'accesso all'<a href="login.php">area riservata</a> potrai vedere,
            modificare o cancellare le tue richieste.
          </p>
        </div>

        <form id="form-prenotazione" action="php/prenota.php" method="post" novalidate="novalidate">
<?php if (is_array($flash)): ?>
          <p class="flash flash-<?= e($flash['tipo'] ?? 'errore') ?>" role="status"><?= e($flash['testo'] ?? '') ?></p>
<?php endif; ?>
          <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>" />

          <fieldset>
            <legend>I tuoi dati</legend>

            <p class="field">
              <label for="nome">Nome e cognome *</label>
              <input type="text" id="nome" name="nome" required="required" maxlength="80"
                     autocomplete="name" value="<?= val($vecchi, 'nome') ?>"
                     aria-describedby="err-nome"<?= invalido($errori, 'nome') ?> />
              <span id="err-nome" class="error" role="alert"><?= err($errori, 'nome') ?></span>
            </p>

            <p class="field">
              <label for="email">Email *</label>
              <input type="email" id="email" name="email" required="required" maxlength="120"
                     autocomplete="email" value="<?= val($vecchi, 'email') ?>"
                     aria-describedby="err-email"<?= invalido($errori, 'email') ?> />
              <span id="err-email" class="error" role="alert"><?= err($errori, 'email') ?></span>
            </p>

            <p class="field">
              <label for="telefono">Telefono</label>
              <input type="tel" id="telefono" name="telefono" maxlength="20"
                     autocomplete="tel" pattern="[0-9+ ]{6,20}" value="<?= val($vecchi, 'telefono') ?>"
                     aria-describedby="aiuto-tel err-telefono"<?= invalido($errori, 'telefono') ?> />
              <span id="aiuto-tel" class="hint">Solo numeri, spazi e il segno +.</span>
              <span id="err-telefono" class="error" role="alert"><?= err($errori, 'telefono') ?></span>
            </p>
          </fieldset>

          <fieldset>
            <legend>La tua richiesta</legend>

            <p class="field">
              <label for="servizio">Servizio *</label>
              <select id="servizio" name="servizio" required="required"
                      aria-describedby="err-servizio"<?= invalido($errori, 'servizio') ?>>
                <option value="">Scegli un servizio</option>
<?php foreach ($servizi as $chiave => $s): ?>
                <option value="<?= e($chiave) ?>"<?= (($vecchi['servizio'] ?? '') === $chiave) ? ' selected="selected"' : '' ?>><?= e($s[0]) ?></option>
<?php endforeach; ?>
              </select>
              <span id="err-servizio" class="error" role="alert"><?= err($errori, 'servizio') ?></span>
            </p>

            <p class="field">
              <label for="data">Data preferita *</label>
              <input type="date" id="data" name="data" required="required" min="<?= date('Y-m-d') ?>"
                     value="<?= val($vecchi, 'data') ?>"
                     aria-describedby="err-data"<?= invalido($errori, 'data') ?> />
              <span id="err-data" class="error" role="alert"><?= err($errori, 'data') ?></span>
            </p>

            <p class="field">
              <label for="messaggio">Messaggio *</label>
              <textarea id="messaggio" name="messaggio" rows="5" cols="40" required="required"
                        minlength="10" maxlength="1000"
                        aria-describedby="aiuto-msg err-messaggio"<?= invalido($errori, 'messaggio') ?>><?= val($vecchi, 'messaggio') ?></textarea>
              <span id="aiuto-msg" class="hint">Racconta cosa desideri: capo, occasione, tempi. Da 10 a 1000 caratteri.</span>
              <span id="err-messaggio" class="error" role="alert"><?= err($errori, 'messaggio') ?></span>
            </p>

            <p class="field field-check">
              <input type="checkbox" id="privacy" name="privacy" value="1" required="required"
                     aria-describedby="err-privacy"<?= invalido($errori, 'privacy') ?> />
              <label for="privacy">Acconsento al trattamento dei dati per essere ricontattato. *</label>
              <span id="err-privacy" class="error" role="alert"><?= err($errori, 'privacy') ?></span>
            </p>
          </fieldset>

          <p>
            <button type="submit" class="btn btn-primary">Invia la richiesta</button>
            <button type="reset" class="btn btn-secondary">Annulla</button>
          </p>
        </form>
      </div>
    </section>

    <section class="section" aria-labelledby="titolo-faq">
      <div class="container">
        <h2 id="titolo-faq">Domande frequenti</h2>
<?php foreach ($faq as $f): ?>
        <details>
          <summary><?= e($f[0]) ?></summary>
          <p><?= e($f[1]) ?></p>
        </details>
<?php endforeach; ?>
      </div>
    </section>

    <section id="dove" class="section section-alt" aria-labelledby="titolo-dove">
      <div class="container two-col">
        <div>
          <h2 id="titolo-dove">Dove siamo</h2>
          <address>
            Atelier Tang<br />
            Via Esempio 12, 35121 Padova (PD)<br />
            Tel. <a href="tel:+390490000000">049 000 0000</a><br />
            Email <a href="mailto:info@ateliertang.example">info@ateliertang.example</a>
          </address>
          <p>A pochi minuti a piedi dal centro storico. Parcheggi a pagamento nelle vie vicine.</p>
        </div>

        <table class="orari">
          <caption>Orari di apertura (su appuntamento)</caption>
          <thead>
            <tr><th scope="col">Giorno</th><th scope="col">Orario</th></tr>
          </thead>
          <tbody>
<?php foreach ($orari as $o): ?>
            <tr><th scope="row"><?= e($o[0]) ?></th><td><?= e($o[1]) ?></td></tr>
<?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <footer class="site-footer">
    <div class="container footer-inner">
      <p class="footer-brand">Atelier Tang, sartoria artigianale a Padova</p>
      <nav aria-label="Link del sito">
        <ul>
          <li><a href="#top">Torna su</a></li>
          <li><a href="login.php">Area riservata</a></li>
          <li><a href="mappa-sito.html">Mappa del sito</a></li>
          <li><a href="privacy.html">Privacy</a></li>
          <li><a href="accessibilita.html">Accessibilità</a></li>
        </ul>
      </nav>
      <p><small>&#169; <?= date('Y') ?> Atelier Tang. Progetto di Tecnologie Web, Università di Padova.</small></p>
    </div>
  </footer>

</body>
</html>