# Kodano API - Product Catalog API

[![Symfony](https://img.shields.io/badge/Symfony-6.3-blueviolet)](https://symfony.com)
[![API Platform](https://img.shields.io/badge/API%20Platform-✓-blue)](https://api-platform.com)
[![Docker](https://img.shields.io/badge/Docker-✓-blue)](https://www.docker.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)](https://www.mysql.com)

REST API do zarządzania produktami i kategoriami w katalogu produktów. Zbudowane przy użyciu Symfony, API Platform i Docker.
Zadanie rekrutacyjne dla Kodano.

---

## Spis treści

- [Stack technologiczny](#stack-technologiczny)
- [Instalacja i uruchomienie](#instalacja-i-uruchomienie)
    - [Wymagania](#wymagania)
    - [Instrukcje](#instrukcje)
- [Struktura projektu](#struktura-projektu)
- [Główne funkcjonalności](#główne-funkcjonalności)
- [Endpointy API](#endpointy-api)
- [System powiadomień](#system-powiadomień)
- [Testy](#testy)

---

## Stack technologiczny

Projekt wykorzystuje następujące technologie:

-   **Backend:** Symfony 6.3
-   **API:** API Platform
-   **Baza danych:** MySQL 8.0
-   **Konteneryzacja:** Docker & Docker Compose

---

## Instalacja i uruchomienie

### Wymagania

-   Docker
-   Docker Compose

### Instrukcje

1.  **Sklonowanie repozytorium:**
    ```bash
    git clone [https://github.com/your-username/product-catalog-api.git](https://github.com/your-username/product-catalog-api.git)
    ```

2.  **Uruchomienie kontenerów Docker:**
    ```bash
    docker-compose up -d
    ```

3.  **Zainstalowanie zależności PHP (Composer):**
    ```bash
    docker-compose exec php composer install
    ```

4.  **Utwórzenie schematu bazy danych:**
    ```bash
    docker-compose exec php bin/console doctrine:schema:create
    ```

5.  **(Opcjonalnie) Wypełnienie bazy danych danymi testowymi:**
    ```bash
    docker-compose exec php bin/console app:create-test-data
    ```

6.  **Dostęp do API:**
    Aplikacja API będzie dostępna pod adresem:
    `http://localhost:8080/api`

    Dokumentacja interaktywna (Swagger UI) wygenerowana przez API Platform jest dostępna pod:
    `http://localhost:8080/api/docs`

---

## Główne funkcjonalności

-   **Zarządzanie produktami:** Pełne operacje CRUD (Create, Read, Update, Delete).
-   **Zarządzanie kategoriami:** Pełne operacje CRUD.
-   **Powiązania:** Możliwość przypisywania produktów do jednej lub wielu kategorii.
-   **System powiadomień:** Automatyczne powiadomienia po zapisie produktu (logowanie, email).
-   **Walidacja:** Walidacja danych wejściowych przy operacjach zapisu.

---

## Endpointy API

Dokumentacja API jest automatycznie generowana przez API Platform i dostępna pod adresem: `http://localhost:8080/api/docs`.

Główne zasoby i operacje:

### Kategorie (`/api/categories`)

-   `GET /api/categories`: Pobierz listę wszystkich kategorii.
-   `GET /api/categories/{id}`: Pobierz szczegóły kategorii o podanym ID.
-   `POST /api/categories`: Utwórz nową kategorię.
-   `PUT /api/categories/{id}`: Zaktualizuj istniejącą kategorię.
-   `DELETE /api/categories/{id}`: Usuń kategorię.

### Produkty (`/api/products`)

-   `GET /api/products`: Pobierz listę wszystkich produktów.
-   `GET /api/products/{id}`: Pobierz szczegóły produktu o podanym ID.
-   `POST /api/products`: Utwórz nowy produkt.
-   `PUT /api/products/{id}`: Zaktualizuj istniejący produkt.
-   `DELETE /api/products/{id}`: Usuń produkt.

### Dodatkowe operacje

-   `POST /api/products/{id}/notify`: Ręczne wywołanie systemu powiadomień dla określonego produktu (może służyć do testów lub specyficznych przypadków użycia).

---

## System powiadomień

Po każdej operacji zapisu produktu (utworzenie nowego lub aktualizacja istniejącego) system automatycznie wysyła powiadomienia za pomocą kanałów:

-   **Log:** Zapisuje informacje o operacji w logach systemowych.
-   **Email:** Wysyła email (obecnie symulowany).

Dodanie nowego kanału powiadomień (np. Slack, SMS) wymaga jedynie implementacji odpowiedniego interfejsu i zarejestrowania nowej usługi w kontenerze Symfony.

---

## Testy

Aby uruchomić zestaw przykładowych testów, należy wykonać polecenie:

```bash
docker-compose exec php bin/phpunit