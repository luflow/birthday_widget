# Birthday Widget

A Nextcloud dashboard widget that displays upcoming and recent birthdays from your contacts. Never miss a birthday again with this simple, elegant widget that keeps you informed about your contacts' special days.

## Features

### For All Users
- **Birthday Overview:** See today's birthdays, upcoming birthdays, and recent birthdays in one glance
- **Age Display:** Shows the age for each person (when birth year is available in contacts)
- **Smart Date Formatting:** Displays dates in a human-friendly format (Today, Tomorrow, Yesterday, weekday names)
- **Today Highlights:** Special styling with cake emoji for today's birthdays
- **Automatic Sync:** Reads from Nextcloud's auto-generated contact_birthdays calendar

### For Administrators
- **Configurable Date Range:** Set how many days in the past and future to display (default: 14 days each)
- **Admin Settings:** Easy configuration through Nextcloud's admin settings panel

## Requirements

- Nextcloud 31 or higher
- PHP 8.1 or higher
- Birthday calendar and contacts app must be enabled (Nextcloud generates this automatically from contacts)

## Installation

1. Download the app from the [Nextcloud App Store](https://apps.nextcloud.com/apps/birthday_widget) or place this app in **nextcloud/apps/**
2. Enable the app in Nextcloud admin settings
3. The widget will appear on your dashboard

## Configuration

### Admin Settings

Navigate to **Settings > Administration > Birthday Widget** to configure:

- **Days in the past:** Number of days to show recent birthdays (default: 14)
- **Days in the future:** Number of days to show upcoming birthdays (default: 14)

### Birthday Calendar

The widget uses Nextcloud's built-in birthday calendar feature, which automatically generates a calendar from your contacts' birthday dates. To enable this:

1. Go to **Settings > Calendar**
2. Enable "Birthday calendar"

## Usage

Once installed, the Birthday Widget will appear on your Nextcloud dashboard. It displays:

- **Today:** Birthdays happening today (highlighted with special styling)
- **Upcoming:** Birthdays in the next configured days
- **Recent:** Birthdays from the past configured days

Click the widget to quickly access your birthday information without navigating away from your dashboard.

## Development

### Prerequisites

- Node.js and npm
- PHP 8.1+
- Composer

### Building

```bash
# Install dependencies
npm install
composer install

# Build for production
npm run build

# Watch for changes during development
npm run watch
```

### Make Commands

```bash
# Full build
make build

# Create app store package
make appstore
```

## License

AGPL-3.0-or-later

## Author

[Florian Ludwig](https://krautnerds.de) - florian@krautnerds.de

## Links

- [GitHub Repository](https://github.com/luflow/birthday-widget)
- [Issue Tracker](https://github.com/luflow/birthday-widget/issues)
