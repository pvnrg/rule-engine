# rule-engine

#### setup

- check `.env` file and verify parameters. Specially debricked credentials.
- run `composer install`.
- run `php bin/console d:d:c` to create database.
- run `php bin/console d:m:m` to migrate database.
- run `php bin/console d:f:l` to load fixtures.
- keep running `php bin/console messenger:consume` in terminal to consume all messages.


#### Login

Using postman or similar tool, send `POST` request to `/api/login` route. Request payload
should be,

```
{
    "username": "john@ruleengine.local",
    "password": "john1234"
}
```

You should receive message `login success`.

#### Upload

Make `POST` to `/upload` route. In postman, select `form-data` option, add key `dependency[]`
and select type `File`. Now you should be able to upload multiple files. 
Files will be uploaded to `/uploads/<user>/` directory.


#### Trigger
On Running migration, you should have predefined list of rules and notifications.

Make `GET` to `/triggers` route. No payload needed. You will receive response as list of all rules,
notifications and current triggers set for this user.

```
{
    "rules": [
        {
            "id": 3,
            "name": "vulnerability_found"
        },
        {
            "id": 4,
            "name": "upload_fail"
        }
    ],
    "notifications": [
        {
            "id": 3,
            "name": "email"
        },
        {
            "id": 4,
            "name": "slack"
        }
    ],
    "triggers": [
        {
            "rule": "upload_fail",
            "notification": "email"
        },
        {
            "rule": "upload_fail",
            "notification": "slack"
        },
        {
            "rule": "vulnerability_found",
            "notification": "email"
        }
    ]
}
```

Make `POST` to same route with required triggers. Payload should be like this.

```
[
    {
        "rule": 4, // id of rule
        "notifications": [
            3, // id of notification
            4
        ]
    },
    {
        "rule": 3,
        "notifications": [
            3
        ]
    }
]
```

#### TODO:

- Slack channel config for notification
- Add logging for detailed debug
- Better error handling
