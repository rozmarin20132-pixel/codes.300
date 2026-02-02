# System Zarządzania Książkami i Autorami (Laravel API)

Projekt to nowoczesne API zbudowane we frameworku Laravel, służące do zarządzania bazą autorów i ich dzieł. System wykorzystuje **Laravel Sanctum** do autoryzacji oraz autorski system **Pipeline** do zaawansowanego filtrowania danych. Całość jest w pełni zkonteneryzowana przy użyciu Dockera.

## 🛠 Konfiguracja środowiska

Zanim uruchomisz projekt, musisz przygotować plik ze zmiennymi środowiskowymi. Projekt zawiera gotowy wzorzec `.env.dev` skonfigurowany pod kontenery Docker.

```bash
# Skopiuj szablon konfiguracji
cp .env.dev .env
```

Upewnij się, że w pliku `.env` dane bazy danych są zgodne z serwisem Docker (domyślnie w `.env.dev`):

```env
DB_HOST=db
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

## 🚀 Szybki start (Docker)

Do zarządzania aplikacją przygotowano plik `Makefile`, który automatyzuje pracę z kontenerami.

### 1. Budowa i instalacja

Poniższa komenda zbuduje obrazy, uruchomi kontenery, zainstaluje zależności PHP, wygeneruje klucz aplikacji oraz przygotuje bazę danych wraz z danymi testowymi (seeding):

```bash
make build
```

### 2. Wyświetlanie Tokena

Podczas procesu budowania (`make build`) w konsoli zostanie wyświetlony token dostępu dla administratora. Zapisz go, aby móc korzystać z chronionych endpointów:

```
-------------------------------------------
Token for admin@example.com:
1|gORQDikcQX18S7rJUGvhYypL66aeLkNWY05ZqrSE3394870d
-------------------------------------------
```

### 3. Komendy Makefile

* `make up` – uruchomienie kontenerów w tle
* `make stop` – zatrzymanie kontenerów
* `make restart` – restart wszystkich usług
* `make test` – uruchomienie pakietu testów PHPUnit
* `make migrate` – uruchomienie migracji bazy danych
* `make clear-cache` – kompleksowe czyszczenie pamięci podręcznej

## 🔐 Autoryzacja (Sanctum)

Dostęp do tworzenia nowych książek (`POST /api/books`) jest zabezpieczony tokenem Sanctum. Pozostałe operacje (`update`, `delete`, `show`) w tej konfiguracji są publiczne.
Aby wykonać zapytanie chronione, dołącz token w nagłówku:

```
Authorization: Bearer <TWÓJ_TOKEN>
```

## 📡 Endpointy API

### 📚 Książki (`books`)

| Metoda | Endpoint        | Opis                         | Autoryzacja   |
| ------ | --------------- | ---------------------------- | ------------- |
| GET    | /api/books      | Lista książek (paginacja 10) | Brak          |
| GET    | /api/books/{id} | Szczegóły książki + autorzy  | Brak          |
| POST   | /api/books      | Utworzenie nowej książki     | Sanctum Token |
| PUT    | /api/books/{id} | Aktualizacja danych książki  | Brak          |
| DELETE | /api/books/{id} | Usunięcie książki            | Brak          |

### ✍️ Autorzy (`authors`)

| Metoda | Endpoint          | Opis                            | Autoryzacja |
| ------ | ----------------- | ------------------------------- | ----------- |
| GET    | /api/authors      | Lista autorów + filtry          | Brak        |
| GET    | /api/authors/{id} | Szczegóły autora i jego książki | Brak        |

**Zaawansowane filtrowanie:**
Endpoint `/api/authors` wspiera filtr `search`, który przeszukuje autorów po tytułach ich książek dzięki wykorzystaniu wzorca Pipeline:

```
GET /api/authors?search=Hobbit
```

## 🧪 Przykłady zapytań cURL

### Tworzenie książki (Wymaga Tokena)

```bash
curl -X POST http://localhost/api/books \
     -H "Authorization: Bearer <TWOJ_TOKEN>" \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{
        "title": "Wiedźmin: Ostatnie życzenie",
        "author_ids": [1, 2]
     }'
```

### Aktualizacja książki (Publiczne)

```bash
curl -X PUT http://localhost/api/books/1 \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{
        "title": "Nowy Tytuł",
        "author_ids": [1]
     }'
```

### Pobranie jednej książki

```bash
curl -X GET http://localhost/api/books/1 \
     -H "Accept: application/json"
```

## 🤖 Interaktywna Konsola (Artisan)

Projekt zawiera dedykowaną komendę CLI do szybkiego tworzenia autorów bezpośrednio z poziomu terminala:

```bash
docker compose exec app php artisan app:create-author
```

Komenda poprosi o podanie imienia i nazwiska, zwaliduje dane i utworzy rekord w bazie.

## 🧪 Testy

Aplikacja posiada pełne pokrycie testami Feature (PHPUnit). Testy sprawdzają:

* Poprawność autoryzacji Sanctum (401 dla nieautoryzowanych prób zapisu)
* Działanie filtrów Pipeline w wyszukiwarce autorów
* Relacje Many-to-Many i poprawność usuwania rekordów z tabel pivot
* Obsługę błędów 404 i walidację danych (422)

### Uruchomienie testów

```bash
make test
```
