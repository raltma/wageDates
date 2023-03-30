# Wage dates
## Introduction
This project is designed to output all the dates when the wages have to be payed. It was created using php 8.2

To get started with the project, please follow the instructions below.
## Running the app with Docker

After downloading or cloning the repository navigate to the root directory of the repository and run the following command to run the app with docker:
```sh
docker-compose up -d
```
To stop the app from running run the following command while in the directory you started the app:

```sh
docker-compose down
```
## Usage

The app can be accessed via the following url:

http://localhost:8080/?year=2023

Where *year* is the year for which you want to calculate the pay dates.  
***The app accepts years between 1800-9999***  
The response will be in JSON format and will contain the pay dates for each month of the year as well as the reminder dates.  
The pay dates will usually be the 10th day of the month, but if that date lands on a weekend or national holiday then the next work day will be chosen.  
### Example

#### Request:

    GET /?year=2023

#### Response:


```json
{
  "year": 2023,
  "dates": [
    {
      "payDate": "2023-01-10",
      "reminderDate": "2023-01-06"
    },
    {
      "payDate": "2023-02-10",
      "reminderDate": "2023-02-07"
    },
    ...
    {
      "payDate": "2023-12-10",
      "reminderDate": "2023-12-06"
    }
  ]
}
```
