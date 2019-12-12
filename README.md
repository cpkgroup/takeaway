# Email Micro Service Coding Challenge

## Installation

To start this application, you need to do following steps:

- Run from the project root:

```
docker-compose build
docker-compose up -d
docker-compose run php composer install
```

- Wait until the docker up after run these commands:

```
docker-compose run php bin/console doctrine:schema:update --force  # generate mysql schema
docker-compose run -d php bin/console messenger:consume            # rabbitMQ message consumer
```

- Open [http://localhost](http://localhost)


## Tests

- Run from the project root:

```
docker-compose run php bin/phpunit              # to run unit tests
docker-compose run php /www/vendor/bin/behat    # to run behat tests
```

- Run this to see the unit-test coverage:

```
docker-compose run php bin/phpunit --coverage-html /www/var/coverage
```

See the results in var/coverage/index.html

## Coding Style
- This project follows [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
- Package used for PSR-2 rules: https://cs.symfony.com/

```
docker-compose run php /www/vendor/bin/php-cs-fixer fix /www/src
```

## Technologies
- Docker
- MySQL
- Symfony 4.3 for PHP Framework
- VueJS
- RabbitMQ

## Cli Commands

#### Send email via CLI Command

**Format:**
```
docker-compose run php bin/console app:send-email {subject} {recipient} {body}
```

**Sample:**
```
docker-compose run php bin/console app:send-email hello habibi.mh@gmail.com "this is the email from command cli"
```

#### Queue again failed emails

**Format:**
```
docker-compose run php bin/console app:queue-failed-emails {limit} # limit is optional, default 100
```

**Sample:**
```
docker-compose run php bin/console app:queue-failed-emails
```

#### Queue again pending emails

This command is useful when we the queue is crashed or reset.

**Format:**
```
docker-compose run php bin/console app:queue-failed-emails {limit} # limit is optional, default 100
```

**Sample:**
```
docker-compose run php bin/console app:queue-failed-emails
```

## Endpoints Examples

#### Send email API
We have 3 `messageType`: 
 - 0: text
 - 1: html
 - 2: markdown

**Simple Request**:
```
POST http://localhost/api/email
{
  "subject": "Test Email",
  "body": "Hello Mohamad! This is a test email!",
  "messageType": 0,
  "recipients": [
    {
      "email": "habibi.mh@gmail.com"
    }
  ]
}
```

**Complete Request:**
```
POST http://localhost/api/email
{
  "fromName": "Takeaway Challenge",
  "fromEmail": "challenge@takeaway.com",
  "subject": "Test Email",
  "body": "Hello _Mohamad_! This is a test email!",
  "bodyTextPart": "Hello Mohamad! This is a test email!",
  "messageType": 2,
  "recipients": [
    {
      "name": "Mohamad",
      "email": "habibi.mh@gmail.com"
    },
    {
      "name": "Mohamad",
      "email": "habibi.mh@yandex.ru"
    }
  ]
}
```

**Response:**
```
Status 202
{
    "succeed": true
}
```

#### Show email logs API
**Request:**
```
GET http://localhost/api/email
```

## Author
- [Mohamad Habibi](https://www.linkedin.com/in/habibimh) 
