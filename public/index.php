<?php
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

function dbconnect() {
    $dbname = "mesproduits2";
    $dbuser = "mesproduits2";
    $dbhost = "localhost";
    $dbpassword = "***";

    try {
        $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
    } catch(PDOException $e) {
        echo "Erreur connexion " . $e->getMessage();
    }
    return $conn;
}

$app = AppFactory::create();

$app->get('/produits', function(Request $request, Response $response, $args) {
    $sql = "SELECT * FROM produits";
    $conn = dbconnect();
    $statement = $conn->prepare($sql);
    $statement->execute();

    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($results);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write($json);
});

$app->get('/produit/{id}', function(Request $request, Response $response, $args) {
    $id = $args['id'];
    $sql = "SELECT * FROM produits WHERE id = :id";
    $conn = dbconnect();
    $statement = $conn->prepare($sql);
    $statement->execute(array('id' => $id));

    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode($results);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write($json);
});

$app->post('/produit', function(Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $nom = $data['nom'];
    $prix = $data['prix'];

    $sql = "INSERT INTO produits (nom, prix) VALUES (:nom, :prix)";
    $conn = dbconnect();
    $statement = $conn->prepare($sql);
    $result = $statement->execute(array('nom' => $nom, 'prix' => $prix));
    $json = json_encode($result);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write($json);

});


$app->delete('/produit/{id}', function(Request $request, Response $response, $args) {
    $id = $args['id'];
    $sql = "DELETE FROM produits WHERE id = :id";
    $conn = dbconnect();
    $statement = $conn->prepare($sql);
    $result = $statement->execute(array('id' => $id));

    $json = json_encode($result);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write($json);
});

$app->run();
