<?php ## Соединение с БД
try {
	$pdo = new PDO("mysql:
			host=" . HOST .";
			dbname=" . DB_NAME . ";
			charset=utf8;", 
			DB_USER, 
			DB_USER_PASS
	);
} catch (Exception $e) {
	die("Невозможно установить соединение с базой данных");
}
