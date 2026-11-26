# Obscura

**CSE423 — Vulnerable and Secure Notes Web Application**

## 1. Project Overview

Obscura is an educational cybersecurity project that demonstrates how common web application vulnerabilities can be exploited and mitigated.

The project contains two versions of the same PHP/MySQL notes application:

- **Vulnerable:** Intentionally insecure implementation for demonstrating attacks.
- **Secure:** Hardened implementation designed to prevent the five selected attacks.

Both versions run locally using Docker Compose and use separate MySQL databases.

**This application is intended for local educational testing only. Do not deploy the vulnerable version publicly.**

## 2. Technology Stack

- PHP 8.3 and Apache
- MySQL 8.4
- PDO for database access
- HTML, CSS and JavaScript
- Docker and Docker Compose
- Python and JavaScript for Proof-of-Concept testing

## 3. Application Features

Both environments provide:

- User registration, login and logout
- Session-based authentication
- User and administrator roles
- Administrator panel with access control
- Notes creation, viewing, editing and deletion
- Notes search
- User profile management
- File export functionality
- URL preview functionality

## 4. Installation and Setup

### Requirements

- Docker Desktop with Docker Compose
- A modern web browser
- Python 3 for Python-based PoCs

### Start the Vulnerable Application

From the project root:

```powershell
docker compose -f vulnerable/compose.yaml up -d --build
```

Open: http://localhost:8081

### Start the Secure Application

```powershell
docker compose -f secure/compose.yaml up -d --build
```

Open: http://localhost:8082

The two applications have separate Docker Compose projects and databases.

### Create Test Accounts

Register a test account separately in each environment using the registration page.

Accounts are not shared between the two environments. New accounts receive the `user` role by default.

### Stop the Applications

```powershell
docker compose -f vulnerable/compose.yaml down
docker compose -f secure/compose.yaml down
```

**Important:** Do not add `-v` unless you intentionally want to delete the database volumes and their contents.

## 5. Security Vulnerabilities and Mitigations

| Vulnerability | Vulnerable implementation | Secure implementation |
|---|---|---|
| SQL Injection | User input is concatenated into the SQL search query | Parameterized queries using PDO |
| Stored Cross-Site Scripting (XSS) | Unsafe HTML is rendered from note content | Parsedown safe mode |
| Mass Assignment | Profile updates accept the `role` field | Only permitted profile fields can be updated |
| Path Traversal | File export accepts traversal paths | Filename restrictions and path validation |
| Server-Side Request Forgery (SSRF) | URL preview can request internal resources | URL, IP address, port and redirect restrictions |

### SQL Injection

**Affected endpoint:** `search.php`

The vulnerable implementation constructs SQL using untrusted search input, allowing an attacker to alter the intended query conditions.

The secure implementation uses prepared statements to separate SQL code from user-supplied data.

### Stored XSS

**Affected functionality:** Note content rendering

The vulnerable implementation can render attacker-controlled HTML containing executable browser event handlers.

The secure implementation uses safe Markdown rendering to prevent unsafe HTML execution.

### Mass Assignment

**Affected endpoint:** `profile.php`

The vulnerable implementation allows a regular user to submit an unauthorized `role=admin` parameter.

The secure implementation restricts profile updates to approved fields.

### Path Traversal

**Affected endpoint:** `export.php`

The vulnerable implementation permits access to files outside the intended export directory.

The secure implementation validates requested filenames and paths.

### SSRF

**Affected endpoint:** `preview.php`

The vulnerable implementation can request an internal endpoint through the server-side URL preview feature.

The secure implementation restricts unsafe destinations and redirects.

## 6. Proofs of Concept (PoCs)

All PoCs must be executed only against the local Obscura environments.

| Attack | PoC |
|---|---|
| SQL Injection | `pocs/01-sqli/poc.py` |
| Stored XSS | `pocs/02-xss/poc.py` |
| Mass Assignment | `pocs/03-mass-assignment/poc.js` |
| Path Traversal | `pocs/04-path-traversal/poc.js` |
| SSRF | `pocs/05-ssrf/poc.js` |

