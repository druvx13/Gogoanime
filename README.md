# GogoAnime Modernized Clone

## Description

This project is an effort to modernize a version of the GogoAnime anime streaming website. The primary focus has been on refactoring the backend PHP code to align with more modern practices, including the introduction of a templating engine, a service class for API interactions, and basic project structure improvements. The original user interface and core user-facing functionalities are intended to be preserved or gradually improved upon.

This project was undertaken as a software engineering exercise.

## Features

*   Browse anime listings (various pages like new seasons, popular, movies, all anime).
*   Search for anime by keyword.
*   View anime details (synopsis, genres, episode lists, etc.).
*   Streaming page for watching episodes (using placeholder video sources).
*   Categorization by genre and status (Completed/Ongoing).
*   **Modernization Efforts:**
    *   PHP 8.x compatibility.
    *   Integration of the Twig templating engine to separate presentation from logic.
    *   Introduction of a service class (`ApiClient`) to abstract external API interactions.
    *   Use of Composer for dependency management (Twig, etc.).
    *   Implementation of PSR-4 autoloading for custom classes.
    *   Update of jQuery to a modern version (3.7.1).
    *   Basic JavaScript file refactoring (IIFEs, 'use strict').

## Tech Stack

*   **Backend:** PHP 8.0+
*   **Templating:** Twig
*   **Dependency Management:** Composer
*   **Frontend:**
    *   HTML, CSS
    *   JavaScript
    *   jQuery 3.7.1
    *   `jquery.tinyscrollbar.min.js` (existing library for scrollbars)
*   **API Interaction:** cURL (via `ApiClient` service) for consuming external anime data APIs.

## Project Structure

*   `src/`: Contains PHP classes following PSR-4 (e.g., `App\Utils\ConfigLoader`, `App\Service\ApiClient`).
*   `templates/`: Contains Twig template files for views.
    *   `templates/layout/`: Base layout files (header, footer).
    *   `templates/parts/`: Reusable parts of templates (popups, sidebars).
*   `config/`: Configuration files (e.g., `config.php` for base URLs, API links, Twig setup).
*   `js/`: JavaScript files.
    *   `js/libraries/`: External libraries like jQuery.
    *   `js/files/`: Site-specific JavaScript.
*   `css/`: CSS stylesheets.
*   `php/`: Older PHP include files, some of which are progressively being refactored or replaced.
    *   `php/include/`: Contains reusable PHP components (some now refactored to Twig parts).
*   Root directory: Contains main page entry points (e.g., `index.php`, `home.php`, `anime-details.php`, etc.) and composer files.

## Setup / Installation

1.  **Prerequisites:**
    *   PHP 8.0 or higher.
    *   Composer (PHP dependency manager).
    *   A web server (e.g., Apache, Nginx) configured to serve PHP sites.

2.  **Clone the Repository:**
    ```bash
    git clone <repository-url>
    cd <repository-directory>
    ```

3.  **Install Dependencies:**
    *   Run Composer to install Twig and other dependencies:
        ```bash
        composer install
        ```

4.  **Environment Configuration:**
    *   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    *   Review `.env` if any specific environment variables were defined (currently, this project primarily uses `config/config.php` for such settings).
    *   Ensure the API URLs (`API_LINK`, `AAPXY_URL` which forms `AAPXYED_API_URL`) in `config/config.php` are correctly set if you intend to use a live API. The `ApiClient` currently uses `AAPXYED_API_URL` for its constructor by default. **Note:** For the cURL requests in `ApiClient` to work as currently implemented (by appending endpoints like `/recent-release`), the `ApiClient` would ideally need to be instantiated with the direct `API_LINK` rather than the `AAPXYED_API_URL` proxy prefix. This may require an adjustment in `src/Utils/ConfigLoader.php` or how the base API URL is handled.

5.  **Web Server Configuration:**
    *   Configure your web server's document root to point to the project's root directory (where `index.php` resides).
    *   Ensure URL rewriting is enabled if your server uses it (e.g., `mod_rewrite` for Apache). The project includes a `.htaccess` file which might need adjustments based on your server setup and if clean URLs (without `.php`) are desired beyond the current file-per-page structure.

## Usage

Once the setup is complete, navigate to the URL configured for your web server (e.g., `http://localhost/your_project_directory/` or a virtual host like `http://gogoanime.local/`).

*   The homepage (`home.php` or `index.php`) will display recent releases and other sections.
*   Use the navigation links for Anime List, Movies, Popular, New Season, etc.
*   Use the search bar to find anime.

## Modernization Notes

This project represents a significant refactoring from a presumed older PHP codebase. Key achievements include:
*   **Templating Engine:** Integration of Twig has separated HTML generation from PHP logic, leading to cleaner and more maintainable view files.
*   **Service Layer:** The `ApiClient` class centralizes external API communication, making it easier to manage API endpoints, request logic, and eventually, caching or more advanced error handling. Actual API calls are now made using cURL, replacing `file_get_contents`.
*   **PSR-4 Autoloading:** Custom classes are organized under the `App` namespace and autoloaded via Composer.
*   **Dependency Management:** Composer is used for managing external libraries like Twig.
*   **JavaScript:** jQuery has been updated to v3.7.1. Initial steps were taken to modernize some JavaScript files by scoping them with IIFEs and applying `'use strict';`. Redundant client-side data-fetching scripts (`js/category.js`, `js/streaming.js`) were neutralized as their functionality is now primarily server-side.

## Known Issues / TODOs

*   **API Dependency & Proxy:**
    *   The application heavily relies on external APIs. The `ApiClient` now uses cURL but the exact structure and reliability of these APIs are external factors.
    *   The current `ApiClient` constructor uses `AAPXYED_API_URL` (which is `PROXY_URL . urlencode(API_LINK_BASE)`). However, the cURL calls within `ApiClient` methods are constructed as if its `$this->apiBaseUrl` is the direct API link (e.g., `https://animeapi-9qlo.onrender.com`). This discrepancy needs to be resolved: either `ConfigLoader` should pass the direct `API_LINK` to `ApiClient`, or `ApiClient::makeApiRequest` needs to be more sophisticated in handling the proxy prefix.
*   **JavaScript:**
    *   The external `main.js` file loaded from `gogocdn.net` could not be analyzed due to `robots.txt` restrictions. Its functionality, compatibility with updated jQuery, and necessity should be reviewed.
    *   The `jquery.tinyscrollbar.min.js` plugin's compatibility with jQuery 3.7.1 is unconfirmed without live browser testing.
    *   Further review of remaining JavaScript (`combo.js`, etc.) for deeper refactoring opportunities (e.g., replacing complex jQuery DOM manipulation if appropriate).
*   **Error Handling:** User-facing error handling for API failures or other issues can be improved.
*   **Security:** No specific security audit has been performed. Further hardening (CSP, XSS protection beyond Twig's auto-escaping, CSRF protection for forms if/when added) is recommended for any production-like environment.
*   **Pagination with ApiClient:** Pagination HTML is still largely hardcoded in PHP files because `ApiClient` methods do not yet return total page/item counts. This needs to be integrated if the APIs support it.
*   **Testing:** No automated tests are in place.
*   **Sandbox Limitations:** Direct execution of PHP scripts producing output and some file system operations repeatedly failed in the development sandbox. This has limited runtime verification for some steps, relying instead on code structure and static content checks.

## License

This project is based on a publicly accessible version of GogoAnime and is intended for educational and software engineering demonstration purposes only. The code developed during this modernization exercise is provided as-is. Users should respect the original creators' rights and terms of service if considering any use beyond personal education.
