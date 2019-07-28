## Quick dev-env deployment guide
Copy `./env.dev` into './env'.
Asuming your env is compatible with the Laravel version mentioned in the composer.json:
```
composer install
npm install
```
If you encounter any problems, you're likely missing some PHP modules required by Laravel

### Start the API dev-server with
```
php artisan serve
```

### Start the PubSub server with
```
php artisan BankWireDaemon

```

## Notes
Task description includes some errors:

- Tests:
there're errors in tests, please see comments in `tests/Unit/BusinesDaysTest.php`

- Outdated references:
the [https://www.usbank.com/hr/holidays.asp](https://www.usbank.com/hr/holidays.asp) link is 404.

## Testing
From the application root folder, run ```vendor/bin/phpunit```

## Checking the pub-sub
Asuming you have a non-protected running redis server on your dev machine (this could be adjusted 
to use other drivers):

####Subscribe with:
```
redis-cli
subscribe BankWire:businessDates 
```

####Request information with:
```
redis-cli
publish BankWire:businessDates '{"initialDate": "2018-12-12T10:10:10Z", "delay": 5}'
```

## HTTP API server
For this development setup, send a GET or POST request to:
```http://localhost:8000/api/v1/businessDates/getBusinessDateWithDelay```
with a body like this:
```
{
  "initialDate": "2018-11-29T10:10:10Z",
  "delay": 10
}
```

## Technical notes
- For DI interface/implementation bindings, please refer to `config/bindings.php` 
- You can easily replace the dates calculator by implementing and binding `app/Tools/BusinessDatesCalcInterface`
- You may want to drammatically improve the holidays data source by by implementing and binding `app/DataSources/HolidaysSourceInterface`
- The PubSub driver and credentials are configured via `config/pubsub`. It uses env variables and has a fallback
  to defaults for typical dev setups.
- API server makes no difference between POST and GET calls but old HTTP RFC says that GET body should be ignored.
  These days its obsolete but many clients still just ignore, ommit or won't support GET queries body. 
- Logging. I would rather create a PubSub redis-backed logger since logging here should not be service life-critical thing.
- There are many things to improve and adjust here but its already far above 3h time-to-develop limit.
- IMHO, the PubSub here is useless in terms of scaling in current implementation. I would rather use a redis-backed queues: 
  a subscriber-server that receives all the calculations requests and puts them into a queue, a set of workers that pick those jobs from the queue
  and publish jobs results to the response bus. Thus it would be possible to have as many workers as needed for heavy calculations. 
