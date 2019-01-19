## Installation

```
bin/dev/install.sh
```
Run script once. After it's done use:
```
docker-compose up -d
```
## Usage
Import records from csv file:
```
docker-compose exec php-fpm bash
bin/console csv:import --path=data.csv
```

app url http://localhost:8000

