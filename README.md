***

# BLOG_SF6

EXEMPLE DE STRUCTURE DE BLOG SUR SYMFONY 6 

***

## Pour cloner le projet
```
git clone https://github.com/VinceFormateur/BLOG_SF6.git
```

## Installation
```
composer install (pour la partie Composants installés)
npm install (pour la partie Webpack Encore)
npm run dev (pour le dossier Public de l'application)
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
symfony console doctrine:fixtures:load
symfony console assets:install public
```

## Les fixtures créent :
   > - Un compte en rôle admin (email: a@a.fr, username: admin, password : Password1* )
   > - Un compte en rôle blogger (email: b@b.fr, username: blogger, password : Password1* )
   > - Un compte en rôle user (email: c@c.fr, username: user, password : Password1* )
   > - 47 comptes utilisateurs (email aléatoire, username aléatoire, password : Password1* )
   > - 100 publications
   > - entre 0 et 20 commentaires par publication



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
composer require --dev orm-fixtures
composer require fakerphp/faker
composer require knplabs/knp-paginator-bundle
composer require friendsofsymfony/ckeditor-bundle
composer require exercise/htmlpurifier-bundle -> (voir une version compatible Symfony 6)
composer require symfony/google-mailer (Uniquement pour le service GMail)
```
