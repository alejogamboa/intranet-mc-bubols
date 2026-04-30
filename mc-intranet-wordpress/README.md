# MC Intranet WordPress Theme

## Overview
This project implements a WordPress theme for the MC Intranet, designed to serve as a centralized portal for multiple companies. The theme is structured to provide a consistent user experience across various sections and functionalities.

## Directory Structure
The theme is organized as follows:

```
tema wordpress/
├── assets/
│   ├── css/
│   │   ├── design-tokens.css   # CSS variables for design tokens
│   │   └── components.css       # Styles for various components
│   └── js/
│       └── nav-toggle.js        # JavaScript for navigation toggle
├── template-parts/
│   ├── hero.php                 # Structure for the hero section
│   ├── form-card.php            # Layout for form cards
│   └── company-badge.php        # Company badge generation
├── front-page.php               # Main template for the front page
├── page.php                     # Default template for standard pages
├── header.php                   # Header structure and navigation
├── footer.php                   # Footer structure
├── functions.php                # Theme-specific functions
├── style.css                    # Main stylesheet and metadata
└── README.md                    # Documentation for the theme
```

## Installation
1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Ensure Docker is installed and running.
4. Rebuild the local stack (containers + WordPress + seed) with one command:
   ```
   cd mc-intranet-wordpress
   bash bin/rebuild-local.sh
   ```
5. Access the WordPress installation via your web browser.

## Recovery After Docker Failure
If Docker Desktop reset removed all containers/services, use the rebuild script:

```bash
cd /Users/alejandrogamboa/Documents/bubols-fryscol/intranet/mc-intranet-wordpress
bash bin/rebuild-local.sh
```

The script is idempotent and performs:
- `docker compose up -d --build`
- WordPress install (if not installed)
- Theme/plugin activation
- Base plugin activation (`advanced-custom-fields`, `elementor`)
- Permalinks and basic settings
- Initial seed execution (`mc-intranet-core/bin/seed-content.sh`)

## Persistent Local Data
The Docker stack stores the MySQL database in a project folder instead of a Docker-managed volume.

- `db_data/`: MySQL database files.

If Docker Desktop is reset or images are removed, `db_data/` remains on disk. Back up this directory if you need to preserve the local environment.

## Usage
- The theme is designed to be user-friendly, with a focus on clarity and efficiency.
- Each component is reusable, allowing for easy updates and maintenance.
- The theme supports multiple company contexts, ensuring that users see relevant information based on their affiliation.

## Development
- For CSS development, use the `design-tokens.css` for consistent styling across components.
- JavaScript functionality for mobile navigation is handled in `nav-toggle.js`.
- Template parts are modular, allowing for easy updates to specific sections without affecting the entire theme.

## Documentation
- WP-CLI commands: [docs/COMANDOS_WP_CLI.md](docs/COMANDOS_WP_CLI.md)

## Contribution
Contributions to the theme are welcome. Please submit a pull request with your changes and a description of the modifications made.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.