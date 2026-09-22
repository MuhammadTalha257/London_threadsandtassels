#!/bin/bash
cd "/Applications/XAMPP/xamppfiles/htdocs/London_threadsandtassels"
echo "Starting Laravel dev server on http://localhost:8000 ..."
which php
php -v
php artisan serve
