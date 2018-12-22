.checkout
=========

A Symfony project created on December 7, 2018, 3:32 pm.

In order to configure S3 file upload, add these parameters in app/config/parameters.yml

```
aws_key: YOUR_AWS_KEY
aws_secret_key: YOUR_AWS_SECRET_KEY
aws_default_region: YOUR_AWS_DEFAULT_REGION
aws_bucket_name: YOUR_AWS_BUCKET_NAME
aws_base_url: YOUR_AWS_BASE_URL
```

...and to configure Pusher real time messaging service set these:

```
pusher_app_id: YOUR_PUSHER_APP_ID
pusher_key: YOUR_PUSHER_KEY
pusher_secret: YOUR_PUSHER_SECRET
pusher_cluster: YOUR_PUSHER_CLUSTER
```

```
composer install
./bin/console doctrine:fixtures:load
```

