# Session09 – 9. hét (MySQLi bevezetés)

Futtatás:
- A Session09 mappában: `php -S localhost:8000`
- Böngésző: `http://localhost:8000/Task01/` … `Task05/`

## Előkészítés (MySQL)
1) Indítsa el a MySQL szervert (pl. XAMPP).
2) Hozzon létre egy adatbázist (ajánlott név): `webprog_lab`
3) Minden Task mappában van `schema.sql`:
   - futtassa le MySQL-ben (phpMyAdmin vagy mysql kliens)
4) Minden Task mappában van `config.sample.php`:
   - másolja `config.php` néven és állítsa be a kapcsolat adatait.

Megjegyzés:
- A megoldások MySQLi **prepared statement**-eket használnak (SQL injection ellen).
- Hibakezelés: `mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)` + `try/catch`.
