Insurance Broker App
=========

A Symfony project created on December 7, 2018, 3:32 pm. for the [PHP MVC Frameworks - Symfony - November 2018](https://softuni.bg/trainings/2198/php-mvc-frameworks-symfony-november-2018) course @ [SoftUni](https://softuni.bg) 

### About

This app is intended to assist insurance brokers to manage their car insurance policies and notify clients for coming payments.

---

### Demo

https://insurance.lynxlake.org/login

Username: admin@admin.com

Password: 111111

---

### Installation

From terminal run these commands:

```
git clone https://github.com/plamen911/symfony-insurance-broker.git
cd symfony-insurance-broker/
composer install
```

In order to configure [Amazon S3](https://s3.console.aws.amazon.com) file upload, you will be asked to set your credentials:

```
aws_key: YOUR_AWS_KEY
aws_secret_key: YOUR_AWS_SECRET_KEY
aws_default_region: YOUR_AWS_DEFAULT_REGION
aws_bucket_name: YOUR_AWS_BUCKET_NAME
aws_base_url: YOUR_AWS_BASE_URL
```

...and to configure [Pusher](https://pusher.com/) real time messaging service set these:

```
pusher_app_id: YOUR_PUSHER_APP_ID
pusher_key: YOUR_PUSHER_KEY
pusher_secret: YOUR_PUSHER_SECRET
pusher_cluster: YOUR_PUSHER_CLUSTER
```

...and finally, run fixtures to populate database with basic data

```
./bin/console doctrine:fixtures:load
```

That's all! Enjoy!