### Python PoC Setup

Install the required packages:

```powershell
python -m pip install requests beautifulsoup4
```

### SQL Injection Testing

Vulnerable:

```powershell
python .\pocs\01-sqli\poc.py http://localhost:8081
```

Secure:

```powershell
python .\pocs\01-sqli\poc.py http://localhost:8082
```

The scripts prompt for the corresponding environment's test account credentials.

**Expected behavior:** The vulnerable search may return unintended notes, while the secure search does not return results for the injection payload.

### Stored XSS Testing

Vulnerable:

```powershell
python .\pocs\02-xss\poc.py http://localhost:8081
```

Secure:

```powershell
python .\pocs\02-xss\poc.py http://localhost:8082
```

The script creates a test note and checks whether an active HTML payload appears in the returned note page.

Confirm JavaScript execution separately in the browser. Finding an HTML attribute alone does not prove execution.

### JavaScript PoC Testing

For Mass Assignment, Path Traversal and SSRF:

1. Start the appropriate Docker environment.
2. Log in using a regular test account.
3. Open the browser Developer Tools Console.
4. Copy and execute the corresponding `poc.js` file.
5. Compare the results between ports 8081 and 8082.

**Note:** The Mass Assignment PoC can change the vulnerable test account's role to `admin`. Restore the account to `user` after testing.

## 7. Project Structure

```text
Obscura/
├── vulnerable/
│   ├── app/
│   ├── database/
│   ├── Dockerfile
│   └── compose.yaml
├── secure/
│   ├── app/
│   ├── database/
│   ├── Dockerfile
│   └── compose.yaml
├── pocs/
│   ├── 01-sqli/
│   ├── 02-xss/
│   ├── 03-mass-assignment/
│   ├── 04-path-traversal/
│   └── 05-ssrf/
├── report/
│   └── screenshots/
├── .gitignore
└── README.md
```

## Demo Database Seeding

Obscura automatically runs the SQL seeder (`seed.sql`) when a new Docker database is initialized. No manual seeding is required for a fresh installation.

The seeder creates:
- 1 administrator account
- 2 regular user accounts
- 124 demo notes (41 admin, 42 Alice, 41 Bob)

### Demo Accounts

| Username | Password | Role |
|---|---|---|
| demo_admin | ObscuraDemo2026! | Admin |
| demo_alice | ObscuraDemo2026! | User |
| demo_bob | ObscuraDemo2026! | User |

**Warning:** These credentials are for local educational testing only.

## 8. Manual Seeding (Existing Databases Only)

For an existing database that was created before automatic seeding was enabled, start the Docker environments first and run the appropriate command below. These commands are not required for a fresh installation.

**Vulnerable:**

```powershell
Get-Content .\seed.sql -Raw | docker compose -f vulnerable/compose.yaml exec -T db mysql -uobscura -pobscura_local obscura
```

**Secure:**

```powershell
Get-Content .\seed.sql -Raw | docker compose -f secure/compose.yaml exec -T db mysql -uobscura -pobscura_local obscura
```

The seeder can be executed multiple times without creating duplicate demo users or notes.

Existing non-demo data is preserved.

## 9. Testing Evidence

Screenshots of vulnerable exploitation and secure mitigation are organized under `report/screenshots/`, grouped by attack.

The testing evidence demonstrates the differences between the two implementations.

## 10. Security and Scope

Obscura is a controlled educational lab. The vulnerable version intentionally contains insecure code and should never be exposed to the public internet.

All attacks and tests are intended for authorized local environments.

## 11. Course Information

**Course:** CSE423  
**Project:** Obscura — Vulnerable and Secure Notes Web Application

### Team Members

| Full Name | Student ID |
|---|---|
| Andria Paphiti | 29180 |
| Christina Antoniou | 30330 |
| Katerina Perikleous | 30270 |
| Andri Georgiou | 30479 |