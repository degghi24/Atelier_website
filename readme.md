# Avvio del progetto in locale (Docker)

L'ambiente locale replica il server di consegna: PHP 8.4, MariaDB 11.8 e phpMyAdmin.

## 1. Installare Docker (una sola volta)

**Windows (consigliato: Docker Engine dentro WSL con Debian)**

1. PowerShell come amministratore: `wsl --install -d Debian`, poi riavvia il PC.
2. Apri "Debian" dal menu Start e crea utente e password.
3. Dentro Debian, attiva systemd: `sudo nano /etc/wsl.conf` e aggiungi

       [boot]
       systemd=true

   poi da PowerShell esegui `wsl --shutdown` e riapri Debian.
4. Installa Docker:

       sudo apt update && sudo apt install -y curl git
       curl -fsSL https://get.docker.com | sudo sh
       sudo usermod -aG docker $USER

   Chiudi e riapri il terminale Debian.
5. Verifica con `docker run hello-world`.

In alternativa si può usare Docker Desktop (con WSL 2 attivo), ma il progetto
va tenuto comunque dentro Debian, non in `C:`.

**macOS**: installa Docker Desktop (versione Apple Silicon o Intel).

**Linux**: `curl -fsSL https://get.docker.com | sudo sh` e `sudo usermod -aG docker $USER`.

## 2. Clonare e avviare

Dal terminale (su Windows: quello di Debian), nella home e non in `/mnt/c/...`:

    cd ~
    git clone https://github.com/UTENTE/NOME-REPO.git
    cd NOME-REPO
    docker compose up -d --build

La prima volta scarica le immagini e impiega qualche minuto.

## 3. Aprire il sito

- Sito: http://localhost:8080
- phpMyAdmin: http://localhost:8081 (utente e password nel file `.env`)

## 4. Uso quotidiano

- Prima di lavorare: `git pull`. Appena finito un pezzo: commit e `git push`.
- Modificare HTML, CSS, JS e PHP non richiede riavvii: basta ricaricare la pagina.
- Spegnere: `docker compose stop`. Riaccendere: `docker compose up -d`.
- Errori di PHP/Apache: `docker compose logs -f web`.

## 5. Se cambia il database

Lo script `db/init.sql` viene eseguito solo con il database vuoto. Dopo un `git pull`
che lo modifica, ricreate il database (i dati locali vanno persi):

    docker compose down -v
    docker compose up -d

## Problemi comuni

| Problema | Soluzione |
| --- | --- |
| `permission denied` su docker | Chiudi e riapri il terminale; su WSL esegui `wsl --shutdown` da PowerShell |
| `port is already allocated` | Un altro programma usa la porta 8080 o 8081: chiudilo o cambia la porta in `compose.yaml` |
| Il sito non parte su Windows | Controlla che il progetto sia dentro Debian e che Docker sia avviato |
| Il sito si carica lentamente | Il progetto è in `/mnt/c/...`: spostalo nella home di Debian |

# Organizzazione sito:

- [ ] prima pagina:
    - [ ] sezione above the fold
        - nome sartoria + logo su navbar (dimensione uguale a quella di esempio)
        - breve descrizione di presentazione
    - immagini 
    - sezione come lavoriamo (?)
- sezioni:
    - [ ] servizi su un'altra pagina
    - [ ] prenotazione
    - [ ] area riservata
        - [ ] lista prenotazioni
        - [ ] modifica orari
        - [ ] cambiare i servizi
- [ ] footer con:
    - orari
    - contatti
    - indirizzo
  

