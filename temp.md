##Below is an example request for all holidays in the United States for 2019. Be sure to replace the api_key with a valid value from your account page.

curl 'https://calendarific.com/api/v2/holidays?&api_key=baa9dc110aa712sd3a9fa2a3dwb6c01d4c875950dc32vs&country=US&year=2019'


BTW api key is 


##Below is a shortened API response that executes successfully

{
    "meta": {
        "code": 200
    },
    "response": {
        "holidays": [
            {
                "name": "Name of holiday goes here",
                "description": "Description of holiday goes here",
                "date": {
                    "iso": "2018-12-31",
                    "datetime": {
                        "year": 2018,
                        "month": 12,
                        "day": 31
                    }
                },
                "type": [
                    "Type of Observance goes here"
                ]
            }
        ]
    }
}