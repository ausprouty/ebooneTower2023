declare(strict_types=1);
include __DIR__ . '/../../.env.api.remote.php';
include './bible-blocks.php';
include './bible-block-link.php';
include './bible-block-migrate.php';


$mysqli = new mysqli(
HOST,
USER,
PASS,
DATABASE_CONTENT
);

if ($mysqli->connect_error) {
die('Database connection failed: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

migrateJapaneseBibleLinksForFile($mysqli, 'hope', 'hope01');