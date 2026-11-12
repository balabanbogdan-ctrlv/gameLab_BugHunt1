# Scenarii orientative de investigare

Scop: identificarea erorilor de programare PHP/SQL și a secvențelor de cod omise, fără a consulta o listă de soluții. Nu analizați CSS pentru punctaj.

- **Date inițiale:** autentifică-te în contul `demo` și confirmă că există jocuri cu genuri, platforme, prețuri și stocuri diferite.
- **Adăugare:** creează jocuri cu nume scurt/lung, preț întreg/zecimal și stoc la limitele intervalului specificat.
- **Căutare:** testează titlul complet și un fragment de titlu. Compară rezultatele.
- **Filtrare:** selectează un gen și verifică fiecare rezultat.
- **Listare:** adaugă un joc nou și verifică poziția lui în tabel.
- **Actualizare:** modifică separat fiecare proprietate, apoi verifică valorile persistate.
- **Ștergere:** încearcă eliminarea unei înregistrări proprii.
- **Statistici:** compară panoul cu o verificare manuală a datelor.
- **Stoc:** verifică jocuri cu 0, 1 și mai multe unități.
- **Confirmare:** pentru fiecare defect, repetă testul după ce propui/implementezi o corecție.

Folosește fișa Word pentru a documenta **10 defecte diferite**, nu 10 capturi ale aceleiași probleme. Notează exact datele introduse și adresa paginii. Pentru FIECARE defect trebuie o captură din editorul de cod și o captură a efectului în aplicație sau a mesajului din jurnalul PHP/Apache. Puteți rula `php -l` pentru a detecta erori de sintaxă.
