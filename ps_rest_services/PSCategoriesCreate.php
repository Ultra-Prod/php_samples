<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Daniel
 * Date: 11/16/12
 * Time: 11:05 AM
 */
?>
<html><head><title>CRUD - Create Categories</title></head><body>
<?php
// Here we define constants /!\ You need to replace this parameters
define('DEBUG', true);
define('PS_SHOP_PATH', 'localhost');
define('PS_WS_AUTH_KEY', 'xxxxxxxxxxxxxxxxxxx');
require_once('./PSWebServiceLibrary.php');

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Here we use the WebService to get the schema of "customers" resource
try
{
    $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
    $opt = array('resource' => 'categories');
    if (isset($_GET['Create']))
        $xml = $webService->get(array('url' => PS_SHOP_PATH.'/api/categories?schema=blank'));
    else
        $xml = $webService->get($opt);
    $resources = $xml->children()->children();
}
catch (PrestaShopWebserviceException $e)
{
    // Here we are dealing with errors
    $trace = $e->getTrace();
    if ($trace[0]['args'][0] == 404) echo 'Bad ID';
    else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
    else echo 'Other error';
}

if (count($_POST) > 0)
{
// Here we have XML before update, lets update XML
    foreach ($resources as $nodeKey => $node)
    {
        $resources->$nodeKey = $_POST[$nodeKey];
    }
    try
    {
        $opt = array('resource' => 'categories');
        if ($_GET['Create'] == 'Creating')
        {
            $opt['postXml'] = $xml->asXML();
            $xml = $webService->add($opt);
            echo "Successfully added.";
        }
    }
    catch (PrestaShopWebserviceException $ex)
    {
        // Here we are dealing with errors
        $trace = $ex->getTrace();
        if ($trace[0]['args'][0] == 404) echo 'Bad ID';
        else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
        else echo 'Other error<br />'.$ex->getMessage();
    }
}

// We set the Title
echo '<h1>Categories\'s ';
if (isset($_GET['Create'])) echo 'Creation';
else echo 'List';
echo '</h1>';

// We set a link to go back to list if we are in creation
if (isset($_GET['Create']))
    echo '<a href="?">Return to the list</a>';

if (!isset($_GET['Create']))
    echo '<input type="button" onClick="document.location.href=\'?Create\'" value="Create">';
else
    echo '<form method="POST" action="?Create=Creating">';

echo '<table border="5">';
if (isset($resources))
{

    echo '<tr>';
    if (count($_GET) == 0)
    {
        echo '<th>Id</th></tr>';

        foreach ($resources as $resource)
        {
            echo '<tr><td>'.$resource->attributes().'</td></tr>';
        }
    }
    else
    {
        echo '</tr>';
        foreach ($resources as $key => $resource)
        {
            echo '<tr><th>'.$key;
            if ((bool)$resource['required'] == true)
                echo ' (*)'; // * for required fields
            echo '</th><td>';
            if (isset($_GET['Create']))
                echo '<input type="text" name="'.$key.'" value=""/>';
            echo '</td></tr>';
        }
    }

}
echo '</table><br/>';

if (isset($_GET['Create']))
    echo '<input type="submit" value="Create"></form>';

?>
</body></html>
