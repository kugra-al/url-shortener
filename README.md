Simple URL redirection service implemented in Laravel 10 and Vue.

* Form that converts valid, submitted URLs into 6 character long alphanumeric short URLs.

* Duplicate URLs show previously generated shortcode.

* After URL is submitted, URL is checked against Google Safe Browsing API before outputing a short URL.

* Short URLs can be previewed. If the URL is safe, browser is redirected to the long URL. If the URL is unsafe or not yet checked, nothing happens.

## Setup

### Clone the repository
```
git clone https://github.com/kugra-al/url-shortener.git
```
### Setup .env file
Copy .env.example to .env.  Add details for DB_DATABASE, DB_USERNAME, DB_PASSWORD.  Setup API access for [Google Safe Browsing](https://developers.google.com/safe-browsing/v4/get-started) and add API key to GOOGLE_API_KEY

### Generate key
```
php artisan key:generate
```

### Install dependencies 
```
composer install
npm install
```

### Run database migrations
```
php artisan migrate
```

### Run servers
```
npm run dev
php artisan serve 
php artisan queue:listen
```
### Use
Navigate to http://127.0.0.1:8000 (or whatever the address returned in `php artisan serve`) and enter a URL to be forwarded.
