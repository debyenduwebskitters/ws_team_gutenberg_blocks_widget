# WS Team Block View(Debyendu)

WS Team Block View(Debyendu) is a dynamic Gutenberg block plugin for WordPress that lets editors build a reusable team section with responsive cards, department filtering, popup profiles, search, icon-based social links, dark mode, and AJAX-powered pagination.

## Folder Structure

```text
blockforge-team-showcase/
├── assets/
│   ├── css/
│   │   ├── team-showcase.css
│   │   └── team-showcase-editor.css
│   ├── images/
│   └── js/
│       ├── team-showcase-editor.js
│       └── team-showcase.js
├── includes/
│   ├── ajax/
│   │   └── class-team-showcase-ajax.php
│   ├── blocks/
│   │   ├── class-team-showcase-block.php
│   │   └── team-showcase/
│   │       └── block.json
│   └── helpers/
│       └── class-utils.php
├── AIUsesReport.txt
├── blockforge-team-showcase.php
└── README.md
```

## Features

- Dynamic Gutenberg block registration with `block.json`
- Repeater-style content controls for team members
- Layout controls for columns, item count before `Load More`, alignment, card height, image position, and popup toggle
- Style controls for background, radius, typography, hover behavior, and button colors
- Responsive grid layout
- Department filtering with secure AJAX
- Search filter and popup modal for member details
- Configurable AJAX `Load More` threshold through the block sidebar
- Icon-based social links in cards and popup profiles, including WordPress support
- Top-right modal close button with `X` icon
- Lazy-loaded images, `No Image` fallback placeholders, and skeleton loading state
- Dark mode on/off toggle for the frontend grid
- ACF integration field reference for editorial workflows
- GSAP-ready frontend animation hook
- Editor-friendly empty state with inline `Add Team Member` prompt

## Installation

1. Copy the `blockforge-team-showcase` folder into `wp-content/plugins/`.
2. Activate **WS Team Block View(Debyendu)** from the WordPress admin.
3. Open the block editor and insert the **WS Team Block View(Debyendu)** block from the Widgets category.
4. Add team member entries and adjust layout and styling from the block sidebar.
5. Use `Show Items` in Layout Controls to decide how many cards appear before the `Load More` button is shown.

## Block Widget Architecture Explanation

The plugin uses an object-oriented architecture with a lightweight bootstrap file that defines constants and loads a main `Plugin` singleton. That bootstrap registers shared assets and initializes two focused services:

- `Team_Showcase_Block`: registers the block from `block.json`, exposes defaults, renders frontend markup server-side, and keeps block HTML in one place for both initial render and AJAX responses.
- `Team_Showcase_Ajax`: provides public and authenticated `wp_ajax` endpoints for filtering and pagination.
- `Utils`: centralizes sanitization, filtering, department extraction, boolean normalization, and inline style generation.

This separation makes the plugin easy to extend with future integrations such as post-based team sources, custom REST endpoints, or richer animation layers.

## Custom Block Registration Code

The block is registered with a dynamic render callback:

```php
register_block_type(
	BLOCKFORGE_TEAM_SHOWCASE_PATH . 'includes/blocks/team-showcase',
	array(
		'editor_script'   => 'blockforge-team-showcase-editor',
		'editor_style'    => 'blockforge-team-showcase-editor-style',
		'style'           => 'blockforge-team-showcase-style',
		'script'          => 'blockforge-team-showcase-view',
		'render_callback' => array( $this, 'render_block' ),
	)
);
```

This keeps the block aligned with modern WordPress block standards while still allowing secure server-rendered output and AJAX pagination with a configurable initial item count.

## Secure AJAX Filtering System

The filtering system posts to `admin-ajax.php` using the action `blockforge_team_showcase_query`. Security measures include:

- Nonce creation with `wp_create_nonce( 'blockforge_team_showcase_nonce' )`
- Verification with `check_ajax_referer()`
- Strict sanitization of all incoming request fields
- Escaping of all rendered HTML values
- Shared sanitization in `Utils::sanitize_members()`

Request flow:

1. Frontend JS sends members, attributes, department, search text, page, and per-page size.
2. PHP verifies the nonce and sanitizes the payload.
3. Members are filtered by department and search term.
4. The next page of card markup is rendered and returned as JSON.

## Responsive CSS Structure

The frontend stylesheet is organized around CSS custom properties so block controls can influence the output without inline style bloat. Core areas:

- Root variables for columns, card background, radius, font size, line height, and button colors
- Toolbar styles for filters, search, dark mode toggle, and load more
- Grid and card styles with hover modifiers
- Modal, social icon, missing-image placeholder, button state, and skeleton loading styles
- Tablet and mobile media queries that collapse the grid from 3 to 2 to 1 column

This approach keeps the block scalable while making future style presets easy to add.

## JavaScript Filtering And Popup Logic

`assets/js/team-showcase.js` handles:

- AJAX requests for department filtering and load more pagination
- Debounced search filtering
- Modal open and close behavior with a top-right `X` close control
- Popup opening only from the `View Profile` button
- Card tilt interaction for the `tilt` hover mode
- Optional GSAP stagger animations when `window.gsap` is available
- Dark mode toggle state
- Dynamic social icon rendering and `No Image` modal fallback when a member photo is missing

The editor script registers the block, provides sidebar controls, manages repeater-like member data, exposes the `Show Items` layout setting, and renders a live server-side preview with a selectable empty state.

## ACF Integration

The current version includes an `acfFieldGroup` attribute to document or pair an ACF field group key with the block in editorial environments. A practical extension is to map that field group into the render pipeline so member data can come from structured ACF repeater fields instead of block attributes.

## Security And Optimization Notes

- Output is escaped with `esc_html()`, `esc_attr()`, and `esc_url()`, while trusted internal SVG/icon markup is rendered from controlled plugin code
- AJAX requests use nonce validation and sanitized request parsing
- Images use native lazy loading
- Initial render limits visible cards based on the configured `Show Items` value, reducing first paint cost
- Reusable render methods avoid duplicated output logic between initial render and AJAX responses
- Modular classes make it safer to add caching, REST endpoints, or post-query data sources later

## AI Usage Disclosure

AI assistance for this plugin is documented in `AIUsesReport.txt`.

That report summarizes:

- the prompts already disclosed for this project
- the files likely created or significantly assisted by AI
- the distinction between AI-assisted development and runtime plugin behavior
- the areas that still require human review

## Future Enhancements

- REST API endpoint support as an alternative to `wp_ajax`
- Full ACF repeater data source mode
- Remote avatar fallbacks via WordPress media IDs
- Optional Isotope.js masonry and animated filtering
- Cached server responses for large teams
