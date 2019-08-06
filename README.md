# gooddata-extractor

KBC Docker app for extracting reports from GoodData.

## Status

[![Build Status](https://travis-ci.org/keboola/gooddata-extractor.svg)](https://travis-ci.org/keboola/gooddata-extractor) [![Code Climate](https://codeclimate.com/github/keboola/gooddata-extractor/badges/gpa.svg)](https://codeclimate.com/github/keboola/gooddata-extractor)


## Configuration

You can either directly specify GoodData credentials (`username` and `#password`) or `pid` of a GoodData project which is registered in GoodData Provisioning. In that case the Provisioning generates temporary credentials and passes them to the Extractor during each job.

There is also a legacy option to specify `writer_id`, i.e. id of configuration of the deprecated version of GoodData Writer. In that case the Extractor gets credentials directly from the Writer's configuration. 

- **parameters**:
    - **username** - GoodData username
    - **#password** - GoodData password, encrypted by KBC
    - **pid** - Pid of GoodData project if it is registered in Provisioning
    - **writer_id** - Id of existing writer whose credentials will be used (*Deprecated*)
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
- **GD_PROVISIONING_PID** - Id of a project provided by Provisioning
- **GD_PROVISIONING_URL** - Url of Provisioning instance (i.e. `https://gooddata-provisioning.keboola.com`)
- **KBC_TOKEN** - Storage token used for auth in the Provisioning
- **KBC_URL** - Url of Connection instance (i.e. `https://connection.keboola.com`)
