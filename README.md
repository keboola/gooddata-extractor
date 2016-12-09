# gooddata-extractor

KBC Docker app for extracting reports from GoodData.

## Status

[![Build Status](https://travis-ci.org/keboola/gooddata-extractor.svg)](https://travis-ci.org/keboola/gooddata-extractor) [![Code Climate](https://codeclimate.com/github/keboola/gooddata-extractor/badges/gpa.svg)](https://codeclimate.com/github/keboola/gooddata-extractor)


## Configuration

You can either directly specify GoodData credentials or id of existing GoodData writer whose credentials will be used.

- **parameters**:
    - **writer_id** - Id of existing writer whose credentials will be used
    - **username** - GoodData username
    - **#password** - GoodData password, encrypted by KBC
    - **bucket** - Name of bucket where the data will be saved
    - **reports** - Array of report uris to download
    
Example:
```
{
    "parameters": {
        "writer_id": "main",
        "bucket": "in.c-ex-gooddata-main",
        "reports": [
            "/gdc/md/wasc4gjy5sphvlt0wjx5fqys5q6bh38j/obj/2284",
            "/gdc/md/wasc4gjy5sphvlt0wjx5fqys5q6bh38j/obj/2113"
        ]
    }
}
```

Or:
```
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
6. Create file `data/config.yml` with configuration, e.g.:

    ```
    parameters:
      username:
      password:
      bucket: in.c-ex-gooddata
      reports:
        ...
    ```
7. Run: `php src/run.php --data=./data`
8. Data tables will be saved to directory `data/out/tables`


## Testing

For integration testing you have to prepare a project in GoodData. Please note that all data and reports in the project will be deleted. 

Run `phpunit` with these env variables

- **EX_GD_USERNAME**
- **EX_GD_PASSWORD**
- **EX_GD_PROJECT** - GoodData project id