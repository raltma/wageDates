# SpinTek Recruitment Task
## Introduction
This project is designed to output all the dates when the wages have to be payed. It was created using php 8.2

To get started with the project, please follow the instructions below.
## Running the app with Docker

Before you get started, make sure you have the following installed:
- Git
- Docker

### Clone the repository

Open up a terminal or command prompt and clone your Git repository:
```sh
git clone https://github.com/raltma/spinTek.git
```
### Build the Docker image

Navigate to the root directory of the cloned repository and run the following command to build the Docker image:
```sh
docker build -t spintek .
```
### Run the Docker container

Once the Docker image is built, you can run the Docker container with the following command:
```sh
docker run -p 80:80 spintek
```
With this command, you can access your PHP application in a web browser by navigating to http://localhost.

### Stopping the Docker container

To stop the Docker container, use the following command:
```sh
docker stop <container-id>
```
Replace \<container-id\> with the ID of the Docker container, which can be found by running the `docker ps` command.

## Usage

The script can be accessed via the following url:

http://localhost/?year=2023

Where *year* is the year for which you want to calculate the pay dates. The response will be in JSON format and will contain the pay dates for each month of the year as well as the reminder dates.  
The pay dates will usually be the 10th day of the month, but if that date lands on weekend or national holiday then the next work day will be chosen.  
Same applies to the remainder dates, but they are initially chosen as 3 days before the pay date.
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
  ]
}
```