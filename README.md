[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b997c94e221a4e84a7b4dfbaed687d04)](https://www.codacy.com/app/Chrisentem/ce_oc_p4?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Chrisentem/ce_oc_p4&amp;utm_campaign=Badge_Grade)

# Online ticketing App for a museum

Project based on Symfony 3.4 and created on September 18, 2018.

This project was part of the OpenClassrooms Multimedia Project Manager Training Course.

---
#### CONSISTS OF & FEATURES :
    * 4 steps purchase tunnel with breadcrumb
    * 1 contact form page
    * 2 static pages - legal notice & Sales Terms
    
    * Stripe payment solution integrated
    * Swiftmailer ticket generation and sending
    * 99.99% FR/EN translated
---
#### REQUIREMENT :

To install this application, the following are required :
* A web server running Apache 
* PHP 5.5.9 at minima (version 7.2 is **recommended**)
* A MySQL database
* composer should be installed on the machine you are going to use for this application. (see https://getcomposer.org/)
---
#### INSTALLATION :

* Create a folder on your server to receive the application.
* Download the all the files from Github into the folder on your server:
you can clone the repo using 'https://github.com/Chrisentem/ce_oc_p4.git'
* Once finished, run composer install to get all the dependencies of this application.
* Open the file parameters.yml and fill the required key values: database, mailer, stripe, reCaptcha
---
#### DATABASE SETUP :

On your root folder run the following commands :
* php bin/console doctrine:database:create (this will create the
database)
* php bin/console doctrine:migrations:migrate Answer "y" at the 
question (this will create your tables in the database)

Your web site is now ready to work.
Enjoy !