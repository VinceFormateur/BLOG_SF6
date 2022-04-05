# BLOG_WF3

EXEMPLE DE STRUCTURE DE BLOG SUR SYMFONY 6 

## Pour cloner le projet
```
git clone https://github.com/VinceFormateur/BLOG_WF3.git
```

## Installation
```
composer install (pour la partie Composants installés)
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
npm install (pour la partie Webpack Encore)
```

### Webpack Encore (voir package.json)
```
 npm install sass-loader@^12.0.0 sass --save-dev
 npm install postcss-loader@^6.0.0 --save-dev
 npm install autoprefixer --save-dev
 npm install bootstrap --save-dev
 npm install bootswatch
 npm install jquery
 npm install --save @fortawesome/fontawesome-free
```

### Composants installés (voir composer.json)
```
pour Update -> composer update
composer require symfony/webpack-encore-bundle (Webpack Encore)
composer require stof/doctrine-extensions-bundle (Slug)
composer require symfony/rate-limiter (Limitation de tentatives de connexion)
```

