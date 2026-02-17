# Birthday Widget App

## Code Style & Conventions

### Vue.js Frontend
- Use Vue 3 Composition API (`<script setup>`)
- **Translations are handled via Transifex** - do NOT manually add translation files or .po files when building new features
- **Always use English keys** for `t()` calls in Vue components, never German strings
- Use and Import mainly Nextcloud components from `@nextcloud/vue`
- Styling with CSS in `<style scoped>`
- Use icons from `vue-material-design-icons`
- When changing the frontend, always build the app with `npm run build`

### Translation Guidelines (Nextcloud Standards)
Translations are managed via **Transifex** and synced automatically. When adding new features, just use `t()` and `n()` with English strings - do NOT create or modify translation files manually.

Follow these Nextcloud translation guidelines (see https://docs.nextcloud.com/server/latest/developer_manual/basics/translations.html):

#### Capitalization
- **Only capitalize the first word** of a sentence/label, not every word
- Correct: `Configure widget`, `Days to show`
- Wrong: `Configure Widget`, `Days To Show`
- Exception: Proper nouns like "Nextcloud" or "Birthday Widget" (app name)

#### Success/Feedback Messages
- **Never use "successfully"** in feedback messages - it's redundant
- Correct: `Settings saved`, `Widget updated`
- Wrong: `Settings saved successfully`, `Widget updated successfully`

#### Ellipsis (…) Spacing
- **Add a non-breaking space** (`\u00A0`) before the ellipsis when trimming sentences
- Correct: `Loading …`, `Search contacts …`
- Wrong: `Loading…`, `Search contacts...`
- Use the Unicode ellipsis character `…` (U+2026), not three dots

#### Format String Placeholders (PHP)
- **Use numbered placeholders** (`%1$s`, `%2$s`) instead of positional (`%s`)
- This allows translators to reorder placeholders for different languages
- Correct: `$l->t('Birthday of %1$s on %2$s', [$name, $date])`
- Wrong: `$l->t('Birthday of %s on %s', [$name, $date])`

#### Complete Sentences
- **Never use incomplete sentences** that rely on adjacent HTML elements
- Include placeholders in the translation string itself
- Correct: `t('birthday_widget', 'Showing birthdays for {user}', { user: userName })`
- Wrong: `t('birthday_widget', 'Showing birthdays for')` followed by `<strong>{{ userName }}</strong>`

#### Plural Forms
- **Use `n()` function** for strings with counts that need singular/plural forms
- Correct: `n('birthday_widget', '{count} birthday today', '{count} birthdays today', count, { count })`
- Wrong: `t('birthday_widget', '{count} birthdays today', { count })`

#### Confirmation Dialogs
- **Keep confirmation language simple** - avoid words like "really" or "all"

## Project Structure

### PHP Backend
- Use PHP 8.1+ syntax
- Follow PSR-12 Code Style
- Use Dependency Injection via Nextcloud Container
- All Services in `lib/Service/` directory
- Controllers in `lib/Controller/` directory
- Define API routes in `appinfo/routes.php`

### Architecture
- This app has **no database** - it reads birthday data from the auto-generated `contact_birthdays` calendar via CalDAV
- `BirthdayService` - parses birthday events from the contact_birthdays calendar
- `ConfigService` - manages admin settings (date ranges for past/future birthdays)
- `Widget` - implements `IAPIWidget` for the Nextcloud dashboard
- Admin settings allow configuring the date range for past and future birthdays

### API Conventions
- Follow RESTful conventions
- Use proper HTTP status codes

## Build & Dependencies
- `package.json` for npm dependencies
- `composer.json` for PHP dependencies
- Vite as build tool (vite.config.js)

## Debugging Discipline
- Identify root cause before implementation
- Prefer minimal upstream fixes over downstream workarounds

## Release Management
- When I ask you to prepare a release, check if everything is committed
- Then check all changes since last release (use for example git log --oneline)
- Disable the app via occ command in the running docker containers (container names: master-stable31-1 and master-nextcloud-1)
- Decide which version jump (fix, patch) based on the changes since last version to create and update version numbers in info.xml and package.json
- Enable the app via occ command in the running docker containers (container names: master-stable31-1 and master-nextcloud-1)
- Write release notes in CHANGELOG.md
- Ask me to review the release notes and version number afterwards
- Commit everything you changed after my review WITHOUT claude co author in the commit
- Create a new tag based on the version number
- Push the tag to the remote repository
- Create a new release on GitHub via GitHub MCP which triggers the release process and upload to nextcloud app store

## Avoid
- NO coauthoring of commits with "claude"!
- No German strings in t() calls
- No client-side file operations (use server-side Nextcloud APIs)
