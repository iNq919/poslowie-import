# Posłowie Importer

WordPress plugin to manually import and synchronize data of MPs of the Polish Parliament of the 10th term from the public API of the Sejm. The data is saved as entries in a dedicated Custom Post Type (`posel`) using ACF fields.

## Features

- Manually synchronize data with the Seym API
- Interrupt synchronization at any time
- MP data saved in Custom Post Type `posel`.
- Custom fields created automatically on activation (ACF Local Fields)
- Professional templates: archive and single view
- Complete security support (nonce, capability checks)
- Optimized data update - update only on changes

---

## Requirements

- WordPress 6.0+
- PHP 8.2+
- ACF installed and active

---

## Installation

1. Download the plugin and place it in the `/wp-content/plugins/poslowie-importer/` directory.
2. Activate the plugin in the WordPress dashboard
3. Make sure that the ACF plugin is active
4. Go to **Administrative panel → MP Import**.
5. Click **Synchronize** to import the data
6. Use templates from theme

---

## 🛠 Usage

### Custom Post Type

The plugin registers CPT `posel` with basic title support and REST API.

### ACF fields

Created automatically using code (`acf_add_local_field_group`). The plugin imports, among others:

- Name
- Parliamentary club
- E-mail
- Province
- Date and place of birth
- District and number
- Education, profession, number of votes
- Photo (remote URL)

---

## Views

- `/posel/` - list of MPs (`archive-posel.php`).
- `/posel/name` - single MP (`single-posel.php`).

---

## Security

- Checking `nonce` for AJAX
- Validation of permissions (`manage_options`)
- Security against importing the same data multiple times
- Abort import after refreshing the page or clicking “Stop”

---

## Testing

- After activation, go to `Member Import` in the WordPress menu.
- You can download the selected number of Members by setting the value of the field `-1` (all)
- If necessary, use the **Stop** button.

---

## Directory structure

- ├── class-importer.php # Main plugin class
- ├── archive-posel.php # Archive template
- ├── single-posel.php # Single-posel template
- ├── style.scss # SCSS file for styling views

##  Author

Przemysław Zienkiewicz
