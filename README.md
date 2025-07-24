## Cara Instalasi
Gunakan composer yang versi terbaru, jika belum install terlebih dahulu di [composer](https://getcomposer.org/download/).

Setelah composer terinstall lakukan perintah 
```
"composer require flynns7/http-logger" 
```
dalam folder project laravel

## Pentujuk Penggunaan
- Langkah selanjutnya publish file config dengan lakukan perintah "php artisan vendor:publish --tag=config" kemudian nanti akan muncul file app-logger.php di config\api-logger.php dan sesuaikan variable constant dalam file tersebut.

- Tahap selanjutnya adalah menambah channel "http" log pada file config/logging.php 
```
    'channels' => [

        'http' => [
            'driver' => 'http',
        ],
    ]
```
- Tahap terakhir adalah untuk membuat middleware atau custom middleware yg dipakai dengan menambahkan code sebagai berikut : 

```
  use Flynns7\HttpLogger\Traits\Logging;
  class LogRequests
  {
      use Logging;
      public function handle($request, Closure $next)
      {
          $request->attributes->set('start', microtime(true));
          return $next($request);
      }
  
      public function terminate($request, $response)
      {
          $request->attributes->set('end', microtime(true));
          $this->log($request, $response);
      }
  }
```

Tambahan jika ingin melakukan logging dalam file php lainnya seperti controller atau ketika melakukan http request : 
```
        $request = '' // diambil dari request body
        $response = '' //diambil dari response api atau response error lainnya
        $context = array(
            'request' => $request->all(),
            'response' => $this->extractResponseContent($response),
            'processing_time' => $duration,
        );
        Log::channel('http')->info("Request Log", $context);
```

untuk versi v0.1.0 ada tambahan untuk bisa mengubah case name sesuai kebuthan di table route_logs_mapping, tetapi sebelum itu wajib menjalankan artisan command sebagai berikut : 
```
php artisan http-logger:install
```

setelah mengubah name case di table lakukan perintahan sinkrosisasi sebagai berikut
```
http-logger:sync-routes-mapping
```