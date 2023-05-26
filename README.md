<h1>ЛЦТ 2023</h1>
<h2>4 - Сервис обработки обратной связи пользователей «Московского постамата»</h2>
<h3>Команда DeCODE-HS</h3>
<h4>Базовая настройка проекта</h4>
<ul>
    <li>Установите зависимости:<br/>
    composer install<br/>
    npm install</li>
    <li>Создайте в корне файл .env (для референса есть .env.example)</li>
    <li>php artisan key:generate</li>
    <li>Настройка .env<br/>
        DB_CONNECTION=pgsql<br/>
        DB_HOST=ваш_хост<br/>
        DB_PORT=порт_хоста<br/>
        DB_DATABASE=название_бд<br/>
        DB_USERNAME=имя_юзера_бд<br/>
        DB_PASSWORD=пароль_юзера_бд</li>
</ul>
