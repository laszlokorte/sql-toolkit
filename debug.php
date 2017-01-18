<?php 
	date_default_timezone_set("Europe/Berlin");

	ob_start();

	use \Firebase\JWT\JWT;

	use LaszloKorte\Schema\Schema;
	use LaszloKorte\Schema\Table;
	use LaszloKorte\Schema\DatabaseId;
	use LaszloKorte\Schema\SchemaBuilder;
	use LaszloKorte\Schema\ColumnType;
	use LaszloKorte\Schema\ForeignKey;

	use LaszloKorte\Resource\IdConverter;
	use LaszloKorte\Resource\TableConverter;
	use LaszloKorte\Resource\ParameterBag;

	use LaszloKorte\Configurator\ConfigurationBuilder;
	use LaszloKorte\Presenter\ApplicationBuilder;
	use LaszloKorte\Presenter\Application;

	use Doctrine\Common\Annotations\AnnotationRegistry;
	use Doctrine\Common\Inflector\Inflector;

	$loader = require __DIR__ . '/vendor/autoload.php';
	AnnotationRegistry::registerLoader([$loader,'loadClass']);

	$jwtKey = "c303c6c7125d5e365ed7323f6143fb58";
	$builder = new SchemaBuilder();


	$connection = new PDO('mysql:host=directus.dev;port=3306;dbname=ishl;charset=utf8', 'ishl', 'ishl', [
			PDO::ATTR_TIMEOUT => 2,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	   		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
		]);

$schemaCache = __DIR__ . '/cache/schema.txt';
if(!file_exists($schemaCache)) {
	$schemaDef = $builder->buildSchemaFor($connection, 'ishl')->getDef();
	file_put_contents($schemaCache, serialize($schemaDef));
}

$schemaDef = unserialize(file_get_contents($schemaCache));

$schema = new Schema($schemaDef);

$inflector = new Inflector();

$confBuilder = new ConfigurationBuilder();

$schemaConf = $confBuilder->buildConfigurationFor($schema);

$appBuilder = new ApplicationBuilder();

$appDef = $appBuilder->buildApplication($schemaConf);

$app = new Application($appDef);

?>

<?php foreach ($app->groups() as $group): ?>
	<h2><?php echo $group->title() ?></h2>

	<?php foreach ($group->entities() as $entity): ?>
		<h3><?php echo $entity->title() ?> [<?php echo $entit->id() ?>]</h3>

		<p>
			<?php echo $entity->description() ?>
		</p>

		<ul>
			<?php if ($entity->isIdentifiable()): ?>
				<li>Identifiable</li>
			<?php endif ?>
			<?php if ($entity->isSearchable()): ?>
				<li>Searchable</li>
			<?php endif ?>
			<?php if ($entity->isSortable()): ?>
				<li>Sortable</li>
			<?php endif ?>
		</ul>

		<ul>
			<?php foreach ($entity->fields() as $field): ?>
				<li>
					<h4>
						<?php echo $field->title() ?> [<?php echo $field->id() ?>]
						<?php if ($field->isRequired()): ?>
							*
						<?php endif ?>
					</h4>
					<p>
						<?php echo $field->description() ?>
					</p>
				</li>
			<?php endforeach ?>
		</ul>
	<?php endforeach ?>
<?php endforeach ?>

</body>
</html>