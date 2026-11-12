# GameLab — catalogul de jocuri electronice

**Laborator de depanare PHP / MySQL.** Proiectul se bazează pe tiparul aplicației „Laborator26”: PHP procedural, PDO, MySQL, autentificare, sesiuni, cookie pentru temă și operații asupra datelor asociate utilizatorului. Tema aplicației este administrarea tabelului `games`.

> **Atenție:** aceasta este o aplicație didactică ce conține **10 defecte de COD SERVER-SIDE introduse intenționat**. Nu este destinată publicării pe un server de producție. Identificarea și documentarea defectelor constituie sarcina elevilor. Există instrucțiuni PHP/SQL greșite și fragmente de cod lipsă. Nu sunt incluse soluțiile. Fișierele CSS și aspectul interfeței nu fac parte din evaluare.

## 1. Instalare pe XAMPP

1. Copiați directorul `GameLab_BugHunt` în `C:\xampp\htdocs\`.
2. Porniți Apache și MySQL/MariaDB din XAMPP.
3. În `http://localhost/phpmyadmin`, accesați **Import** și încărcați fișierul `schema.sql`. Importul creează baza de date `gamelab_bug_hunt` și tabelele `users` și `games`, apoi adaugă un utilizator demonstrativ și 12 jocuri fictive.
4. Dacă MySQL nu utilizează `root` cu parolă goală, copiați `db.example.php` sub numele `db.local.php` și adaptați setările. Nu modificați direct `db.php` dacă nu este necesar.
5. Accesați `http://localhost/GameLab_BugHunt/`.
6. Autentificați-vă cu contul demonstrativ sau creați un cont nou (care va începe cu lista goală).

**Cont demonstrativ (doar pentru XAMPP local):** utilizator `demo`, parolă `Demo2026!`. Modificați parola/ștergeți contul înainte de orice utilizare în afara laboratorului.

**Cerințe:** PHP 8.1+ cu extensia `pdo_mysql`, MySQL 8/MariaDB, Apache, browser. Nu este necesar Composer sau Node.js.

## 2. Baza de date

- `users(id, username, password_hash, created_at)` — utilizatori autentificați;
- `games(id, user_id, title, genre, platform, price, stock, release_date, created_at)` — jocurile contului;
- relație: `users (1) -> (N) games`; prețul este păstrat în `DECIMAL(10,2)`, iar stocul în `INT`.
- toate interogările de modificare folosesc `user_id` din sesiune, nu din formular.

## 3. Funcționalitatea așteptată

1. Înregistrare cont și autentificare.
2. Listarea jocurilor deținute de contul autentificat.
3. Adăugarea unui joc (titlu, gen, platformă, preț, stoc, dată lansare).
4. Modificarea proprietăților jocului existent.
5. Ștergerea jocului selectat.
6. Căutare după fragmente de titlu; filtru după gen; afișarea celor mai recente înregistrări primele.
7. Statistici: număr total de titluri, titluri cu stoc pozitiv și valoarea stocului (`preț × cantitate`).
8. Etichetă „Epuizat” pentru stoc = 0 și „În stoc” pentru stoc pozitiv.
9. Validare: titlu 1–150 caractere, preț în intervalul 0–9999,99 MDL, stoc întreg în intervalul 0–500.
10. Schimbarea temei luminoase/întunecate și deconectare.

## 4. Fișierele principale

| Fișier | Rol |
|---|---|
| `schema.sql` | Crearea bazei de date și înregistrările demonstrative |
| `config.php` | Sesiune și token CSRF |
| `db.php`, `db.example.php` | Configurarea și conexiunea PDO |
| `functions.php` | Funcții de validare, escapare și redirecționare |
| `register.php`, `login.php`, `logout.php` | Fluxul contului |
| `dashboard.php` | Statistici, filtrare, tabel și formular de adăugare |
| `game_add.php`, `game_update.php`, `game_delete.php` | Operații INSERT, UPDATE, DELETE |
| `game_edit.php` | Formularul de editare |
| `preferinte.php` | Cookie pentru temă |
| `partials/`, `style.css` | Interfața |
| `teste/SCENARII.md` | Idei de verificare manuală (fără soluții) |

## 5. Sarcina elevului

Explorați funcționalitățile, identificați **10 defecte distincte în codul backend PHP/SQL** și documentați-le în fișa Word separată, cu **captură de ecran proprie**, pași de reproducere, comportament obținut, comportament așteptat, cauză, fișier/linie, captură obligatorie a secvenței de cod afectate și propunere de remediere. După corectare, realizați o nouă testare și notați rezultatul.

Dacă browserul afișează o eroare de server, verificați și jurnalul Apache/PHP; pentru o eroare de sintaxă puteți folosi `php -l nume_fisier.php`. Nu inserați parole în capturile de ecran. Lucrați doar pe o copie locală și cu date fictive. Pentru refacerea setului de test, importați într-o bază de date nouă sau ștergeți datele de test din contul vostru.

## 6. Limitări și bune practici

Aplicația este prevăzută exclusiv pentru exerciții într-un server local. Sunt folosite `password_hash`/`password_verify`, interogări parametrizate PDO, verificare CSRF, escaparea afișării și verificarea proprietarului în `WHERE`. Aceste măsuri nu transformă varianta de laborator într-un produs pregătit pentru publicare.
