<!DOCTYPE html>
<html lang="es">
<head>
    <title>Prueba de Formulario</title>
    <meta charset="UTF-8">
</head>
<body>

<?php
require_once './google-api-php-client/src/Google/autoload.php';
require_once './google-api-php-client/src/Google/Client.php';
require_once './google-api-php-client/src/Google/Service/Calendar.php';

define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', __DIR__ . '/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR)
));

/*if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}*/

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfigFile(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = file_get_contents($credentialsPath);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->authenticate($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, $accessToken);
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->refreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, $client->getAccessToken());
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
  }
  return str_replace('~', realpath($homeDirectory), $path);
}


if(isset($_POST['submit'])){

   // Get the API client and construct the service object.
   $client = getClient();
   $service = new Google_Service_Calendar($client);

   // Print the next 10 events on the user's calendar.
   $calendarId = 'primary';
   $optParams = array(
     'maxResults' => 10,
     'orderBy' => 'startTime',
     'singleEvents' => TRUE,
     'timeMin' => date('c'),
   );
   $results = $service->events->listEvents($calendarId, $optParams);

   if (count($results->getItems()) == 0) {
     print "No upcoming events found.\n";
   } else {
     print "Upcoming events:\n";
     foreach ($results->getItems() as $event) {
       $start = $event->start->dateTime;
       if (empty($start)) {
         $start = $event->start->date;
       }
       printf("%s (%s)\n", $event->getSummary(), $start);
     }
   }

    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $telefono = $_POST["telefono"];
    $tevento = $_POST["tevento"];
    $organizador = $_POST["organizador"];
    $organismo = $_POST["organismo"];
    $lugar = $_POST["lugar"];
    $ponentes = $_POST["ponentes"];
    $numponentes = $_POST["numponentes"];
    $fecha_inicio = $_POST["fecha_inicio"];
    $hora_inicio = $_POST["hora_inicio"];
    $fecha_fin = $_POST["fecha_fin"];
    $hora_fin = $_POST["hora_fin"];

    $descripcion= $nombre. "\n". $correo. "\n". $telefono. "\n".$organizador. "\n". $organismo. "\n\n". "El múmero de ponentes es ". $numponentes. "\n". "Ponentes:". "\n". $ponentes;
    $inicio= $fecha_inicio. "T". $hora_inicio. ":00.000+01:00";
    $fin= $fecha_fin. "T". $hora_fin. ":00.000+01:00";

   if ($client->getAccessToken()){
      //echo "<hr><font size=+1>I have access to your calendar</font>";
      $event = new Google_Service_Calendar_Event();
      $event->setSummary('Evento:'.$tevento);
      $event->setLocation($lugar);
      $event->setDescription($descripcion);
      $start = new Google_Service_Calendar_EventDateTime();
      //$start->setDateTime('2016-4-30T10:00:00.000-05:00');
      $start->setDateTime($inicio);
      $event->setStart($start);
      $end = new Google_Service_Calendar_EventDateTime();
      //$end->setDateTime('2016-4-30T10:25:00.000-05:00');
      $end->setDateTime($fin);
      $event->setEnd($end);
      $createdEvent = $service->events->insert($calendarId, $event);
      //echo "<br><font size=+1>Event created</font>";
   }

   //header("Location: http://localhost/index.html");
   //die();
}
?>

<div align="center">
<iframe src="https://calendar.google.com/calendar/embed?src=jrealgom%40ull.edu.es&ctz=Atlantic/Canary" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
<br><br>
</div>

<form action="" method="post" >
 <table width="400" border="0" cellspacing="0" cellpadding="0" align="center">
 <tr>
 <td>Nombre del solicitante:</td>
 <td><input type="text" name="nombre" placeholder="Escribe Tu nombre" required></td>
 </tr>
<tr>
 <td>E-mail del solicitante:</td>
 <td><input type="email" name="correo" placeholder="Aqui@tucorreo.com" required></td>
 </tr>
<tr>
 <td>Telefono del solicitante:</td>
 <td><input type="text" name="telefono" placeholder="Telefono" required></td>
 </tr>
<tr>
<td>Titulo del Evento:</td>
 <td><input type="text" name="tevento" required></td>
</tr>
<tr>
 <td>Organizador del Evento:</td>
 <td><input type="text" name="organizador" placeholder="Telefono" required></td>
 </tr>
<tr>
<td>Entidad u organismo:</td>
 <td><input type="text" name="organismo" required></td>
</tr>
<tr>
 <td>Lugar de celebracion:</td>
 <td><textarea cols="25" rows="6" name="lugar" placeholder="Escribe el Lugar" required></textarea></td>
</tr>
<tr>
 <td>Nombre, apellidos y cargo de cada ponente</td>
 <td><textarea cols="25" rows="6" name="ponentes" placeholder="Ponentes" required></textarea></td>
</tr>
<tr>
 <td>Numero de ponentes:</td>
 <td><input type="number" name="numponentes" max="100" min="0" placeholder="Numero de ponentes" required></td>
 </tr>
<tr>
<tr>
 <td>Fecha inicio:</td>
 <td><input type="date" name="fecha_inicio"></td>
 </tr>
<tr>
<tr>
 <td>Hora Inicio:</td>
 <td><input type="time" name="hora_inicio"></td>
 </tr>
<tr>
<tr>
 <td>Fecha fin:</td>
 <td><input type="date" name="fecha_fin"></td>
 </tr>
<tr>
<tr>
 <td>Hora fin:</td>
 <td><input type="time" name="hora_fin"></td>
 </tr>
<tr>
<tr>
 <td>&nbsp;</td>
 <td><input type="submit" value="Reservar" name='submit'/></td>
 </tr>
<tr>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
 </tr>
 </table> 
</form>

</body>
</html>
