# README for MC Intranet WordPress Theme

## Overview

This document provides an overview of the MC Intranet WordPress theme, detailing its structure, installation instructions, and usage guidelines. The theme is designed to serve as a centralized intranet solution for the MC group, integrating various functionalities and ensuring a consistent user experience across different company contexts.

## Theme Structure

The theme is organized into several directories and files, each serving a specific purpose:

- **assets/**: Contains CSS and JavaScript files for styling and functionality.
  - **css/**: 
    - `design-tokens.css`: CSS variables for design tokens used throughout the theme.
    - `components.css`: Styles for various components like buttons, cards, and forms.
  - **js/**: 
    - `nav-toggle.js`: JavaScript for handling navigation toggle functionality, especially for mobile views.

- **template-parts/**: Contains reusable template parts for the theme.
  - `hero.php`: Structure and content for the hero section of the front page.
  - `form-card.php`: Layout for form cards used throughout the site.
  - `company-badge.php`: Generates the company badge displayed in the navigation or header.

- **front-page.php**: Main template for the front page, integrating various components and sections.

- **page.php**: Default template for all standard pages in the theme.

- **header.php**: Contains the header structure, including the navigation menu and site branding.

- **footer.php**: Defines the footer structure, including copyright information and important resource links.

- **functions.php**: Contains theme-specific functions, including script and style enqueuing, and theme support features.

- **style.css**: Main stylesheet for the theme, containing overall styles and metadata.

## Installation Instructions

1. **Clone the Repository**: Clone the repository to your local machine.
   ```
   git clone <repository-url>
   ```

2. **Navigate to the Theme Directory**: Change into the theme directory.
   ```
   cd mc-intranet-wordpress/tema\ wordpress
   ```

3. **Set Up Docker**: Ensure Docker is installed and running on your machine. Use the provided `docker-compose.yml` to set up the environment.
   ```
   docker-compose up -d
   ```

4. **Access the WordPress Site**: Open your web browser and navigate to `http://localhost:8000` (or the port specified in your `docker-compose.yml`).

5. **Activate the Theme**: Log in to the WordPress admin panel, go to Appearance > Themes, and activate the MC Intranet theme.

## Usage Guidelines

- The theme is designed to be responsive and mobile-friendly, ensuring a seamless experience across devices.
- Utilize the template parts for consistent layouts and easy maintenance.
- Customize the theme by modifying the CSS files in the `assets/css/` directory and the PHP files in the `template-parts/` directory as needed.

## Contributing

Contributions to the theme are welcome. Please submit a pull request with your changes or improvements.

## License

This theme is licensed under the MIT License. See the LICENSE file for more details.