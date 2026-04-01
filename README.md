# testlaraveldt

# Translation Service API

A Laravel-based Translation Service API for managing multilingual translations with tagging support. The project includes authentication, CRUD operations, export functionality, and performance-optimized endpoints.

---

## Table of Contents
- [Requirements](#requirements)
- [Setup](#setup)
- [Running the Project](#running-the-project)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Design Choices](#design-choices)

---

## Requirements
- PHP 8.2+
- Laravel 12.x
- SQLite (for local dev) or MySQL/PostgreSQL
- Composer
- Node.js (optional for frontend scaffolding)

---

## Setup

1. Clone the repository:

```bash
git clone <repo-url>
cd translation-service

API Endpoints
Method	Endpoint	Description	Auth Required
POST	/api/login	Authenticate user and receive token	No
GET	/api/translations	List translations with optional filters (key, content, tag)	Yes
POST	/api/translations	Create new translation with optional tags	Yes
GET	/api/translations/{id}	Retrieve a single translation	Yes
PUT/PATCH	/api/translations/{id}	Update a translation and its tags	Yes
DELETE	/api/translations/{id}	Delete a translation	Yes
GET	/api/translations/export	Export all translations as JSON	Yes
