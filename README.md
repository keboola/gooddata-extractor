# GoodData Extractor

[![Build Status](https://travis-ci.org/keboola/gooddata-extractor.svg)](https://travis-ci.org/keboola/gooddata-extractor)
[![Code Climate](https://codeclimate.com/github/keboola/gooddata-extractor/badges/gpa.svg)](https://codeclimate.com/github/keboola/gooddata-extractor)

Application for extracting reports from GoodData.

## Configuration

GoodData credentials (`username` and `#password`) is the only supported method of authentication.

- **parameters**:
    - **username** - GoodData username
    - **#password** - GoodData password, encrypted by KBC
    - **pid** - Pid of GoodData project (**Deprecated**, ignored)
    - **writer_id** - ID of existing writer (**Deprecated**, ignored)
    - **bucket** - Name of bucket where the data will be saved
    - **reports** - Array of report uris to download
    
Example:

```json
{
    "parameters": {
        "username": "user@email.com",
        "#password": "nvksldfklsflks",
        "bucket": "in.c-ex-gooddata-main",
        "reports": [
            "/gdc/md/wasc4gjy5sphvlt0wjx5fqys5q6bh38j/obj/2284",
            "/gdc/md/wasc4gjy5sphvlt0wjx5fqys5q6bh38j/obj/2113"
        ]
    }
}
```

## Output

Extraction saves each report to one table in a specified bucket and names it with id of the report in GoodData.


## Installation

If you want to run this app standalone:

1. Clone the repository: `git@github.com:keboola/gooddata-extractor.git`
2. Go to the directory: `cd gooddata-extractor`
3. Install composer: `curl -s http://getcomposer.org/installer | php`
4. Install packages: `php composer.phar install`
5. Create folder `data`
6. Create file `data/config.json` with configuration, e.g.:

    ```
    {
        "parameters": {
            "username": "",
            "password": "",
            "bucket": "in.c-ex-gooddata",
            "reports": []
        }
    }
    ```
7. Run: `php src/run.php --data=./data`
8. Data tables will be saved to directory `data/out/tables`


## Testing

For integration testing you have to prepare a project in GoodData. Please note that all data and reports in the project will be deleted. 

Run `phpunit` with these env variables

- **EX_GD_USERNAME** - credentials to a user
- **EX_GD_PASSWORD** - credentials to a user
- **EX_GD_PROJECT** - Id of a project accessible by the user 
- **EX_GD_HOST** - Host of GoodData backend (`https://secure.gooddata.com`)
- **EX_GD_ALT_USERNAME** - credentials to a user from alternate backend
- **EX_GD_ALT_PASSWORD** - credentials to a user from alternate backend
- **EX_GD_ALT_PROJECT** - Id of a project accessible by the user from alternate backend
- **EX_GD_ALT_HOST** - Host of alternate GoodData backend (`https://gd-wl-dev.keboola.com`)
